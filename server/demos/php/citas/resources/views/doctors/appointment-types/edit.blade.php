<x-app-layout>
    <x-slot name="header">Editar Tipo de Cita - Dr. {{ $doctor->user->name }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('doctors.appointment-types.update', [$doctor, $appointmentType]) }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Servicio/Cita</label>
                    <input type="text" name="name" value="{{ old('name', $appointmentType->name) }}" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración (minutos)</label>
                    <select name="duration_minutes" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        @foreach([10, 15, 20, 30, 40, 45, 60, 90, 120] as $min)
                            <option value="{{ $min }}" {{ old('duration_minutes', $appointmentType->duration_minutes) == $min ? 'selected' : '' }}>{{ $min }} minutos</option>
                        @endforeach
                    </select>
                    @error('duration_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio (Opcional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span
                                class="text-gray-500 sm:text-sm">{{ \App\Models\Setting::get('currency_symbol', 'S/') }}</span>
                        </div>
                        <input type="number" step="0.01" min="0" name="price"
                            value="{{ old('price', $appointmentType->price) }}"
                            class="w-full rounded-lg border border-gray-200 pl-8 pr-4 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                    </div>
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $appointmentType->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="text-sm text-gray-700">Servicio Activo (Disponible para
                        agendar)</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('doctors.appointment-types.index', $doctor) }}"
                    class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 font-medium">Cancelar</a>
                <button type="submit"
                    class="px-5 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>