@extends('layouts.app')
@section('title', 'Editar producto')

@section('content')
<div class="card card-pad max-w-4xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Editar producto: {{ $producto->nombre }}</h2>
    <form method="POST" action="{{ route('productos.update', $producto) }}">
        @method('PUT')
        @include('productos._form')
    </form>
</div>
@endsection
