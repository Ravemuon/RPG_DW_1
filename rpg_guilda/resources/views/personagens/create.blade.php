@extends('layouts.app')

@section('title', 'Criar Novo Personagem')

@section('content')
{{-- Verifica se a campanha tem um sistema válido --}}
@if(empty($campanha->sistema))
    <div class="container my-5">
        <div class="alert alert-danger text-center shadow-lg">
            <h4 class="alert-heading">⚠️ Erro: Sistema de Regras Ausente!</h4>
            <p>A campanha **{{ $campanha->nome ?? 'Atual' }}** não tem um sistema de regras definido. É necessário definir um sistema antes de criar personagens.</p>
            @if(isset($campanha) && $campanha->criador_id === auth()->id())
                <p class="mb-0"><a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-danger mt-3">Editar Campanha para Definir Sistema</a></p>
            @endif
        </div>
    </div>
@else
{{-- Variáveis do Sistema --}}
@php
    $sistema = $campanha->sistema;
    $atributosJson = json_encode($sistema->atributos ?? []);
    $formulaModificador = $sistema->formula_modificador ?? 'dnd';
    $sistemaId = $sistema->id;
    $sistemaNome = $sistema->nome;

    // Perícias do sistema (acessando a relação hasMany)
    $periciasSistema = $sistema->pericias ?? collect();

    // Mapeamento de atributos para perícias
    $periciasMapeamento = [];
    foreach ($periciasSistema as $pericia) {
        $periciasMapeamento[] = [
            'id' => $pericia->id,
            'nome' => $pericia->nome,
            'modificador_base' => $pericia->modificador,
            'atributo_relacionado' => $pericia->atributo_relacionado,
            'atributo_nome' => $pericia->atributo_nome ?? $sistema->atributos[$pericia->atributo_relacionado] ?? ucfirst($pericia->atributo_relacionado)
        ];
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>

<div class="container my-5">
    <div class="card shadow-lg border-top border-5 border-primary rounded-3">
        <div class="card-body p-4 p-md-5">
            <h1 class="h2 fw-bolder text-dark mb-4 border-bottom pb-3">
                Crie Seu Herói em <span class="text-primary">{{ $sistemaNome }}</span>
            </h1>
            <p class="text-muted mb-4">
                Personalize a ficha do seu personagem para a campanha **{{ $campanha->nome ?? 'Nova Campanha' }}**.
            </p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('personagens.store') }}" method="POST" id="character-form">
                @csrf
                <input type="hidden" name="campanha_id" value="{{ $campanha->id ?? 1 }}">
                <input type="hidden" name="sistema_id" id="sistema_id_input" value="{{ $sistemaId }}">

                {{-- Campos para dados calculados --}}
                <input type="hidden" name="atributos" id="finalAttributesJsonInput">
                <input type="hidden" name="pericias_escolhidas" id="periciasEscolhidasInput">
                <input type="hidden" name="equipamento_escolhido" id="equipamentoEscolhidoInput">
                <input type="hidden" name="vida_rolada" id="vidaRoladaInput">

                {{-- Informação do Sistema --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="alert alert-info py-2 mb-0">
                            <i class="fas fa-book"></i> <strong>Sistema de Regras:</strong> {{ $sistemaNome }}
                            <span class="badge bg-dark ms-2" id="sistema-modificador-info"></span>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- COLUNA ESQUERDA --}}
                    <div class="col-xl-6 d-flex flex-column gap-4">
                        {{-- DADOS BÁSICOS --}}
                        <div class="card shadow-sm border-secondary border-opacity-25">
                            <div class="card-header bg-transparent border-0">
                                <legend class="h5 text-primary fw-bold mb-0">1. Informações Básicas</legend>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome do Personagem</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required class="form-control" placeholder="Ex: Aragorn, Geralt...">
                                </div>

                                <div class="mb-3">
                                    <label for="raca_id" class="form-label fw-bold">Raça</label>
                                    <select name="raca_id" id="raca_id" required class="form-select">
                                        <option value="">Selecione uma raça...</option>
                                        @foreach ($racas->where('sistema_id', $sistemaId) as $raca)
                                            <option
                                                value="{{ $raca->id }}"
                                                data-modificadores='@json($raca->modificadores ?? [])'
                                                data-descricao="{{ $raca->descricao ?? '' }}"
                                                {{ old('raca_id') == $raca->id ? 'selected' : '' }}>
                                                {{ $raca->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p id="raca-descricao-display" class="small text-muted mt-2"></p>
                                    <div id="raca-bonus-display" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="classe_id" class="form-label fw-bold">Classe</label>
                                    <select name="classe_id" id="classe_id" required class="form-select">
                                        <option value="">Selecione uma classe...</option>
                                        @foreach ($classes->where('sistema_id', $sistemaId) as $classe)
                                            @php
                                                $periciasIniciais = is_string($classe->pericias_iniciais) ?
                                                    json_decode($classe->pericias_iniciais, true) :
                                                    ($classe->pericias_iniciais ?? []);

                                                $equipamentoInicial = is_string($classe->equipamento_inicial) ?
                                                    json_decode($classe->equipamento_inicial, true) :
                                                    ($classe->equipamento_inicial ?? []);
                                            @endphp
                                            <option
                                                value="{{ $classe->id }}"
                                                data-atributos-bonus='@json($classe->atributos_bonus ?? [])'
                                                data-pericias-iniciais='@json($periciasIniciais)'
                                                data-equipamento-inicial='@json($equipamentoInicial)'
                                                data-usa-magia="{{ $classe->usa_magia ? 'true' : 'false' }}"
                                                data-dado-vida="{{ $classe->dado_vida ?? 'd6' }}"
                                                data-descricao="{{ $classe->descricao ?? '' }}"
                                                {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                                {{ $classe->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p id="classe-descricao-display" class="small text-muted mt-2"></p>
                                    <div id="classe-detalhes-display" class="mt-2"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="origem_id" class="form-label fw-bold">Origem</label>
                                    <select name="origem_id" id="origem_id" class="form-select">
                                        <option value="">Selecione uma origem (opcional)...</option>
                                        @foreach ($origens->where('sistema_id', $sistemaId) as $origem)
                                            @php
                                                $bonusPericias = is_string($origem->bonus_pericias_data) ?
                                                    json_decode($origem->bonus_pericias_data, true) :
                                                    ($origem->bonus_pericias_data ?? []);

                                                $recursosAdicionais = is_string($origem->recursos_adicionais_data) ?
                                                    json_decode($origem->recursos_adicionais_data, true) :
                                                    ($origem->recursos_adicionais_data ?? []);
                                            @endphp
                                            <option
                                                value="{{ $origem->id }}"
                                                data-bonus-pericias='@json($bonusPericias)'
                                                data-recursos-adicionais='@json($recursosAdicionais)'
                                                data-descricao="{{ $origem->descricao ?? '' }}"
                                                {{ old('origem_id') == $origem->id ? 'selected' : '' }}>
                                                {{ $origem->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p id="origem-descricao-display" class="small text-muted mt-2"></p>
                                    <div id="origem-recursos-display" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        {{-- ATRIBUTOS --}}
                        <div class="card shadow border-success border-opacity-25">
                            <div class="card-header bg-success text-white">
                                <legend class="h5 fw-bold mb-0">2. Distribuição de Atributos</legend>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Método de Distribuição:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="distribution_method" id="method-point-buy" value="point_buy" checked>
                                        <label class="form-check-label" for="method-point-buy">Compra de Pontos (27)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="distribution_method" id="method-manual" value="manual">
                                        <label class="form-check-label" for="method-manual">Manual</label>
                                    </div>
                                </div>

                                <div id="point-buy-ui" class="border p-3 rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span>Pontos Disponíveis:</span>
                                        <span id="points-remaining" class="fw-bold text-success h5">27</span>
                                    </div>
                                    <div id="attribute-list-container" class="d-flex flex-column gap-2"></div>
                                </div>

                                <div id="manual-ui" class="border p-3 rounded bg-light d-none">
                                    <p class="text-muted small mb-3">Insira os valores manualmente (normalmente 8-18)</p>
                                    <div id="attribute-manual-list-container" class="d-flex flex-column gap-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUNA DIREITA --}}
                    <div class="col-xl-6 d-flex flex-column gap-4">
                        {{-- OPÇÕES DE CLASSE --}}
                        <div id="class-options-card" class="card shadow border-warning border-opacity-25 d-none">
                            <div class="card-header bg-warning text-dark">
                                <legend class="h5 fw-bold mb-0">3. Opções de Classe</legend>
                            </div>
                            <div class="card-body">
                                {{-- VIDA --}}
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">❤️ Vida Inicial</h6>
                                    <p class="small text-muted mb-2">Sua classe usa: <strong id="dado-vida-display">d6</strong></p>
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button" id="roll-hp-button" class="btn btn-sm btn-warning">Rolar Dado de Vida</button>
                                        <span class="small">Resultado: <strong id="hp-roll-result" class="text-warning">0</strong></span>
                                    </div>
                                </div>

                                {{-- EQUIPAMENTO --}}
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark">🎒 Equipamento Inicial</h6>
                                    <div id="fixed-equipment-display" class="mb-3 p-2 bg-light rounded small"></div>
                                    <div id="equipment-options-container"></div>
                                </div>

                                {{-- PERÍCIAS --}}
                                <div>
                                    <h6 class="fw-bold text-dark">📚 Perícias Iniciais</h6>
                                    <div id="fixed-skills-display" class="mb-3 p-2 bg-light rounded small"></div>
                                    <div id="skill-options-container"></div>
                                </div>
                            </div>
                        </div>

                        {{-- DASHBOARD DE ATRIBUTOS --}}
                        <div class="card shadow border-primary border-opacity-25">
                            <div class="card-header bg-primary text-white">
                                <h5 class="fw-bold mb-0">📊 Dashboard de Atributos</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h6 class="fw-bold text-dark">Valores Finais</h6>
                                        <div id="final-attributes-display" class="d-flex flex-column gap-2"></div>
                                        <div id="mod-formula-display" class="small mt-3 p-2 bg-light rounded text-muted"></div>
                                    </div>
                                    <div class="col-md-5" style="height:260px;">
                                        <canvas id="attribute-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- LISTA DE PERÍCIAS --}}
                        <div class="card shadow border-info border-opacity-25">
                            <div class="card-header bg-info text-white">
                                <h5 class="fw-bold mb-0">🗡️ Perícias do Sistema</h5>
                            </div>
                            <div class="card-body">
                                <div id="pericias-list-display" class="row row-cols-1 row-cols-md-2 g-2">
                                    <p class="text-muted small">Complete os atributos para calcular as perícias...</p>
                                </div>
                            </div>
                        </div>

                        {{-- FICHA PRÉVIA --}}
                        <div class="card shadow border-dark border-opacity-25">
                            <div class="card-header bg-dark text-white">
                                <h5 class="fw-bold mb-0">📝 Ficha Prévia</h5>
                            </div>
                            <div class="card-body">
                                <div id="ficha-previa" class="small">
                                    <p class="text-muted">Preencha todas as informações para ver a ficha completa...</p>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" id="submit-button" class="btn btn-success w-100 fw-bold py-2" disabled>
                                        🎭 Criar Personagem
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS DO SISTEMA
    const SISTEMA_ID = '{{ $sistemaId }}';
    const ATRIBUTOS_JSON = {!! $atributosJson !!};
    const FORMULA_MODIFICADOR = '{{ $formulaModificador }}';
    const PERICIAS_SISTEMA = {!! json_encode($periciasMapeamento) !!};

    // CONSTANTES
    let ATTRIBUTE_MAP = {};
    let ATTRIBUTES = [];
    const ATTRIBUTE_COSTS = {8:0, 9:1, 10:2, 11:3, 12:4, 13:5, 14:7, 15:9};
    const MAX_POINTS = 27;

    // VARIÁVEIS DE ESTADO
    let personagem = {
        atributos: {},
        pericias: [],
        equipamento: [],
        proficiencia: 2
    };

    // ELEMENTOS DOM
    const elements = {
        // Seletores
        racaSelect: document.getElementById('raca_id'),
        classeSelect: document.getElementById('classe_id'),
        origemSelect: document.getElementById('origem_id'),
        nomeInput: document.getElementById('nome'),

        // Distribuição
        methodPointBuy: document.getElementById('method-point-buy'),
        methodManual: document.getElementById('method-manual'),
        pointBuyUI: document.getElementById('point-buy-ui'),
        manualUI: document.getElementById('manual-ui'),
        pointsRemaining: document.getElementById('points-remaining'),
        attrListContainer: document.getElementById('attribute-list-container'),
        attrManualListContainer: document.getElementById('attribute-manual-list-container'),

        // Opções de Classe
        classOptionsCard: document.getElementById('class-options-card'),
        dadoVidaDisplay: document.getElementById('dado-vida-display'),
        rollHpButton: document.getElementById('roll-hp-button'),
        hpRollResult: document.getElementById('hp-roll-result'),
        fixedEquipmentDisplay: document.getElementById('fixed-equipment-display'),
        equipmentOptionsContainer: document.getElementById('equipment-options-container'),
        fixedSkillsDisplay: document.getElementById('fixed-skills-display'),
        skillOptionsContainer: document.getElementById('skill-options-container'),

        // Dashboard
        finalAttributesDisplay: document.getElementById('final-attributes-display'),
        finalAttributesJsonInput: document.getElementById('finalAttributesJsonInput'),
        modFormulaDisplay: document.getElementById('mod-formula-display'),
        periciasListDisplay: document.getElementById('pericias-list-display'),
        periciasEscolhidasInput: document.getElementById('periciasEscolhidasInput'),
        equipamentoEscolhidoInput: document.getElementById('equipamentoEscolhidoInput'),
        vidaRoladaInput: document.getElementById('vidaRoladaInput'),

        // Ficha
        fichaPrevia: document.getElementById('ficha-previa'),
        submitButton: document.getElementById('submit-button')
    };

    // INICIALIZAÇÃO
    function init() {
        setupAttributes();
        setupEventListeners();
        updateSistemaInfo();
        updateFinalAttributesAndChart();
    }

    function setupAttributes() {
        ATTRIBUTE_MAP = ATRIBUTOS_JSON;
        ATTRIBUTES = Object.keys(ATTRIBUTE_MAP);

        initializeAttributeInputs();
    }

    function setupEventListeners() {
        // Mudanças nas seleções
        elements.racaSelect.addEventListener('change', updateRaceDetails);
        elements.classeSelect.addEventListener('change', updateClassDetails);
        elements.origemSelect.addEventListener('change', updateOrigemDetails);
        elements.nomeInput.addEventListener('input', validateForm);

        // Distribuição
        elements.methodPointBuy.addEventListener('change', handleDistributionMethodChange);
        elements.methodManual.addEventListener('change', handleDistributionMethodChange);

        // Vida
        elements.rollHpButton.addEventListener('click', handleHpRoll);

        // Atualizações iniciais
        updateRaceDetails();
        updateClassDetails();
        updateOrigemDetails();
    }

    // SISTEMA E FÓRMULAS
    function updateSistemaInfo() {
        let formulaText = '';
        switch(FORMULA_MODIFICADOR) {
            case 'dnd':
                formulaText = 'D&D 5e: Mod = ⌊(Atributo - 10) / 2⌋';
                break;
            case 'ordem':
                formulaText = 'Ordem Paranormal: Mod = Atributo';
                break;
            case 'cthulhu':
                formulaText = 'Call of Cthulhu: Mod = ⌊Atributo / 2⌋';
                break;
            default:
                formulaText = 'Sistema personalizado';
        }
        document.getElementById('sistema-modificador-info').textContent = formulaText;
    }

    function calcularModificador(valor) {
        valor = parseInt(valor) || 0;

        switch(FORMULA_MODIFICADOR) {
            case 'dnd':
                return Math.floor((valor - 10) / 2);
            case 'ordem':
                return valor;
            case 'cthulhu':
                return Math.floor(valor / 2);
            default:
                return Math.floor((valor - 10) / 2);
        }
    }

    // ATRIBUTOS
    function initializeAttributeInputs() {
        elements.attrListContainer.innerHTML = '';
        elements.attrManualListContainer.innerHTML = '';

        ATTRIBUTES.forEach(attrKey => {
            const attrLabel = ATTRIBUTE_MAP[attrKey];
            const attrShort = attrLabel.substring(0, 3).toUpperCase();

            // Point Buy UI
            const pointBuyHtml = `
                <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white">
                    <span class="fw-bold" title="${attrLabel}">${attrShort}</span>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger point-buy-btn" data-attr="${attrKey}" data-action="decrease">-</button>
                        <input type="number" class="form-control form-control-sm text-center point-buy-score" data-attr="${attrKey}" value="8" min="8" max="15" style="width: 60px;" readonly>
                        <button type="button" class="btn btn-sm btn-outline-success point-buy-btn" data-attr="${attrKey}" data-action="increase">+</button>
                    </div>
                </div>
            `;
            elements.attrListContainer.insertAdjacentHTML('beforeend', pointBuyHtml);

            // Manual UI
            const manualHtml = `
                <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white">
                    <span class="fw-bold" title="${attrLabel}">${attrShort}</span>
                    <input type="number" class="form-control form-control-sm text-center manual-score" data-attr="${attrKey}" value="10" min="1" max="30" style="width: 80px;">
                </div>
            `;
            elements.attrManualListContainer.insertAdjacentHTML('beforeend', manualHtml);
        });

        // Event listeners para point buy
        elements.attrListContainer.querySelectorAll('.point-buy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                updatePointBuyState(this.dataset.attr, this.dataset.action);
            });
        });

        // Event listeners para manual
        elements.attrManualListContainer.querySelectorAll('.manual-score').forEach(input => {
            input.addEventListener('input', updateFinalAttributesAndChart);
        });

        updatePointsDisplay();
    }

    function handleDistributionMethodChange() {
        const isPointBuy = elements.methodPointBuy.checked;
        elements.pointBuyUI.classList.toggle('d-none', !isPointBuy);
        elements.manualUI.classList.toggle('d-none', isPointBuy);
        updateFinalAttributesAndChart();
    }

    function updatePointBuyState(attrKey, action) {
        const input = elements.attrListContainer.querySelector(`.point-buy-score[data-attr="${attrKey}"]`);
        if (!input) return;

        let current = parseInt(input.value);
        let newValue = current;

        if (action === 'increase' && current < 15) newValue = current + 1;
        if (action === 'decrease' && current > 8) newValue = current - 1;

        if (newValue !== current) {
            const oldCost = ATTRIBUTE_COSTS[current] || 0;
            const newCost = ATTRIBUTE_COSTS[newValue] || 0;
            const currentTotalCost = calculateTotalPointCost();

            if (currentTotalCost + (newCost - oldCost) <= MAX_POINTS) {
                input.value = newValue;
                updatePointsDisplay();
                updateFinalAttributesAndChart();
            }
        }
    }

    function calculateTotalPointCost() {
        let total = 0;
        elements.attrListContainer.querySelectorAll('.point-buy-score').forEach(input => {
            const score = parseInt(input.value) || 0;
            total += ATTRIBUTE_COSTS[score] || 0;
        });
        return total;
    }

    function updatePointsDisplay() {
        const totalCost = calculateTotalPointCost();
        const remaining = MAX_POINTS - totalCost;
        elements.pointsRemaining.textContent = remaining;
        elements.pointsRemaining.className = `fw-bold h5 ${remaining < 0 ? 'text-danger' : 'text-success'}`;
    }

    function getBaseScores() {
        const scores = {};
        const isPointBuy = elements.methodPointBuy.checked;
        const selector = isPointBuy ? '.point-buy-score' : '.manual-score';

        document.querySelectorAll(selector).forEach(input => {
            const key = input.dataset.attr;
            scores[key] = parseInt(input.value) || 0;
        });

        return scores;
    }

    // ATUALIZAÇÃO DE DETALHES
    function updateRaceDetails() {
        const selected = elements.racaSelect.options[elements.racaSelect.selectedIndex];
        const descricao = selected?.dataset.descricao || '';
        const modificadores = JSON.parse(selected?.dataset.modificadores || '{}');

        // Atualizar descrição
        elements.racaSelect.parentNode.querySelector('#raca-descricao-display').textContent = descricao;

        // Atualizar bônus
        let bonusHtml = '';
        if (Object.keys(modificadores).length > 0) {
            bonusHtml = '<div class="mt-2"><strong>Bônus de Atributos:</strong><div class="d-flex flex-wrap gap-1 mt-1">';
            for (const [atributo, valor] of Object.entries(modificadores)) {
                const nomeAtributo = ATTRIBUTE_MAP[atributo] || atributo;
                bonusHtml += `<span class="badge bg-success">${nomeAtributo}: +${valor}</span>`;
            }
            bonusHtml += '</div></div>';
        }
        elements.racaSelect.parentNode.querySelector('#raca-bonus-display').innerHTML = bonusHtml;

        updateFinalAttributesAndChart();
    }

    function updateClassDetails() {
        const selected = elements.classeSelect.options[elements.classeSelect.selectedIndex];
        const hasClass = selected && selected.value;

        elements.classOptionsCard.classList.toggle('d-none', !hasClass);

        if (!hasClass) {
            elements.classeSelect.parentNode.querySelector('#classe-descricao-display').textContent = '';
            elements.classeSelect.parentNode.querySelector('#classe-detalhes-display').innerHTML = '';
            return;
        }

        const descricao = selected.dataset.descricao || '';
        const usaMagia = selected.dataset.usaMagia === 'true';
        const dadoVida = selected.dataset.dadoVida || 'd6';
        const atributosBonus = JSON.parse(selected.dataset.atributosBonus || '{}');
        const periciasIniciais = JSON.parse(selected.dataset.periciasIniciais || '{}');
        const equipamentoInicial = JSON.parse(selected.dataset.equipamentoInicial || '{}');

        // Atualizar informações básicas
        elements.classeSelect.parentNode.querySelector('#classe-descricao-display').textContent = descricao;

        // Detalhes da classe
        let detalhesHtml = '<div class="mt-2">';
        detalhesHtml += `<p><strong>Dado de Vida:</strong> ${dadoVida.toUpperCase()}</p>`;
        detalhesHtml += `<p><strong>Usa Magia:</strong> ${usaMagia ? '✅ Sim' : '❌ Não'}</p>`;

        if (Object.keys(atributosBonus).length > 0) {
            detalhesHtml += '<p><strong>Bônus de Atributos:</strong>';
            for (const [atributo, valor] of Object.entries(atributosBonus)) {
                const nomeAtributo = ATTRIBUTE_MAP[atributo] || atributo;
                detalhesHtml += ` <span class="badge bg-info">${nomeAtributo}: +${valor}</span>`;
            }
            detalhesHtml += '</p>';
        }
        detalhesHtml += '</div>';

        elements.classeSelect.parentNode.querySelector('#classe-detalhes-display').innerHTML = detalhesHtml;

        // Atualizar opções de classe
        elements.dadoVidaDisplay.textContent = dadoVida.toUpperCase();
        renderEquipmentOptions(equipamentoInicial);
        renderSkillOptions(periciasIniciais);

        updateFinalAttributesAndChart();
    }

    function updateOrigemDetails() {
        const selected = elements.origemSelect.options[elements.origemSelect.selectedIndex];
        const descricao = selected?.dataset.descricao || '';
        const bonusPericias = JSON.parse(selected?.dataset.bonusPericias || '{}');
        const recursosAdicionais = JSON.parse(selected?.dataset.recursosAdicionais || '{}');

        elements.origemSelect.parentNode.querySelector('#origem-descricao-display').textContent = descricao;

        let recursosHtml = '';
        if (Object.keys(bonusPericias).length > 0 || Object.keys(recursosAdicionais).length > 0) {
            recursosHtml = '<div class="mt-2">';

            if (Object.keys(bonusPericias).length > 0) {
                recursosHtml += '<p><strong>Bônus de Perícias:</strong><br>';
                for (const [pericia, bonus] of Object.entries(bonusPericias)) {
                    recursosHtml += `<span class="badge bg-warning text-dark">${pericia}: +${bonus}</span> `;
                }
                recursosHtml += '</p>';
            }

            if (Object.keys(recursosAdicionais).length > 0) {
                recursosHtml += '<p><strong>Recursos Adicionais:</strong><br>';
                for (const [recurso, desc] of Object.entries(recursosAdicionais)) {
                    recursosHtml += `<small>• <strong>${recurso}:</strong> ${desc}</small><br>`;
                }
                recursosHtml += '</p>';
            }

            recursosHtml += '</div>';
        }

        elements.origemSelect.parentNode.querySelector('#origem-recursos-display').innerHTML = recursosHtml;
        updateFinalAttributesAndChart();
    }

    // EQUIPAMENTO E PERÍCIAS
    function renderEquipmentOptions(equipamento) {
        const fixas = equipamento.fixas || [];
        const opcoes = equipamento.opcoes || [];

        // Equipamento fixo
        let fixedHtml = '';
        if (fixas.length > 0) {
            fixedHtml = `<div class="mb-3"><strong>Equipamento Fixo:</strong><ul class="mb-0">`;
            fixas.forEach(item => {
                fixedHtml += `<li>${item}</li>`;
            });
            fixedHtml += '</ul></div>';
        }
        elements.fixedEquipmentDisplay.innerHTML = fixedHtml;

        // Opções de equipamento
        let optionsHtml = '';
        if (opcoes.length > 0) {
            optionsHtml = '<div><strong>Escolhas de Equipamento:</strong>';
            opcoes.forEach((grupo, index) => {
                optionsHtml += `<div class="mt-2"><small>Grupo ${index + 1}:</small>`;
                optionsHtml += `<select class="form-select form-select-sm equipment-choice" data-group="${index}">`;
                grupo.forEach(item => {
                    optionsHtml += `<option value="${item}">${item}</option>`;
                });
                optionsHtml += '</select></div>';
            });
            optionsHtml += '</div>';
        }
        elements.equipmentOptionsContainer.innerHTML = optionsHtml;

        // Event listeners para escolhas de equipamento
        elements.equipmentOptionsContainer.querySelectorAll('.equipment-choice').forEach(select => {
            select.addEventListener('change', updateEquipamentoEscolhido);
        });

        updateEquipamentoEscolhido();
    }

    function renderSkillOptions(pericias) {
        const fixas = pericias.fixas || [];
        const lista = pericias.lista || [];
        const escolha = pericias.escolha || 0;

        // Perícias fixas
        let fixedHtml = '';
        if (fixas.length > 0) {
            fixedHtml = `<div class="mb-3"><strong>Perícias Fixas:</strong><br>`;
            fixas.forEach(pericia => {
                fixedHtml += `<span class="badge bg-success">${pericia}</span> `;
            });
            fixedHtml += '</div>';
        }
        elements.fixedSkillsDisplay.innerHTML = fixedHtml;

        // Opções de perícias
        let optionsHtml = '';
        if (lista.length > 0 && escolha > 0) {
            optionsHtml = `<div class="mb-3"><strong>Escolha ${escolha} perícias:</strong><div class="mt-2">`;
            lista.forEach(pericia => {
                const id = `skill_${pericia.replace(/\s+/g, '_')}`;
                optionsHtml += `
                    <div class="form-check">
                        <input class="form-check-input skill-choice" type="checkbox" id="${id}" value="${pericia}">
                        <label class="form-check-label small" for="${id}">${pericia}</label>
                    </div>
                `;
            });
            optionsHtml += '</div></div>';
        }
        elements.skillOptionsContainer.innerHTML = optionsHtml;

        // Event listeners para perícias
        elements.skillOptionsContainer.querySelectorAll('.skill-choice').forEach(checkbox => {
            checkbox.addEventListener('change', updatePericiasEscolhidas);
        });

        updatePericiasEscolhidas();
    }

    function updateEquipamentoEscolhido() {
        const escolhas = {};
        elements.equipmentOptionsContainer.querySelectorAll('.equipment-choice').forEach(select => {
            escolhas[select.dataset.group] = select.value;
        });
        elements.equipamentoEscolhidoInput.value = JSON.stringify(escolhas);
        validateForm();
    }

    function updatePericiasEscolhidas() {
        const periciasEscolhidas = [];
        const escolhaNecessaria = parseInt(elements.classeSelect.selectedOptions[0]?.dataset.periciasIniciais?.escolha) || 0;

        elements.skillOptionsContainer.querySelectorAll('.skill-choice:checked').forEach(checkbox => {
            periciasEscolhidas.push(checkbox.value);
        });

        // Validar número de escolhas
        if (periciasEscolhidas.length > escolhaNecessaria) {
            alert(`Você só pode escolher ${escolhaNecessaria} perícias.`);
            // Desmarcar a última escolha
            periciasEscolhidas.pop();
            elements.skillOptionsContainer.querySelectorAll('.skill-choice:checked').forEach((checkbox, index) => {
                if (index >= escolhaNecessaria) {
                    checkbox.checked = false;
                }
            });
        }

        elements.periciasEscolhidasInput.value = JSON.stringify(periciasEscolhidas);
        validateForm();
        updatePericiasCalculadas();
    }

    // CÁLCULOS E ATUALIZAÇÕES
    function updateFinalAttributesAndChart() {
        const baseScores = getBaseScores();
        const finalScores = {};
        const bonuses = calcularBonusAtributos();

        // Calcular scores finais
        ATTRIBUTES.forEach(attr => {
            const base = baseScores[attr] || 0;
            const bonus = bonuses[attr] || 0;
            finalScores[attr] = base + bonus;
        });

        // Atualizar display
        updateAttributesDisplay(finalScores);
        updateChart(finalScores);
        updatePericiasCalculadas();
        validateForm();

        // Salvar no hidden input
        elements.finalAttributesJsonInput.value = JSON.stringify({
            base: baseScores,
            final: finalScores,
            bonuses: bonuses
        });
    }

    function calcularBonusAtributos() {
        const bonuses = {};

        // Bônus da raça
        const racaModificadores = JSON.parse(elements.racaSelect.selectedOptions[0]?.dataset.modificadores || '{}');
        for (const [attr, bonus] of Object.entries(racaModificadores)) {
            bonuses[attr] = (bonuses[attr] || 0) + bonus;
        }

        // Bônus da classe
        const classeBonus = JSON.parse(elements.classeSelect.selectedOptions[0]?.dataset.atributosBonus || '{}');
        for (const [attr, bonus] of Object.entries(classeBonus)) {
            bonuses[attr] = (bonuses[attr] || 0) + bonus;
        }

        return bonuses;
    }

    function updateAttributesDisplay(finalScores) {
        let html = '';

        ATTRIBUTES.forEach(attr => {
            const score = finalScores[attr] || 0;
            const modifier = calcularModificador(score);
            const modifierSign = modifier >= 0 ? '+' : '';
            const label = ATTRIBUTE_MAP[attr];
            const shortLabel = label.substring(0, 3).toUpperCase();

            html += `
                <div class="d-flex justify-content-between align-items-center p-2 border rounded bg-light">
                    <strong class="text-uppercase">${shortLabel}</strong>
                    <div class="text-end">
                        <div class="h5 mb-0 text-primary">${score}</div>
                        <small class="text-muted">${modifierSign}${modifier}</small>
                    </div>
                </div>
            `;
        });

        elements.finalAttributesDisplay.innerHTML = html;
    }

    function updatePericiasCalculadas() {
        const finalData = JSON.parse(elements.finalAttributesJsonInput.value || '{}');
        const finalScores = finalData.final || {};

        if (Object.keys(finalScores).length === 0) {
            elements.periciasListDisplay.innerHTML = '<p class="text-muted small">Complete os atributos para calcular as perícias...</p>';
            return;
        }

        let html = '';
        const periciasProficientes = JSON.parse(elements.periciasEscolhidasInput.value || '[]');
        const bonusOrigem = JSON.parse(elements.origemSelect.selectedOptions[0]?.dataset.bonusPericias || '{}');

        PERICIAS_SISTEMA.forEach(pericia => {
            const atributo = pericia.atributo_relacionado;
            const score = finalScores[atributo] || 0;
            const modificador = calcularModificador(score);
            const isProficiente = periciasProficientes.includes(pericia.nome);
            const bonusProficiencia = isProficiente ? personagem.proficiencia : 0;
            const bonusPericiaOrigem = bonusOrigem[pericia.nome] || 0;

            const total = modificador + bonusProficiencia + bonusPericiaOrigem + (pericia.modificador_base || 0);
            const totalSign = total >= 0 ? '+' : '';

            const badgeClass = isProficiente ? 'bg-success' : 'bg-secondary';
            const borderClass = isProficiente ? 'border-success' : '';

            html += `
                <div class="col">
                    <div class="p-2 border rounded ${borderClass}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold small">${pericia.nome}</span>
                            <span class="badge ${badgeClass}">${totalSign}${total}</span>
                        </div>
                        <div class="small text-muted">
                            ${ATTRIBUTE_MAP[atributo]?.substring(0, 3).toUpperCase()} ${modificador >= 0 ? '+' : ''}${modificador}
                            ${isProficiente ? `+ PB${personagem.proficiencia}` : ''}
                            ${bonusPericiaOrigem > 0 ? `+ Origem${bonusPericiaOrigem}` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        elements.periciasListDisplay.innerHTML = html;
    }

    // GRÁFICO E VISUAIS
    function updateChart(finalScores) {
        const ctx = document.getElementById('attribute-chart').getContext('2d');
        const labels = ATTRIBUTES.map(attr => ATTRIBUTE_MAP[attr].substring(0, 3).toUpperCase());
        const data = ATTRIBUTES.map(attr => finalScores[attr] || 0);

        if (window.attributeChart) {
            window.attributeChart.destroy();
        }

        window.attributeChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Atributos',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(54, 162, 235)'
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 20
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // VALIDAÇÃO E SUBMISSÃO
    function validateForm() {
        const hasName = elements.nomeInput.value.trim() !== '';
        const hasRace = elements.racaSelect.value !== '';
        const hasClass = elements.classeSelect.value !== '';
        const pointsValid = elements.methodPointBuy.checked ? calculateTotalPointCost() <= MAX_POINTS : true;
        const hasHpRoll = elements.hpRollResult.textContent !== '0';

        const isValid = hasName && hasRace && hasClass && pointsValid && hasHpRoll;

        elements.submitButton.disabled = !isValid;
        updateFichaPrevia();

        return isValid;
    }

    function updateFichaPrevia() {
        if (!validateForm()) {
            elements.fichaPrevia.innerHTML = '<p class="text-muted">Preencha todas as informações para ver a ficha completa...</p>';
            return;
        }

        const finalData = JSON.parse(elements.finalAttributesJsonInput.value || '{}');
        const finalScores = finalData.final || {};

        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informações Básicas</h6>
                    <p><strong>Nome:</strong> ${elements.nomeInput.value}</p>
                    <p><strong>Raça:</strong> ${elements.racaSelect.selectedOptions[0]?.text}</p>
                    <p><strong>Classe:</strong> ${elements.classeSelect.selectedOptions[0]?.text}</p>
                    <p><strong>Origem:</strong> ${elements.origemSelect.selectedOptions[0]?.text || 'Nenhuma'}</p>
                    <p><strong>Vida:</strong> ${elements.hpRollResult.textContent} + CONST</p>
                </div>
                <div class="col-md-6">
                    <h6>Atributos Principais</h6>
        `;

        ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'].forEach(attr => {
            if (finalScores[attr]) {
                const modifier = calcularModificador(finalScores[attr]);
                const sign = modifier >= 0 ? '+' : '';
                html += `<p><strong>${ATTRIBUTE_MAP[attr]}:</strong> ${finalScores[attr]} (${sign}${modifier})</p>`;
            }
        });

        html += `
                </div>
            </div>
            <div class="mt-3">
                <small class="text-muted">Personagem balanceado e pronto para criação!</small>
            </div>
        `;

        elements.fichaPrevia.innerHTML = html;
    }

    // ROLAGEM DE VIDA
    function handleHpRoll() {
        const dadoVida = elements.classeSelect.selectedOptions[0]?.dataset.dadoVida || 'd6';
        const sides = parseInt(dadoVida.substring(1));
        const roll = Math.floor(Math.random() * sides) + 1;

        elements.hpRollResult.textContent = roll;
        elements.vidaRoladaInput.value = roll;
        validateForm();
    }

    // INICIAR APLICAÇÃO
    init();
});
</script>
@endif
@endsection
