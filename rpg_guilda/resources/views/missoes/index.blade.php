{{-- resources/views/missoes/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Missões da Campanha')

@section('content')
<div class="container py-4">
    <h2>🎮 Missões da Campanha: {{ $campanha->nome }}</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary">Criar Nova Missão</a>
        <!-- Botão para voltar à campanha -->
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary">Voltar à Campanha</a>
    </div>

    <div class="list-group">
        @foreach ($missoes as $missao)
            <a href="{{ route('missoes.show', [$campanha->id, $missao->id]) }}" class="list-group-item list-group-item-action">
                <h5 class="mb-1">{{ $missao->titulo }}</h5>
                <p class="mb-1">{{ $missao->descricao }}</p>
                <small>Status: {{ ucfirst($missao->status) }}</small>
            </a>
        @endforeach
    </div>
</div>
@endsection
