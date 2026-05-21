@extends('layouts.app')
@section('title', 'Nueva categoría')
@section('content')
<div class="card card-pad max-w-xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Nueva categoría</h2>
    <form method="POST" action="{{ route('categorias.store') }}">@include('categorias._form')</form>
</div>
@endsection
