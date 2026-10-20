<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first() ?: new Setting();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required',
            'company_email' => 'required|email',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        $setting = Setting::first() ?: new Setting();
        $data = $request->except('company_logo');

        if ($request->hasFile('company_logo')) {
            $imageName = \App\Helpers\ImageHelper::compressAndSave($request->company_logo, 'uploads/logo', 80, 500);
            $data['company_logo'] = 'uploads/logo/' . $imageName;
        }

        $setting->fill($data);
        $setting->save();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
