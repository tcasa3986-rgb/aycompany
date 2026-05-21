@extends('layouts.app')
@section('title', 'Editar categoría')
@section('content')
<div class="card card-pad max-w-xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Editar: {{ $categoria->nombre }}</h2>
    <form method="POST" action="{{ route('categorias.update', $categoria) }}">
        @method('PUT') @include('categorias._form')
    </form>
</div>
@endsection
