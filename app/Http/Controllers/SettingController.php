<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Shop;

class SettingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $shopId = null;
        if ($user->isSuperAdmin()) {
            $shopId = current_shop_id() ?: Shop::first()?->id;
        } else {
            $shopId = (int) $user->shop_id;
        }

        if (!$shopId) {
            $shop = Shop::first();
            $shopId = $shop?->id;
        }

        if (!$shopId) {
            return redirect()->route('super_admin.platform_settings');
        }

        $shop = Shop::findOrFail($shopId);
        $setting = Setting::withoutGlobalScopes()->where('shop_id', $shopId)->first();

        if (!$setting) {
            $setting = new Setting([
                'shop_id' => $shopId,
                'company_name' => $shop->name,
                'company_email' => $shop->email ?? '',
                'company_phone' => $shop->phone ?? '',
                'company_address' => $shop->address ?? '',
                'company_city' => $shop->city ?? '',
                'company_country' => $shop->country ?? 'Saudi Arabia',
                'currency_symbol' => $shop->currency_symbol ?? '$',
                'invoice_prefix' => $shop->invoice_prefix ?? 'INV-',
                'quote_prefix' => $shop->quote_prefix ?? 'QT-',
                'company_logo' => $shop->logo ?? null,
            ]);
        }

        return view('admin.settings.index', compact('setting', 'shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'company_stamp' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'bank_stamp_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'bank_name' => 'nullable|string|max:255',
            'bank_name_ar' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:100',
            'bank_iban' => 'nullable|string|max:100',
            'bank_swift' => 'nullable|string|max:50',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string',
            'company_city' => 'nullable|string|max:100',
            'company_country' => 'nullable|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'invoice_prefix' => 'required|string|max:20',
            'quote_prefix' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        $shopId = null;
        if ($user->isSuperAdmin()) {
            $shopId = current_shop_id() ?: Shop::first()?->id;
        } else {
            $shopId = (int) $user->shop_id;
        }

        if (!$shopId) {
            $shopId = Shop::first()?->id;
        }

        if (!$shopId) {
            return redirect()->route('super_admin.platform_settings')
                ->with('error', 'Active shop not identified.');
        }

        $shop = Shop::findOrFail($shopId);
        $setting = Setting::withoutGlobalScopes()->where('shop_id', $shopId)->first();

        if (!$setting) {
            $setting = new Setting();
            $setting->shop_id = $shopId;
        }

        $data = $request->except(['company_logo', 'remove_logo', 'company_stamp', 'remove_stamp', 'bank_stamp_logo', 'remove_bank_stamp_logo']);
        $data['show_customer_name_on_pdf'] = $request->boolean('show_customer_name_on_pdf', true);
        $data['use_custom_bank_image'] = $request->boolean('use_custom_bank_image', false);

        if ($request->hasFile('company_logo')) {
            $imageName = \App\Helpers\ImageHelper::compressAndSave($request->company_logo, 'uploads/logo', 80, 500);
            $data['company_logo'] = 'uploads/logo/' . $imageName;
        } elseif ($request->boolean('remove_logo')) {
            $data['company_logo'] = null;
        }

        if ($request->hasFile('company_stamp')) {
            $stampName = \App\Helpers\ImageHelper::compressAndSave($request->company_stamp, 'uploads/logo', 90, 500);
            $data['company_stamp'] = 'uploads/logo/' . $stampName;
        } elseif ($request->boolean('remove_stamp')) {
            $data['company_stamp'] = null;
        }

        if ($request->hasFile('bank_stamp_logo')) {
            $bankImageName = \App\Helpers\ImageHelper::compressAndSave($request->bank_stamp_logo, 'uploads/logo', 90, 800);
            $data['bank_stamp_logo'] = 'uploads/logo/' . $bankImageName;
        } elseif ($request->boolean('remove_bank_stamp_logo')) {
            $data['bank_stamp_logo'] = null;
        }

        $data['shop_id'] = $shopId;
        $setting->fill($data);
        $setting->save();

        // Also synchronize shop details strictly for this shop
        $shopUpdate = [
            'name' => $data['company_name'] ?? $shop->name,
            'email' => $data['company_email'] ?? $shop->email,
            'phone' => $data['company_phone'] ?? $shop->phone,
            'address' => $data['company_address'] ?? $shop->address,
            'city' => $data['company_city'] ?? $shop->city,
            'country' => $data['company_country'] ?? $shop->country,
            'currency_symbol' => $data['currency_symbol'] ?? $shop->currency_symbol,
            'invoice_prefix' => $data['invoice_prefix'] ?? $shop->invoice_prefix,
            'quote_prefix' => $data['quote_prefix'] ?? $shop->quote_prefix,
        ];
        if (isset($data['company_logo']) || $request->boolean('remove_logo')) {
            $shopUpdate['logo'] = $data['company_logo'] ?? null;
        }
        if (isset($data['bank_stamp_logo']) || $request->boolean('remove_bank_stamp_logo')) {
            $shopUpdate['bank_stamp_logo'] = $data['bank_stamp_logo'] ?? null;
        }
        $shop->update($shopUpdate);

        return redirect()->back()->with('success', "Branding and settings for '{$shop->name}' updated successfully!");
    }
}
