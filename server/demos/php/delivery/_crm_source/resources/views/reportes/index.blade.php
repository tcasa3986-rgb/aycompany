@extends('layouts.app')
@section('title', 'Reportes')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes</li>
    </ol></nav>
@endsection

@section('content')
<h4 class="page-title mb-4"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Centro de Reportes</h4>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div style="width:70px;height:70px;background:linear-gradient(135deg,#0d6efd,#0a58ca);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem">
                    <i class="bi bi-graph-up-arrow text-white fs-2"></i>
                </div>
                <h5 class="fw-bold">Reporte de Ventas</h5>
                <p class="text-muted small">Análisis de pedidos e ingresos por rango de fechas. Total de pedidos, entregados, cancelados e ingresos totales.</p>
                <a href="{{ route('reportes.ventas') }}" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-right me-1"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div style="width:70px;height:70px;background:linear-gradient(135deg,#198754,#146c43);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem">
                    <i class="bi bi-bicycle text-white fs-2"></i>
                </div>
                <h5 class="fw-bold">Rendimiento de Repartidores</h5>
                <p class="text-muted small">Entregas realizadas, tiempos promedio y calificaciones por repartidor en un período seleccionado.</p>
                <a href="{{ route('reportes.repartidores') }}" class="btn btn-success w-100">
                    <i class="bi bi-arrow-right me-1"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div style="width:70px;height:70px;background:linear-gradient(135deg,#6f42c1,#59359a);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem">
                    <i class="bi bi-people text-white fs-2"></i>
                </div>
                <h5 class="fw-bold">Clientes más Activos</h5>
                <p class="text-muted small">Ranking de clientes por cantidad de pedidos y monto total gastado en el período seleccionado.</p>
                <a href="{{ route('reportes.clientes') }}" class="btn btn-purple w-100" style="background:#6f42c1;color:#fff;border:none">
                    <i class="bi bi-arrow-right me-1"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
