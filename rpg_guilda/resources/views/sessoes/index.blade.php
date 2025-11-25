@extends('layouts.app')

@section('title', "Sessões - {$campanha->nome}")

@section('content')

{{--
    INÍCIO DA DEFINIÇÃO DE VARIÁVEIS DE PERMISSÃO
    Define se o usuário logado é o Mestre (criador da campanha).
--}}
@php
    $user = auth()->user();
    $isMestre = $user && $user->id === $campanha->criador_id;
@endphp
{{-- FIM DA DEFINIÇÃO DE VARIÁVEIS DE PERMISSÃO --}}

<div class="container py-5 text-light">

    {{-- HEADER DA PÁGINA --}}
    <header class="text-center mb-5 border-bottom border-secondary pb-3">
        <h1 class="display-4 fw-bold text-success">📖 Gerenciamento de Sessões</h1>
        <p class="lead text-muted fst-italic">{{ $campanha->nome }}</p>
    </header>

    {{-- Mensagens de feedback --}}
    @if(session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} bg-{{ session('success') ? 'success' : 'danger' }} text-light border-0 shadow-lg mb-4">
            {{ session('success') ?? session('error') }}
        </div>
    @endif

    {{-- Botões de Ação Global --}}
    <div class="d-flex flex-wrap gap-3 mb-5 justify-content-center justify-content-md-start">
        {{-- 🔑 O botão CRIAR SESSÃO só deve aparecer para o MESTRE --}}
        @if($isMestre)
            <a href="{{ route('sessoes.create', $campanha->id) }}" class="btn btn-success btn-lg rounded-pill shadow-lg px-4 fw-bold d-flex align-items-center">
                <span class="me-2 fs-5">➕</span> Criar Nova Sessão
            </a>
        @endif
        <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4 fw-bold d-flex align-items-center">
            <span class="me-2 fs-5">⬅️</span> Voltar ao Painel
        </a>
    </div>

    @if($sessoes->count())
        <div class="row g-4">
            @foreach($sessoes->sortByDesc('data_hora') as $sessao)
                @php
                    // Helper para cor do status
                    $statusMapping = [
                        'agendada' => ['color' => 'primary', 'icon' => '⏰'],
                        'em_andamento' => ['color' => 'warning', 'icon' => '▶️'],
                        'concluida' => ['color' => 'success', 'icon' => '✅'],
                        'cancelada' => ['color' => 'danger', 'icon' => '❌'],
                    ];
                    $statusData = $statusMapping[$sessao->status] ?? ['color' => 'secondary', 'icon' => '❓'];
                    $statusColor = $statusData['color'];
                    $statusIcon = $statusData['icon'];
                @endphp

                {{-- CARD MELHORADO --}}
                <div class="col-12 col-lg-6">
                    <div class="card bg-dark-card h-100 shadow-xl border-{{ $statusColor }} border-3 transition-hover">
                        <div class="card-body d-flex flex-column">

                            {{-- TÍTULO E STATUS --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="card-title fw-bolder text-light text-truncate me-3">
                                    {{ $statusIcon }} {{ $sessao->titulo }}
                                </h4>
                                <span class="badge bg-{{ $statusColor }} text-uppercase fw-bold p-2">
                                    {{ ucfirst(str_replace('_', ' ', $sessao->status)) }}
                                </span>
                            </div>

                            {{-- INFORMAÇÕES SECUNDÁRIAS --}}
                            <div class="text-muted small mb-3 border-bottom border-dark pb-2">
                                <p class="mb-0">
                                    <i class="bi bi-calendar-event me-1"></i> Data:
                                    <span class="fw-bold text-light">
                                        {{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Não Agendada' }}
                                    </span>
                                </p>
                            </div>

                            {{-- RESUMO --}}
                            <p class="card-text text-secondary mb-4 flex-grow-1">
                                {{ Str::limit($sessao->resumo ?? 'Sem resumo disponível. Clique em detalhes para adicionar.', 120) }}
                            </p>

                            {{-- BOTÕES DE AÇÃO (Rodapé do Card) --}}
                            <div class="mt-auto d-flex flex-wrap gap-2 pt-3 border-top border-dark">

                                {{-- Botão Detalhes SEMPRE visível --}}
                                <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-sm btn-info rounded-pill fw-bold">
                                    🔍 Detalhes
                                </a>

                                {{-- 🔑 Botões EDITAR e DELETAR só visíveis para o MESTRE --}}
                                @if($isMestre)
                                    <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-sm btn-warning rounded-pill">
                                        ✏️ Editar
                                    </a>
                                    <form action="{{ route('sessoes.destroy', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST" class="d-inline ms-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Tem certeza que deseja deletar a sessão: {{ $sessao->titulo }}?')">
                                            🗑️ Deletar
                                        </button>
                                    </form>
                                @endif

                                {{-- Botão PDF SEMPRE visível (se for conteúdo do jogador) --}}
                                <a href="{{ route('sessoes.exportar-pdf', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-sm btn-light rounded-pill">
                                    📄 PDF
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- MENSAGEM DE NENHUMA SESSÃO --}}
        <div class="card bg-dark-card border-info text-center p-5 shadow-lg">
            <div class="card-body">
                <p class="fs-4 fw-bold text-info">🌌 Nada encontrado por aqui...</p>
                <p class="text-secondary mb-4">Nenhuma sessão foi registrada para esta campanha. Comece a aventura agora!</p>
                {{-- 🔑 Botão de criação só visível para o MESTRE --}}
                @if($isMestre)
                    <a href="{{ route('sessoes.create', $campanha->id) }}" class="btn btn-info btn-lg rounded-pill fw-bold">
                        Criar Primeira Sessão
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
/* Estilos Customizados para o Tema Escuro */

.text-success {
    color: #198754 !important; /* Padrão Bootstrap Green */
}

.bg-dark-card {
    background-color: #212529 !important; /* Cor mais escura para o card (darker background) */
}

.border-secondary {
    border-color: #495057 !important;
}

.transition-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.transition-hover:hover {
    transform: translateY(-5px); /* Efeito "flutuante" */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5) !important;
}

/* Garante o texto claro nos cards */
.card-title, .card-subtitle {
    color: #f8f9fa !important;
}
</style>

@endsection
