@extends('layouts.app')

@section('title', 'Criação - Perícias')

@section('content')
@php
    $sistema = $personagem->sistema;
    $classe = $personagem->classe;
    $raca = $personagem->raca;
    $origem = $personagem->origem;
    $atributos = $personagem->atributos ?? [];

    // Calcular modificadores
    $modificadores = [];
    foreach ($atributos as $atributo => $valor) {
        $modificadores[$atributo] = floor(($valor - 10) / 2);
    }

    $bonusProficiencia = $personagem->bonus_proficiencia ?? 2;
@endphp

<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-purple text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Perícias e Proficiências</h1>
                    <p class="mb-0">Personagem: {{ $personagem->nome }} | Sistema: {{ $sistema->nome }}</p>
                </div>
                <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('personagens.store.step5', $personagem->id) }}" method="POST" id="step5-form">
            @csrf

            <div class="card-body">
                <!-- Configurações -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Configurações</h5>

                                <div class="mb-3">
                                    <label for="bonus_proficiencia" class="form-label">Bônus de Proficiência</label>
                                    <select name="bonus_proficiencia" id="bonus_proficiencia" class="form-select">
                                        <option value="2" {{ $bonusProficiencia == 2 ? 'selected' : '' }}>+2 (Nível 1-4)</option>
                                        <option value="3" {{ $bonusProficiencia == 3 ? 'selected' : '' }}>+3 (Nível 5-8)</option>
                                        <option value="4" {{ $bonusProficiencia == 4 ? 'selected' : '' }}>+4 (Nível 9-12)</option>
                                        <option value="5" {{ $bonusProficiencia == 5 ? 'selected' : '' }}>+5 (Nível 13-16)</option>
                                        <option value="6" {{ $bonusProficiencia == 6 ? 'selected' : '' }}>+6 (Nível 17-20)</option>
                                    </select>
                                    <div class="form-text">Este valor é somado às perícias em que você é proficiente.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Perícias Automáticas</h5>

                                @if(!empty($periciasClasse['fixas']) || !empty($periciasOrigem))
                                    @if(!empty($periciasClasse['fixas']))
                                    <div class="mb-2">
                                        <strong>Classe:</strong><br>
                                        @foreach($periciasClasse['fixas'] as $pericia)
                                            <span class="badge bg-success me-1 mb-1">{{ $pericia }}</span>
                                        @endforeach
                                    </div>
                                    @endif

                                    @if(!empty($periciasOrigem))
                                    <div>
                                        <strong>Origem:</strong><br>
                                        @foreach($periciasOrigem as $pericia => $bonus)
                                            <span class="badge bg-info me-1 mb-1">{{ $pericia }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">Nenhuma perícia automática.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Perícias da Classe para Escolher -->
                @if(!empty($periciasClasse['lista']) && $periciasClasse['escolha'] > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">🎯 Escolha de Perícias da Classe</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    Sua classe permite escolher <strong>{{ $periciasClasse['escolha'] }} perícias</strong> da lista abaixo:
                                </p>

                                <div class="row" id="skill-selection-area">
                                    @foreach($periciasClasse['lista'] as $pericia)
                                    @php
                                        $atributoRelacionado = $periciasSistema[$pericia] ?? 'inteligencia';
                                        $modificador = $modificadores[$atributoRelacionado] ?? 0;
                                        $modificadorDisplay = $modificador >= 0 ? "+{$modificador}" : $modificador;
                                        $total = $modificador + $bonusProficiencia;
                                        $totalDisplay = $total >= 0 ? "+{$total}" : $total;
                                    @endphp
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="form-check skill-option">
                                            <input class="form-check-input skill-checkbox"
                                                   type="checkbox"
                                                   name="pericias_escolhidas[]"
                                                   id="skill_{{ Str::slug($pericia) }}"
                                                   value="{{ $pericia }}"
                                                   data-modifier="{{ $modificador }}">
                                            <label class="form-check-label d-flex justify-content-between w-100"
                                                   for="skill_{{ Str::slug($pericia) }}">
                                                <span>{{ $pericia }}</span>
                                                <small class="text-muted">{{ $modificadorDisplay }}</small>
                                            </label>
                                            <div class="small text-muted">
                                                {{ $sistema->atributos[$atributoRelacionado] ?? $atributoRelacionado }} | Total: {{ $totalDisplay }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div id="skill-selection-feedback" class="alert alert-warning mt-3 d-none">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span id="skill-feedback-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Lista Completa de Perícias -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">📊 Todas as Perícias do Sistema</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    Lista completa de perícias disponíveis no sistema. As perícias em que você é proficiente são destacadas.
                                </p>

                                <div class="row" id="all-skills-list">
                                    @foreach($periciasSistema as $pericia => $atributo)
                                    @php
                                        $modificador = $modificadores[$atributo] ?? 0;
                                        $modificadorDisplay = $modificador >= 0 ? "+{$modificador}" : $modificador;

                                        // Verificar se é proficiente
                                        $proficiente = in_array($pericia, $periciasClasse['fixas'] ?? []) ||
                                                      array_key_exists($pericia, $periciasOrigem ?? []) ||
                                                      (isset($periciasEscolhidas) && in_array($pericia, $periciasEscolhidas));

                                        $total = $modificador + ($proficiente ? $bonusProficiencia : 0);
                                        $totalDisplay = $total >= 0 ? "+{$total}" : $total;
                                    @endphp
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card skill-card {{ $proficiente ? 'border-success bg-success-subtle' : '' }}">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="{{ $proficiente ? 'fw-bold text-success' : '' }}">
                                                            {{ $pericia }}
                                                        </span>
                                                        <div class="small text-muted">
                                                            {{ $sistema->atributos[$atributo] ?? $atributo }}
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="fw-bold {{ $proficiente ? 'text-success' : 'text-dark' }}">
                                                            {{ $totalDisplay }}
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ $modificadorDisplay }} + {{ $proficiente ? $bonusProficiencia : 0 }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($proficiente)
                                                <div class="text-center mt-1">
                                                    <small class="badge bg-success">Proficiente</small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
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
                    <button type="submit" class="btn btn-purple btn-lg">
                        <i class="fas fa-save me-2"></i>Salvar Perícias
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/personagem-step5.js') }}"></script>
<script>
    const PERICIAS_SISTEMA = @json($periciasSistema);
    const ATRIBUTOS = @json($atributos);
    const MODIFICADORES = @json($modificadores);
    const PERICIAS_CLASSE = @json($periciasClasse);
    const PERICIAS_ORIGEM = @json($periciasOrigem);
    const MAX_ESCOLHAS = {{ $periciasClasse['escolha'] ?? 0 }};
    const BONUS_PROFICIENCIA_INICIAL = {{ $bonusProficiencia }};
</script>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}
.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}
.btn-purple:hover {
    background-color: #5a2d91;
    border-color: #5a2d91;
    color: white;
}
</style>
@endsection
