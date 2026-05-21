@extends('layouts.app')
@section('title', 'Editar cliente')

@section('content')
<div class="card card-pad max-w-3xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Editar: {{ $cliente->nombre_completo }}</h2>
    <form method="POST" action="{{ route('clientes.update', $cliente) }}">
        @method('PUT')
        @include('clientes._form')
    </form>
</div>
@endsection
