@extends('layouts.app')
@section('title', 'Nuevo cliente')

@section('content')
<div class="card card-pad max-w-3xl">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Nuevo cliente</h2>
    <form method="POST" action="{{ route('clientes.store') }}">
        @include('clientes._form')
    </form>
</div>
@endsection
