@extends('layouts.app')

@section('title', "Missão - {$missao->titulo}")

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-3">🎯 Missão: {{ $missao->titulo }}</h2>

    <div class="mb-3">
        <span class="badge bg-info">{{ ucfirst($missao->prioridade) }}</span>
        <span class="badge bg-secondary">{{ ucfirst($missao->status) }}</span>
    </div>

    <div class="card bg-dark text-light mb-3">
        <div class="card-body">
            <h5>Descrição:</h5>
            <p>{{ $missao->descricao ?? 'Nenhuma descrição.' }}</p>

            <h5>Recompensa:</h5>
            <p>{{ $missao->recompensa ?? 'Nenhuma recompensa definida.' }}</p>

            <small>Criada em: {{ $missao->created_at->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    <a href="{{ route('missoes.edit', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-warning rounded-pill">✏️ Editar</a>
    <form action="{{ route('missoes.destroy', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger rounded-pill">🗑️ Deletar</button>
    </form>
    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-secondary rounded-pill">⬅️ Voltar</a>
    <a href="{{ route('missoes.exportarPdf', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-info rounded-pill">📄 Exportar PDF</a>
</div>
@endsection
