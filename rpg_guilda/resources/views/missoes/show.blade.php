@extends('layouts.app')

{{-- CORRIGIDO: O título deve ser da Missão --}}
@section('title', 'Detalhes da Missão: ' . $missao->titulo)

@section('content')

{{--
    INÍCIO DA DEFINIÇÃO DE VARIÁVEIS DE PERMISSÃO
    A Missão é gerenciada pelo Mestre da Campanha
--}}
@php
    $user = auth()->user();
    // Verifica se o usuário logado existe E se o ID dele é o mesmo que o criador_id da campanha.
    $isMestre = $user && $user->id === $campanha->criador_id;
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
            {{-- Ícone e cor para Missão, ajustado de acordo com o CRUD --}}
            <div class="card bg-dark-card shadow-lg border-primary">
                <div class="card-header bg-primary text-white">
                    {{-- CORRIGIDO: Exibe o título da Missão --}}
                    <h1 class="h3 mb-0">📜 Missão: {{ $missao->titulo }}</h1>
                </div>
                <div class="card-body">

                    {{-- Detalhes da Missão --}}
                    <dl class="row mb-4">
                        <dt class="col-sm-3 text-info">Campanha:</dt>
                        <dd class="col-sm-9 text-light">{{ $campanha->nome }} (ID: {{ $campanha->id }})</dd>

                        <dt class="col-sm-3 text-info">Prioridade:</dt>
                        {{-- CORRIGIDO: Exibe a prioridade --}}
                        <dd class="col-sm-9 text-light">{{ ucfirst($missao->prioridade ?? 'Não definida') }}</dd>

                        <dt class="col-sm-3 text-info">Status:</dt>
                        {{-- CORRIGIDO: Exibe o status --}}
                        <dd class="col-sm-9 text-light">{{ ucfirst(str_replace('_', ' ', $missao->status ?? 'Não definido')) }}</dd>

                        <dt class="col-sm-3 text-info">Recompensa:</dt>
                        {{-- CORRIGIDO: Exibe a recompensa --}}
                        <dd class="col-sm-9 text-secondary">{{ $missao->recompensa ?? 'Nenhuma recompensa definida.' }}</dd>

                        <dt class="col-sm-3 text-info">Criada em:</dt>
                        {{-- CORRIGIDO: Exibe a data de criação --}}
                        <dd class="col-sm-9 text-light">
                             {{ optional($missao->created_at)->format('d/m/Y H:i') ?? 'Data Indefinida' }}
                        </dd>
                    </dl>

                    <hr class="border-secondary">

                    {{-- 📝 Descrição Detalhada da Missão --}}
                    <h4 class="mt-4 mb-3 text-info">Descrição da Missão:</h4>
                    <div class="bg-secondary p-3 rounded text-light" style="min-height: 150px;">
                        <p class="card-text">
                            @if (isset($missao->descricao))
                                {{-- CORRIGIDO: Usa a descrição da Missão --}}
                                {!! nl2br(e($missao->descricao)) !!}
                            @else
                                *Nenhuma descrição disponível para esta missão.*
                            @endif
                        </p>
                    </div>

                </div>
                <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                    {{-- CORRIGIDO: ID da Missão --}}
                    <small>ID da Missão: {{ $missao->id }}</small>

                    {{-- Botões de Ação na Missão --}}
                    <div class="d-flex gap-2">

                        {{-- 🔑 BOTÃO DE EDIÇÃO SÓ APARECE PARA O MESTRE/CRIADOR --}}
                        @if($isMestre)
                            {{-- CORRIGIDO: Rota para edição de Missão --}}
                            <a href="{{ route('missoes.edit', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-sm btn-warning">
                                ✏️ Editar
                            </a>

                            {{-- 🗑️ BOTÃO DE EXCLUIR SÓ APARECE PARA O MESTRE/CRIADOR --}}
                            <form action="{{ route('missoes.destroy', [$campanha->id, $missao->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja EXCLUIR a missão? Esta ação é irreversível.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    🗑️ Excluir
                                </button>
                            </form>
                        @endif

                        {{-- CORRIGIDO: Rota para o Index de Missões --}}
                        <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-sm btn-outline-secondary">
                            ← Todas as Missões
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
