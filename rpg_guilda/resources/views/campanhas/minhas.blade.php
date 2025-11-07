@extends('layouts.app')

@section('title', 'Minhas Campanhas')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-warning mb-4">🎲 Minhas Campanhas</h2>

    @forelse($minhasCampanhas as $campanha)
        <div class="card bg-secondary text-light mb-3 shadow">
            <div class="card-body">
                <h5 class="card-title fw-bold text-light">{{ $campanha->nome }}</h5>
                <p class="card-text small text-warning">Mestre: {{ $campanha->criador->nome ?? 'Desconhecido' }}</p>
                <p class="card-text small">{{ Str::limit($campanha->descricao, 80) }}</p>
                <p class="card-text small text-muted mb-3">
                    Sistema: {{ $campanha->sistema->nome ?? 'N/A' }} |
                    Status: <span class="badge bg-{{ $campanha->status == 'ativa' ? 'success' : 'secondary' }}">{{ ucfirst($campanha->status) }}</span>
                </p>

                <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning btn-sm">Ver Detalhes</a>

                @auth
                    @if(auth()->id() !== $campanha->criador_id && !$campanha->jogadores->pluck('id')->contains(auth()->id()))
                        <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Deseja solicitar entrada nesta campanha?')">
                                Pedir para Entrar
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <p class="text-light">Você não participa de nenhuma campanha ainda.</p>
    @endforelse
</div>
@endsection
