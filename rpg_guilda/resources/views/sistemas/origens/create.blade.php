@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">🧭 Criar Nova Origem</h1>

    <form action="{{ route('origens.store') }}" method="POST">
        @include('sistemas.origens._form')
    </form>
</div>
@endsection
