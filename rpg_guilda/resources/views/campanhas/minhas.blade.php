@extends('layouts.app')

@section('title', 'Minhas Campanhas')

@section('content')

<div class="container py-5">
<h1 class="text-warning text-center mb-5" style="text-shadow: 0 0 8px var(--btn-bg);">
⚔️ Minhas Campanhas
</h1>

{{-- Botão para Criar Nova Campanha --}}
<div class="d-flex justify-content-end mb-5">
    <a href="{{ route('campanhas.create') }}" class="btn btn-warning shadow-lg fw-bold d-flex align-items-center">
        <i class="fas fa-plus-circle me-2"></i> Criar Nova Campanha
    </a>
</div>

{{-- ========================= --}}
{{-- Campanhas Criadas pelo Usuário (Mestre) --}}
{{-- ========================= --}}
<h3 class="text-warning mt-4">📜 Campanhas Criadas por Mim</h3>
@if($campanhasMestre->count())
    <div class="row g-4 mb-5">
        @foreach($campanhasMestre as $campanha)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-lg" style="background-color: var(--card-bg); border-color: var(--card-border);">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3 text-warning">{{ $campanha->nome }}</h5>
                        <p class="mb-2"><strong>Sistema:</strong> {{ $campanha->sistemaRPG ?? 'Desconhecido' }}</p>
                        <p class="mb-2"><strong>Status:</strong>
                            @if($campanha->status === 'ativa')
                                <span class="badge bg-success">Ativa</span>
                            @elseif($campanha->status === 'pausada')
                                <span class="badge bg-warning text-dark">Pausada</span>
                            @else
                                <span class="badge bg-secondary">Encerrada</span>
                            @endif
                        </p>

                        {{-- Botões do MESTRE: Detalhes, Editar e Sessões --}}
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning btn-sm">
                                🔍 Detalhes
                            </a>
                            {{-- O filtro de dono é implícito, pois estamos no loop $campanhasMestre --}}
                            <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-warning btn-sm">
                                ✏️ Editar
                            </a>
                            <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-outline-light btn-sm">
                                🎲 Sessões
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-secondary fst-italic">Você ainda não criou nenhuma campanha. Use o botão **Criar Nova Campanha** para começar!</p>
@endif

<hr class="border-warning my-5">

{{-- ========================= --}}
{{-- Campanhas que o usuário é JOGADOR --}}
{{-- ========================= --}}
<h3 class="text-warning mt-5">🎭 Campanhas em que Participo</h3>
@if($campanhasJogador->count())
    <div class="row g-4">
        @foreach($campanhasJogador as $campanha)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-lg" style="background-color: var(--card-bg); border-color: var(--card-border);">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3 text-warning">{{ $campanha->nome }}</h5>
                        <p class="mb-2"><strong>Mestre:</strong> {{ $campanha->criador->nome ?? 'Desconhecido' }}</p>
                        <p class="mb-2"><strong>Sistema:</strong> {{ $campanha->sistemaRPG ?? 'Desconhecido' }}</p>
                        <p class="mb-2"><strong>Status:</strong>
                            @if($campanha->status === 'ativa')
                                <span class="badge bg-success">Ativa</span>
                            @elseif($campanha->status === 'pausada')
                                <span class="badge bg-warning text-dark">Pausada</span>
                            @else
                                <span class="badge bg-secondary">Encerrada</span>
                            @endif
                        </p>

                        {{-- Botões do JOGADOR: Detalhes e Chat --}}
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning btn-sm flex-fill">
                                🔍 Ver Detalhes
                            </a>
                            <a href="{{ route('chat.index', $campanha->id) }}" class="btn btn-outline-light btn-sm flex-fill">
                                💬 Chat
                            </a>
                            {{-- Não há botão de Editar aqui, pois o usuário é apenas Jogador --}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-secondary fst-italic">Você ainda não está participando de nenhuma campanha.</p>
@endif


</div>
@endsection
