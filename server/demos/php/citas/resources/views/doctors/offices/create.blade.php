<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('doctors.offices.index', $doctor) }}"
                class="text-gray-400 hover:text-blue-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            Nuevo Consultorio — Dr. {{ $doctor->user->name }}
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('doctors.offices.store', $doctor) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Consultorio <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Ej. Consultorio Principal, Clínica Norte..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Av. Principal 123..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Piso / Nivel / Referencia</label>
                        <input type="text" name="floor" value="{{ old('floor') }}" placeholder="Piso 3, Cons. 302..."
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Directo</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Enlace a Google Maps URL</label>
                    <input type="url" name="maps_url" value="{{ old('maps_url') }}"
                        placeholder="https://maps.google.com/..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    @error('maps_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 pb-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked
                            class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Consultorio Activo</span>
                    </label>
                    <p class="text-xs text-gray-400 ml-8 mt-0.5">Si se desactiva, no aparecerá como opción para agendar
                        citas.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('doctors.offices.index', $doctor) }}"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 font-medium">Cancelar</a>
                    <button
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow">Guardar
                        Consultorio</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>