<x-app-layout>
    <x-slot name="header">Nuevo Paciente</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('patients.store') }}" class="space-y-5">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
                        <input type="date" name="dob" value="{{ old('dob') }}"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                        <select name="gender"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">Seleccionar</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femenino</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de sangre</label>
                        <select name="blood_type"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">Seleccionar</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alergias conocidas</label>
                    <textarea name="allergies" rows="2"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('allergies') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seguro Médico (ARS)</label>
                        <select name="insurance_id"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">Particular / Sin Seguro</option>
                            @foreach($insurances as $insurance)
                                <option value="{{ $insurance->id }}" {{ old('insurance_id') == $insurance->id ? 'selected' : '' }}>
                                    {{ $insurance->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('insurance_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nro. de Póliza / Afiliado</label>
                        <input type="text" name="policy_number" value="{{ old('policy_number') }}"
                            placeholder="Opcional"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('policy_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Médico de Cabecera
                        <span class="text-gray-400 font-normal text-xs ml-1">(opcional)</span>
                    </label>
                    <select name="primary_doctor_id"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Sin médico de cabecera asignado</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ old('primary_doctor_id') == $doc->id ? 'selected' : '' }}>
                                Dr. {{ $doc->user->name }}
                                {{ $doc->specialty ? '— ' . optional($doc->specialty)->name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('primary_doctor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('patients.index') }}"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition shadow">
                        Guardar Paciente
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>