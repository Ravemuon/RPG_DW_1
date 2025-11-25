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
{{-- Variáveis do Sistema extraídas do objeto $campanha->sistema para uso direto no JS --}}
@php
    $sistema = $campanha->sistema;
    // O controller DEVE garantir que $racas, $classes e $origens são filtrados pelo $sistema->id.
    $atributosJson = json_encode($sistema->atributos ?? []);
    $formulaPv = $sistema->formula_pontos_vida ?? '';
    $formulaModificador = $sistema->formula_modificador ?? 'dnd';
    $sistemaId = $sistema->id;
    $sistemaNome = $sistema->nome;

    // Adicionando a lista de perícias do sistema diretamente (se existir no modelo Sistema)
    // Se não existir, o JS fará o fallback.
    $periciasSistema = is_string($sistema->pericias_sistema) ? json_decode($sistema->pericias_sistema, true) : ($sistema->pericias_sistema ?? []);

    // Mapeamento de Atributos para as Perícias (para o JS)
    $periciasMapeamento = [];
    if (!empty($periciasSistema) && is_array($periciasSistema)) {
        foreach ($periciasSistema as $pericia) {
            $periciasMapeamento[] = [
                'nome' => $pericia['nome'] ?? 'Perícia Sem Nome',
                'atributo_relacionado' => $pericia['atributo_relacionado'] ?? 'inteligencia', // Fallback
            ];
        }
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
                {{-- INPUTS HIDDEN COM DADOS DA CAMPANHA E SISTEMA --}}
                <input type="hidden" name="campanha_id" value="{{ $campanha->id ?? 1 }}">
                <input type="hidden" name="sistema_id" id="sistema_id_input" value="{{ $sistemaId }}">

                {{-- campos auxiliares que serão preenchidos pelo JS --}}
                <input type="hidden" name="atributos" id="finalAttributesJsonInput">
                <input type="hidden" name="race_choices" id="raceChoicesInput">
                <input type="hidden" name="selected_equipment" id="selectedEquipmentInput">
                <input type="hidden" name="selected_skills" id="selectedSkillsInput">
                <input type="hidden" name="rolled_hp" id="rolledHpInput">
                {{-- Campo auxiliar para Proficiência --}}
                <input type="hidden" name="proficiencia_bonus" id="proficienciaBonusInput">

                {{-- Informação do Sistema --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="alert alert-info py-2 mb-0">
                            <i class="fas fa-book"></i> **Sistema de Regras:** <strong class="text-dark">{{ $sistemaNome }}</strong>.
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- coluna esquerda --}}
                    <div class="col-xl-6 d-flex flex-column gap-4">
                        <div class="card shadow-sm border-secondary border-opacity-25">
                            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <legend class="h5 text-primary fw-bold mb-0">1. Informações Essenciais</legend>
                                <div>
                                    <button type="button" id="randomize-button" class="btn btn-sm btn-info text-white fw-bold">🎲 Sortear Personagem</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required class="form-control">
                                </div>

                                {{-- FILTRAGEM POR SISTEMA AQUI (ASSUMIDA) --}}
                                <div class="mb-3">
                                    <label for="raca_id" class="form-label">Raça</label>
                                    <select name="raca_id" id="raca_id" required class="form-select">
                                        <option value="">(Nenhuma)</option>
                                        @foreach ($racas->where('sistema_id', $sistemaId) as $raca) {{-- FILTRO ADICIONADO AQUI POR SEGURANÇA --}}
                                            @php
                                                $modificadores = $raca->modificadores_atributos ?? (is_string($raca->modificadores_atributos) ? json_decode($raca->modificadores_atributos,true) : []);
                                            @endphp
                                            <option
                                                value="{{ $raca->id }}"
                                                data-bonus='@json($modificadores)'
                                                data-tipo-bonus="{{ $raca->tipo_bonus ?? 'flat' }}"
                                                data-bonus-livre="{{ $raca->bonus_livre ?? 0 }}"
                                                data-descricao="{{ $raca->descricao ?? '' }}">
                                                {{ $raca->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p id="raca-descricao-display" class="small text-muted mt-2"></p>
                                </div>

                                <div id="race-choice-container" class="mt-3 p-3 bg-warning bg-opacity-10 rounded border border-warning d-none">
                                    <h6 class="small fw-bold">Escolhas de Bônus Raciais:</h6>
                                    <div id="race-choices-area"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="classe_id" class="form-label">Classe</label>
                                    <select name="classe_id" id="classe_id" required class="form-select">
                                        <option value="">(Nenhuma)</option>

                                        @foreach ($classes->where('sistema_id', $sistemaId) as $classe) {{-- FILTRO ADICIONADO AQUI POR SEGURANÇA --}}
                                            @php
                                                $equipRaw = $classe->equipamento_inicial;
                                                if (is_string($equipRaw)) { $equipRaw = json_decode($equipRaw, true); }
                                                if (!is_array($equipRaw)) { $equipRaw = []; }
                                                $fixas = $equipRaw['fixas'] ?? [];
                                                if (empty($fixas) && array_is_list($equipRaw)) { $fixas = $equipRaw; }
                                                $opcoes = $equipRaw['opcoes'] ?? [];
                                                $equipFormatado = ['fixas' => $fixas, 'opcoes' => $opcoes];

                                                $periciasIniciais = is_string($classe->pericias_iniciais) ? json_decode($classe->pericias_iniciais, true) : ($classe->pericias_iniciais ?? []);
                                                $atributosBonus = is_string($classe->atributos_bonus) ? json_decode($classe->atributos_bonus, true) : ($classe->atributos_bonus ?? []);
                                            @endphp

                                            <option
                                                value="{{ $classe->id }}"
                                                data-class-bases='@json($atributosBonus)'
                                                data-class-skills='@json($periciasIniciais)'
                                                data-class-equipment='@json($equipFormatado)'
                                                data-dado-vida="{{ $classe->dado_vida ?? 'd6' }}"
                                                data-usa-magia="{{ $classe->usa_magia ? 'Sim' : 'Não' }}"
                                                data-descricao="{{ $classe->descricao ?? '' }}"
                                            >
                                                {{ $classe->nome }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <p id="classe-descricao-display" class="small text-muted mt-2"></p>
                                    <p id="classe-magia-display" class="small text-muted mt-1 fw-bold"></p>
                                </div>

                                <div class="mb-3">
                                    <label for="origem_id" class="form-label">Origem / Background</label>
                                    <select name="origem_id" id="origem_id" class="form-select">
                                        <option value="">(Opcional)</option>
                                        @foreach ($origens->where('sistema_id', $sistemaId) as $origem) {{-- FILTRO ADICIONADO AQUI POR SEGURANÇA --}}
                                            @php
                                                $bonusPericiasData = is_string($origem->bonus_pericias_data) ? json_decode($origem->bonus_pericias_data, true) : ($origem->bonus_pericias_data ?? []);
                                                $recursosAdicionaisData = is_string($origem->recursos_adicionais_data) ? json_decode($origem->recursos_adicionais_data, true) : ($origem->recursos_adicionais_data ?? []);
                                            @endphp
                                            <option
                                                value="{{ $origem->id }}"
                                                data-skills='@json($bonusPericiasData)'
                                                data-resources='@json($recursosAdicionaisData)'
                                                data-descricao="{{ $origem->descricao ?? '' }}"
                                            >
                                                {{ $origem->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p id="origem-descricao-display" class="small text-muted mt-2"></p>
                                    <div id="origem-resources-display" class="alert alert-info small mt-2 d-none"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Distribuição --}}
                        <div class="card shadow border-success border-opacity-25">
                            <div class="card-header bg-success text-white">
                                <legend class="h5 fw-bold mb-0">2. Distribuição de Atributos</legend>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="proficiencia_bonus_select" class="form-label fw-bold text-dark">🎯 Modificador de Proficiência (PB)</label>
                                    <select id="proficiencia_bonus_select" class="form-select" name="proficiencia_bonus_display">
                                        {{-- Opções comuns em D&D para Nível 1 --}}
                                        <option value="2" selected>+2 (Nível 1 - Padrão)</option>
                                        <option value="3">+3 (Nível 5)</option>
                                        <option value="4">+4 (Nível 9)</option>
                                        <option value="5">+5 (Nível 13)</option>
                                        <option value="6">+6 (Nível 17)</option>
                                    </select>
                                    <p class="small text-muted mt-1">Este valor será usado no cálculo das perícias proficientes.</p>
                                </div>
                                <hr>

                                <div class="mb-3" id="distribution-methods-container">
                                    <label class="form-label">Método de Distribuição:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="distribution_method" id="method-point-buy" value="point_buy" checked>
                                        <label class="form-check-label" for="method-point-buy">Compra de Pontos (27)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="distribution_method" id="method-manual" value="manual">
                                        <label class="form-check-label" for="method-manual">Manual / Rolagem</label>
                                    </div>
                                </div>

                                <div id="point-buy-ui" class="border p-3 rounded bg-light">
                                    <p class="mb-2">Pontos Restantes: <span id="points-remaining" class="fw-bold text-success">27</span> / 27</p>
                                    <div id="attribute-list-container" class="d-flex flex-column gap-2"></div>
                                </div>

                                <div id="manual-ui" class="border p-3 rounded bg-light d-none">
                                    <h6 class="fw-bold">Insira seus valores de atributo (ex: 15, 14, 13...)</h6>
                                    <div id="attribute-manual-list-container" class="d-flex flex-column gap-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- coluna direita --}}
                    <div class="col-xl-6 d-flex flex-column gap-4">
                        <div id="class-options-card" class="card shadow border-danger border-opacity-25 bg-danger bg-opacity-10 d-none">
                            <div class="card-header bg-transparent border-0">
                                <legend class="h5 text-danger fw-bold mb-0">3. Opções de Classe (Perícias e Equipamento)</legend>
                            </div>
                            <div class="card-body">
                                <div id="hp-roll-section">
                                    <h6 class="fw-semibold text-danger">Vida Inicial (PV)</h6>
                                    <p class="small text-muted mb-2">Sua classe usa um <strong id="dado-vida-display">d6</strong>. Rolar valor:</p>
                                    <div class="d-flex align-items-center mb-4">
                                        <button type="button" id="roll-hp-button" class="btn btn-sm btn-danger me-3">Rolar Dado de Vida</button>
                                        <div class="small">Valor do Dado Rolado: <strong id="hp-roll-result" class="text-danger">0</strong></div>
                                    </div>
                                    <hr>
                                </div>

                                <h6 class="fw-semibold text-danger mt-4">Equipamento Inicial</h6>
                                <div id="fixed-equipment-display" class="mb-2"></div>
                                <div id="equipment-options-container" class="d-flex flex-column gap-3 mb-4">
                                    <p class="small text-muted">Selecione uma opção de cada grupo (se houver).</p>
                                </div>

                                <hr>

                                <h6 class="fw-semibold text-danger mt-4">Escolha de Perícias</h6>
                                <div id="fixed-skills-info" class="mb-3">
                                    <p class="small text-muted" id="fixed-skills-display"></p>
                                </div>
                                <div id="skill-options-container" class="mb-3 d-none">
                                    <p id="skill-choice-instructions" class="small text-muted mb-2"></p>
                                    <div id="skill-choice-checkboxes" class="d-flex flex-column gap-2"></div>
                                    <div id="skill-choice-alert" class="alert alert-warning small mt-2 d-none"></div>
                                </div>

                            </div>
                        </div>

                        <div class="card shadow border-primary border-opacity-25">
                            <div class="card-header bg-primary text-white">
                                <h5 class="fw-bold mb-0">Dashboard de Atributos Finais e Perícias</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h6 class="fw-bold text-dark">Valores Finais de Atributo</h6>
                                        <div id="final-attributes-display" class="d-flex flex-column gap-2"></div>
                                        <div id="mod-formula-display" class="small mt-3 p-2 bg-light rounded text-muted"></div>
                                    </div>
                                    <div class="col-md-5" style="height:260px;">
                                        <canvas id="attribute-chart"></canvas>
                                    </div>
                                </div>
                                <hr class="my-4">

                                {{-- NOVO BLOCO: Lista de Perícias --}}
                                <div class="mt-3">
                                    <h6 class="fw-bold text-success">Perícias Calculadas ({{ $sistemaNome }})</h6>
                                    <div id="pericias-list-display" class="row row-cols-1 row-cols-md-2 g-2">
                                        {{-- Perícias serão renderizadas aqui pelo JS --}}
                                        <p class="text-muted small">Selecione uma classe e complete a distribuição de atributos para calcular as perícias.</p>
                                    </div>
                                </div>
                                {{-- FIM DO NOVO BLOCO --}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-end">
                    <button type="submit" id="submit-button" class="btn btn-success btn-lg fw-bolder px-5 py-3 shadow-lg border-bottom border-4 border-success-subtle" disabled>
                        Criar Personagem Lendário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Adaptações de JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS DO SISTEMA INJETADAS PELO PHP
    const SISTEMA_ID = '{{ $sistemaId }}';
    const ATRIBUTOS_JSON = {!! $atributosJson !!};
    const FORMULA_MODIFICADOR = '{{ $formulaModificador }}';
    const FORMULA_PV = '{{ $formulaPv }}';
    const PERICIAS_MAPEAMENTO_JSON = {!! json_encode($periciasMapeamento) !!}; // Lista de Perícias do Sistema

    // CONSTANTES
    let ATTRIBUTE_MAP = {};
    let ATTRIBUTES = [];
    const ATTRIBUTE_COSTS = {8:0,9:1,10:2,11:3,12:4,13:5,14:7,15:9};
    const MAX_POINTS = 27;

    let CURRENT_MODIFIER_FORMULA = FORMULA_MODIFICADOR;
    let CURRENT_HP_FORMULA = FORMULA_PV;
    // Variável para armazenar as perícias do sistema (preenchida pelo PHP)
    let ALL_SYSTEM_SKILLS = PERICIAS_MAPEAMENTO_JSON;

    // DOM (Selecionar elementos)
    const proficienciaBonusSelect = document.getElementById('proficiencia_bonus_select');
    const proficienciaBonusInput = document.getElementById('proficienciaBonusInput');

    const racaSelect = document.getElementById('raca_id');
    const racaDescricaoDisplay = document.getElementById('raca-descricao-display');
    const classeSelect = document.getElementById('classe_id');
    const classeDescricaoDisplay = document.getElementById('classe-descricao-display');
    const classeMagiaDisplay = document.getElementById('classe-magia-display');
    const origemSelect = document.getElementById('origem_id');
    const origemDescricaoDisplay = document.getElementById('origem-descricao-display');
    const origemResourcesDisplay = document.getElementById('origem-resources-display');

    const nomeInput = document.getElementById('nome');
    const randomizeButton = document.getElementById('randomize-button');
    const submitButton = document.getElementById('submit-button');

    const methodPointBuy = document.getElementById('method-point-buy');
    const methodManual = document.getElementById('method-manual');
    const pointBuyUI = document.getElementById('point-buy-ui');
    const manualUI = document.getElementById('manual-ui');
    const pointsRemainingDisplay = document.getElementById('points-remaining');
    const attrListContainer = document.getElementById('attribute-list-container');
    const attrManualListContainer = document.getElementById('attribute-manual-list-container');

    const raceChoiceContainer = document.getElementById('race-choice-container');
    const raceChoicesArea = document.getElementById('race-choices-area');
    const raceChoicesInput = document.getElementById('raceChoicesInput');

    const classOptionsCard = document.getElementById('class-options-card');
    const dadoVidaDisplay = document.getElementById('dado-vida-display');
    const rollHpButton = document.getElementById('roll-hp-button');
    const hpRollResultDisplay = document.getElementById('hp-roll-result');
    const rolledHpInput = document.getElementById('rolledHpInput');
    const hpRollSection = document.getElementById('hp-roll-section');

    const fixedEquipmentDisplay = document.getElementById('fixed-equipment-display');
    const equipmentOptionsContainer = document.getElementById('equipment-options-container');
    const selectedEquipmentInput = document.getElementById('selectedEquipmentInput');

    const skillOptionsContainer = document.getElementById('skill-options-container');
    const skillChoiceInstructions = document.getElementById('skill-choice-instructions');
    const skillChoiceCheckboxes = document.getElementById('skill-choice-checkboxes');
    const skillChoiceAlert = document.getElementById('skill-choice-alert');
    const selectedSkillsInput = document.getElementById('selectedSkillsInput');
    const fixedSkillsDisplay = document.getElementById('fixed-skills-display');

    const finalAttributesDisplay = document.getElementById('final-attributes-display');
    const finalAttributesJsonInput = document.getElementById('finalAttributesJsonInput');
    const modFormulaDisplay = document.getElementById('mod-formula-display');
    const periciasListDisplay = document.getElementById('pericias-list-display');

    let attributeChart;

    // --- UTILS ---
    function parseDataAttributeString(str) {
        if (str === null || str === undefined) return {};
        try { return typeof str === 'object' ? str : JSON.parse(str); } catch(e) { return {}; }
    }
    function rollDice(diceString) {
        const m = String(diceString).match(/d(\d+)/i);
        if (!m) return 0;
        const sides = parseInt(m[1],10);
        return Math.floor(Math.random()*sides)+1;
    }

    // Função para calcular o modificador com base na regra do sistema
    function calculateModifier(score) {
        score = parseInt(score);
        if (isNaN(score)) return 0;

        switch (CURRENT_MODIFIER_FORMULA) {
            case 'ordem': // Ordem Paranormal: Modificador é igual ao Atributo
                return score;
            case 'dnd': // D&D 5e: Padrão
            default:
                return Math.floor((score - 10) / 2);
        }
    }

    // --- SISTEMA E ATRIBUTOS ---

    function setAttributesFromSistema() {
        const attrs = ATRIBUTOS_JSON;

        let newAttributeMap = { forca: 'Força', destreza: 'Destreza', constituicao: 'Constituição', inteligencia: 'Inteligência', sabedoria: 'Sabedoria', carisma: 'Carisma' };

        if (attrs && Object.keys(attrs).length) {
            ATTRIBUTES = Object.keys(attrs);
            ATTRIBUTE_MAP = attrs;
        } else {
             // Fallback para D&D 5e-like
            ATTRIBUTES = ['forca','destreza','constituicao','inteligencia','sabedoria','carisma'];
            ATTRIBUTE_MAP = newAttributeMap;
        }

        // Adaptações de UI para diferentes sistemas
        if (CURRENT_MODIFIER_FORMULA === 'ordem' || CURRENT_MODIFIER_FORMULA === 'cthulhu' || CURRENT_HP_FORMULA !== '') {
            // Desativar Point Buy se as regras forem incompatíveis com o padrão D&D 5e
            methodPointBuy.disabled = true;
            if (methodPointBuy.checked) {
                methodManual.checked = true;
            }
            if (CURRENT_HP_FORMULA !== '') {
                 hpRollSection.classList.add('d-none');
            }
        } else {
            methodPointBuy.disabled = false;
            hpRollSection.classList.remove('d-none');
        }
        handleDistributionMethodChange();

        // Re-inicializa os inputs de atributos e o gráfico após a mudança
        initializeAttributeInputs();
        updateFinalAttributesAndChart();
    }

    // --- UI DE ATRIBUTOS ---
    function initializeAttributeInputs() {
        attrListContainer.innerHTML = '';
        attrManualListContainer.innerHTML = '';

        ATTRIBUTES.forEach(attrKey => {
            const attrLabel = ATTRIBUTE_MAP[attrKey] || attrKey.toUpperCase();
            const attrLabelShort = (attrLabel.length > 3) ? attrLabel.substr(0,3).toUpperCase() : attrLabel.toUpperCase();

            // Point Buy UI (Mínimo 8, Máximo 15, D&D 5e padrão)
            const pointBuyHtml = `
                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                    <span class="fw-bold me-3" title="${attrLabel}">${attrLabelShort}</span>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger me-2 point-buy-btn" data-attr="${attrKey}" data-action="decrease">-</button>
                        <input type="number" class="form-control form-control-sm text-center point-buy-score" data-attr="${attrKey}" value="8" min="8" max="15" style="width: 60px;" readonly>
                        <button type="button" class="btn btn-sm btn-outline-success ms-2 point-buy-btn" data-attr="${attrKey}" data-action="increase">+</button>
                    </div>
                </div>
            `;
            attrListContainer.insertAdjacentHTML('beforeend', pointBuyHtml);

            // Manual UI (Geral)
            const manualHtml = `
                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                    <label class="fw-bold me-3" title="${attrLabel}">${attrLabelShort}</label>
                    <input type="number" class="form-control form-control-sm text-center manual-score" data-attr="${attrKey}" value="10" min="1" max="30" style="width: 80px;">
                </div>
            `;
            attrManualListContainer.insertAdjacentHTML('beforeend', manualHtml);
        });

        attrListContainer.querySelectorAll('.point-buy-btn').forEach(btn => btn.addEventListener('click', function() {
            updatePointBuyState(this.getAttribute('data-attr'), this.getAttribute('data-action'));
        }));

        attrManualListContainer.querySelectorAll('.manual-score').forEach(input => input.addEventListener('input', updateFinalAttributesAndChart));

        pointsRemainingDisplay.textContent = MAX_POINTS - calculatePointCost();
    }

    function handleDistributionMethodChange() {
        const isPointBuy = methodPointBuy.checked && !methodPointBuy.disabled;
        pointBuyUI.classList.toggle('d-none', !isPointBuy);
        manualUI.classList.toggle('d-none', isPointBuy);
        updateFinalAttributesAndChart();
    }

    function calculatePointCost() {
        let total = 0;
        document.querySelectorAll('.point-buy-score').forEach(input => {
            const score = parseInt(input.value) || 0;
            total += ATTRIBUTE_COSTS[score] || 0;
        });
        return total;
    }

    function updatePointBuyState(attrKey, action) {
        const input = attrListContainer.querySelector(`.point-buy-score[data-attr="${attrKey}"]`);
        if (!input) return;
        let current = parseInt(input.value);
        let next = current;
        if (action === 'increase' && current < 15) next = current + 1;
        if (action === 'decrease' && current > 8) next = current - 1;
        if (next === current) return;

        const oldCost = ATTRIBUTE_COSTS[current] || 0;
        const newCost = ATTRIBUTE_COSTS[next] || 0;
        const currentCost = calculatePointCost();
        const totalAfter = currentCost + (newCost - oldCost);

        if (totalAfter <= MAX_POINTS) {
            input.value = next;
        }

        const finalCost = calculatePointCost();
        pointsRemainingDisplay.textContent = MAX_POINTS - finalCost;
        pointsRemainingDisplay.classList.toggle('text-danger', finalCost > MAX_POINTS);
        pointsRemainingDisplay.classList.toggle('text-success', finalCost <= MAX_POINTS);
        updateFinalAttributesAndChart();
    }

    function getBaseScores() {
        const obj = {};
        const isPointBuy = methodPointBuy.checked && !methodPointBuy.disabled;
        const selector = isPointBuy ? '.point-buy-score' : '.manual-score';
        document.querySelectorAll(selector).forEach(input => {
            const key = input.getAttribute('data-attr');
            obj[key] = parseInt(input.value) || 0;
        });
        return obj;
    }

    function updateFinalAttributesAndChart() {
        const base = getBaseScores();
        const final = {};
        let html = '';

        const raceOption = racaSelect.options[racaSelect.selectedIndex];
        const raceFixedBonus = parseDataAttributeString(raceOption?.getAttribute('data-bonus') || null);

        const classOption = classeSelect.options[classeSelect.selectedIndex];
        const classBonus = parseDataAttributeString(classOption?.getAttribute('data-class-bases') || null);

        const raceChoices = JSON.parse(raceChoicesInput.value || '[]');
        const raceChoiceMap = {};
        if (Array.isArray(raceChoices)) {
            raceChoices.forEach(c => { raceChoiceMap[c.attribute] = (raceChoiceMap[c.attribute] || 0) + (parseInt(c.value) || 0); });
        }

        // Objeto para armazenar Modificadores
        const finalModifiers = {};

        ATTRIBUTES.forEach(key => {
            const b = base[key] || 0;
            const totalBonus = (parseInt(raceFixedBonus[key] || 0) || 0) + (parseInt(raceChoiceMap[key] || 0) || 0) + (parseInt(classBonus[key] || 0) || 0);
            const finalScore = b + totalBonus;
            final[key] = finalScore;

            const modifier = calculateModifier(finalScore);
            finalModifiers[key] = modifier;

            const modifierSign = modifier >= 0 ? '+' : '';
            const totalBonusSign = totalBonus >= 0 ? '+' : '';

            const attrLabel = ATTRIBUTE_MAP[key] || key;
            const attrLabelShort = (attrLabel.length > 3) ? attrLabel.substr(0,3).toUpperCase() : attrLabel.toUpperCase();

            const baseBonusHtml = totalBonus > 0 ? `<span class="small text-success ms-1">(${totalBonusSign}${totalBonus})</span>` : '';

            html += `<div class="p-2 border rounded d-flex justify-content-between align-items-center">
                        <strong class="text-uppercase me-2" title="${attrLabel}">${attrLabelShort}</strong>
                        <span class="d-flex align-items-baseline">
                            <span class="text-muted small">${b}</span>
                            ${baseBonusHtml}
                            <span class="h5 fw-bolder text-primary ms-3">${finalScore}</span>
                            <span class="small text-secondary ms-3">Mod: <strong class="text-dark">${modifierSign}${modifier}</strong></span>
                        </span>
                    </div>`;
        });

        finalAttributesDisplay.innerHTML = html;
        finalAttributesJsonInput.value = JSON.stringify({ base, final });

        // Display da fórmula do modificador
        let formulaText = '';
        if (CURRENT_MODIFIER_FORMULA === 'dnd') {
            formulaText = `Modificador (D&D): $$\\lfloor \\frac{\\text{Atributo} - 10}{2} \\rfloor$$`;
        } else if (CURRENT_MODIFIER_FORMULA === 'ordem') {
            formulaText = `Modificador (Ordem): $$\\text{Atributo}$$`;
        }
        modFormulaDisplay.innerHTML = formulaText;

        updateChart(final);
        validateBaseDistribution();

        // Chama a atualização das perícias
        updateSkillCalculation(finalModifiers, final);
    }

    // ... Funções updateRaceDetails, renderRaceChoices, updateRaceChoicesInput (sem alterações) ...
    function updateRaceDetails() {
        const selected = racaSelect.options[racaSelect.selectedIndex];
        const hasRace = selected && selected.value;
        const descricao = selected?.getAttribute('data-descricao') || '';
        const tipoBonus = selected?.getAttribute('data-tipo-bonus') || 'flat';
        const bonusLivre = parseInt(selected?.getAttribute('data-bonus-livre') || 0);

        racaDescricaoDisplay.textContent = descricao;

        if (hasRace && tipoBonus !== 'flat' && bonusLivre > 0) {
            raceChoiceContainer.classList.remove('d-none');
            renderRaceChoices(bonusLivre);
        } else {
            raceChoiceContainer.classList.add('d-none');
            raceChoicesInput.value = '[]';
        }
        updateFinalAttributesAndChart();
    }

    function renderRaceChoices(choicesCount) {
        raceChoicesArea.innerHTML = `<p class="small">Escolha ${choicesCount} bônus de atributo de +1 cada (não pode escolher o mesmo atributo mais de uma vez, ou conforme a regra da raça).</p>`;

        for (let i = 0; i < choicesCount; i++) {
            let selectHtml = `<select class="form-select form-select-sm mt-2 race-choice-select" data-index="${i}">`;
            selectHtml += `<option value="">--- Escolha um Atributo ---</option>`;

            ATTRIBUTES.forEach(attrKey => {
                const attrLabel = ATTRIBUTE_MAP[attrKey] || attrKey;
                selectHtml += `<option value="${attrKey}">+1 ${attrLabel}</option>`;
            });
            selectHtml += `</select>`;
            raceChoicesArea.insertAdjacentHTML('beforeend', selectHtml);
        }

        raceChoicesArea.querySelectorAll('.race-choice-select').forEach(select => {
            select.addEventListener('change', updateRaceChoicesInput);
        });
        updateRaceChoicesInput();
    }

    function updateRaceChoicesInput() {
        const selectedChoices = [];
        raceChoicesArea.querySelectorAll('.race-choice-select').forEach(select => {
            if (select.value) {
                selectedChoices.push({ attribute: select.value, value: 1 });
            }
        });
        raceChoicesInput.value = JSON.stringify(selectedChoices);
        updateFinalAttributesAndChart();
    }
    // ... FIM das funções sem alteração (Race Details) ...

    function updateClassDetails() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        const hasClass = selected && selected.value;

        classOptionsCard.classList.toggle('d-none', !hasClass);

        if (!hasClass) {
             // Limpa campos se nenhuma classe for selecionada
            classeDescricaoDisplay.textContent = '';
            classeMagiaDisplay.textContent = '';
            dadoVidaDisplay.textContent = 'd6';
            hpRollResultDisplay.textContent = '0';
            rolledHpInput.value = '';
            fixedEquipmentDisplay.innerHTML = '';
            equipmentOptionsContainer.innerHTML = '<p class="small text-muted">Selecione uma classe para ver as opções.</p>';
            skillOptionsContainer.classList.add('d-none');
            fixedSkillsDisplay.innerHTML = '';
            updateSelectedEquipment();
            updateSelectedSkills(); // Limpa as escolhas
            return;
        }

        const descricao = selected.getAttribute('data-descricao') || '';
        const usaMagia = selected.getAttribute('data-usa-magia') || 'Não';
        classeDescricaoDisplay.textContent = descricao;
        classeMagiaDisplay.textContent = `Usa Magia: ${usaMagia}`;

        const dadoVida = selected.getAttribute('data-dado-vida') || 'd6';
        dadoVidaDisplay.textContent = dadoVida.toUpperCase();
        hpRollResultDisplay.textContent = '0';
        rolledHpInput.value = '';

        const equip = parseDataAttributeString(selected.getAttribute('data-class-equipment') || null);
        renderEquipmentOptions(equip);

        const skills = parseDataAttributeString(selected.getAttribute('data-class-skills') || null);
        renderSkillOptions(skills);

        updateFinalAttributesAndChart();
    }

    function updateOrigemDetails() {
        const selected = origemSelect.options[origemSelect.selectedIndex];
        const hasOrigem = selected && selected.value;

        if (!hasOrigem) {
            origemDescricaoDisplay.textContent = '';
            origemResourcesDisplay.classList.add('d-none');
            origemResourcesDisplay.innerHTML = '';
            // Origem removida, deve recalcular as perícias para remover o bônus de origem
            updateSelectedSkills();
            return;
        }

        const descricao = selected.getAttribute('data-descricao') || '';
        const resources = parseDataAttributeString(selected.getAttribute('data-resources') || null);

        origemDescricaoDisplay.textContent = descricao;

        if (Object.keys(resources).length > 0) {
            let html = '<h6 class="small fw-bold border-bottom pb-1">Recursos de Origem:</h6><ul class="list-unstyled mb-0">';
            for (const [key, value] of Object.entries(resources)) {
                html += `<li><strong class="text-dark">${key}:</strong> ${value}</li>`;
            }
            html += '</ul>';
            origemResourcesDisplay.innerHTML = html;
            origemResourcesDisplay.classList.remove('d-none');
        } else {
            origemResourcesDisplay.classList.add('d-none');
        }

        updateSelectedSkills(); // A origem pode adicionar perícias fixas
    }

    // ... Funções handleHpRoll, renderEquipmentOptions, updateSelectedEquipment (sem alterações) ...
    // --- ROLAGEM DE HP ---
    function handleHpRoll() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        if (!selected || !selected.value) return;
        const dado = selected.getAttribute('data-dado-vida') || 'd6';
        const roll = rollDice(dado);
        hpRollResultDisplay.textContent = roll;
        rolledHpInput.value = roll;
        validateBaseDistribution();
    }

    // --- EQUIPAMENTO ---
    function renderEquipmentOptions(equipData) {
        fixedEquipmentDisplay.innerHTML = '';
        equipmentOptionsContainer.innerHTML = '';
        selectedEquipmentInput.value = '';

        const fixed = equipData.fixas || [];
        const options = equipData.opcoes || [];

        if (fixed.length > 0) {
            fixedEquipmentDisplay.innerHTML = `<p class="small fw-bold mb-1">Equipamento Fixo:</p><p class="small text-dark">${fixed.join(', ')}</p>`;
        } else {
             fixedEquipmentDisplay.innerHTML = `<p class="small text-muted">Nenhum equipamento fixo.</p>`;
        }


        if (!Array.isArray(options) || options.length === 0) {
            equipmentOptionsContainer.innerHTML = '<p class="small text-muted">Nenhum equipamento inicial de escolha fornecido por esta classe.</p>';
            updateSelectedEquipment();
            return;
        }

        options.forEach((group, idx) => {
            const isChoiceGroup = group.instrucao && Array.isArray(group.opcoes);
            const instr = isChoiceGroup ? group.instrucao : `Escolha um item do Grupo ${idx+1}`;
            const groupOptions = isChoiceGroup ? group.opcoes : group;

            const groupKey = `equipment_group_${idx}`;
            let html = `<div class="p-3 border rounded"><h6 class="small fw-bold mb-2">${instr}</h6>`;

            const items = Array.isArray(groupOptions) ? groupOptions : (Array.isArray(groupOptions.opcoes) ? groupOptions.opcoes : [groupOptions]);

            items.forEach((item, i) => {
                const itemLabel = typeof item === 'object' && item.nome ? item.nome : item;
                const itemValue = typeof item === 'object' && item.item_id ? item.item_id : item;
                const id = `${groupKey}_${i}`;
                const checked = i === 0 ? 'checked' : '';

                html += `
                    <div class="form-check small">
                        <input class="form-check-input equipment-choice" type="radio" name="${groupKey}" id="${id}" value="${itemValue}" ${checked}>
                        <label class="form-check-label" for="${id}">${itemLabel}</label>
                    </div>
                `;
            });
            html += `</div>`;
            equipmentOptionsContainer.insertAdjacentHTML('beforeend', html);
        });

        equipmentOptionsContainer.querySelectorAll('.equipment-choice').forEach(r => r.addEventListener('change', updateSelectedEquipment));
        updateSelectedEquipment();
    }

    function updateSelectedEquipment() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        if (!selected || !selected.value) {
            selectedEquipmentInput.value = '';
            return;
        }

        const equipData = parseDataAttributeString(selected.getAttribute('data-class-equipment') || null);
        const fixed = equipData.fixas || [];
        const choices = {};
        let allChoicesMade = true;
        let choiceGroupsCount = 0;

        equipmentOptionsContainer.querySelectorAll('div.p-3').forEach(div => {
            const firstInput = div.querySelector('input');
            if (!firstInput) return;

            choiceGroupsCount++;

            const name = firstInput.name;
            const selectedRadio = div.querySelector(`input[name="${name}"]:checked`);

            if (selectedRadio) {
                choices[name] = selectedRadio.value;
            } else {
                allChoicesMade = false;
            }
        });

        if (choiceGroupsCount === 0) allChoicesMade = true;

        selectedEquipmentInput.value = JSON.stringify({
            fixas: fixed,
            escolhas: choices
        });

        validateBaseDistribution(allChoicesMade, checkSkillRequirements());
    }
    // ... FIM das funções sem alteração (HP e Equipment) ...

    // --- PERÍCIAS DE CLASSE E ORIGEM ---
    function renderSkillOptions(skills) {
        skillOptionsContainer.classList.add('d-none');
        skillChoiceCheckboxes.innerHTML = '';
        skillChoiceAlert.classList.add('d-none');
        fixedSkillsDisplay.innerHTML = '';

        if (!skills || typeof skills !== 'object') {
            return;
        }

        const fixedSkills = skills.fixas || [];
        const choiceSkills = skills.lista || [];
        const choicesCount = parseInt(skills.escolha) || 0;

        // Perícias Fixas de Origem (Para exibir no bloco de info fixo)
        const origemOption = origemSelect.options[origemSelect.selectedIndex];
        const origemSkills = parseDataAttributeString(origemOption?.getAttribute('data-skills') || null);
        // As perícias de Origem estão como chaves no objeto de Origem:
        const fixedOrigem = Object.keys(origemSkills);

        const allFixedSkills = [...new Set([...fixedSkills, ...fixedOrigem])];

        if (allFixedSkills.length > 0) {
            fixedSkillsDisplay.innerHTML = `<p class="small fw-bold">Perícias Fixas (Classe e Origem):</p><p class="small text-dark">${allFixedSkills.join(', ')}</p>`;
        } else {
            fixedSkillsDisplay.innerHTML = `<p class="small text-muted">Nenhuma perícia fixa.</p>`;
        }

        if (choicesCount > 0 && Array.isArray(choiceSkills) && choiceSkills.length > 0) {
            skillOptionsContainer.classList.remove('d-none');
            skillChoiceInstructions.textContent = `Escolha ${choicesCount} perícias da lista abaixo:`;

            choiceSkills.forEach(skill => {
                const id = `skill_choice_${skill.replace(/\s/g, '_')}`;
                // Desabilita se a perícia já for fixa por Classe ou Origem
                const isDisabled = allFixedSkills.includes(skill);
                const disabledAttr = isDisabled ? 'disabled' : '';

                skillChoiceCheckboxes.insertAdjacentHTML('beforeend', `
                    <div class="form-check small">
                        <input class="form-check-input skill-choice-checkbox" type="checkbox" id="${id}" value="${skill}" ${disabledAttr}>
                        <label class="form-check-label" for="${id}">${skill} ${isDisabled ? '(Já é fixa)' : ''}</label>
                    </div>
                `);
            });
            skillChoiceCheckboxes.querySelectorAll('.skill-choice-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedSkills));
        }

        updateSelectedSkills();
    }

    function checkSkillRequirements() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        if (!selected || !selected.value) return true;

        const skills = parseDataAttributeString(selected.getAttribute('data-class-skills') || null);
        const choicesCount = parseInt(skills.escolha) || 0;

        if (choicesCount === 0) return true;

        const checkedCount = skillChoiceCheckboxes.querySelectorAll('.skill-choice-checkbox:checked').length;
        const requiredMet = checkedCount === choicesCount;

        skillChoiceAlert.classList.toggle('d-none', requiredMet);
        if (!requiredMet) {
             skillChoiceAlert.textContent = `Você deve selecionar exatamente ${choicesCount} perícia(s). Selecionado: ${checkedCount}`;
        }

        return requiredMet;
    }


    function updateSelectedSkills() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        if (!selected || !selected.value) {
            selectedSkillsInput.value = '[]';
            updateSkillCalculation(); // Recalcula com 0 proficiência
            return;
        }

        const skillsData = parseDataAttributeString(selected.getAttribute('data-class-skills') || null);
        const fixedSkills = skillsData.fixas || [];

        const origemOption = origemSelect.options[origemSelect.selectedIndex];
        const origemSkills = parseDataAttributeString(origemOption?.getAttribute('data-skills') || null);
        const fixedOrigem = Object.keys(origemSkills);

        const chosenSkills = [];
        skillChoiceCheckboxes.querySelectorAll('.skill-choice-checkbox:checked').forEach(cb => {
            chosenSkills.push(cb.value);
        });

        // Junta as fixas de classe, fixas de origem e as escolhidas
        const allSkills = [...new Set([...fixedSkills, ...fixedOrigem, ...chosenSkills])];

        selectedSkillsInput.value = JSON.stringify(allSkills);

        checkSkillRequirements();
        validateBaseDistribution(null, checkSkillRequirements());
        updateSkillCalculation(); // Recalcula com as perícias atualizadas
    }

    // --- CÁLCULO E EXIBIÇÃO DE PERÍCIAS FINAIS (CORRIGIDO PARA MOSTRAR TODAS) ---

    function updateSkillCalculation(finalModifiers = null, finalScores = null) {
        // Obter Modificadores Finais
        if (!finalModifiers || !finalScores) {
            const finalAttrData = finalAttributesJsonInput.value ? JSON.parse(finalAttributesJsonInput.value) : null;
            if (finalAttrData && finalAttrData.final) {
                finalScores = finalAttrData.final;
                finalModifiers = {};
                Object.keys(finalScores).forEach(key => {
                    finalModifiers[key] = calculateModifier(finalScores[key]);
                });
            } else {
                periciasListDisplay.innerHTML = '<p class="text-muted small">Complete a distribuição de atributos e selecione uma classe para calcular as perícias.</p>';
                return;
            }
        }

        if (ALL_SYSTEM_SKILLS.length === 0) {
            periciasListDisplay.innerHTML = '<p class="text-muted small">Nenhuma perícia cadastrada no sistema.</p>';
            return;
        }

        // Proficiência
        const proficienciaBonus = parseInt(proficienciaBonusSelect.value) || 0;
        proficienciaBonusInput.value = proficienciaBonus;

        // Perícias proficientes selecionadas
        const proficientSkills = JSON.parse(selectedSkillsInput.value || '[]');

        let html = '';

        ALL_SYSTEM_SKILLS.forEach(skill => {
            const attrKey = skill.atributo_relacionado ? skill.atributo_relacionado.toLowerCase() : '';
            const attrLabelShort = (ATTRIBUTE_MAP[attrKey] || attrKey).substr(0,3).toUpperCase();

            const attrScore = finalScores[attrKey] !== undefined ? finalScores[attrKey] : 0;
            const attrMod = finalModifiers[attrKey] !== undefined ? finalModifiers[attrKey] : 0;

            const isProficient = proficientSkills.includes(skill.nome);
            const pb = isProficient ? proficienciaBonus : 0;
            const totalSkillBonus = attrMod + pb;
            const sign = totalSkillBonus >= 0 ? '+' : '';

            let skillClass = 'text-secondary';
            let statusBadge = '<span class="badge bg-light text-muted border border-secondary border-opacity-25">Básica</span>';

            if (isProficient) {
                skillClass = 'text-dark fw-bold';
                statusBadge = '<span class="badge bg-success">Proficiente</span>';
            }

            html += `
                <div class="col-12 col-md-6">
                    <div class="p-2 border rounded ${isProficient ? 'border-success bg-success-subtle' : 'bg-light'}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="${skillClass} me-2">${skill.nome}</span>
                            ${statusBadge}
                        </div>
                        <div class="small text-muted mt-1">
                            <span class="me-3">${attrLabelShort} Mod: ${attrMod >= 0 ? '+' : ''}${attrMod}</span>
                            <span class="fw-bold text-dark">Total: ${sign}${totalSkillBonus}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        periciasListDisplay.innerHTML = html;
    }


    // --- VALIDAÇÃO E SUBMISSÃO ---
    function validateBaseDistribution(equipmentComplete = null, skillsComplete = null) {
        const isPointBuy = methodPointBuy.checked && !methodPointBuy.disabled;
        const totalCost = isPointBuy ? calculatePointCost() : 0;
        const hasClass = classeSelect.value;
        const hpRolled = rolledHpInput.value !== '';
        const hasName = nomeInput.value.trim() !== '';

        const isCostValid = !isPointBuy || totalCost <= MAX_POINTS;

        if (equipmentComplete === null) {
            equipmentComplete = true;
            equipmentOptionsContainer.querySelectorAll('div.p-3').forEach(div => {
                const firstInput = div.querySelector('input');
                if (!firstInput) return;
                const name = firstInput.name;
                const selectedRadio = div.querySelector(`input[name="${name}"]:checked`);
                if (!selectedRadio) equipmentComplete = false;
            });
        }

        if (skillsComplete === null) {
            skillsComplete = checkSkillRequirements();
        }

        const raceOption = racaSelect.options[racaSelect.selectedIndex];
        const tipoBonus = raceOption?.getAttribute('data-tipo-bonus') || 'flat';
        const bonusLivre = parseInt(raceOption?.getAttribute('data-bonus-livre') || 0);
        let raceChoicesComplete = true;

        if (tipoBonus !== 'flat' && bonusLivre > 0) {
            raceChoicesComplete = JSON.parse(raceChoicesInput.value || '[]').length === bonusLivre;
        }

        const isHpValid = !hasClass || hpRollSection.classList.contains('d-none') || hpRolled;

        const isValid = isCostValid && hasClass && isHpValid && equipmentComplete && skillsComplete && raceChoicesComplete && hasName;

        submitButton.disabled = !isValid;
        submitButton.textContent = isValid ? 'Criar Personagem Lendário' : 'Preencha todos os campos obrigatórios';
    }

    // ... Função updateChart (sem alterações) ...
    function updateChart(finalScores) {
        const labels = ATTRIBUTES.map(key => (ATTRIBUTE_MAP[key]||key).substr(0,3).toUpperCase());
        const data = ATTRIBUTES.map(key => finalScores[key] || 0);
        const backgroundColors = [
            'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'
        ];

        const chartData = {
            labels: labels,
            datasets: [{
                label: 'Atributos Finais',
                data: data,
                backgroundColor: backgroundColors.slice(0, ATTRIBUTES.length),
                borderColor: backgroundColors.slice(0, ATTRIBUTES.length).map(c => c.replace('0.7', '1')),
                borderWidth: 1
            }]
        };

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { display: false },
                    suggestedMin: 0,
                    suggestedMax: 20,
                    pointLabels: { font: { size: 12 } },
                    ticks: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                datalabels: {
                    formatter: (value) => value,
                    color: '#fff',
                    font: { weight: 'bold' }
                }
            }
        };

        if (attributeChart) {
            attributeChart.data = chartData;
            attributeChart.update();
        } else {
            const ctx = document.getElementById('attribute-chart').getContext('2d');
            attributeChart = new Chart(ctx, {
                type: 'radar',
                data: chartData,
                options: chartOptions,
                plugins: [ChartDataLabels]
            });
        }
    }
    // ... FIM da função updateChart ...

    // --- INICIALIZAÇÃO E LISTENERS GLOBAIS ---

    // Função de Randomização (Corrigida)
    randomizeButton.addEventListener('click', () => {
        // 1. Informações Básicas
        nomeInput.value = ['Aria', 'Kael', 'Lyra', 'Zarok', 'Faelar'][Math.floor(Math.random() * 5)] + ' ' + Math.floor(Math.random() * 999);

        // Seleção aleatória de raça/classe/origem (ignora a primeira opção que é "(Nenhuma)")
        if (racaSelect.options.length > 1) racaSelect.selectedIndex = Math.floor(Math.random() * (racaSelect.options.length - 1)) + 1;
        if (classeSelect.options.length > 1) classeSelect.selectedIndex = Math.floor(Math.random() * (classeSelect.options.length - 1)) + 1;
        if (origemSelect.options.length > 1) origemSelect.selectedIndex = Math.floor(Math.random() * origemSelect.options.length); // 0 pode ser "(Opcional)"

        // 2. Distribuição
        methodManual.checked = true; // Força para Manual/Rolagem para simplificar a randomização
        handleDistributionMethodChange();

        const rolledValues = [15, 14, 13, 12, 10, 8].sort(() => Math.random() - 0.5); // Valores D&D padrão
        document.querySelectorAll('.manual-score').forEach((input, index) => {
            input.value = rolledValues[index] || 10;
            input.dispatchEvent(new Event('input'));
        });

        // 3. Opções de Raça, Classe, Origem
        racaSelect.dispatchEvent(new Event('change'));
        classeSelect.dispatchEvent(new Event('change'));
        origemSelect.dispatchEvent(new Event('change'));

        // Se houver opções de raça livre, escolhe o primeiro atributo disponível para todas
        if (!raceChoiceContainer.classList.contains('d-none')) {
            raceChoicesArea.querySelectorAll('.race-choice-select').forEach((select, index) => {
                if (select.options.length > 1) {
                    select.selectedIndex = (index % (ATTRIBUTES.length)) + 1; // Escolhe atributos diferentes (cíclico)
                    select.dispatchEvent(new Event('change'));
                }
            });
        }

        // Seleção aleatória de equipamentos
        equipmentOptionsContainer.querySelectorAll('div.p-3').forEach(div => {
            const radios = div.querySelectorAll('input[type="radio"]');
            if (radios.length > 0) {
                const randomIndex = Math.floor(Math.random() * radios.length);
                radios[randomIndex].checked = true;
                radios[randomIndex].dispatchEvent(new Event('change'));
            }
        });
        updateSelectedEquipment();

        // Seleção aleatória de perícias
        const skillsData = parseDataAttributeString(classeSelect.options[classeSelect.selectedIndex]?.getAttribute('data-class-skills') || null);
        const choicesCount = parseInt(skillsData.escolha) || 0;
        const availableCheckboxes = Array.from(skillChoiceCheckboxes.querySelectorAll('.skill-choice-checkbox:not(:disabled)'));

        // Desmarca tudo primeiro
        availableCheckboxes.forEach(cb => { cb.checked = false; });

        // Sorteia as escolhas
        const shuffledSkills = availableCheckboxes.sort(() => 0.5 - Math.random());
        shuffledSkills.slice(0, choicesCount).forEach(cb => { cb.checked = true; });
        updateSelectedSkills();


        // Rolagem de HP
        if (rolledHpInput.value === '') {
            rollHpButton.click();
        }

        validateBaseDistribution();
    });

    // Inicialização principal
    setAttributesFromSistema();

    // Listeners para mudanças
    methodPointBuy.addEventListener('change', handleDistributionMethodChange);
    methodManual.addEventListener('change', handleDistributionMethodChange);

    racaSelect.addEventListener('change', updateRaceDetails);
    classeSelect.addEventListener('change', updateClassDetails);
    origemSelect.addEventListener('change', updateOrigemDetails);

    // NOVO LISTENER: Mudança no Modificador de Proficiência
    proficienciaBonusSelect.addEventListener('change', updateSkillCalculation);


    rollHpButton.addEventListener('click', handleHpRoll);
    nomeInput.addEventListener('input', validateBaseDistribution);

    updateRaceDetails();
    updateClassDetails();
    updateOrigemDetails();
    updateSkillCalculation();
    validateBaseDistribution();
});
</script>
@endif
@endsection
