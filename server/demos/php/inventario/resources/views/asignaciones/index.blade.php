<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Asignaciones</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header Actions --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-white md:hidden">Asignaciones</h2>
            <div class="flex gap-3 ml-auto">
                <a href="{{ route('asignaciones.export') }}"
                    class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition flex items-center gap-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar Excel
                </a>
                <a href="{{ route('asignaciones.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nueva Asignación
                </a>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-6 bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            {{-- Total Asignaciones --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Total Asignaciones</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['total'] }}</div>
                </div>
            </div>

            {{-- Activas --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Activas</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['activas'] }}</div>
                </div>
            </div>

            {{-- Finalizadas --}}
            <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Finalizadas</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['finalizadas'] }}</div>
                </div>
            </div>

            {{-- Anuladas --}}
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow p-5">
                <div class="text-white">
                    <div class="text-sm opacity-90">Anuladas</div>
                    <div class="text-4xl font-bold mt-2">{{ $stats['anuladas'] }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-gray-800 rounded-xl p-6 mb-6 shadow-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <select name="estado"
                        class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="Activa" {{ request('estado') == 'Activa' ? 'selected' : '' }}>Activa</option>
                        <option value="Finalizada" {{ request('estado') == 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="Anulada" {{ request('estado') == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Filtrar
                    </button>
                    <a href="{{ route('asignaciones.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center justify-center">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Asignaciones Table --}}
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Equipo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Empleado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Fecha Entrega</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Fecha Devolución</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($asignaciones as $asignacion)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">
                                        {{ $asignacion->equipo->marca->nombre ?? '' }}
                                        {{ $asignacion->equipo->modelo->nombre ?? '' }}
                                    </div>
                                    <div class="text-sm text-gray-400">{{ $asignacion->equipo->codigo_inventario }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $asignacion->empleado->nombreCompleto() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $asignacion->fecha_entrega->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $asignacion->fecha_devolucion ? $asignacion->fecha_devolucion->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estados = [
                                            'Activa' => 'bg-green-500/20 text-green-400',
                                            'Finalizada' => 'bg-gray-500/20 text-gray-400',
                                            'Anulada' => 'bg-red-500/20 text-red-400'
                                        ];
                                        $clase = $estados[$asignacion->estado_asignacion] ?? 'bg-gray-500/20 text-gray-400';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $clase }}">
                                        {{ $asignacion->estado_asignacion }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        {{-- Ver --}}
                                        <a href="{{ route('asignaciones.show', $asignacion) }}"
                                            class="text-blue-400 hover:text-blue-300 transition-colors p-1 hover:bg-blue-500/10 rounded-full"
                                            title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        {{-- PDF Acta --}}
                                        <a href="{{ route('asignaciones.acta', $asignacion) }}"
                                            class="text-purple-400 hover:text-purple-300 transition-colors p-1 hover:bg-purple-500/10 rounded-full"
                                            title="Descargar Acta de Entrega">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </a>

                                        {{-- Subir/Ver Acta Firmada --}}
                                        @if($asignacion->acta_firmada_path)
                                            <button type="button"
                                                onclick="viewSignedActa('{{ asset('storage/' . $asignacion->acta_firmada_path) }}', '{{ Str::endsWith($asignacion->acta_firmada_path, '.pdf') ? 'pdf' : 'image' }}')"
                                                class="text-teal-400 hover:text-teal-300 transition-colors p-1 hover:bg-teal-500/10 rounded-full"
                                                title="Ver Acta Firmada">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                onclick="openUploadModal({{ $asignacion->id }})"
                                                class="text-orange-400 hover:text-orange-300 transition-colors p-1 hover:bg-orange-500/10 rounded-full"
                                                title="Subir Acta Firmada">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                            </button>
                                        @endif

                                        @if(in_array($asignacion->estado_asignacion, ['Finalizada', 'Anulada']))
                                            {{-- PDF Acta Devolución --}}
                                            <a href="{{ route('asignaciones.acta-devolucion', $asignacion) }}"
                                                class="text-red-400 hover:text-red-300 transition-colors p-1 hover:bg-red-500/10 rounded-full"
                                                title="Descargar Acta de Devolución">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </a>

                                            {{-- Subir/Ver Acta Devolución --}}
                                            @if($asignacion->acta_devolucion_path)
                                                <button type="button"
                                                    onclick="viewSignedActa('{{ asset('storage/' . $asignacion->acta_devolucion_path) }}', '{{ Str::endsWith($asignacion->acta_devolucion_path, '.pdf') ? 'pdf' : 'image' }}', 'Devolución')"
                                                    class="text-teal-400 hover:text-teal-300 transition-colors p-1 hover:bg-teal-500/10 rounded-full"
                                                    title="Ver Acta Devolución Firmada">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button"
                                                    onclick="openUploadModal({{ $asignacion->id }}, 'devolucion')"
                                                    class="text-orange-400 hover:text-orange-300 transition-colors p-1 hover:bg-orange-500/10 rounded-full"
                                                    title="Subir Acta Devolución Firmada">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif

                                        @if($asignacion->estado_asignacion !== 'Anulada')
                                            {{-- Editar --}}
                                            <a href="{{ route('asignaciones.edit', $asignacion) }}"
                                                class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 hover:bg-yellow-500/10 rounded-full"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- Anular --}}
                                            <form action="{{ route('asignaciones.annul', $asignacion) }}" method="POST"
                                                class="inline-block form-annul-{{ $asignacion->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="motivo_anulacion" id="motivo_anulacion_{{ $asignacion->id }}">
                                                
                                                <button type="button"
                                                    onclick="confirmAnnul({{ $asignacion->id }})"
                                                    class="text-red-400 hover:text-red-300 transition-colors p-1 hover:bg-red-500/10 rounded-full"
                                                    title="Anular Asignación">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                </button>
                                            </form>
                                            </form>
                                        @endif

                                        {{-- Devolver (Solo si Activa) --}}
                                        @if($asignacion->estado_asignacion === 'Activa')
                                            <button type="button"
                                                onclick="openReturnModal({{ $asignacion->id }})"
                                                class="text-indigo-400 hover:text-indigo-300 transition-colors p-1 hover:bg-indigo-500/10 rounded-full"
                                                title="Devolver Equipo">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                    No se encontraron asignaciones
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $asignaciones->links() }}
        </div>
    </div>

    {{-- Return Modal --}}
    <div x-data="{ show: false, actionUrl: '' }"
        x-show="show"
        @open-return-modal.window="show = true; actionUrl = '/asignaciones/' + $event.detail.id + '/return'"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="show = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form :action="actionUrl" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-white">Devolver Equipo</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Fecha de Devolución</label>
                                        <input type="date" name="fecha_devolucion" required
                                            value="{{ date('Y-m-d') }}"
                                            max="{{ date('Y-m-d') }}"
                                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Observaciones</label>
                                        <textarea name="observaciones_devolucion" rows="3"
                                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500"
                                            placeholder="Estado del equipo, accesorios devueltos, etc..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmar Devolución
                        </button>
                        <button type="button" @click="show = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-500 shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div x-data="{ show: false, actionUrl: '', type: '' }"
        x-show="show"
        @open-upload-modal.window="show = true; type = $event.detail.type; actionUrl = '/asignaciones/' + $event.detail.id + ($event.detail.type === 'devolucion' ? '/upload-acta-devolucion' : '/upload-acta')"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form :action="actionUrl" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-white" x-text="type === 'devolucion' ? 'Subir Acta de Devolución' : 'Subir Acta de Entrega'"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-400 mb-4">
                                        Seleccione el archivo del acta firmada (PDF o Imagen).
                                    </p>
                                    <input type="file" :name="type === 'devolucion' ? 'acta_devolucion' : 'acta_firmada'" accept=".pdf,.jpg,.jpeg,.png" required
                                        class="block w-full text-sm text-gray-400
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-500 file:text-white
                                        hover:file:bg-blue-600
                                        cursor-pointer bg-gray-700 rounded-lg border border-gray-600">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Subir
                        </button>
                        <button type="button" @click="show = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-500 shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div x-data="{ show: false, fileUrl: '', fileType: '', title: '' }"
        x-show="show"
        @view-acta.window="show = true; fileUrl = $event.detail.url; fileType = $event.detail.type; title = $event.detail.title || 'Acta Firmada'"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75" @click="show = false"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-white" x-text="title"></h3>
                        <button @click="show = false" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 h-[75vh]">
                        <template x-if="fileType === 'pdf'">
                            <iframe :src="fileUrl" class="w-full h-full rounded border border-gray-600"></iframe>
                        </template>
                        <template x-if="fileType === 'image'">
                            <div class="w-full h-full flex items-center justify-center bg-gray-900 rounded border border-gray-600 overflow-auto">
                                <img :src="fileUrl" class="max-w-full max-h-full object-contain">
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')

        <script>
            function openUploadModal(id, type = 'entrega') {
                window.dispatchEvent(new CustomEvent('open-upload-modal', {
                    detail: { id: id, type: type }
                }));
            }

            function openReturnModal(id) {
                window.dispatchEvent(new CustomEvent('open-return-modal', {
                    detail: { id: id }
                }));
            }

            function viewSignedActa(url, type, title = 'Acta Firmada') {
                window.dispatchEvent(new CustomEvent('view-acta', {
                    detail: { url: url, type: type, title: title }
                }));
            }

            function confirmAnnul(id) {
                Swal.fire({
                    title: '¿Anular Asignación?',
                    text: "Por favor ingrese el motivo de la anulación:",
                    input: 'textarea',
                    inputPlaceholder: 'Escriba el motivo aquí...',
                    inputAttributes: {
                        'aria-label': 'Escriba el motivo aquí'
                    },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, anular',
                    cancelButtonText: 'Cancelar',
                    background: '#1F2937', // gray-800
                    color: '#F3F4F6', // gray-100
                    inputValidator: (value) => {
                        if (!value) {
                            return '¡Debe ingresar un motivo!'
                        }
                    },
                    customClass: {
                        input: 'bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`motivo_anulacion_${id}`).value = result.value;
                        document.querySelector(`.form-annul-${id}`).submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>