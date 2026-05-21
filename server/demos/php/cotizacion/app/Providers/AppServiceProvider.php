<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $smtpHost = \App\Models\Setting::get('smtp_host');
                if ($smtpHost) {
                    config([
                        'mail.mailers.smtp.host'       => $smtpHost,
                        'mail.mailers.smtp.port'       => \App\Models\Setting::get('smtp_port', 587),
                        'mail.mailers.smtp.encryption' => \App\Models\Setting::get('smtp_encryption', 'tls') ?: null,
                        'mail.mailers.smtp.username'   => \App\Models\Setting::get('smtp_username'),
                        'mail.mailers.smtp.password'   => \App\Models\Setting::get('smtp_password'),
                        'mail.from.address'            => \App\Models\Setting::get('smtp_from_address', 'hello@example.com'),
                        'mail.from.name'               => \App\Models\Setting::get('smtp_from_name', 'CotizaPro'),
                    ]);
                }
                
                $defCurrency = \App\Models\Setting::get('default_currency', 'PEN');
                $defSym = ['PEN' => 'S/', 'USD' => '$', 'EUR' => '€'][$defCurrency] ?? 'S/';
                \Illuminate\Support\Facades\View::share('globalCurrency', $defCurrency);
                \Illuminate\Support\Facades\View::share('globalSym', $defSym);
            }
        } catch (\Exception $e) {
            // Ignorar errores si la BD no está lista (ej. durante migraciones)
        }
    }
}
