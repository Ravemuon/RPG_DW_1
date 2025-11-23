@extends('layouts.app')

@section('title', "Missões da Campanha - {$campanha->nome}")

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">🎯 Missões da Campanha: {{ $campanha->nome }}</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary rounded-pill shadow-sm">➕ Criar Nova Missão</a>
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary rounded-pill shadow-sm">⬅️ Voltar à Campanha</a>
    </div>

    @if($missoes->count())
        <div class="row g-3">
            @foreach($missoes as $missao)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title fw-bold">{{ $missao->titulo }}</h5>
                                <span class="badge bg-info text-dark mb-2">{{ ucfirst($missao->prioridade) }}</span>
                                <p class="card-text text-truncate">{{ $missao->descricao }}</p>
                            </div>
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-primary btn-sm flex-grow-1 rounded-pill">🔍 Ver</a>
                                <a href="{{ route('missoes.edit', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-warning btn-sm flex-grow-1 rounded-pill">✏️ Editar</a>
                                <form action="{{ route('missoes.destroy', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm w-100 rounded-pill">🗑️ Deletar</button>
                                </form>
                                <a href="{{ route('missoes.exportarPdf', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-info btn-sm flex-grow-1 rounded-pill">📄 PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-secondary text-center fst-italic">Nenhuma missão criada ainda.</div>
    @endif
</div>
@endsection
