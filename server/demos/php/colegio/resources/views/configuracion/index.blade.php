@extends('layouts.app')
@section('title', 'Ajustes del Sistema')
@section('page-title', 'Configuración General')

@section('content')

<form method="POST" action="{{ route('configuracion.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-2" style="gap:24px;align-items:start;">

        {{-- COLUMNA IZQUIERDA --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Colegio --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-school" style="color:var(--primary);margin-right:8px;"></i>Institución</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Nombre del Colegio</label>
                            <input type="text" name="colegio_nombre" class="form-control" value="{{ \App\Models\Configuracion::get('colegio_nombre') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">RUC</label>
                            <input type="text" name="colegio_ruc" class="form-control" value="{{ \App\Models\Configuracion::get('colegio_ruc') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="colegio_direccion" class="form-control" value="{{ \App\Models\Configuracion::get('colegio_direccion') }}">
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="colegio_telefono" class="form-control" value="{{ \App\Models\Configuracion::get('colegio_telefono') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="colegio_email" class="form-control" value="{{ \App\Models\Configuracion::get('colegio_email') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Director(a) General</label>
                        <input type="text" name="colegio_director" class="form-control" value="{{ \App\Models\Configuracion::get('colegio_director') }}">
                    </div>
                </div>
            </div>

            {{-- Sistema --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-cog" style="color:var(--primary);margin-right:8px;"></i>Parámetros del Sistema</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Año Escolar Activo</label>
                            <select name="anio_escolar" class="form-control">
                                @for($y = date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ \App\Models\Configuracion::get('anio_escolar') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Símbolo de Moneda</label>
                            <select name="moneda" class="form-control">
                                <option value="S/." {{ \App\Models\Configuracion::get('moneda') == 'S/.' ? 'selected' : '' }}>S/. (Soles)</option>
                                <option value="$" {{ \App\Models\Configuracion::get('moneda') == '$' ? 'selected' : '' }}>$ (Dólares)</option>
                                <option value="€" {{ \App\Models\Configuracion::get('moneda') == '€' ? 'selected' : '' }}>€ (Euros)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Académico --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-graduation-cap" style="color:var(--primary);margin-right:8px;"></i>Parámetros Académicos</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-3">
                        <div class="form-group">
                            <label class="form-label">Nota Mínima</label>
                            <input type="number" name="nota_minima" step="0.1" class="form-control" value="{{ \App\Models\Configuracion::get('nota_minima') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nota Máxima</label>
                            <input type="number" name="nota_maxima" step="0.1" class="form-control" value="{{ \App\Models\Configuracion::get('nota_maxima') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">N° Bimestres</label>
                            <select name="num_bimestres" class="form-control">
                                <option value="2" {{ \App\Models\Configuracion::get('num_bimestres') == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ \App\Models\Configuracion::get('num_bimestres') == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ \App\Models\Configuracion::get('num_bimestres') == 4 ? 'selected' : '' }}>4</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Logo --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-image" style="color:var(--primary);margin-right:8px;"></i>Logo Institucional</span>
                </div>
                <div class="card-body" style="text-align:center;">
                    @php $logo = \App\Models\Configuracion::get('logo_url'); @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="Logo Colegio" style="max-height:120px;max-width:100%;border-radius:12px;margin-bottom:16px;">
                    @else
                        <div style="width:120px;height:120px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#cbd5e1;font-size:32px;">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                    <div class="form-group" style="text-align:left;">
                        <label class="form-label">Actualizar Logo (JPG, PNG)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            {{-- Botón Guardar --}}
            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary" style="padding:12px 24px;font-size:15px;">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>

        </div>

    </div>

</form>

@endsection
