<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">Reportes PDF</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Reporte de Equipos --}}
                <div
                    class="group relative bg-gray-800 rounded-2xl p-6 border border-gray-700/50 hover:border-blue-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
                    <div class="absolute top-0 right-0 p-4 opacity-50 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-gray-500 group-hover:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="p-3 bg-blue-500/10 rounded-xl group-hover:bg-blue-500/20 transition-colors">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors">
                                Inventario de Equipos</h3>
                            <p class="text-sm text-gray-400 mt-1">Listado completo y detallado de todo el equipamiento
                                registrado.</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('reportes.equipos') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-700 hover:bg-blue-600 text-gray-200 hover:text-white rounded-lg text-sm font-medium transition-all duration-300 group-hover:shadow-md">
                            Generar Reporte
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Reporte de Asignaciones --}}
                <div
                    class="group relative bg-gray-800 rounded-2xl p-6 border border-gray-700/50 hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
                    <div class="absolute top-0 right-0 p-4 opacity-50 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-gray-500 group-hover:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="p-3 bg-purple-500/10 rounded-xl group-hover:bg-purple-500/20 transition-colors">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white group-hover:text-purple-400 transition-colors">
                                Historial de Asignaciones</h3>
                            <p class="text-sm text-gray-400 mt-1">Registro de entregas, devoluciones y responsables
                                activos.</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('reportes.asignaciones') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-700 hover:bg-purple-600 text-gray-200 hover:text-white rounded-lg text-sm font-medium transition-all duration-300 group-hover:shadow-md">
                            Generar Reporte
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Reporte de Reparaciones --}}
                <div
                    class="group relative bg-gray-800 rounded-2xl p-6 border border-gray-700/50 hover:border-orange-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-orange-500/10">
                    <div class="absolute top-0 right-0 p-4 opacity-50 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-gray-500 group-hover:text-orange-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="p-3 bg-orange-500/10 rounded-xl group-hover:bg-orange-500/20 transition-colors">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white group-hover:text-orange-400 transition-colors">
                                Mantenimiento y Reparaciones</h3>
                            <p class="text-sm text-gray-400 mt-1">Estado de equipos en servicio técnico.</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('reportes.reparaciones') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-700 hover:bg-orange-600 text-gray-200 hover:text-white rounded-lg text-sm font-medium transition-all duration-300 group-hover:shadow-md">
                            Generar Reporte
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Reporte de Bajas --}}
                <div
                    class="group relative bg-gray-800 rounded-2xl p-6 border border-gray-700/50 hover:border-red-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-red-500/10">
                    <div class="absolute top-0 right-0 p-4 opacity-50 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-gray-500 group-hover:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="p-3 bg-red-500/10 rounded-xl group-hover:bg-red-500/20 transition-colors">
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white group-hover:text-red-400 transition-colors">Equipos
                                de Baja</h3>
                            <p class="text-sm text-gray-400 mt-1">Dispositivos retirados del inventario y causas.</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('reportes.bajas') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-700 hover:bg-red-600 text-gray-200 hover:text-white rounded-lg text-sm font-medium transition-all duration-300 group-hover:shadow-md">
                            Generar Reporte
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Reporte por Empleado --}}
                <div
                    class="group relative bg-gray-800 rounded-2xl p-6 border border-gray-700/50 hover:border-emerald-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
                    <div class="absolute top-0 right-0 p-4 opacity-50 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6 text-gray-500 group-hover:text-emerald-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="p-3 bg-emerald-500/10 rounded-xl group-hover:bg-emerald-500/20 transition-colors">
                            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors">
                                Reporte por Empleado</h3>
                            <p class="text-sm text-gray-400 mt-1">Generar reportes individuales desde el módulo.</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('empleados.index') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-700 hover:bg-emerald-600 text-gray-200 hover:text-white rounded-lg text-sm font-medium transition-all duration-300 group-hover:shadow-md">
                            Ir a Empleados
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Info Card --}}
                <div
                    class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700/50 flex flex-col justify-center items-center text-center">
                    <div class="p-3 bg-gray-700/50 rounded-full mb-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Información</h3>
                    <p class="text-gray-400 text-sm max-w-xs">Los reportes se generan en formato PDF listos para
                        imprimir o archivar. Usa los filtros en cada módulo para resultados específicos.</p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>