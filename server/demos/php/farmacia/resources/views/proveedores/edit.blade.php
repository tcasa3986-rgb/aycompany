@extends('layouts.app')
@section('title', 'Editar proveedor')
@section('content')
<div class="card card-pad max-w-3xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Editar: {{ $proveedor->razon_social }}</h2>
    <form method="POST" action="{{ route('proveedores.update', $proveedor) }}">
        @method('PUT') @include('proveedores._form')
    </form>
</div>
@endsection
