<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'empresa_nombre' => 'nullable|string|max:255',
            'empresa_direccion' => 'nullable|string|max:255',
            'empresa_telefono' => 'nullable|string|max:20',
            'empresa_ruc' => 'nullable|string|max:20',
            'empresa_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency_symbol' => 'nullable|string|max:10',
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $data = $request->except('empresa_logo');

        if ($request->hasFile('empresa_logo')) {
            // Delete old logo if exists and not default (if applicable)
            if ($setting->empresa_logo) {
                Storage::disk('public')->delete($setting->empresa_logo);
            }
            // Store new logo
            $path = $request->file('empresa_logo')->store('logos', 'public');
            $data['empresa_logo'] = $path;
        }

        if ($setting->exists) {
            $setting->update($data);
        } else {
            $setting = Setting::create($data);
        }

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
