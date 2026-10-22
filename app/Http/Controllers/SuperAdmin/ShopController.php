<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Display a listing of shops.
     */
    public function index(Request $request)
    {
        $query = Shop::withCount([
            'users',
            'customers' => fn($q) => $q->withoutGlobalScopes(),
            'quotes' => fn($q) => $q->withoutGlobalScopes(),
            'jobOrders' => fn($q) => $q->withoutGlobalScopes(),
            'invoices' => fn($q) => $q->withoutGlobalScopes(),
        ])->with(['admins', 'setting']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shops = $query->latest('id')->paginate(12)->withQueryString();

        foreach ($shops as $shop) {
            $shop->total_revenue = Invoice::withoutGlobalScopes()
                ->where('shop_id', $shop->id)
                ->where('status', '!=', 'Cancelled')
                ->sum('amount_paid');
        }

        return view('super_admin.shops.index', compact('shops'));
    }

    /**
     * Show the form for creating a new shop.
     */
    public function create()
    {
        return view('super_admin.shops.create');
    }

    /**
     * Store a newly created shop with initial admin user, settings, and departments.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Shop Details
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:shops,slug',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country_code' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'invoice_prefix' => 'required|string|max:20',
            'quote_prefix' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',

            // Admin User Details
            'admin_name' => 'required|string|max:255',
            'admin_username' => 'required|string|max:255|unique:users,username',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ]);

        DB::transaction(function () use ($request) {
            // Generate slug if not provided
            $slug = $request->filled('slug')
                ? Str::slug($request->slug)
                : Str::slug($request->name);

            // Handle duplicate slug
            $originalSlug = $slug;
            $count = 1;
            while (Shop::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $imageName = \App\Helpers\ImageHelper::compressAndSave($request->logo, 'uploads/logo', 80, 500);
                $logoPath = 'uploads/logo/' . $imageName;
            }

            // Normalize phone with country code
            $normalizedPhone = null;
            if ($request->filled('phone')) {
                $normalizedPhone = \App\Models\Customer::normalizePhone(
                    $request->phone,
                    $request->input('country_code', '+966')
                );
            }

            // 1. Create Shop
            $shop = Shop::create([
                'name' => $request->name,
                'slug' => $slug,
                'email' => $request->email,
                'phone' => $normalizedPhone,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
                'currency_symbol' => $request->currency_symbol,
                'invoice_prefix' => $request->invoice_prefix,
                'quote_prefix' => $request->quote_prefix,
                'status' => 'active',
                'logo' => $logoPath,
                'notes' => $request->notes,
            ]);

            // 2. Create Settings record for this shop
            Setting::create([
                'shop_id' => $shop->id,
                'company_name' => $shop->name,
                'company_address' => $shop->address,
                'company_city' => $shop->city,
                'company_country' => $shop->country,
                'company_phone' => $normalizedPhone,
                'company_email' => $shop->email,
                'company_logo' => $logoPath,
                'currency_symbol' => $shop->currency_symbol,
                'invoice_prefix' => $shop->invoice_prefix,
                'quote_prefix' => $shop->quote_prefix,
            ]);

            // 3. Seed default departments for the new shop
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

            // 4. Create Shop Admin User
            $adminUser = User::create([
                'shop_id' => $shop->id,
                'name' => $request->admin_name,
                'username' => $request->admin_username,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin',
            ]);

            // 5. Create linked Employee record for Admin
            Employee::create([
                'shop_id' => $shop->id,
                'user_id' => $adminUser->id,
                'name' => $adminUser->name,
                'designation' => 'Shop Owner / Admin',
            ]);
        });

        return redirect()->route('super_admin.shops.index')
            ->with('success', 'New Shop and Admin account successfully created!');
    }

    /**
     * Show the form for editing the specified shop.
     */
    public function edit(Shop $shop)
    {
        $shop->load(['admins', 'setting']);
        return view('super_admin.shops.edit', compact('shop'));
    }

    /**
     * Update the specified shop in storage.
     */
    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug,' . $shop->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country_code' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'invoice_prefix' => 'required|string|max:20',
            'quote_prefix' => 'required|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'status_reason' => 'nullable|string|max:255',
            'status_message' => 'nullable|string',
            'grace_period_days' => 'nullable|integer|min:1|max:365',
            'action_deadline' => 'nullable|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        $data = $request->except(['logo', 'country_code']);

        if ($request->filled('phone')) {
            $data['phone'] = \App\Models\Customer::normalizePhone(
                $request->phone,
                $request->input('country_code', '+966')
            );
        } else {
            $data['phone'] = null;
        }

        if ($request->hasFile('logo')) {
            $imageName = \App\Helpers\ImageHelper::compressAndSave($request->logo, 'uploads/logo', 80, 500);
            $data['logo'] = 'uploads/logo/' . $imageName;
        }

        $shop->update($data);

        // Update corresponding shop setting
        $setting = Setting::where('shop_id', $shop->id)->first();
        if ($setting) {
            $settingUpdate = [
                'company_name' => $shop->name,
                'company_email' => $shop->email,
                'company_phone' => $shop->phone,
                'company_address' => $shop->address,
                'company_city' => $shop->city,
                'company_country' => $shop->country,
                'currency_symbol' => $shop->currency_symbol,
                'invoice_prefix' => $shop->invoice_prefix,
                'quote_prefix' => $shop->quote_prefix,
            ];
            if (!empty($data['logo'])) {
                $settingUpdate['company_logo'] = $data['logo'];
            }
            $setting->update($settingUpdate);
        }

        return redirect()->route('super_admin.shops.index')
            ->with('success', 'Shop updated successfully!');
    }

    /**
     * Remove the specified shop from storage.
     */
    public function destroy(Shop $shop)
    {
        $name = $shop->name;
        $shop->delete();

        return redirect()->route('super_admin.shops.index')
            ->with('success', "Shop '{$name}' has been moved to trash / archived.");
    }

    /**
     * Update shop status (active, inactive, suspended) with reason, deadline, and email notification.
     */
    public function updateStatus(Request $request, Shop $shop)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
            'status_reason' => 'nullable|string|max:255',
            'status_message' => 'nullable|string',
            'grace_period_days' => 'nullable|integer|min:1|max:365',
            'action_deadline' => 'nullable|date',
            'send_email' => 'nullable',
        ]);

        $newStatus = $request->status;
        $reason = $request->status_reason;
        $message = $request->status_message;
        $graceDays = $request->grace_period_days;
        $deadline = null;

        if ($newStatus === 'inactive') {
            if ($request->filled('action_deadline')) {
                $deadline = \Carbon\Carbon::parse($request->action_deadline);
            } elseif ($request->filled('grace_period_days')) {
                $graceDays = (int) $request->grace_period_days;
                $deadline = now()->addDays($graceDays);
            } else {
                $graceDays = 7;
                $deadline = now()->addDays(7);
            }
            if (empty($reason)) {
                $reason = 'Pending Payment / Unsettled Invoices';
            }
        } elseif ($newStatus === 'suspended') {
            if (empty($reason)) {
                $reason = 'Permanent Suspension: Policy or Payment Default';
            }
        } elseif ($newStatus === 'active') {
            $reason = null;
            $message = null;
            $graceDays = null;
            $deadline = null;
        }

        $shop->update([
            'status' => $newStatus,
            'status_reason' => $reason,
            'status_message' => $message,
            'grace_period_days' => $graceDays,
            'action_deadline' => $deadline,
            'status_updated_at' => now(),
        ]);

        // Send email notification if checkbox is checked (default on)
        $shouldSendEmail = $request->has('send_email') ? $request->boolean('send_email') : true;
        $emailSent = false;
        if ($shouldSendEmail) {
            $emailSent = $this->sendStatusEmail($shop, $newStatus, $reason, $message, $graceDays, $deadline);
        }

        $statusName = match ($newStatus) {
            'active' => 'Active',
            'inactive' => 'Inactive (Temporary)',
            'suspended' => 'Suspended (Blocked)',
            default => ucfirst($newStatus),
        };

        $flashMsg = "Shop '{$shop->name}' status updated to {$statusName}.";
        if ($emailSent) {
            $flashMsg .= " Official email notification dispatched.";
        }

        return back()->with('success', $flashMsg);
    }

    /**
     * Fast toggle status (kept for backward compatibility).
     */
    public function toggleStatus(Shop $shop)
    {
        $newStatus = $shop->status === 'active' ? 'inactive' : 'active';
        $deadline = $newStatus === 'inactive' ? now()->addDays(7) : null;
        $reason = $newStatus === 'inactive' ? 'Pending Payment / Unsettled Invoices' : null;

        $shop->update([
            'status' => $newStatus,
            'status_reason' => $reason,
            'grace_period_days' => $newStatus === 'inactive' ? 7 : null,
            'action_deadline' => $deadline,
            'status_updated_at' => now(),
        ]);

        $this->sendStatusEmail($shop, $newStatus, $reason, null, 7, $deadline);

        $msg = $newStatus === 'active' 
            ? "Shop '{$shop->name}' is now Active." 
            : "Shop '{$shop->name}' set to Inactive (7 days grace period).";

        return back()->with('success', $msg);
    }

    /**
     * Dispatch status notification email to shop and its primary administrators.
     */
    protected function sendStatusEmail(Shop $shop, string $status, ?string $reason, ?string $customMessage, ?int $gracePeriodDays, $deadline): bool
    {
        $recipients = collect();

        if (!empty($shop->email) && filter_var($shop->email, FILTER_VALIDATE_EMAIL)) {
            $recipients->push(trim($shop->email));
        }

        foreach ($shop->admins as $admin) {
            if (!empty($admin->email) && filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                $recipients->push(trim($admin->email));
            }
        }

        $recipients = $recipients->unique()->values();

        if ($recipients->isEmpty()) {
            return false;
        }

        $subject = match ($status) {
            'inactive' => "Action Required: {$shop->name} - Temporary Account Deactivation",
            'suspended' => "Urgent Notice: {$shop->name} - Account Suspended",
            default => "Good News: {$shop->name} - Account Re-activated",
        };

        $recipientName = $shop->admins->first()?->name ?? $shop->name;

        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.shop_status_notification',
                [
                    'shop' => $shop,
                    'status' => $status,
                    'reason' => $reason,
                    'customMessage' => $customMessage,
                    'gracePeriodDays' => $gracePeriodDays,
                    'deadline' => $deadline,
                    'recipientName' => $recipientName,
                    'emailSubject' => $subject,
                ],
                function ($message) use ($recipients, $subject) {
                    $message->to($recipients->toArray())
                            ->subject($subject);
                }
            );
            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send shop status email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Switch active shop for Super Admin.
     */
    public function switchShop(Request $request, $shopId)
    {
        $shop = Shop::findOrFail($shopId);
        session(['active_shop_id' => $shop->id]);

        return redirect()->route('dashboard')
            ->with('success', "Switched active shop to: {$shop->name}");
    }

    /**
     * Switch to All Shops / Super Admin overview.
     */
    public function switchShopAll()
    {
        session(['active_shop_id' => 'all']);

        return redirect()->route('super_admin.dashboard')
            ->with('success', 'Switched to Global / All Shops view.');
    }
}
