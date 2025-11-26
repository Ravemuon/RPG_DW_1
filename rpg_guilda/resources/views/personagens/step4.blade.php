@extends('layouts.app')

@section('title', 'Criação - Vida e Equipamento')

@section('content')
@php
    $sistema = $personagem->sistema;
    $classe = $personagem->classe;
    $atributos = $personagem->atributos ?? [];
    $constituicao = $atributos['constituicao'] ?? 10;
    $modificadorConstituicao = floor(($constituicao - 10) / 2);
@endphp

<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Vida e Equipamento</h1>
                    <p class="mb-0">Personagem: {{ $personagem->nome }} | Classe: {{ $classe->nome ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('personagens.store.step4', $personagem->id) }}" method="POST" id="step4-form">
            @csrf

            <div class="card-body">
                <div class="row">
                    <!-- Coluna Vida -->
                    <div class="col-lg-6">
                        <div class="card mb-4 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">❤️ Pontos de Vida</h5>
                            </div>
                            <div class="card-body">
                                @if($classe)
                                    <div class="text-center mb-4">
                                        <p class="mb-1"><strong>Classe:</strong> {{ $classe->nome }}</p>
                                        <p class="mb-1"><strong>Dado de Vida:</strong> {{ strtoupper($classe->dado_vida ?? 'd6') }}</p>
                                        <p class="mb-1"><strong>Mod. Constituição:</strong> {{ $modificadorConstituicao >= 0 ? '+' : '' }}{{ $modificadorConstituicao }}</p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Método de Cálculo</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metodo_vida" id="vida_media" value="media" checked>
                                            <label class="form-check-label" for="vida_media">
                                                Valor Médio ({{ $vidaBase }})
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metodo_vida" id="vida_rolagem" value="rolagem">
                                            <label class="form-check-label" for="vida_rolagem">
                                                Rolagem do Dado
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Seção de Rolagem -->
                                    <div id="secao-rolagem" class="mb-3 d-none">
                                        <div class="d-flex align-items-center justify-content-center mb-3">
                                            <button type="button" id="rolar-vida" class="btn btn-danger btn-lg me-3">
                                                <i class="fas fa-dice me-2"></i>Rolar Dado
                                            </button>
                                            <div class="text-center">
                                                <div class="small text-muted">Resultado:</div>
                                                <div id="resultado-rolagem" class="h4 fw-bold text-danger">-</div>
                                            </div>
                                        </div>
                                        <div id="historico-rolagem" class="small text-muted text-center"></div>
                                    </div>

                                    <!-- Vida Final -->
                                    <div class="mt-4 p-3 bg-light rounded text-center">
                                        <label class="form-label fw-bold">Vida Inicial Final</label>
                                        <input type="number" name="vida" id="vida-final"
                                               class="form-control form-control-lg text-center fw-bold"
                                               value="{{ $vidaBase }}"
                                               required readonly
                                               style="font-size: 1.5rem; background: white;">
                                        <div class="form-text">
                                            Base: <span id="vida-base">{{ $vidaBase - $modificadorConstituicao }}</span> +
                                            Mod. Constituição: <span id="mod-constituicao-display">{{ $modificadorConstituicao >= 0 ? '+' : '' }}{{ $modificadorConstituicao }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Selecione uma classe primeiro para calcular os pontos de vida.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Equipamento -->
                    <div class="col-lg-6">
                        <div class="card mb-4 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">🎒 Equipamento Inicial</h5>
                            </div>
                            <div class="card-body">
                                @if($classe && !empty($equipamentoInicial['fixas']) || !empty($equipamentoInicial['opcoes']))
                                    <!-- Equipamento Fixo -->
                                    @if(!empty($equipamentoInicial['fixas']))
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-success">✅ Equipamento Fixo</h6>
                                        <div class="bg-success bg-opacity-10 p-3 rounded">
                                            <ul class="mb-0">
                                                @foreach($equipamentoInicial['fixas'] as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Opções de Equipamento -->
                                    @if(!empty($equipamentoInicial['opcoes']))
                                    <div id="equipment-options">
                                        <h6 class="fw-bold text-warning">📦 Escolhas de Equipamento</h6>
                                        <p class="small text-muted mb-3">Escolha uma opção de cada grupo:</p>

                                        @foreach($equipamentoInicial['opcoes'] as $index => $grupo)
                                        <div class="mb-3 p-3 border rounded">
                                            <label class="fw-bold mb-2">Grupo {{ $index + 1 }}</label>

                                            @if(is_array($grupo) && isset($grupo['instrucao']))
                                                <p class="small text-muted mb-2">{{ $grupo['instrucao'] }}</p>
                                                <div class="equipment-group" data-group="{{ $index }}">
                                                    @foreach($grupo['opcoes'] as $itemIndex => $item)
                                                    <div class="form-check">
                                                        <input class="form-check-input equipment-choice"
                                                               type="radio"
                                                               name="equipamento_escolhido[{{ $index }}]"
                                                               id="equip_{{ $index }}_{{ $itemIndex }}"
                                                               value="{{ is_array($item) ? ($item['nome'] ?? $item) : $item }}"
                                                               {{ $itemIndex === 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="equip_{{ $index }}_{{ $itemIndex }}">
                                                            {{ is_array($item) ? ($item['nome'] ?? implode(', ', $item)) : $item }}
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="equipment-group" data-group="{{ $index }}">
                                                    @foreach($grupo as $itemIndex => $item)
                                                    <div class="form-check">
                                                        <input class="form-check-input equipment-choice"
                                                               type="radio"
                                                               name="equipamento_escolhido[{{ $index }}]"
                                                               id="equip_{{ $index }}_{{ $itemIndex }}"
                                                               value="{{ is_array($item) ? ($item['nome'] ?? $item) : $item }}"
                                                               {{ $itemIndex === 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="equip_{{ $index }}_{{ $itemIndex }}">
                                                            {{ is_array($item) ? ($item['nome'] ?? implode(', ', $item)) : $item }}
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <p>Nenhum equipamento inicial definido para esta classe.</p>
                                    </div>
                                @endif

                                <!-- Resumo do Equipamento -->
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="fw-bold">Resumo do Equipamento</h6>
                                    <div id="equipment-summary" class="small">
                                        <p class="text-muted mb-1">Seu equipamento será atualizado aqui...</p>
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
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-save me-2"></i>Salvar Vida e Equipamento
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/personagem-step4.js') }}"></script>
<script>
    const PERSONAGEM_ID = {{ $personagem->id }};
    const VIDA_BASE = {{ $vidaBase }};
    const MOD_CONSTITUICAO = {{ $modificadorConstituicao }};
    const DADO_VIDA = '{{ $classe->dado_vida ?? "d6" }}';
    const EQUIPAMENTO_FIXO = @json($equipamentoInicial['fixas'] ?? []);
</script>
@endsection
