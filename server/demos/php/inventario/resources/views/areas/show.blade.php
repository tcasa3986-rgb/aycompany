<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">
            {{-- Header Content --}}
            <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white">Detalles del Área</h2>
                <div class="flex gap-3">
                    <a href="{{ route('areas.edit', $area) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('areas.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Nombre</label>
                        <p class="text-white text-lg">{{ $area->nombre }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Estado</label>
                        <span
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $area->estado === 'Activo' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $area->estado }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Cargos Asociados</label>
                        <p class="text-white text-lg">{{ $area->cargos->count() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Empleados Asociados</label>
                        <p class="text-white text-lg">{{ $area->empleados->count() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Fecha de Creación</label>
                        <p class="text-gray-300">{{ $area->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Última Actualización</label>
                        <p class="text-gray-300">{{ $area->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>


            </div>
        </div>
    </div>
</x-app-layout>