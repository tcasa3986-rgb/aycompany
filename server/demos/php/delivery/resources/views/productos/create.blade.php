@extends('layouts.app')
@section('title', 'Nuevo Producto')

@section('breadcrumb')
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol></nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Nuevo Producto</h4>
    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Información del Producto</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código *</label>
                            <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control @error('codigo') is-invalid @enderror" required placeholder="CF001">
                            @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre del Producto *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                                <option value="">Seleccionar...</option>
                                @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id')==$cat->id?'selected':'' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Unidad</label>
                            <select name="unidad" class="form-select">
                                @foreach(['unidad','porción','litro','kg','docena'] as $u)
                                <option value="{{ $u }}" {{ old('unidad')===  $u?'selected':'' }}>{{ ucfirst($u) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', 100) }}" class="form-control" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea name="descripcion" rows="3" class="form-control" placeholder="Describe el producto...">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Precios</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio Base (S/) *</label>
                        <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" class="form-control @error('precio') is-invalid @enderror" required>
                        @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio Delivery (S/)</label>
                        <input type="number" step="0.01" name="precio_delivery" value="{{ old('precio_delivery') }}" class="form-control">
                        <div class="form-text">Si se deja vacío, se usa el precio base</div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="disponible" value="1" id="disponible" {{ old('disponible', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="disponible">Disponible para pedidos</label>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Imagen (opcional)</div>
                <div class="card-body">
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Guardar Producto</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
