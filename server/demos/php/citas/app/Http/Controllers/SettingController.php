<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // Default values used when the DB has no row yet
    private array $defaults = [
        'clinic_name' => 'CitasMédicas',
        'clinic_tagline' => 'Sistema de Gestión Médica',
        'clinic_ruc' => '',
        'clinic_address' => '',
        'clinic_phone' => '',
        'clinic_email' => '',
        'appointment_duration' => '30',
        'appointment_max_days' => '60',
        'working_hours_start' => '08:00',
        'working_hours_end' => '18:00',
        'timezone' => 'America/Lima',
        'currency_symbol' => 'S/',
        'logo_path' => '',
        'notify_on_confirm' => '1',
        'notify_on_cancel' => '1',
        'notify_reminder_24h' => '1',
    ];

    public function index()
    {
        $settings = array_merge($this->defaults, Setting::allAsArray());
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:100',
            'clinic_tagline' => 'nullable|string|max:150',
            'clinic_ruc' => 'nullable|string|max:20',
            'clinic_address' => 'nullable|string|max:255',
            'clinic_phone' => 'nullable|string|max:30',
            'clinic_email' => 'nullable|email|max:100',
            'appointment_duration' => 'required|integer|min:5|max:240',
            'appointment_max_days' => 'required|integer|min:1|max:365',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i|after:working_hours_start',
            'timezone' => 'required|string',
            'currency_symbol' => 'nullable|string|max:5',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('logo_path');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            Setting::set('logo_path', $path);
        }

        // Persist all text settings
        $keys = [
            'clinic_name',
            'clinic_tagline',
            'clinic_ruc',
            'clinic_address',
            'clinic_phone',
            'clinic_email',
            'appointment_duration',
            'appointment_max_days',
            'working_hours_start',
            'working_hours_end',
            'timezone',
            'currency_symbol',
            'notify_on_confirm',
            'notify_on_cancel',
            'notify_reminder_24h',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        return redirect()->route('settings.index')
            ->with('success', 'Configuración guardada correctamente.');
    }
}
