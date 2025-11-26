@extends('layouts.app')

@section('title', 'Criação - Raça, Classe e Origem')

@section('content')
@php
    $sistema = $personagem->sistema;
@endphp

<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Escolha de Raça, Classe e Origem</h1>
                    <p class="mb-0">Personagem: {{ $personagem->nome }} | Sistema: {{ $sistema->nome }}</p>
                </div>
                <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-dark">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('personagens.store.step2', $personagem->id) }}" method="POST" id="step2-form">
            @csrf

            <div class="card-body">
                <div class="row g-4">
                    <!-- Coluna Raça -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h4 class="mb-0">🧬 Raça</h4>
                            </div>
                            <div class="card-body">
                                <select name="raca_id" id="raca_id" class="form-select form-select-lg mb-3" required>
                                    <option value="">Selecione uma raça</option>
                                    @foreach($racas as $raca)
                                        @php
                                            $modificadores = is_string($raca->modificadores_atributos) ?
                                                json_decode($raca->modificadores_atributos, true) :
                                                ($raca->modificadores_atributos ?? []);
                                        @endphp
                                        <option value="{{ $raca->id }}"
                                                data-descricao="{{ $raca->descricao ?? '' }}"
                                                data-modificadores='@json($modificadores)'
                                                data-bonus-livre="{{ $raca->bonus_livre ?? 0 }}"
                                                data-pagina="{{ $raca->pagina ?? '' }}"
                                                {{ $personagem->raca_id == $raca->id ? 'selected' : '' }}>
                                            {{ $raca->nome }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Detalhes da Raça -->
                                <div id="raca-detalhes" class="mt-3">
                                    <div class="alert alert-info">
                                        <p class="mb-0">Selecione uma raça para ver seus detalhes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Classe -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">⚔️ Classe</h4>
                            </div>
                            <div class="card-body">
                                <select name="classe_id" id="classe_id" class="form-select form-select-lg mb-3" required>
                                    <option value="">Selecione uma classe</option>
                                    @foreach($classes as $classe)
                                        @php
                                            $periciasIniciais = is_string($classe->pericias_iniciais) ?
                                                json_decode($classe->pericias_iniciais, true) :
                                                ($classe->pericias_iniciais ?? []);
                                            $equipamentoInicial = is_string($classe->equipamento_inicial) ?
                                                json_decode($classe->equipamento_inicial, true) :
                                                ($classe->equipamento_inicial ?? []);
                                            $atributosBonus = is_string($classe->atributos_bonus) ?
                                                json_decode($classe->atributos_bonus, true) :
                                                ($classe->atributos_bonus ?? []);
                                            $poderes = is_string($classe->poderes) ?
                                                json_decode($classe->poderes, true) :
                                                ($classe->poderes ?? []);
                                        @endphp
                                        <option value="{{ $classe->id }}"
                                                data-descricao="{{ $classe->descricao ?? '' }}"
                                                data-dado-vida="{{ $classe->dado_vida ?? 'd6' }}"
                                                data-usa-magia="{{ $classe->usa_magia ? 'Sim' : 'Não' }}"
                                                data-pericias='@json($periciasIniciais)'
                                                data-equipamento='@json($equipamentoInicial)'
                                                data-atributos-bonus='@json($atributosBonus)'
                                                data-poderes='@json($poderes)'
                                                data-pagina="{{ $classe->pagina ?? '' }}"
                                                {{ $personagem->classe_id == $classe->id ? 'selected' : '' }}>
                                            {{ $classe->nome }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Detalhes da Classe -->
                                <div id="classe-detalhes" class="mt-3">
                                    <div class="alert alert-info">
                                        <p class="mb-0">Selecione uma classe para ver seus detalhes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Origem -->
                    <div class="col-lg-4">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0">📖 Origem</h4>
                            </div>
                            <div class="card-body">
                                <select name="origem_id" id="origem_id" class="form-select form-select-lg mb-3">
                                    <option value="">Selecione uma origem (opcional)</option>
                                    @foreach($origens as $origem)
                                        @php
                                            $bonusPericias = is_string($origem->bonus_pericias) ?
                                                json_decode($origem->bonus_pericias, true) :
                                                ($origem->bonus_pericias ?? []);
                                            $recursosAdicionais = is_string($origem->recursos_adicionais) ?
                                                json_decode($origem->recursos_adicionais, true) :
                                                ($origem->recursos_adicionais ?? []);
                                        @endphp
                                        <option value="{{ $origem->id }}"
                                                data-descricao="{{ $origem->descricao ?? '' }}"
                                                data-bonus-pericias='@json($bonusPericias)'
                                                data-recursos-adicionais='@json($recursosAdicionais)'
                                                data-pagina="{{ $origem->pagina ?? '' }}"
                                                {{ $personagem->origem_id == $origem->id ? 'selected' : '' }}>
                                            {{ $origem->nome }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Detalhes da Origem -->
                                <div id="origem-detalhes" class="mt-3">
                                    <div class="alert alert-info">
                                        <p class="mb-0">Selecione uma origem para ver seus detalhes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumo das Escolhas -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="mb-0">📊 Resumo das Escolhas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row" id="resumo-escolhas">
                                    <div class="col-md-4 text-center">
                                        <h6>Raça</h6>
                                        <div id="resumo-raca" class="text-muted">Não selecionada</div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6>Classe</h6>
                                        <div id="resumo-classe" class="text-muted">Não selecionada</div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6>Origem</h6>
                                        <div id="resumo-origem" class="text-muted">Não selecionada</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar ao Overview
                    </a>
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>Salvar Escolhas
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/personagem-step2.js') }}"></script>
@endsection
