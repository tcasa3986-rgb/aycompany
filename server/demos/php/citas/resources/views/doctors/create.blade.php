<x-app-layout>
    <x-slot name="header">Nuevo Médico</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('doctors.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-400 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad <span
                                class="text-red-500">*</span></label>
                        <select name="specialty_id" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('specialty_id') border-red-400 @enderror">
                            <option value="">Seleccionar especialidad</option>
                            @foreach($specialties as $sp)
                                <option value="{{ $sp->id }}" {{ old('specialty_id') == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->name }}</option>
                            @endforeach
                        </select>
                        @error('specialty_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° de Colegiatura <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="collegiate_number" value="{{ old('collegiate_number') }}" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('collegiate_number') border-red-400 @enderror">
                        @error('collegiate_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biografía</label>
                    <textarea name="biography" rows="3"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('biography') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('doctors.index') }}"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
                        Guardar Médico
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>