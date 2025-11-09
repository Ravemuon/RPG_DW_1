@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-primary mb-4">
        <i class="bi bi-dice-5-fill me-2"></i> {{ $sistema->nome }}
    </h1>

    @if(auth()->check() && auth()->user()->is_admin)
        @include('sistemas.partials._adm_show', ['sistema' => $sistema])
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <p><strong>Foco:</strong> {{ $sistema->foco }}</p>
            <p><strong>Complexidade:</strong> {{ $sistema->complexidade }}</p>
            <p><strong>Mecânica Principal:</strong> {{ $sistema->mecanica_principal }}</p>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('sistemas.classes.index', $sistema->id) }}" class="btn btn-outline-primary">
            <i class="bi bi-person-badge"></i> Ver Classes
        </a>
        <a href="{{ route('sistemas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

</div>
@endsection
