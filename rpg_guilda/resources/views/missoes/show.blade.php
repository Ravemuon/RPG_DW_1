@extends('layouts.app')

@section('title', 'Detalhes da Missão: ' . $missao->titulo)

@section('content')

@php
    $user = auth()->user();
    $isMestre = $user && $user->id === $campanha->criador_id;
@endphp

@include('amizades.partials._alertas')

<div class="container mt-5 text-light">

    <div class="row">
        <div class="col-md-8 offset-md-2">

            <div class="card bg-dark-card shadow-lg border-primary">

                {{-- HEADER --}}
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">📜 Missão: {{ $missao->titulo }}</h1>
                </div>

                {{-- BODY --}}
                <div class="card-body">

                    {{-- DETALHES --}}
                    <dl class="row mb-4">

                        <dt class="col-sm-3 text-info">Campanha:</dt>
                        <dd class="col-sm-9 text-light">
                            {{ $campanha->nome }} (ID: {{ $campanha->id }})
                        </dd>

                        <dt class="col-sm-3 text-info">Prioridade:</dt>
                        <dd class="col-sm-9 text-light">
                            {{ ucfirst($missao->prioridade ?? 'Não definida') }}
                        </dd>

                        <dt class="col-sm-3 text-info">Status:</dt>
                        <dd class="col-sm-9 text-light">
                            {{ ucfirst(str_replace('_', ' ', $missao->status ?? 'Não definido')) }}
                        </dd>

                        <dt class="col-sm-3 text-info">Recompensa:</dt>
                        <dd class="col-sm-9 text-secondary">
                            {{ $missao->recompensa ?: 'Nenhuma recompensa definida.' }}
                        </dd>

                        <dt class="col-sm-3 text-info">Criada em:</dt>
                        <dd class="col-sm-9 text-light">
                            {{ $missao->created_at ? $missao->created_at->format('d/m/Y H:i') : 'Data Indefinida' }}
                        </dd>

                    </dl>

                    <hr class="border-secondary">

                    {{-- DESCRIÇÃO --}}
                    <h4 class="mt-4 mb-3 text-info">Descrição da Missão:</h4>

                    <div class="bg-secondary p-3 rounded text-light" style="min-height: 150px;">
                        @if($missao->descricao)
                            {!! nl2br(e($missao->descricao)) !!}
                        @else
                            <i>Nenhuma descrição disponível.</i>
                        @endif
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="card-footer text-muted d-flex justify-content-between align-items-center">

                    <small>ID da Missão: {{ $missao->id }}</small>

                    <div class="d-flex gap-2">

                        {{-- AÇÕES DO MESTRE --}}
                        @if($isMestre)

                            <a href="{{ route('missoes.edit', [
                                'campanha' => $campanha->id,
                                'missao' => $missao->id
                            ]) }}" class="btn btn-sm btn-warning">
                                ✏️ Editar
                            </a>

                            <form action="{{ route('missoes.destroy', [$campanha->id, $missao->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Tem certeza que deseja EXCLUIR a missão? Esta ação é irreversível.')">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
                                    🗑️ Excluir
                                </button>
                            </form>

                        @endif

                        <a href="{{ route('missoes.index', $campanha->id) }}"
                           class="btn btn-sm btn-outline-secondary">
                            ← Todas as Missões
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

@endsection
