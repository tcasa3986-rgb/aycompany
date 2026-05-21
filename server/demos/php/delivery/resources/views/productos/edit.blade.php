@extends('layouts.app')
@section('title', 'Editar Producto')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-pencil me-2 text-warning"></i>Editar Producto</h4>
    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Información del Producto</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código *</label>
                            <input type="text" name="codigo" value="{{ old('codigo', $producto->codigo) }}" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre *</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <select name="categoria_id" class="form-select" required>
                                @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id)==$cat->id?'selected':'' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Unidad</label>
                            <select name="unidad" class="form-select">
                                @foreach(['unidad','porción','litro','kg','docena'] as $u)
                                <option value="{{ $u }}" {{ old('unidad', $producto->unidad)===$u?'selected':'' }}>{{ ucfirst($u) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', $producto->stock) }}" class="form-control" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea name="descripcion" rows="3" class="form-control">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Precios y Estado</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio Base (S/) *</label>
                        <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio Delivery (S/)</label>
                        <input type="number" step="0.01" name="precio_delivery" value="{{ old('precio_delivery', $producto->precio_delivery) }}" class="form-control">
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="disponible" value="1" id="disp" {{ old('disponible', $producto->disponible) ? 'checked' : '' }}>
                        <label class="form-check-label" for="disp">Disponible para pedidos</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Producto activo</label>
                    </div>
                </div>
            </div>
            @if($producto->imagen)
            <div class="card mt-3">
                <div class="card-header">Imagen actual</div>
                <div class="card-body text-center">
                    <img src="{{ $producto->imagen_url }}" style="max-height:120px;border-radius:8px;" alt="">
                </div>
            </div>
            @endif
            <div class="card mt-3">
                <div class="card-header">Nueva Imagen</div>
                <div class="card-body">
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-warning w-100"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
