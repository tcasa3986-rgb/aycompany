<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings  = array_merge(Setting::defaults(), Setting::all_keyed());
        $hasLogo   = file_exists(public_path('logo.png'));
        return view('settings.index', compact('settings', 'hasLogo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name'          => 'required|string|max:255',
            'company_ruc'           => 'nullable|string|max:20',
            'company_address'       => 'nullable|string|max:500',
            'company_phone'         => 'nullable|string|max:30',
            'company_email'         => 'nullable|email|max:255',
            'company_website'       => 'nullable|url|max:255',
            'default_currency'      => 'required|in:PEN,USD,EUR',
            'default_tax_rate'      => 'required|numeric|min:0|max:100',
            'default_validity_days' => 'nullable|integer|min:0|max:365',
            'quotation_prefix'      => 'required|string|max:10|alpha_num',
            'terms_and_conditions'  => 'nullable|string',
            'logo'                  => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'smtp_host'             => 'nullable|string|max:255',
            'smtp_port'             => 'nullable|string|max:10',
            'smtp_username'         => 'nullable|string|max:255',
            'smtp_password'         => 'nullable|string|max:255',
            'smtp_encryption'       => 'nullable|string|in:tls,ssl',
            'smtp_from_address'     => 'nullable|email|max:255',
            'smtp_from_name'        => 'nullable|string|max:255',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $request->file('logo')->move(public_path(), 'logo.png');
        }

        // Handle logo deletion
        if ($request->boolean('delete_logo') && file_exists(public_path('logo.png'))) {
            unlink(public_path('logo.png'));
        }

        foreach ($request->except(['_token', '_method', 'logo', 'delete_logo']) as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }

    public function testEmail(Request $request)
    {
        try {
            // Configurar temporalmente el mailer con los datos del request
            config([
                'mail.mailers.smtp.host'       => $request->smtp_host,
                'mail.mailers.smtp.port'       => $request->smtp_port,
                'mail.mailers.smtp.encryption' => $request->smtp_encryption ?: null,
                'mail.mailers.smtp.username'   => $request->smtp_username,
                'mail.mailers.smtp.password'   => $request->smtp_password,
                'mail.from.address'            => $request->smtp_from_address,
                'mail.from.name'               => $request->smtp_from_name,
            ]);

            \Illuminate\Support\Facades\Mail::raw('¡Felicidades! La configuración SMTP de CotizaPro está funcionando correctamente.', function ($message) use ($request) {
                $message->to($request->smtp_from_address)
                        ->subject('Mensaje de Prueba - CotizaPro ERP');
            });

            return response()->json(['success' => true, 'message' => 'Correo enviado exitosamente a ' . $request->smtp_from_address]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
