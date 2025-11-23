@extends('layouts.app')

@section('title', "Sessões - {$campanha->nome}")

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">📖 Sessões da Campanha: {{ $campanha->nome }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="{{ route('sessoes.create', $campanha->id) }}" class="btn btn-warning rounded-pill">➕ Criar Nova Sessão</a>
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary rounded-pill">⬅️ Voltar à Campanha</a>
    </div>

    @if($sessoes->count())
        <div class="list-group">
            @foreach($sessoes as $sessao)
                <div class="list-group-item list-group-item-dark d-flex justify-content-between align-items-center mb-2 rounded">
                    <div>
                        <strong>{{ $sessao->titulo }}</strong>
                        <small class="text-muted">({{ ucfirst($sessao->status) }})</small>
                        <br>
                        <small class="text-muted">
                            📅 {{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Sem data definida' }}
                        </small>
                        <br>
                        <small>{{ Str::limit($sessao->resumo ?? 'Sem resumo disponível.', 80) }}</small>
                    </div>
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-info btn-sm rounded-pill">🔍 Ver</a>
                        <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-warning btn-sm rounded-pill">✏️ Editar</a>
                        <form action="{{ route('sessoes.destroy', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Deletar esta sessão?')">🗑️ Deletar</button>
                        </form>
                        <a href="{{ route('sessoes.exportar-pdf', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-secondary btn-sm rounded-pill">📄 PDF</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-secondary fst-italic">Nenhuma sessão cadastrada ainda.</p>
    @endif
</div>
@endsection
