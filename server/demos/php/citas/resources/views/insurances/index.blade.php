<x-app-layout>
    <x-slot name="header">Gestión de Seguros (ARS)</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Aseguradoras Registradas</h2>
            <a href="{{ route('insurances.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow">
                + Nueva Aseguradora
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($insurances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Nombre / ARS</th>
                                <th class="px-6 py-4">RNC o Código</th>
                                <th class="px-6 py-4">Contacto</th>
                                <th class="px-6 py-4">Cobertura Base</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($insurances as $insurance)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $insurance->name }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $insurance->rnc_or_code ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-800">{{ $insurance->contact_phone ?? '—' }}</div>
                                        <div class="text-xs text-gray-500">{{ $insurance->contact_email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2.5 py-1 bg-green-50 text-green-700 rounded-md text-xs font-medium">{{ number_format($insurance->coverage_percentage, 0) }}%</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('insurances.edit', $insurance) }}"
                                                class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                                            <form action="{{ route('insurances.destroy', $insurance) }}" method="POST"
                                                onsubmit="return confirm('¿Seguro de eliminar esta aseguradora?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $insurances->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div
                        class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No hay Aseguradoras</h3>
                    <p class="text-gray-500 text-sm mb-4">Añade los seguros médicos con los que trabajas.</p>
                    <a href="{{ route('insurances.create') }}"
                        class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition">
                        Crear Aseguradora
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>