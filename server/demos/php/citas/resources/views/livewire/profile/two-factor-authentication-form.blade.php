<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use PragmaRX\Google2FA\Google2FA;

new class extends Component {
    public bool $showingQrCode = false;
    public bool $showingConfirmation = false;
    public bool $showingRecoveryCodes = false;
    public string $code = '';
    public string $setupKey = '';
    public string $qrCodeSvg = '';
    public array $recoveryCodes = [];

    public function mount(): void
    {
        // ...
    }

    public function enableTwoFactorAuthentication(Google2FA $google2fa): void
    {
        $user = Auth::user();
        $this->setupKey = $google2fa->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($this->setupKey),
        ])->save();

        $this->qrCodeSvg = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $this->setupKey
        );

        $this->showingQrCode = true;
        $this->showingConfirmation = true;
    }

    public function confirmTwoFactorAuthentication(Google2FA $google2fa): void
    {
        $user = Auth::user();

        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $this->code
        );

        if ($valid) {
            $user->forceFill([
                'two_factor_confirmed_at' => now(),
            ])->save();

            $this->generateRecoveryCodes();
            $this->showingConfirmation = false;
            $this->showingRecoveryCodes = true;
            $this->code = '';

            $this->dispatch('two-factor-enabled');
        } else {
            $this->addError('code', 'El código ingresado es incorrecto.');
        }
    }

    public function disableTwoFactorAuthentication(): void
    {
        $user = Auth::user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->generateRecoveryCodes();
        $this->showingRecoveryCodes = true;
    }

    public function showRecoveryCodes(): void
    {
        if (Auth::user()->two_factor_recovery_codes) {
            $this->recoveryCodes = json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true);
            $this->showingRecoveryCodes = true;
        }
    }

    protected function generateRecoveryCodes(): void
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10);
        }

        Auth::user()->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($codes))
        ])->save();

        $this->recoveryCodes = $codes;
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Autenticación de Dos Factores (2FA)
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Añade seguridad adicional a tu cuenta requiriendo un código aleatorio durante el inicio de sesión.
        </p>
    </header>

    @php
        $user = auth()->user();
        $isEnabled = !empty($user->two_factor_secret) && !empty($user->two_factor_confirmed_at);
        $isPending = !empty($user->two_factor_secret) && empty($user->two_factor_confirmed_at);
    @endphp

    <div class="mt-6">
        @if (!$isEnabled && !$isPending)
            <x-primary-button wire:click="enableTwoFactorAuthentication" wire:loading.attr="disabled">
                Habilitar 2FA
            </x-primary-button>
        @elseif($isPending)
            <div class="mb-4 text-sm text-gray-600">
                Para terminar de habilitar 2FA, escanea el siguiente código QR usando una aplicación de autenticación (como
                Google Authenticator) y escribe el código generado.
            </div>

            <div class="mt-4 p-2 inline-block bg-white border rounded">
                {!! $qrCodeSvg !!}
            </div>

            <div class="mt-4 max-w-xl text-sm text-gray-600">
                Clave de configuración manual: <span class="font-mono font-bold">{{ $setupKey }}</span>
            </div>

            <div class="mt-4 max-w-xl">
                <x-input-label for="code" value="Código" />
                <x-text-input id="code" type="text" name="code" class="mt-1 block w-1/2" wire:model="code" autofocus
                    autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div class="mt-4 flex gap-3">
                <x-primary-button wire:click="confirmTwoFactorAuthentication" wire:loading.attr="disabled">
                    Confirmar
                </x-primary-button>
                <x-secondary-button wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>
            </div>
        @elseif($isEnabled)
            <div class="mb-4 text-sm text-green-600 font-bold">
                Has habilitado la Autenticación de Dos Factores.
            </div>

            @if ($showingRecoveryCodes)
                <div class="mb-4 text-sm text-gray-600">
                    Guarda estos códigos de recuperación en un lugar seguro. Podrás usarlos para acceder si pierdes tu
                    dispositivo.
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                    @foreach ($recoveryCodes as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif

            <div class="mt-5 flex items-center gap-3">
                @if (!$showingRecoveryCodes)
                    <x-secondary-button wire:click="showRecoveryCodes">
                        Ver Códigos de Recuperación
                    </x-secondary-button>
                @else
                    <x-secondary-button wire:click="regenerateRecoveryCodes">
                        Regenerar Códigos
                    </x-secondary-button>
                @endif

                <x-danger-button wire:click="disableTwoFactorAuthentication">
                    Deshabilitar 2FA
                </x-danger-button>
            </div>
        @endif
    </div>
</section>