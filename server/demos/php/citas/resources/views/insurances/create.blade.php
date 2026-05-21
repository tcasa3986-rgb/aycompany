<x-app-layout>
    <x-slot name="header">Nueva Aseguradora</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('insurances.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de ARS o Seguro <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Ej: Humano, Palic, Senasa..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">RNC o Código Identificador</label>
                        <input type="text" name="rnc_or_code" value="{{ old('rnc_or_code') }}"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">% de Cobertura Promedio <span
                                class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" name="coverage_percentage"
                            value="{{ old('coverage_percentage', '0') }}" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <p class="text-xs text-gray-400 mt-1">Se usará como valor por defecto al generar un cobro.</p>
                        @error('coverage_percentage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono de Contacto</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email de Contacto</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('insurances.index') }}"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
                        Guardar Aseguradora
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>