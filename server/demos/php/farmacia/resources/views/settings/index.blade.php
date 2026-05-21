@extends('layouts.app')

@section('title', 'Configuración')
@section('section', 'Ajustes del Sistema')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        {{-- Header con Botón Guardar --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Configuración General</h1>
                <p class="text-sm text-gray-500 mt-1">Administra los datos de la empresa, logo y preferencias del sistema.</p>
            </div>
            <button type="submit" class="flex items-center gap-2 bg-farmacia-600 hover:bg-farmacia-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-farmacia-100 active:scale-95">
                <x-icon name="save" class="h-5 w-5" />
                Guardar Cambios
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Panel Izquierdo: Datos de Empresa --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Sección: Identidad --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <h2 class="flex items-center gap-2 text-base font-bold text-gray-700 uppercase tracking-wider">
                            <x-icon name="office-building" class="h-5 w-5 text-farmacia-500" />
                            Datos de la Farmacia
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="label">Nombre Comercial / Razón Social</label>
                            <input type="text" name="company_name" class="input" value="{{ setting('company_name', 'ERP Farmacia') }}" placeholder="Nombre de tu farmacia">
                        </div>
                        
                        <div>
                            <label class="label">RUC / NIT / Identificación Fiscal</label>
                            <input type="text" name="company_tax_id" class="input" value="{{ setting('company_tax_id') }}" placeholder="Ej: 20123456789">
                        </div>

                        <div>
                            <label class="label">Teléfono / WhatsApp de contacto</label>
                            <input type="text" name="company_phone" class="input" value="{{ setting('company_phone') }}" placeholder="Ej: +51 987 654 321">
                        </div>

                        <div class="md:col-span-2">
                            <label class="label">Dirección Fiscal / Principal</label>
                            <textarea name="company_address" class="input min-h-[80px]" placeholder="Dirección completa...">{{ setting('company_address') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sección: Preferencias Regionales y de Alerta --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <h2 class="flex items-center gap-2 text-base font-bold text-gray-700 uppercase tracking-wider">
                            <x-icon name="cog" class="h-5 w-5 text-indigo-500" />
                            Preferencias de Operación
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="label">Símbolo de Moneda</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <x-icon name="currency-dollar" class="h-5 w-5" />
                                </span>
                                <input type="text" name="currency_symbol" class="input pl-10" value="{{ setting('currency_symbol', 'S/') }}">
                            </div>
                        </div>

                        <div>
                            <label class="label">Impuesto Base (%)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <span class="font-bold text-sm">%</span>
                                </span>
                                <input type="number" name="tax_percentage" class="input pl-10" value="{{ setting('tax_percentage', 18) }}">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="label">Email para Alertas Críticas</label>
                            <p class="text-[11px] text-gray-400 mb-2">Aquí recibirás avisos de stock bajo y lotes próximos a vencer.</p>
                            <input type="email" name="admin_email" class="input" value="{{ setting('admin_email') }}" placeholder="admin@tudominio.com">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Derecho: Logo y Backup --}}
            <div class="space-y-8">
                {{-- Sección: Logo --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <h2 class="flex items-center gap-2 text-base font-bold text-gray-700 uppercase tracking-wider">
                            <x-icon name="photograph" class="h-5 w-5 text-amber-500" />
                            Identidad Visual
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex flex-col items-center">
                            <div class="w-full aspect-video bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden mb-4 group relative">
                                @if(setting('company_logo'))
                                    <img src="{{ setting('company_logo') }}" class="max-h-full object-contain p-4 group-hover:opacity-50 transition-opacity" alt="Logo">
                                @else
                                    <x-icon name="image" class="h-12 w-12 text-gray-300" />
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <label class="bg-white px-4 py-2 rounded-lg shadow-md cursor-pointer font-bold text-sm text-gray-700 hover:bg-gray-50">
                                        Subir Archivo
                                        <input type="file" name="logo" class="hidden">
                                    </label>
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 text-center">Formatos sugeridos: PNG, JPG o SVG. Máx 2MB.</p>
                        </div>
                        
                        <div>
                            <label class="label">O pega una URL de imagen</label>
                            <input type="text" name="company_logo" class="input text-xs" placeholder="https://ejemplo.com/logo.png" value="{{ setting('company_logo') }}">
                        </div>
                    </div>
                </div>

                {{-- Sección: Mantenimiento --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <h2 class="flex items-center gap-2 text-base font-bold text-gray-700 uppercase tracking-wider">
                            <x-icon name="database" class="h-5 w-5 text-rose-500" />
                            Mantenimiento
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="p-4 bg-rose-50 rounded-xl border border-rose-100 mb-4">
                            <p class="text-sm font-bold text-rose-800 mb-1">Copia de Seguridad</p>
                            <p class="text-xs text-rose-600 leading-relaxed">Genera un respaldo completo de la base de datos (SQL) para prevenir pérdida de información.</p>
                        </div>
                        <a href="{{ route('settings.backup') }}" class="flex items-center justify-center gap-2 w-full bg-white border-2 border-rose-200 text-rose-600 hover:bg-rose-50 py-3 rounded-xl font-bold transition-all active:scale-95">
                            <x-icon name="download" class="h-5 w-5" />
                            Descargar Backup SQL
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
