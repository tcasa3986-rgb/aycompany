@extends('layouts.app')
@section('title', 'Nuevo producto')

@section('content')
<div class="card card-pad max-w-4xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Nuevo producto</h2>
    <form method="POST" action="{{ route('productos.store') }}">
        @include('productos._form')
    </form>
</div>
@endsection
