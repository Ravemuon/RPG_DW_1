@extends('layouts.app')

@section('title', 'Criação - Atributos')

@section('content')
@php
    $sistema = $personagem->sistema;
    $atributosSistema = json_decode($sistema->atributos, true) ?? [];
    $usaSanidade = $sistema->usa_sanidade ?? false;

    // Assumimos que 'sorte' é um recurso, ou que a flag usa_sorte é definida no backend
    // Aqui usamos o seeder de CoC: 'recursos' => [['nome' => 'Sorte', ...]]
    $recursosSistema = json_decode($sistema->recursos, true) ?? [];
    $usaSorte = collect($recursosSistema)->contains('nome', 'Sorte');

    // **IMPORTANTE**: Assumimos que a fórmula do modificador foi adicionada ao modelo Sistema
    // Exemplo D&D 5e: (valor - 10) / 2
    $formulaModificador = $sistema->formula_modificador_atributo ?? '(valor - 10) / 2';
@endphp

<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Distribuição de Atributos</h1>
                    <p class="mb-0">Personagem: {{ $personagem->nome }} | Sistema: {{ $sistema->nome }}</p>
                </div>
                <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('personagens.store.step3', $personagem->id) }}" method="POST" id="step3-form">
            @csrf

            <div class="card-body">
                <!-- Método de Distribuição -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Método de Distribuição</h5>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="metodo_distribuicao" id="metodo_rolagem" value="rolagem" checked>
                                    <label class="form-check-label" for="metodo_rolagem">Rolagem (4d6k3)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="metodo_distribuicao" id="metodo_pontos" value="pontos">
                                    <label class="form-check-label" for="metodo_pontos">Compra de Pontos (27)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="metodo_distribuicao" id="metodo_manual" value="manual">
                                    <label class="form-check-label" for="metodo_manual">Manual</label>
                                </div>
                                <button type="button" id="sortear-atributos" class="btn btn-sm btn-outline-info ms-3">
                                    <i class="fas fa-dice me-1"></i>Sortear Tudo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Atributos do Sistema -->
                    <div class="col-lg-{{ $usaSanidade || $usaSorte ? '8' : '12' }}">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Atributos Principais</h5>
                            </div>
                            <div class="card-body">
                                <div class="row" id="atributos-principais">
                                    @foreach($atributosSistema as $chave => $nome)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card atributo-card">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ $nome }}</h6>
                                                <input type="number"
                                                        name="{{ $chave }}"
                                                        id="atributo-{{ $chave }}"
                                                        class="form-control form-control-lg text-center atributo-input"
                                                        value="{{ old($chave, $personagem->atributos[$chave] ?? 10) }}"
                                                        min="1" max="20"
                                                        data-atributo="{{ $chave }}"
                                                        data-cost="0"
                                                        required>
                                                <div class="mt-2">
                                                    <small class="text-muted">Modificador: <span id="mod-{{ $chave }}">0</span></small>
                                                </div>
                                                <div class="btn-group btn-group-sm mt-2" role="group">
                                                    <button type="button" class="btn btn-outline-secondary decrementar" data-atributo="{{ $chave }}">-</button>
                                                    <button type="button" class="btn btn-outline-secondary incrementar" data-atributo="{{ $chave }}">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atributos Especiais (Sanidade/Sorte) -->
                    @if($usaSanidade || $usaSorte)
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Atributos Especiais</h5>
                            </div>
                            <div class="card-body">
                                @if($usaSanidade)
                                <div class="mb-3">
                                    <label for="sanidade" class="form-label">Sanidade (Base)</label>
                                    <input type="number" name="sanidade" id="sanidade"
                                            class="form-control"
                                            value="{{ old('sanidade', $personagem->sanidade ?? 50) }}"
                                            min="0" max="100">
                                    <div class="form-text">Saúde mental base do personagem.</div>
                                </div>
                                @endif

                                @if($usaSorte)
                                <div class="mb-3">
                                    <label for="sorte" class="form-label">Sorte (Base)</label>
                                    <div class="input-group">
                                        <input type="number" name="sorte" id="sorte"
                                                class="form-control"
                                                value="{{ old('sorte', $personagem->sorte ?? 50) }}"
                                                min="1" max="100">
                                        <button type="button" class="btn btn-outline-secondary" id="sortear-sorte">
                                            <i class="fas fa-dice me-1"></i> Sortear (3d6x5)
                                        </button>
                                    </div>
                                    <div class="form-text">Sorte do personagem (1-100), geralmente rolado.</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Resumo dos Atributos -->
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Resumo dos Atributos</h5>
                                <div class="row" id="resumo-atributos">
                                    @foreach($atributosSistema as $chave => $nome)
                                    <div class="col-md-3 col-6 text-center mb-2">
                                        <div class="border rounded p-2">
                                            <strong>{{ $nome }}</strong>
                                            <div class="h5 mb-0" id="resumo-{{ $chave }}">10</div>
                                            <small class="text-muted" id="resumo-mod-{{ $chave }}">Mod: 0</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-center d-none" id="point-buy-summary">
                                    <strong>Pontos Restantes: <span id="pontos-restantes" class="text-success">27</span></strong>
                                    (Custo Total: <span id="total-pontos">0</span>)
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
                    <button type="submit" class="btn btn-info btn-lg">
                        <i class="fas fa-save me-2"></i>Salvar Atributos
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Variáveis injetadas pelo Laravel para o script JS
    const ATRIBUTOS_SISTEMA = @json(array_keys($atributosSistema));
    const USA_SANIDADE = @json($usaSanidade);
    const USA_SORTE = @json($usaSorte);
    const PERSONAGEM_ID = {{ $personagem->id }};
    // FÓRMULA DE MODIFICADOR (Ex: "(valor - 10) / 2")
    const FORMULA_MODIFICADOR = @json($formulaModificador);
</script>
<script src="{{ asset('js/personagem-step3.js') }}"></script>
@endsection
