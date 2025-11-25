@extends('layouts.app')

@section('title', 'Detalhes da Sessão: ' . $sessao->titulo)

@section('content')

{{--
    INÍCIO DA DEFINIÇÃO DE VARIÁVEIS DE PERMISSÃO
--}}
@php
    $user = auth()->user();
    // Verifica se o usuário logado existe E se o ID dele é o mesmo que o criador_id da campanha.
    $isMestre = $user && $user->id === $campanha->criador_id;

    // Regra para mostrar o botão de presença (usa a variável $jaMarqueiPresenca injetada pelo Controller)
    $podeMarcarPresenca = $user && !$isMestre && !$jaMarqueiPresenca &&
                          $sessao->status !== 'concluida' && $sessao->status !== 'cancelada';
@endphp
{{-- FIM DA DEFINIÇÃO DE VARIÁVEIS DE PERMISSÃO --}}

{{-- Exibe mensagens de sucesso ou erro --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show fixed-top-right mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show fixed-top-right mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container mt-5 text-light">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card bg-dark-card shadow-lg border-primary">
                {{-- Usando o nome da Sessão --}}
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">Sessão: {{ $sessao->titulo }}</h1>
                </div>
                <div class="card-body">

                    {{-- Detalhes da Sessão --}}
                    <dl class="row mb-4">
                        <dt class="col-sm-3 text-info">Título:</dt>
                        <dd class="col-sm-9 font-weight-bold text-light">{{ $sessao->titulo }}</dd>

                        <dt class="col-sm-3 text-info">Campanha:</dt>
                        <dd class="col-sm-9 text-light">{{ $campanha->nome }} (ID: {{ $campanha->id }})</dd>

                        <dt class="col-sm-3 text-info">Status:</dt>
                        <dd class="col-sm-9 text-light">{{ ucfirst(str_replace('_', ' ', $sessao->status ?? 'Não definido')) }}</dd>

                        <dt class="col-sm-3 text-info">Data e Hora:</dt>
                        <dd class="col-sm-9 text-light">
                            {{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Data não agendada' }}
                        </dd>

                        <dt class="col-sm-3 text-info">Resumo:</dt>
                        <dd class="col-sm-9 text-secondary">{{ $sessao->resumo ?? 'Nenhum resumo inicial disponível.' }}</dd>
                    </dl>

                    <hr class="border-secondary">

                    {{-- 🟢 STATUS E LISTA DE PRESENÇA (Mestre sempre confirmado + Jogadores) --}}
                    <h4 class="mt-4 mb-3 text-info">Presença Confirmada:</h4>
                    <div class="mb-4">
                        @php
                            // 1. Inicializa a lista de confirmados com o Mestre (sempre presente)
                            $confirmados = [];
                            if (isset($campanha->criador)) {
                                $confirmados[] = [
                                    'name' => $campanha->criador->name . ' (Mestre)',
                                    'isMestre' => true,
                                    'id' => $campanha->criador->id
                                ];
                            }

                            // 2. Adiciona os jogadores que marcaram presença
                            foreach($sessao->presencas as $jogador) {
                                // Adiciona apenas se não for o Mestre (garantia)
                                if ($jogador->id !== optional($campanha->criador)->id) {
                                    $confirmados[] = [
                                        'name' => $jogador->name,
                                        'isMestre' => false,
                                        'id' => $jogador->id
                                    ];
                                }
                            }
                        @endphp

                        @if(!empty($confirmados))
                            @foreach($confirmados as $confirmado)
                                <span class="badge {{ $confirmado['isMestre'] ? 'bg-primary' : 'bg-success' }} me-2 p-2 fw-bold shadow-sm">
                                    {{ $confirmado['name'] }}
                                </span>
                            @endforeach
                        @else
                            <p class="text-warning">Nenhum jogador confirmou presença ainda (o Mestre não foi carregado).</p>
                        @endif
                    </div>
                    <hr class="border-secondary">


                    {{-- Descrição Detalhada (História, Notas, etc.) --}}
                    <h4 class="mt-4 mb-3 text-info">Notas do Mestre / Detalhes da História:</h4>
                    <div class="bg-secondary p-3 rounded text-light" style="min-height: 150px;">
                        <p class="card-text">
                            @if (isset($sessao->descricao_detalhada))
                                {!! nl2br(e($sessao->descricao_detalhada)) !!}
                            @else
                                *Nenhuma descrição detalhada disponível para esta sessão.*
                            @endif
                        </p>
                    </div>

                </div>
                <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                    <small>ID da Sessão: {{ $sessao->id }}</small>

                    {{-- Botões de Ação na Sessão --}}
                    <div class="d-flex gap-2">

                        {{-- 🎯 BOTÃO DE MARCAR PRESENÇA (Para Jogadores) --}}
                        @if($podeMarcarPresenca)
                            <form action="{{ route('sessoes.marcar_presenca', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm rounded-pill fw-bold shadow-sm">
                                    ✅ Marcar Presença
                                </button>
                            </form>
                        @elseif ($jaMarqueiPresenca)
                            <span class="badge bg-success p-2 fw-bold align-self-center shadow-sm">Presença Confirmada!</span>
                        @endif

                        {{-- 🔑 BOTÃO DE EDIÇÃO SÓ APARECE PARA O MESTRE/CRIADOR --}}
                        @if($isMestre)
                            <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-sm btn-warning">
                                ✏️ Editar
                            </a>
                        @endif

                        <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-sm btn-outline-secondary">
                            ← Todas as Sessões
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
