<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            {{-- Header Content --}}
            <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white">Detalle de la Marca</h2>
                <div class="flex gap-3">
                    <a href="{{ route('marcas.edit', $marca) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('marcas.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Información Principal --}}
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Información de la Marca</h3>
                    @if($marca->estado === 'Activo')
                        <span
                            class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-semibold">Activo</span>
                    @else
                        <span class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm font-semibold">Inactivo</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Nombre</p>
                        <p class="text-white font-semibold text-lg">{{ $marca->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Total de Modelos</p>
                        <p class="text-white font-medium">{{ $marca->modelos_count ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Total de Equipos</p>
                        <p class="text-white font-medium">{{ $marca->equipos_count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Lista de Modelos Recientes (Opcional) --}}
            @if($marca->modelos && $marca->modelos->count() > 0)
                <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                    <h3 class="text-xl font-bold text-white mb-6">Modelos Asociados</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr class="text-left">
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Nombre
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($marca->modelos->take(5) as $modelo)
                                    <tr class="hover:bg-gray-750">
                                        <td class="px-4 py-3 text-white">{{ $modelo->nombre }}</td>
                                        <td class="px-4 py-3">
                                            @if($modelo->estado === 'Activo')
                                                <span class="text-green-400 text-xs">Activo</span>
                                            @else
                                                <span class="text-red-400 text-xs">Inactivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>