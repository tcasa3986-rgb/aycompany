<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Bajas de Equipos</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-end mb-6">
            <a href="{{ route('bajas.create') }}"
                class="px-6 py-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-semibold rounded-lg shadow-lg">
                + Registrar Baja
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-gray-800 rounded-xl p-6 mb-6 shadow-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por código de equipo..."
                        class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <select name="motivo"
                    class="bg-gray-700 border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los motivos</option>
                    <option value="Obsolescencia" {{ request('motivo') == 'Obsolescencia' ? 'selected' : '' }}>
                        Obsolescencia</option>
                    <option value="Daño Irreparable" {{ request('motivo') == 'Daño Irreparable' ? 'selected' : '' }}>Daño
                        Irreparable</option>
                    <option value="Perdida" {{ request('motivo') == 'Perdida' ? 'selected' : '' }}>Pérdida</option>
                    <option value="Robo" {{ request('motivo') == 'Robo' ? 'selected' : '' }}>Robo</option>
                    <option value="Otro" {{ request('motivo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg flex-1">
                        Buscar
                    </button>
                    <a href="{{ route('bajas.index') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-6 py-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Equipo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Motivo</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Fecha de Baja</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Autorizado Por</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($bajas as $baja)
                            <tr class="hover:bg-gray-750 transition">
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium">{{ $baja->equipo->codigo_inventario }}</div>
                                    <div class="text-gray-400 text-sm">
                                        {{ $baja->equipo->marca->nombre ?? '' }} {{ $baja->equipo->modelo->nombre ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($baja->motivo === 'Obsolescencia')
                                        <span
                                            class="px-3 py-1 bg-gray-500/20 text-gray-400 rounded-full text-xs font-semibold">Obsolescencia</span>
                                    @elseif($baja->motivo === 'Daño Irreparable')
                                        <span
                                            class="px-3 py-1 bg-orange-500/20 text-orange-400 rounded-full text-xs font-semibold">Daño
                                            Irreparable</span>
                                    @elseif($baja->motivo === 'Perdida')
                                        <span
                                            class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">Pérdida</span>
                                    @elseif($baja->motivo === 'Robo')
                                        <span
                                            class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold">Robo</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-xs font-semibold">Otro</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-300">{{ $baja->fecha_baja->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-gray-300">{{ $baja->autorizado_por ?: 'No especificado' }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <div class="flex items-center justify-end space-x-3">
                                        {{-- Ver --}}
                                        <a href="{{ route('bajas.show', $baja) }}"
                                            class="text-blue-400 hover:text-blue-300 transition-colors p-1 hover:bg-blue-500/10 rounded-full"
                                            title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('bajas.edit', $baja) }}"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 hover:bg-yellow-500/10 rounded-full"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Restaurar (Eliminar Baja) --}}
                                        <form action="{{ route('bajas.destroy', $baja) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('¿Eliminar esta baja y restaurar el equipo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-300 transition-colors p-1 hover:bg-red-500/10 rounded-full"
                                                title="Eliminar Baja (Restaurar Equipo)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    No hay equipos dados de baja
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($bajas->hasPages())
                <div class="px-6 py-4 bg-gray-750">
                    {{ $bajas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>