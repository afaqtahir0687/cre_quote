<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Setting;
use App\Models\Department;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    /**
     * Resolve the active shop model safely for both Admin and Super Admin.
     */
    protected function resolveShop($user): ?Shop
    {
        // 1. Session active shop (if selected via switcher)
        $sessionShop = current_shop();
        if ($sessionShop) {
            return $sessionShop;
        }

        // 2. User's explicitly assigned shop
        if ($user->shop) {
            return $user->shop;
        }

        // 3. Fallback: First available shop on platform
        return Shop::first();
    }

    /**
     * Display all branches for this Business Account.
     */
    public function index()
    {
        $user = auth()->user();
        $shop = $this->resolveShop($user);

        if (!$shop) {
            if ($user->isSuperAdmin()) {
                return redirect()->route('super_admin.shops.create')->with('info', __('Please create your first shop.'));
            }
            abort(403, __('No shop assigned.'));
        }

        // Get the main / parent shop
        $mainShop = ($shop->parent_shop_id && $shop->parentShop) ? $shop->parentShop : $shop;

        // Check if on Business plan (Super Admin always has business permissions)
        $isBusiness = $user->isSuperAdmin() || $mainShop->isBusinessAccount();
        $package = $mainShop->package;

        $branches = $mainShop->branches()->with(['setting', 'admins', 'departments'])->latest('id')->get();
        $maxShops = $user->isSuperAdmin() ? max(5, (int)($package->max_shops ?? 5)) : (int)($package->max_shops ?? 1);
        $usedShops = $branches->count() + 1; // main shop + branches
        $canAddMore = $user->isSuperAdmin() || ($isBusiness && $usedShops < $maxShops);

        return view('admin.branches.index', compact('mainShop', 'branches', 'isBusiness', 'package', 'maxShops', 'usedShops', 'canAddMore'));
    }

    /**
     * Show form to add a new branch.
     */
    public function create()
    {
        $user = auth()->user();
        $shop = $this->resolveShop($user);

        if (!$shop) {
            abort(403, __('No shop assigned.'));
        }

        $mainShop = ($shop->parent_shop_id && $shop->parentShop) ? $shop->parentShop : $shop;

        if (!$user->isSuperAdmin() && !$mainShop->canAddBranch()) {
            return redirect()->route('branches.index')->with('error', __('You have reached the maximum allowed shops for your Business Package.'));
        }

        return view('admin.branches.create', compact('mainShop'));
    }

    /**
     * Store a newly created branch under this Business account.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $shop = $this->resolveShop($user);

        if (!$shop) {
            abort(403, __('No shop assigned.'));
        }

        $mainShop = ($shop->parent_shop_id && $shop->parentShop) ? $shop->parentShop : $shop;

        if (!$user->isSuperAdmin() && !$mainShop->canAddBranch()) {
            return redirect()->route('branches.index')->with('error', __('Maximum shops limit reached for your Business Package.'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'invoice_prefix' => 'required|string|max:20',
            'quote_prefix' => 'required|string|max:20',
            'currency_symbol' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        DB::transaction(function () use ($request, $mainShop, $user) {
            // Slug
            $slug = Str::slug($request->name);
            $origSlug = $slug;
            $c = 1;
            while (Shop::where('slug', $slug)->exists()) {
                $slug = $origSlug . '-' . $c++;
            }

            // Logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $imageName = \App\Helpers\ImageHelper::compressAndSave($request->logo, 'uploads/logo', 80, 500);
                $logoPath = 'uploads/logo/' . $imageName;
            } else {
                $logoPath = $mainShop->logo; // inherit parent logo if not uploaded
            }

            // Create branch shop
            $branch = Shop::create([
                'name' => $request->name,
                'slug' => $slug,
                'email' => $request->email ?: $mainShop->email,
                'phone' => $request->phone ?: $mainShop->phone,
                'address' => $request->address ?: $mainShop->address,
                'city' => $request->city ?: $mainShop->city,
                'country' => $mainShop->country,
                'currency_symbol' => $request->currency_symbol ?: $mainShop->currency_symbol,
                'invoice_prefix' => $request->invoice_prefix,
                'quote_prefix' => $request->quote_prefix,
                'status' => 'active',
                'logo' => $logoPath,
                'package_id' => $mainShop->package_id,
                'subscription_starts_at' => $mainShop->subscription_starts_at,
                'subscription_expires_at' => $mainShop->subscription_expires_at,
                'subscription_status' => 'active',
                'parent_shop_id' => $mainShop->id,
                'owner_id' => $user->id,
            ]);

            // Create setting for branch
            Setting::create([
                'shop_id' => $branch->id,
                'company_name' => $branch->name,
                'company_address' => $branch->address,
                'company_city' => $branch->city,
                'company_country' => $branch->country,
                'company_phone' => $branch->phone,
                'company_email' => $branch->email,
                'company_logo' => $logoPath,
                'currency_symbol' => $branch->currency_symbol,
                'invoice_prefix' => $branch->invoice_prefix,
                'quote_prefix' => $branch->quote_prefix,
            ]);

            // Seed default departments for branch
            $defaultDepts = [
                ['name' => 'Workshop', 'type' => 'Workshop'],
                ['name' => 'Banner Machine', 'type' => 'Banner Machine'],
                ['name' => 'Sticker Print', 'type' => 'Sticker Print'],
                ['name' => 'Sign Board', 'type' => 'Sign Board'],
            ];
            foreach ($defaultDepts as $dept) {
                Department::create([
                    'shop_id' => $branch->id,
                    'name' => $dept['name'],
                    'type' => $dept['type'],
                    'status' => 'active',
                ]);
            }

            // Log
            SystemLog::create([
                'shop_id' => $mainShop->id,
                'user_id' => $user->id,
                'action' => 'BRANCH_CREATED',
                'model_type' => 'Shop',
                'description' => "Created new branch '{$branch->name}' under '{$mainShop->name}'.",
            ]);
        });

        return redirect()->route('branches.index')->with('success', __('New Branch created successfully under your Business Account!'));
    }

    /**
     * Switch active branch for Business Admin or Super Admin.
     */
    public function switchBranch($shopId)
    {
        $user = auth()->user();
        $shop = $this->resolveShop($user);

        if (!$shop) {
            abort(403, __('No shop assigned.'));
        }

        $mainShop = ($shop->parent_shop_id && $shop->parentShop) ? $shop->parentShop : $shop;

        // Verify that target shop is either main shop or one of its branches (Super Admin can switch to any shop)
        $allowedIds = $mainShop->branches()->pluck('id')->push($mainShop->id)->all();

        if (!$user->isSuperAdmin() && !in_array((int)$shopId, $allowedIds)) {
            abort(403, __('Unauthorized shop access.'));
        }

        session(['active_shop_id' => (int)$shopId]);
        $target = Shop::findOrFail($shopId);

        return redirect()->route('dashboard')->with('success', __("Switched active branch to: :name", ['name' => $target->name]));
    }
}
