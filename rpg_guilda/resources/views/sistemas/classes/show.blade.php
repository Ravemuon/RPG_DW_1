@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">📖 Detalhes da Classe: {{ $classe->nome }}</h1>
    <div class="mb-3">
        <strong>Descrição:</strong>
        <p>{{ $classe->descricao }}</p>
    </div>
    <div class="mb-3">
        <strong>Página:</strong>
        <p>{{ $classe->pagina }}</p>
    </div>

    <a href="{{ route('sistemas.classes.index', $classe->sistema_id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
