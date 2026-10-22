<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $shopId = current_shop_id();
        $setting = Setting::where('shop_id', $shopId)->first() ?: (Setting::first() ?: new Setting(['shop_id' => $shopId]));
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required',
            'company_email' => 'required|email',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        $shopId = current_shop_id();
        $setting = Setting::where('shop_id', $shopId)->first();
        if (!$setting) {
            $setting = new Setting();
            $setting->shop_id = $shopId;
        }

        $data = $request->except('company_logo');

        if ($request->hasFile('company_logo')) {
            $imageName = \App\Helpers\ImageHelper::compressAndSave($request->company_logo, 'uploads/logo', 80, 500);
            $data['company_logo'] = 'uploads/logo/' . $imageName;
        }

        $data['shop_id'] = $shopId;
        $setting->fill($data);
        $setting->save();

        // Also synchronize shop details
        if ($shop = current_shop()) {
            $shop->update([
                'name' => $data['company_name'] ?? $shop->name,
                'email' => $data['company_email'] ?? $shop->email,
                'phone' => $data['company_phone'] ?? $shop->phone,
                'address' => $data['company_address'] ?? $shop->address,
                'city' => $data['company_city'] ?? $shop->city,
                'country' => $data['company_country'] ?? $shop->country,
                'currency_symbol' => $data['currency_symbol'] ?? $shop->currency_symbol,
                'invoice_prefix' => $data['invoice_prefix'] ?? $shop->invoice_prefix,
                'quote_prefix' => $data['quote_prefix'] ?? $shop->quote_prefix,
                'logo' => $data['company_logo'] ?? $shop->logo,
            ]);
        }

        return redirect()->back()->with('success', 'Shop settings updated successfully.');
    }
}
