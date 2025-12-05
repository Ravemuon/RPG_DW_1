@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">🎯 Criar Nova Perícia</h1>

    <form action="{{ route('pericias.store') }}" method="POST">
        @include('sistemas.pericias._form')
    </form>
</div>
@endsection
