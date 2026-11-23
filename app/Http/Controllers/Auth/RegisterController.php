<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\CmsSetting;
use App\Models\ProposalOption;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new tenant users as well as
    | creating their Shop on a guaranteed 7-Day Free Trial, recording the service
    | they are interested in, and alerting the platform Super Admin via email.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form with dynamic services.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $businessTypes = Shop::availableBusinessTypes();

        return view('auth.register', compact('businessTypes'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $businessTypeKeys = array_keys(Shop::availableBusinessTypes());

        return Validator::make($data, [
            'company_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:' . implode(',', $businessTypeKeys)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'company_name.required' => __('Please enter your Shop or Company name.'),
            'business_type.required' => __('Please select your business type or industry.'),
            'name.required' => __('Please enter your full name.'),
            'email.required' => __('Please enter a valid email address.'),
            'email.unique' => __('This email address is already registered. Please sign in instead.'),
            'phone.required' => __('Please enter your contact phone / WhatsApp number.'),
            'password.min' => __('Password must be at least 8 characters.'),
            'password.confirmed' => __('Password confirmation does not match.'),
        ]);
    }

    /**
     * Create a new tenant user instance and provision their Shop environment on a 7-Day Free Trial.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Mandatory 7-Day Free Trial Package
            $package = Package::where('slug', 'trial')->first() 
                    ?: Package::where('is_active', true)->orderBy('sort_order')->first();

            $durationDays = 7; // Fixed 7-day trial period
            $startsAt = now();
            $expiresAt = now()->addDays($durationDays);

            // 2. Normalize Phone Number
            $countryCode = $data['country_code'] ?? '+966';
            $rawPhone = $data['phone'] ?? null;
            $normalizedPhone = $rawPhone ? \App\Models\Customer::normalizePhone($rawPhone, $countryCode) : null;

            // 3. Business Type & Modules Resolution
            $businessType = $data['business_type'] ?? 'travel_umrah';
            $allTypes = Shop::availableBusinessTypes();
            $typeInfo = $allTypes[$businessType] ?? ($allTypes['travel_umrah'] ?? null);
            $typeName = $typeInfo['name'] ?? ucfirst(str_replace('_', ' ', $businessType));

            $enabledModules = Shop::defaultModulesForType($businessType);

            // 4. Generate Unique Slug for Shop
            $baseSlug = Str::slug($data['company_name']);
            if (empty($baseSlug)) {
                $baseSlug = 'shop-' . Str::lower(Str::random(6));
            }
            $slug = $baseSlug;
            $counter = 1;
            while (Shop::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // 5. Create Shop Record (with SAR Currency and Enabled Modules)
            $shop = Shop::create([
                'name' => $data['company_name'],
                'business_type' => $businessType,
                'enabled_modules' => $enabledModules,
                'slug' => $slug,
                'email' => $data['email'],
                'phone' => $normalizedPhone,
                'status' => 'active',
                'currency_symbol' => 'SAR',
                'invoice_prefix' => 'INV-',
                'quote_prefix' => 'QT-',
                'package_id' => $package?->id,
                'subscription_starts_at' => $startsAt,
                'subscription_expires_at' => $expiresAt,
                'subscription_status' => 'active',
                'notes' => "Industry: {$typeName} (7-Day Free Trial Self-Registration)",
            ]);

            // 6. Create Settings record for this shop
            Setting::create([
                'shop_id' => $shop->id,
                'company_name' => $shop->name,
                'company_phone' => $shop->phone,
                'company_email' => $shop->email,
                'currency_symbol' => 'SAR',
                'invoice_prefix' => 'INV-',
                'quote_prefix' => 'QT-',
            ]);

            // 7. Seed default departments tailored to the selected business type
            $btModel = \App\Models\BusinessType::where('slug', $businessType)->first();
            $dynamicDepts = ($btModel && is_array($btModel->default_departments) && !empty($btModel->default_departments))
                ? $btModel->default_departments
                : ($typeInfo['default_departments'] ?? null);

            $defaultDepartments = [];
            if (!empty($dynamicDepts)) {
                foreach ($dynamicDepts as $dItem) {
                    $dName = is_array($dItem) ? ($dItem['name'] ?? 'General') : trim($dItem);
                    $dDesc = is_array($dItem) ? ($dItem['description'] ?? "Department for {$shop->name}") : "Department for {$shop->name}";
                    if (!empty($dName)) {
                        $defaultDepartments[] = ['name' => $dName, 'description' => $dDesc];
                    }
                }
            } elseif ($businessType === 'travel_umrah') {
                $defaultDepartments = [
                    ['name' => 'Transport & Bus Fleet', 'description' => 'Bus scheduling, drivers, fuel and trip logistics'],
                    ['name' => 'Ticketing & Visa Operations', 'description' => 'Passports, Umrah e-visas and booking validation'],
                    ['name' => 'Hotel Accommodations', 'description' => 'Makkah & Madinah hotel rooms, catering and check-in'],
                    ['name' => 'Customer Relations', 'description' => 'Pilgrim inquiries, passenger support and guidance'],
                ];
            } else {
                $defaultDepartments = [
                    ['name' => 'Workshop', 'description' => 'General fabrication and assembly workshop'],
                    ['name' => 'Banner Machine', 'description' => 'Flex and banner printing machinery'],
                    ['name' => 'Sticker Print', 'description' => 'Sticker printing and vinyl cutting unit'],
                    ['name' => 'Sign Board', 'description' => 'Sign board design, frame, and mounting team'],
                ];
            }

            foreach ($defaultDepartments as $dept) {
                Department::create([
                    'shop_id' => $shop->id,
                    'name' => $dept['name'],
                    'description' => $dept['description'],
                    'is_active' => true,
                ]);
            }

            // 8. Generate unique username
            $emailParts = explode('@', $data['email']);
            $baseUsername = Str::slug($emailParts[0], '');
            if (empty($baseUsername)) {
                $baseUsername = 'user';
            }
            $username = $baseUsername;
            $uCounter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $uCounter;
                $uCounter++;
            }

            // 9. Create Admin User for this Shop
            $user = User::create([
                'shop_id' => $shop->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $username,
                'password' => Hash::make($data['password']),
                'role' => 'admin',
            ]);

            // 10. Create linked Employee record
            Employee::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'designation' => 'Shop Owner / Admin',
            ]);

            // Set shop owner
            $shop->update(['owner_id' => $user->id]);

            // 11. Record Subscription Record (7-Day Free Trial)
            Subscription::create([
                'shop_id' => $shop->id,
                'package_id' => $package?->id,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'amount_paid' => 0.00,
                'payment_method' => '7-Day Free Trial',
                'notes' => "Business Category: {$typeName}. Self-registration on 7-Day Free Trial.",
            ]);

            // 12. Send notification email to Super Admin
            $this->notifySuperAdmin($shop, $user, $package, $typeName);

            return $user;
        });
    }

    /**
     * Dispatch email notification to platform Super Admin.
     *
     * @param  \App\Models\Shop  $shop
     * @param  \App\Models\User  $user
     * @param  \App\Models\Package|null  $package
     * @param  string  $serviceName
     * @return void
     */
    protected function notifySuperAdmin(Shop $shop, User $user, ?Package $package, string $serviceName)
    {
        try {
            // Strictly send to designated Super Admin email
            $recipientEmail = 'creelogics@gmail.com';

            Mail::send('emails.new_user_registered', [
                'shop' => $shop,
                'user' => $user,
                'package' => $package,
                'serviceName' => $serviceName,
            ], function ($m) use ($recipientEmail, $shop, $user, $serviceName) {
                $m->to($recipientEmail)
                  ->subject("New Tenant Registration: {$shop->name} - {$serviceName} ({$user->name})");
            });

            Log::info("Super Admin new user alert dispatched to: {$recipientEmail}");
        } catch (\Throwable $e) {
            Log::warning("Could not send new registration email to super admin: " . $e->getMessage());
        }
    }
}
