<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use PragmaRX\Google2FA\Google2FA;

new #[Layout('layouts.guest')] class extends Component {
    public string $code = '';
    public string $recovery_code = '';
    public bool $recovery = false;

    public function mount(): void
    {
        if (!Session::has('login.id')) {
            $this->redirectRoute('login', navigate: true);
        }
    }

    public function toggleRecovery(): void
    {
        $this->recovery = !$this->recovery;
        $this->code = '';
        $this->recovery_code = '';
    }

    public function authenticate(Google2FA $google2fa): void
    {
        $userId = Session::get('login.id');
        $remember = Session::get('login.remember', false);

        $user = User::find($userId);

        if (!$user) {
            Session::forget('login.id');
            $this->redirectRoute('login', navigate: true);
            return;
        }

        if ($this->recovery) {
            $this->validate(['recovery_code' => 'required|string']);

            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];

            $validCodeIndex = collect($recoveryCodes)->search(function ($code) {
                return hash_equals($this->recovery_code, $code) || hash_equals(str_replace('-', '', $this->recovery_code), str_replace('-', '', $code));
            });

            if ($validCodeIndex !== false) {
                unset($recoveryCodes[$validCodeIndex]);
                $user->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes)))
                ])->save();
            } else {
                throw ValidationException::withMessages([
                    'recovery_code' => 'El código de recuperación proporcionado no es válido.',
                ]);
            }
        } else {
            $this->validate(['code' => 'required|string']);

            $valid = $google2fa->verifyKey(
                decrypt($user->two_factor_secret),
                $this->code
            );

            if (!$valid) {
                throw ValidationException::withMessages([
                    'code' => 'El código de autenticación proporcionado no es válido.',
                ]);
            }
        }

        Auth::login($user, $remember);
        Session::forget('login.id');
        Session::forget('login.remember');
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        @if (!$recovery)
            {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
        @else
            {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
        @endif
    </div>

    <form wire:submit="authenticate">
        @if (!$recovery)
            <div>
                <x-input-label for="code" value="{{ __('Code') }}" />
                <x-text-input wire:model="code" id="code" class="block mt-1 w-full" type="text" inputmode="numeric"
                    autofocus autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="recovery_code" value="{{ __('Recovery Code') }}" />
                <x-text-input wire:model="recovery_code" id="recovery_code" class="block mt-1 w-full" type="text"
                    autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                wire:click="toggleRecovery">
                @if (!$recovery)
                    {{ __('Use a recovery code') }}
                @else
                    {{ __('Use an authentication code') }}
                @endif
            </button>

            <x-primary-button class="ms-4">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>