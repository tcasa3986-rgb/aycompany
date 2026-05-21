<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Configuración del Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        @method('POST')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre de la Empresa -->
                            <div>
                                <x-input-label for="empresa_nombre" :value="__('Nombre de la Empresa')" />
                                <x-text-input id="empresa_nombre" name="empresa_nombre" type="text"
                                    class="mt-1 block w-full" :value="old('empresa_nombre', $setting?->empresa_nombre)" />
                                <x-input-error class="mt-2" :messages="$errors->get('empresa_nombre')" />
                            </div>

                            <!-- RUC -->
                            <div>
                                <x-input-label for="empresa_ruc" :value="__('RUC / ID Tributario')" />
                                <x-text-input id="empresa_ruc" name="empresa_ruc" type="text" class="mt-1 block w-full"
                                    :value="old('empresa_ruc', $setting?->empresa_ruc)" />
                                <x-input-error class="mt-2" :messages="$errors->get('empresa_ruc')" />
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <x-input-label for="empresa_telefono" :value="__('Teléfono')" />
                                <x-text-input id="empresa_telefono" name="empresa_telefono" type="text"
                                    class="mt-1 block w-full" :value="old('empresa_telefono', $setting?->empresa_telefono)" />
                                <x-input-error class="mt-2" :messages="$errors->get('empresa_telefono')" />
                            </div>

                            <x-input-error class="mt-2" :messages="$errors->get('empresa_direccion')" />
                        </div>

                        <!-- Símbolo de Moneda -->
                        <div class="md:col-span-2">
                            <x-input-label for="currency_symbol" :value="__('Símbolo de Moneda')" />
                            <x-text-input id="currency_symbol" name="currency_symbol" type="text"
                                class="mt-1 block w-full" :value="old('currency_symbol', $setting?->currency_symbol ?? 'S/')" placeholder="Ej: S/, $, €" />
                            <x-input-error class="mt-2" :messages="$errors->get('currency_symbol')" />
                        </div>
                </div>

                <!-- Logo -->
                <div class="border-t border-gray-700 pt-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Logo de la Empresa
                    </h3>

                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            @if($setting?->empresa_logo)
                                <img class="h-16 w-16 object-cover rounded-full bg-gray-700"
                                    src="{{ asset('storage/' . $setting->empresa_logo) }}" alt="Logo Actual">
                            @else
                                <div
                                    class="h-16 w-16 rounded-full bg-gray-700 flex items-center justify-center text-gray-400">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <x-input-label for="empresa_logo" :value="__('Subir nuevo logo')" />
                            <input type="file" id="empresa_logo" name="empresa_logo" class="mt-1 block w-full text-sm text-gray-300
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-600 file:text-white
                                        hover:file:bg-blue-700
                                    " />
                            <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF hasta 2MB. Se recomienda una
                                imagen cuadrada.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('empresa_logo')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="ml-4">
                        {{ __('Guardar Configuración') }}
                    </x-primary-button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>