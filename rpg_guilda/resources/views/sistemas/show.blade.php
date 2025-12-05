@extends('layouts.app')

@section('title', "Detalhes do Sistema: {$sistema->nome}")

@section('content')
<div class="container py-4">

    {{-- PRÉ-PROCESSAMENTO --}}
    @php
        // Garantir que os dados sejam arrays
        $atributosDecodificados = is_array($sistema->atributos) ? $sistema->atributos : json_decode($sistema->atributos ?? '[]', true) ?? [];
        $recursosDecodificados = is_array($sistema->recursos) ? $sistema->recursos : json_decode($sistema->recursos ?? '[]', true) ?? [];

        // Mapeamento de complexidade
        $complexidade = strtolower($sistema->complexidade ?? 'Não Avaliado');
        $complexidadeBadge = match(true) {
            str_contains($complexidade, 'baixa') => ['Baixa', 'bg-success'],
            str_contains($complexidade, 'média'), str_contains($complexidade, 'media') => ['Média', 'bg-warning text-dark'],
            str_contains($complexidade, 'alta') => ['Alta', 'bg-danger'],
            default => ['Não Avaliado', 'bg-secondary']
        };

        // Determinar se usa Sanidade
        $usaSanidade = $sistema->usa_sanidade ?? false;
        $usaSanidade = $usaSanidade ? 'Sim' : 'Não';
    @endphp

    {{-- HEADER E AÇÕES --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5 fw-bold text-primary">
            {{ $sistema->nome }} 
            <span class="badge {{ $complexidadeBadge[1] ?? 'bg-secondary' }} align-middle fs-6">
                {{ $complexidadeBadge[0] ?? 'Não Avaliado' }}
            </span>
        </h1>
        <div class="btn-group" role="group" aria-label="Ações do Sistema">
            <a href="{{ route('sistemas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <a href="{{ route('sistemas.edit', $sistema) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
        </div>
    </div>

    <hr class="mb-5">

    {{-- DETALHES ESSENCIAIS --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light-subtle">
            <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i> Detalhes Essenciais</h4>
        </div>
        <div class="card-body">
            @if($sistema->descricao)
                <div class="mb-4">
                    <p class="text-muted mb-1">Descrição do Sistema:</p>
                    <p class="card-text border-start border-3 border-primary ps-3 fst-italic">{{ $sistema->descricao }}</p>
                </div>
            @endif

            <div class="row text-center">
                <div class="col-md-4 border-end">
                    <p class="text-muted mb-1">Foco Temático</p>
                    <h5 class="fw-bold text-success">{{ $sistema->foco ?? 'Geral' }}</h5>
                </div>
                <div class="col-md-4 border-end">
                    <p class="text-muted mb-1">Mecânica Principal</p>
                    <h5 class="fw-bold text-info">{{ $sistema->mecanica_principal ?? 'Não Especificada' }}</h5>
                </div>
                <div class="col-md-4">
                    <p class="text-muted mb-1">Usa Sanidade?</p>
                    <h5 class="fw-bold {{ str_contains($usaSanidade, 'Sim') ? 'text-danger' : 'text-success' }}">
                        {{ $usaSanidade }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- ATRIBUTOS E RECURSOS --}}
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-dice-d20 me-2"></i> Atributos Base</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($atributosDecodificados as $sigla => $nome)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong class="text-uppercase">{{ $sigla }}</strong>
                            <span class="badge bg-dark rounded-pill">{{ $nome }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted fst-italic">Nenhum atributo definido.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-wrench me-2"></i> Regras Adicionais</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Fórmula de PV:</strong>
                        <span class="float-end text-monospace">{{ $sistema->formula_pontos_vida ?? 'Padrão ou Indefinida' }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Máximo de Atributos:</strong>
                        <span class="float-end">{{ $sistema->max_atributos ?? 'Sem Limite' }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Recursos Especiais:</strong>
                        <span class="float-end text-muted small">{{ count($recursosDecodificados) }} item(s) listados</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
