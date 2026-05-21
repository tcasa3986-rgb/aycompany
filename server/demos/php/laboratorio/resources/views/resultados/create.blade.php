@extends('layouts.app')

@section('title', 'Ingresar Resultados')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title text-gradient">Resultados - Orden {{ $orden->numero_orden }}</h1>
        <p class="text-secondary">Paciente: {{ $orden->paciente->nombre_completo }}</p>
    </div>
    <div>
        <a href="{{ route('resultados.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Volver a Lista</a>
    </div>
</div>

<div class="card" style="max-width: 1200px;">
    @if ($errors->any())
        <div class="alert-error" style="background: rgba(255, 71, 87, 0.1); color: var(--danger); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('resultados.store', $orden->id) }}" method="POST">
        @csrf
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Prueba</th>
                        <th>Valor Obtenido</th>
                        <th>Unidad</th>
                        <th>Ref. Normal</th>
                        <th>Interpretación</th>
                        <th>Método</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orden->detalles as $detalle)
                        @if($detalle->estado === 'Completado')
                            <!-- Ya completado, se puede mostrar o ignorar -->
                            @php $resultado = $detalle->resultado; @endphp
                            <tr style="background: rgba(46, 213, 115, 0.05);">
                                <td>{{ $detalle->prueba->nombre }}</td>
                                <td><strong class="text-success">{{ $resultado->valor ?? 'Completado' }}</strong></td>
                                <td>{{ $detalle->prueba->unidad }}</td>
                                <td>{{ $detalle->prueba->valores_referencia }}</td>
                                <td><span class="status-badge status-completed">{{ $resultado->interpretacion ?? 'Normal' }}</span></td>
                                <td>Validado</td>
                            </tr>
                        @else
                            <tr>
                                <td><strong>{{ $detalle->prueba->codigo }}</strong> - {{ $detalle->prueba->nombre }}</td>
                                <td>
                                    <input type="text" name="resultados[{{ $detalle->id }}][valor]" class="form-control" placeholder="Ingrese valor..." style="width: 150px; background: rgba(0,0,0,0.3);">
                                </td>
                                <td>{{ $detalle->prueba->unidad }}</td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $detalle->prueba->valores_referencia }}</td>
                                <td>
                                    <select name="resultados[{{ $detalle->id }}][interpretacion]" class="form-control" style="width: 130px; background: rgba(0,0,0,0.3);">
                                        <option value="Normal">Normal</option>
                                        <option value="Bajo">Bajo</option>
                                        <option value="Alto">Alto</option>
                                        <option value="Crítico" class="text-danger">Crítico</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="resultados[{{ $detalle->id }}][metodo]" class="form-control" value="Automatizado" style="width: 140px; background: rgba(0,0,0,0.3);">
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px; text-align: right; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check-double"></i> Guardar y Validar Resultados</button>
        </div>
    </form>
</div>
@endsection
