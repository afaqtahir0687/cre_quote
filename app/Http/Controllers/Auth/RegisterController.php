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
        $services = ProposalOption::where('type', 'service')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('auth.register', compact('services'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'company_name' => ['required', 'string', 'max:255'],
            'service_interest' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'company_name.required' => __('Please enter your Shop or Company name.'),
            'service_interest.required' => __('Please select the primary service or solution you require.'),
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

            // 2. Generate Unique Slug for Shop
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

            // 3. Create Shop Record (Store selected service in notes/reason)
            $serviceName = $data['service_interest'] ?? 'General ERP & Signage';

            $shop = Shop::create([
                'name' => $data['company_name'],
                'slug' => $slug,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'status' => 'active',
                'currency_symbol' => '$',
                'invoice_prefix' => 'INV-',
                'quote_prefix' => 'QT-',
                'package_id' => $package?->id,
                'subscription_starts_at' => $startsAt,
                'subscription_expires_at' => $expiresAt,
                'subscription_status' => 'active',
                'notes' => "Selected Service: {$serviceName} (7-Day Trial Self-Registration)",
            ]);

            // 4. Create Settings record for this shop
            Setting::create([
                'shop_id' => $shop->id,
                'company_name' => $shop->name,
                'company_phone' => $shop->phone,
                'company_email' => $shop->email,
                'currency_symbol' => '$',
                'invoice_prefix' => 'INV-',
                'quote_prefix' => 'QT-',
            ]);

            // 5. Seed default departments for the shop
            $defaultDepartments = [
                ['name' => 'Workshop', 'description' => 'General fabrication and assembly workshop'],
                ['name' => 'Banner Machine', 'description' => 'Flex and banner printing machinery'],
                ['name' => 'Sticker Print', 'description' => 'Sticker printing and vinyl cutting unit'],
                ['name' => 'Sign Board', 'description' => 'Sign board design, frame, and mounting team'],
            ];

            foreach ($defaultDepartments as $dept) {
                Department::create([
                    'shop_id' => $shop->id,
                    'name' => $dept['name'],
                    'description' => $dept['description'],
                    'is_active' => true,
                ]);
            }

            // 6. Generate unique username
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

            // 7. Create Admin User for this Shop
            $user = User::create([
                'shop_id' => $shop->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $username,
                'password' => Hash::make($data['password']),
                'role' => 'admin',
            ]);

            // 8. Create linked Employee record
            Employee::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'designation' => 'Shop Owner / Admin',
            ]);

            // Set shop owner
            $shop->update(['owner_id' => $user->id]);

            // 9. Record Subscription Record (7-Day Free Trial)
            Subscription::create([
                'shop_id' => $shop->id,
                'package_id' => $package?->id,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'amount_paid' => 0.00,
                'payment_method' => '7-Day Free Trial',
                'notes' => "Service Requested: {$serviceName}. Self-registration on 7-Day Free Trial.",
            ]);

            // 10. Send notification email to Super Admin
            $this->notifySuperAdmin($shop, $user, $package, $serviceName);

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
