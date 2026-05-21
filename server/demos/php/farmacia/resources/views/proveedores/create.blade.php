@extends('layouts.app')
@section('title', 'Nuevo proveedor')
@section('content')
<div class="card card-pad max-w-3xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Nuevo proveedor</h2>
    <form method="POST" action="{{ route('proveedores.store') }}">@include('proveedores._form')</form>
</div>
@endsection
