<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                {{-- Header Content --}}
                <div class="flex justify-between items-center bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-white">Detalles del Empleado</h2>
                    <div class="flex gap-3">
                        <a href="{{ route('empleados.edit', $empleado) }}"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route('empleados.index') }}"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>

                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Información Personal --}}
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-lg font-semibold text-blue-400 mb-4 border-b border-gray-700 pb-2">
                                    Información Personal</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">DNI</label>
                                <p class="mt-1 text-lg font-semibold">{{ $empleado->dni }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Nombre Completo</label>
                                <p class="mt-1 text-lg font-semibold">{{ $empleado->nombreCompleto() }}</p>
                            </div>

                            {{-- Información Laboral --}}
                            <div class="col-span-1 md:col-span-2 mt-4">
                                <h3 class="text-lg font-semibold text-blue-400 mb-4 border-b border-gray-700 pb-2">
                                    Información Laboral</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Sucursal</label>
                                <p class="mt-1 text-lg">{{ $empleado->sucursal->nombre ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Área</label>
                                <p class="mt-1 text-lg">{{ $empleado->area->nombre ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Cargo</label>
                                <p class="mt-1 text-lg">{{ $empleado->cargo->nombre ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Estado</label>
                                <p class="mt-1">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $empleado->estado === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $empleado->estado }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        {{-- Equipos Asignados --}}
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-blue-400 mb-4 border-b border-gray-700 pb-2">Equipos
                                Asignados</h3>
                            @if($empleado->asignaciones->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-700">
                                        <thead class="bg-gray-700">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                    Equipo</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                    Marca/Modelo</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                    Serie</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                    Fecha Asignación</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                    Estado Asignación</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                                            @foreach($empleado->asignaciones as $asignacion)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                                        {{ $asignacion->equipo->tipoEquipo->nombre ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                                        {{ $asignacion->equipo->marca->nombre ?? '' }}
                                                        {{ $asignacion->equipo->modelo->nombre ?? '' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                                        {{ $asignacion->equipo->numero_serie ?? 'S/N' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                                        {{ $asignacion->fecha_entrega ? \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') : 'N/A' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $asignacion->estado_asignacion === 'Activa' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $asignacion->estado_asignacion }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-400">No hay equipos asignados actualmente.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>