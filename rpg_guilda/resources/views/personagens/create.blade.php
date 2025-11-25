@extends('layouts.app')

@section('title', 'Criar Novo Personagem')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>

<div class="container my-5">
    <div class="card shadow-lg border-top border-5 border-primary rounded-3">
        <div class="card-body p-4 p-md-5">
            <h1 class="h2 fw-bolder text-dark mb-4 border-bottom pb-3">Crie Seu Herói - {{ $campanha->nome ?? 'Nova Campanha' }}</h1>
            <p class="text-muted mb-4">Personalize a ficha do seu personagem ou deixe o destino decidir.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('personagens.store') }}" method="POST" id="character-form">
                @csrf
                <input type="hidden" name="campanha_id" value="{{ $campanha->id ?? 1 }}">
                <input type="hidden" name="sistema_id" id="sistema_id_input" value="{{ $campanha->sistema->id ?? ($sistemas->first()->id ?? 1) }}">

                {{-- campos auxiliares que serão preenchidos pelo JS --}}
                <input type="hidden" name="atributos" id="finalAttributesJsonInput">
                <input type="hidden" name="race_choices" id="raceChoicesInput">
                <input type="hidden" name="selected_equipment" id="selectedEquipmentInput">
                <input type="hidden" name="selected_skills" id="selectedSkillsInput">
                <input type="hidden" name="rolled_hp" id="rolledHpInput">

                <div class="row g-4">
                    <div class="col-12 mb-3">
                        <label for="sistema_select" class="form-label">Sistema</label>
                        <select id="sistema_select" class="form-select mb-2">
                            @foreach($sistemas as $s)
                                <option value="{{ $s->id }}"
                                    data-atributos='@json(json_decode($s->atributos, true) ?? [])'
                                    data-formula-pv="{{ $s->formula_pontos_vida ?? '' }}"
                                >{{ $s->nome }}</option>
                            @endforeach
                        </select>
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

                                <div class="mb-3">
                                    <label for="raca_id" class="form-label">Raça</label>
                                    <select name="raca_id" id="raca_id" required class="form-select">
                                        <option value="">(Nenhuma)</option>
                                        @foreach ($racas as $raca)
                                            <option
                                                value="{{ $raca->id }}"
                                                data-bonus='@json($raca->modificadores_atributos ?? (is_string($raca->modificadores_atributos) ? json_decode($raca->modificadores_atributos,true) : []))'
                                                data-tipo-bonus="{{ $raca->tipo_bonus ?? 'flat' }}"
                                                data-bonus-livre="{{ $raca->bonus_livre ?? 0 }}">
                                                {{ $raca->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="race-choice-container" class="mt-3 p-3 bg-warning bg-opacity-10 rounded border border-warning d-none">
                                    <h6 class="small fw-bold">Escolhas de Bônus Raciais:</h6>
                                    <div id="race-choices-area"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="classe_id" class="form-label">Classe</label>
                                    <select name="classe_id" id="classe_id" required class="form-select">
                                        <option value="">(Nenhuma)</option>

                                       @foreach ($classes as $classe)

                                            @php
                                                // 1. Decodificar qualquer formato vindo do banco
                                                $equipRaw = $classe->equipamento_inicial;

                                                if (is_string($equipRaw)) {
                                                    $equipRaw = json_decode($equipRaw, true);
                                                }

                                                if (!is_array($equipRaw)) {
                                                    $equipRaw = [];
                                                }

                                                // 2. Se não tiver fixas/opcoes, tentar identificar automaticamente
                                                $fixas = $equipRaw['fixas'] ?? [];

                                                // Se o dev salvou só uma lista simples, tratar como "fixas"
                                                if (empty($fixas) && array_is_list($equipRaw)) {
                                                    $fixas = $equipRaw;
                                                }

                                                $opcoes = $equipRaw['opcoes'] ?? [];

                                                // Estrutura final limpa
                                                $equipFormatado = [
                                                    'fixas'  => $fixas,
                                                    'opcoes' => $opcoes,
                                                ];
                                            @endphp

                                            <option
                                                value="{{ $classe->id }}"
                                                data-class-bases='@json($classe->atributos_bonus)'
                                                data-class-skills='@json($classe->pericias_iniciais)'
                                                data-class-equipment='@json($equipFormatado)'
                                                data-dado-vida="{{ $classe->dado_vida ?? 'd6' }}"
                                            >
                                                {{ $classe->nome }}
                                            </option>

                                        @endforeach

                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="origem_id" class="form-label">Origem / Background</label>
                                    <select name="origem_id" id="origem_id" class="form-select">
                                        <option value="">(Opcional)</option>
                                        @foreach ($origens as $origem)
                                            <option value="{{ $origem->id }}">{{ $origem->nome }}</option>
                                        @endforeach
                                    </select>
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
                                    <label class="form-label">Método de Distribuição:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="distribution_method" id="method-point-buy" value="point_buy" checked>
                                        <label class="form-check-label" for="method-point-buy">Compra de Pontos (27)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="distribution_method" id="method-manual" value="manual">
                                        <label class="form-check-label" for="method-manual">Manual (Rolagem)</label>
                                    </div>
                                </div>

                                <div id="point-buy-ui" class="border p-3 rounded bg-light">
                                    <p class="mb-2">Pontos Restantes: <span id="points-remaining" class="fw-bold text-success">27</span> / 27</p>
                                    <div id="attribute-list-container" class="d-flex flex-column gap-2"></div>
                                </div>

                                <div id="manual-ui" class="border p-3 rounded bg-light d-none">
                                    <h6 class="fw-bold">Insira seus valores rolados (ex: 15, 14, 13...)</h6>
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
                                <h6 class="fw-semibold text-danger">Vida Inicial (PV)</h6>
                                <p class="small text-muted mb-2">Sua classe usa um <strong id="dado-vida-display">d6</strong>. Rolar valor:</p>
                                <div class="d-flex align-items-center mb-4">
                                    <button type="button" id="roll-hp-button" class="btn btn-sm btn-danger me-3">Rolar Dado de Vida</button>
                                    <div class="small">Valor do Dado Rolado: <strong id="hp-roll-result" class="text-danger">0</strong></div>
                                </div>

                                <hr>

                                <h6 class="fw-semibold text-danger mt-4">Equipamento Inicial</h6>
                                <div id="equipment-options-container" class="d-flex flex-column gap-3 mb-4">
                                    <p class="small text-muted">Selecione uma opção de cada grupo.</p>
                                </div>

                                <hr>

                                <h6 class="fw-semibold text-danger mt-4">Escolha de Perícias</h6>
                                <div id="skill-options-container" class="mb-3 d-none">
                                    <p id="skill-choice-instructions" class="small text-muted mb-2"></p>
                                    <div id="skill-choice-checkboxes" class="d-flex flex-column gap-2"></div>
                                    <div id="skill-choice-alert" class="alert alert-warning small mt-2 d-none"></div>
                                </div>
                                <div id="fixed-skills-info" class="mb-3">
                                    <p class="small text-muted" id="fixed-skills-display"></p>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow border-primary border-opacity-25">
                            <div class="card-header bg-primary text-white">
                                <h5 class="fw-bold mb-0">Dashboard de Atributos Finais</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div id="final-attributes-display" class="d-flex flex-column gap-2"></div>
                                    </div>
                                    <div class="col-md-5" style="height:260px;">
                                        <canvas id="attribute-chart"></canvas>
                                    </div>
                                </div>
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

<script>
(function(){
    // CONSTANTES
    const ATTRIBUTE_MAP = { forca: 'FOR', destreza: 'DES', constituicao: 'CON', inteligencia: 'INT', sabedoria: 'SAB', carisma: 'CAR' };
    let ATTRIBUTES = Object.keys(ATTRIBUTE_MAP);
    const ATTRIBUTE_COSTS = {8:0,9:1,10:2,11:3,12:4,13:5,14:7,15:9};
    const MAX_POINTS = 27;

    // DOM
    const sistemaSelect = document.getElementById('sistema_select');
    const sistemaIdInput = document.getElementById('sistema_id_input');
    const racaSelect = document.getElementById('raca_id');
    const classeSelect = document.getElementById('classe_id');
    const origemSelect = document.getElementById('origem_id');
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

    let attributeChart;

    // UTILS
    function parseDataAttributeString(str) {
        if (!str) return {};
        try { return typeof str === 'object' ? str : JSON.parse(str); } catch(e) { return {}; }
    }
    function rollDice(diceString) {
        const m = String(diceString).match(/d(\d+)/i);
        if (!m) return 0;
        const sides = parseInt(m[1],10);
        return Math.floor(Math.random()*sides)+1;
    }

    // Inicialização de atributos dinâmicos com base no sistema
    function setAttributesFromSistema() {
        const raw = sistemaSelect.options[sistemaSelect.selectedIndex].getAttribute('data-atributos');
        const attrs = parseDataAttributeString(raw);
        // attrs é um objeto como { forca: "Força", destreza: "Destreza", ... }
        if (attrs && Object.keys(attrs).length) {
            // Substitui ATTRIBUTE_MAP e ATTRIBUTES mantendo ordem
            for (const k in attrs) {
                if (!ATTRIBUTE_MAP[k]) ATTRIBUTE_MAP[k] = attrs[k].substr(0,3).toUpperCase();
            }
            ATTRIBUTES = Object.keys(attrs);
        } else {
            ATTRIBUTES = ['forca','destreza','constituicao','inteligencia','sabedoria','carisma'];
        }
    }

    // UI de atributos
    function initializeAttributeInputs() {
        attrListContainer.innerHTML = '';
        attrManualListContainer.innerHTML = '';

        ATTRIBUTES.forEach(attrKey => {
            const attrLabel = ATTRIBUTE_MAP[attrKey] || attrKey.toUpperCase();

            const pointBuyHtml = `
                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                    <span class="fw-bold me-3">${attrLabel}</span>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger me-2 point-buy-btn" data-attr="${attrKey}" data-action="decrease">-</button>
                        <input type="number" class="form-control form-control-sm text-center point-buy-score" data-attr="${attrKey}" value="8" min="8" max="15" style="width: 60px;" readonly>
                        <button type="button" class="btn btn-sm btn-outline-success ms-2 point-buy-btn" data-attr="${attrKey}" data-action="increase">+</button>
                    </div>
                </div>
            `;
            attrListContainer.insertAdjacentHTML('beforeend', pointBuyHtml);

            const manualHtml = `
                <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                    <label class="fw-bold me-3">${attrLabel}</label>
                    <input type="number" class="form-control form-control-sm text-center manual-score" data-attr="${attrKey}" value="10" min="1" max="30" style="width: 80px;">
                </div>
            `;
            attrManualListContainer.insertAdjacentHTML('beforeend', manualHtml);
        });

        // Atualiza display de pontos
        pointsRemainingDisplay.textContent = MAX_POINTS - calculatePointCost();
    }

    function handleDistributionMethodChange() {
        const isPointBuy = methodPointBuy.checked;
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
        pointsRemainingDisplay.textContent = MAX_POINTS - calculatePointCost();
        pointsRemainingDisplay.classList.toggle('text-danger', calculatePointCost() > MAX_POINTS);
        pointsRemainingDisplay.classList.toggle('text-success', calculatePointCost() <= MAX_POINTS);
        updateFinalAttributesAndChart();
    }

    function getBaseScores() {
        const obj = {};
        const isPointBuy = methodPointBuy.checked;
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

        // race fixed bonuses
        const raceOption = racaSelect.options[racaSelect.selectedIndex];
        const raceFixedBonus = parseDataAttributeString(raceOption?.getAttribute('data-bonus') || '{}');

        // class bonuses
        const classOption = classeSelect.options[classeSelect.selectedIndex];
        const classBonus = parseDataAttributeString(classOption?.getAttribute('data-class-bases') || '{}');

        // race choices (from hidden input)
        const raceChoices = JSON.parse(raceChoicesInput.value || '[]');
        const raceChoiceMap = {};
        if (Array.isArray(raceChoices)) {
            raceChoices.forEach(c => { raceChoiceMap[c.attribute] = (raceChoiceMap[c.attribute] || 0) + (c.value || 0); });
        }

        ATTRIBUTES.forEach(key => {
            const b = base[key] || 0;
            const totalBonus = (parseInt(raceFixedBonus[key] || 0) || 0) + (parseInt(raceChoiceMap[key] || 0) || 0) + (parseInt(classBonus[key] || 0) || 0);
            const finalScore = b + totalBonus;
            final[key] = finalScore;
            html += `<div class="p-2 border rounded d-flex justify-content-between align-items-center">
                        <strong class="text-uppercase me-2">${(ATTRIBUTE_MAP[key]||key).substr(0,3)}</strong>
                        <span class="h5 fw-bolder text-primary ms-1">${finalScore}</span>
                    </div>`;
        });

        finalAttributesDisplay.innerHTML = html;
        finalAttributesJsonInput.value = JSON.stringify({ base, final });

        updateChart(final);
        validateBaseDistribution();
    }

    function updateClassDetails() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        if (!selected) return;
        classOptionsCard.classList.remove('d-none');

        const dadoVida = selected.getAttribute('data-dado-vida') || 'd6';
        dadoVidaDisplay.textContent = dadoVida.toUpperCase();

        hpRollResultDisplay.textContent = '0';
        rolledHpInput.value = '';

        // equipment and skills
        const equip = parseDataAttributeString(selected.getAttribute('data-class-equipment') || '[]');
        renderEquipmentOptions(equip);

        const skills = parseDataAttributeString(selected.getAttribute('data-class-skills') || '{"tipo":"fixa","quantidade":0,"lista":[]}');
        renderSkillOptions(skills);

        updateFinalAttributesAndChart();
    }

    function handleHpRoll() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        if (!selected) return;
        const dado = selected.getAttribute('data-dado-vida') || 'd6';
        const roll = rollDice(dado);
        hpRollResultDisplay.textContent = roll;
        rolledHpInput.value = roll;
        validateBaseDistribution();
    }

    function renderEquipmentOptions(equipmentData) {
        equipmentOptionsContainer.innerHTML = '';
        selectedEquipmentInput.value = '';
        if (!Array.isArray(equipmentData) || equipmentData.length === 0) {
            equipmentOptionsContainer.innerHTML = '<p class="small text-muted">Nenhum equipamento inicial fornecido por esta classe.</p>';
            return;
        }

        equipmentData.forEach((group, idx) => {
            const groupKey = `equipment_group_${idx}`;
            const instr = group.instrucao || `Escolha um item do Grupo ${idx+1}`;
            let html = `<div class="p-3 border rounded"><h6 class="small fw-bold mb-2">${instr}</h6>`;
            (group.opcoes || []).forEach((item, i) => {
                const id = `${groupKey}_${i}`;
                const checked = i === 0 ? 'checked' : '';
                html += `
                    <div class="form-check small">
                        <input class="form-check-input equipment-choice" type="radio" name="${groupKey}" id="${id}" value="${item}" ${checked}>
                        <label class="form-check-label" for="${id}">${item}</label>
                    </div>
                `;
            });
            html += `</div>`;
            equipmentOptionsContainer.insertAdjacentHTML('beforeend', html);
        });

        // listeners
        equipmentOptionsContainer.querySelectorAll('.equipment-choice').forEach(r => r.addEventListener('change', updateSelectedEquipment));
        updateSelectedEquipment();
    }

    function updateSelectedEquipment() {
        const groups = {};
        equipmentOptionsContainer.querySelectorAll('div.p-3').forEach(div => {
            const input = div.querySelector('input');
            if (!input) return;
            const name = input.name;
            const selected = div.querySelector(`input[name="${name}"]:checked`);
            if (selected) groups[name] = selected.value;
        });
        selectedEquipmentInput.value = JSON.stringify(groups);
        validateBaseDistribution();
    }

    function renderSkillOptions(skillsData) {
        skillChoiceCheckboxes.innerHTML = '';
        selectedSkillsInput.value = '';
        fixedSkillsDisplay.innerHTML = '';

        const tipo = skillsData.tipo || 'fixa';
        const quantidade = parseInt(skillsData.quantidade || 0);
        const lista = skillsData.lista || [];

        if (tipo === 'fixa' && lista.length) {
            fixedSkillsDisplay.innerHTML = `Perícias Fixas: <span class="fw-bold text-success">${lista.join(', ')}</span>.`;
            selectedSkillsInput.value = JSON.stringify(lista);
        } else if (tipo === 'escolha' && quantidade > 0 && lista.length) {
            skillOptionsContainer.classList.remove('d-none');
            skillChoiceInstructions.textContent = `Escolha ${quantidade} perícia(s) da lista:`;
            lista.forEach(skill => {
                const id = `skill_${skill.toLowerCase().replace(/\s+/g,'_')}`;
                skillChoiceCheckboxes.insertAdjacentHTML('beforeend', `
                    <div class="form-check small">
                        <input class="form-check-input skill-choice" type="checkbox" id="${id}" value="${skill}" data-limit="${quantidade}">
                        <label class="form-check-label" for="${id}">${skill}</label>
                    </div>
                `);
            });
            document.querySelectorAll('.skill-choice').forEach(cb => cb.addEventListener('change', handleSkillChoice));
        } else {
            skillOptionsContainer.classList.add('d-none');
        }
        validateBaseDistribution();
    }

    function handleSkillChoice(e) {
        const limit = parseInt(e.target.getAttribute('data-limit') || 0);
        const all = Array.from(document.querySelectorAll('.skill-choice'));
        const selected = all.filter(cb => cb.checked);
        if (selected.length > limit) {
            e.target.checked = false;
            skillChoiceAlert.textContent = `Limite atingido! Você só pode escolher ${limit} perícia(s).`;
            skillChoiceAlert.classList.remove('d-none');
        } else {
            skillChoiceAlert.classList.add('d-none');
        }
        updateSelectedSkills();
    }

    function updateSelectedSkills() {
        const chosen = Array.from(document.querySelectorAll('.skill-choice')).filter(cb => cb.checked).map(cb => cb.value);
        selectedSkillsInput.value = JSON.stringify(chosen);
        validateBaseDistribution();
    }

    // Raça
    function updateRaceDetails() {
        const option = racaSelect.options[racaSelect.selectedIndex];
        if (!option) return;
        const free = parseInt(option.getAttribute('data-bonus-livre') || 0);
        const tipo = option.getAttribute('data-tipo-bonus') || 'flat';
        raceChoicesArea.innerHTML = '';
        raceChoicesInput.value = '';

        if (free > 0 && tipo === 'flat') {
            raceChoiceContainer.classList.remove('d-none');
            ATTRIBUTES.forEach(attr => {
                const id = `race_choice_${attr}`;
                raceChoicesArea.insertAdjacentHTML('beforeend', `
                    <div class="form-check small">
                        <input class="form-check-input race-choice" type="checkbox" id="${id}" value="${attr}" data-limit="${free}">
                        <label class="form-check-label" for="${id}">${(ATTRIBUTE_MAP[attr]||attr).substr(0,3)} (+1)</label>
                    </div>
                `);
            });
            document.querySelectorAll('.race-choice').forEach(cb => cb.addEventListener('change', handleRaceChoice));
        } else {
            raceChoiceContainer.classList.add('d-none');
        }
        updateFinalAttributesAndChart();
    }

    function handleRaceChoice(e) {
        const limit = parseInt(e.target.getAttribute('data-limit') || 0);
        const all = Array.from(document.querySelectorAll('.race-choice'));
        const chosen = all.filter(cb => cb.checked);
        if (chosen.length > limit) {
            e.target.checked = false;
        }
        updateSelectedRaceChoices();
        updateFinalAttributesAndChart();
    }

    function updateSelectedRaceChoices() {
        const chosen = Array.from(document.querySelectorAll('.race-choice')).filter(cb => cb.checked).map(cb => ({ attribute: cb.value, value: 1 }));
        raceChoicesInput.value = JSON.stringify(chosen);
    }

    // Chart
    function updateChart(scores) {
        const data = ATTRIBUTES.map(a => scores[a] || 0);
        const labels = ATTRIBUTES.map(a => (ATTRIBUTE_MAP[a] || a).substr(0,3));

        if (attributeChart) {
            attributeChart.data.datasets[0].data = data;
            attributeChart.update();
            return;
        }

        const ctx = document.getElementById('attribute-chart').getContext('2d');
        attributeChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Atributos Finais',
                    data: data,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { r: { suggestedMin: 0, suggestedMax: 20 } },
                plugins: { legend: { display: false }, datalabels: { formatter: v => v } }
            },
            plugins: [ChartDataLabels]
        });
    }

    // validação
    function validateBaseDistribution() {
        // point buy
        if (methodPointBuy.checked && calculatePointCost() > MAX_POINTS) { submitButton.disabled = true; return; }

        // hp
        const hp = parseInt(rolledHpInput.value || 0);
        if (!hp || hp <= 0) { submitButton.disabled = true; return; }

        // skills
        const skillsOption = parseDataAttributeString(classeSelect.options[classeSelect.selectedIndex]?.getAttribute('data-class-skills') || '{}');
        if (skillsOption.tipo === 'escolha') {
            const need = parseInt(skillsOption.quantidade || 0);
            const chosen = JSON.parse(selectedSkillsInput.value || '[]').length;
            if (chosen < need) { submitButton.disabled = true; return; }
        }

        // equipment
        const equipOption = parseDataAttributeString(classeSelect.options[classeSelect.selectedIndex]?.getAttribute('data-class-equipment') || '[]');
        const requiredGroups = Array.isArray(equipOption) ? equipOption.length : 0;
        const chosenEquip = Object.keys(JSON.parse(selectedEquipmentInput.value || '{}')).length;
        if (requiredGroups > 0 && chosenEquip < requiredGroups) { submitButton.disabled = true; return; }

        submitButton.disabled = false;
    }

    // randomize
    function randomizePointBuy() {
        const scores = {};
        ATTRIBUTES.forEach(a => scores[a] = 8);
        let pointsLeft = MAX_POINTS;
        while (pointsLeft > 0) {
            const possible = [];
            for (const a of ATTRIBUTES) {
                if (scores[a] < 15) {
                    const next = scores[a] + 1;
                    const cost = (ATTRIBUTE_COSTS[next]||0) - (ATTRIBUTE_COSTS[scores[a]]||0);
                    if (pointsLeft >= cost) possible.push({a, cost});
                }
            }
            if (!possible.length) break;
            const pick = possible[Math.floor(Math.random()*possible.length)];
            scores[pick.a] += 1;
            pointsLeft -= pick.cost;
        }
        document.querySelectorAll('.point-buy-score').forEach(input => { input.value = scores[input.getAttribute('data-attr')]; });
        pointsRemainingDisplay.textContent = MAX_POINTS - calculatePointCost();
    }

    function randomizeSkillsAndEquipment() {
        // race choices
        const raceOpt = racaSelect.options[racaSelect.selectedIndex];
        const free = parseInt(raceOpt?.getAttribute('data-bonus-livre')||0);
        if (free > 0) {
            const copy = [...ATTRIBUTES];
            const chosen = [];
            for (let i=0;i<free && copy.length;i++){
                const idx = Math.floor(Math.random()*copy.length);
                chosen.push({attribute: copy[idx], value: 1});
                copy.splice(idx,1);
            }
            raceChoicesInput.value = JSON.stringify(chosen);
            document.querySelectorAll('.race-choice').forEach(cb => { cb.checked = chosen.some(c=>c.attribute===cb.value); });
        }

        // roll hp
        handleHpRoll();

        // equipment
        const equip = parseDataAttributeString(classeSelect.options[classeSelect.selectedIndex]?.getAttribute('data-class-equipment') || '[]');
        const randomEquip = {};
        (equip || []).forEach((g,i) => {
            const groupKey = `equipment_group_${i}`;
            const opts = g.opcoes || [];
            if (opts.length) {
                const pick = opts[Math.floor(Math.random()*opts.length)];
                randomEquip[groupKey] = pick;
            }
        });
        selectedEquipmentInput.value = JSON.stringify(randomEquip);
        Object.keys(randomEquip).forEach(groupKey=>{
            const radio = document.querySelector(`input[name="${groupKey}"][value="${randomEquip[groupKey]}"]`);
            if (radio) radio.checked = true;
        });

        // skills
        const sk = parseDataAttributeString(classeSelect.options[classeSelect.selectedIndex]?.getAttribute('data-class-skills') || '{}');
        if (sk.tipo === 'escolha') {
            const qty = parseInt(sk.quantidade||0);
            const pool = [...(sk.lista||[])];
            const chosen = [];
            for (let i=0;i<qty && pool.length;i++){
                const idx = Math.floor(Math.random()*pool.length);
                chosen.push(pool[idx]);
                pool.splice(idx,1);
            }
            selectedSkillsInput.value = JSON.stringify(chosen);
            document.querySelectorAll('.skill-choice').forEach(cb => cb.checked = chosen.includes(cb.value));
        }
    }

    function handleRandomizeCharacter(){
        // pick random sistema/class/race
        const racas = Array.from(racaSelect.options).filter(o=>o.value);
        const classes = Array.from(classeSelect.options).filter(o=>o.value);
        if (!racas.length || !classes.length) { alert('Sem raças ou classes para sortear.'); return; }

        // aplicar random selects
        racaSelect.value = racas[Math.floor(Math.random()*racas.length)].value;
        classeSelect.value = classes[Math.floor(Math.random()*classes.length)].value;

        // atributos
        methodPointBuy.checked = true;
        handleDistributionMethodChange();
        randomizePointBuy();

        // atualizar painel e sortear equipamentos/perícias/hp
        updateRaceDetails();
        updateClassDetails();
        randomizeSkillsAndEquipment();
        updateFinalAttributesAndChart();

        const nomes = ["Aragorn","Gandalf","Elara","Kaelen","Thrain","Seraphina","Ragnar","Lyra"];
        document.getElementById('nome').value = nomes[Math.floor(Math.random()*nomes.length)];
        alert('Personagem aleatório gerado! Verifique as opções e clique Criar.');
    }

    // listeners & init
    document.addEventListener('DOMContentLoaded', () => {
        setAttributesFromSistema();
        initializeAttributeInputs();
        updateRaceDetails();
        updateClassDetails();
        updateFinalAttributesAndChart();

        attrListContainer.addEventListener('click', e => {
            const btn = e.target.closest('.point-buy-btn');
            if (btn) updatePointBuyState(btn.getAttribute('data-attr'), btn.getAttribute('data-action'));
        });

        attrManualListContainer.addEventListener('input', e => { if (e.target.classList.contains('manual-score')) updateFinalAttributesAndChart(); });

        document.querySelectorAll('input[name="distribution_method"]').forEach(r => r.addEventListener('change', handleDistributionMethodChange));
        sistemaSelect.addEventListener('change', () => {
            sistemaIdInput.value = sistemaSelect.value;
            setAttributesFromSistema();
            initializeAttributeInputs();
            updateFinalAttributesAndChart();
        });

        racaSelect.addEventListener('change', updateRaceDetails);
        classeSelect.addEventListener('change', updateClassDetails);

        rollHpButton.addEventListener('click', handleHpRoll);
        randomizeButton.addEventListener('click', handleRandomizeCharacter);

        // delegated listeners for equipment choices (dynamic)
        equipmentOptionsContainer.addEventListener('change', e => {
            if (e.target.classList.contains('equipment-choice')) updateSelectedEquipment();
        });

        // on submit: ensure hidden inputs are populated
        document.getElementById('character-form').addEventListener('submit', e => {
            // finalAttributesJsonInput já populado por updateFinalAttributesAndChart
            // ensure rolled hp set
            if (!rolledHpInput.value) {
                alert('Role o dado de vida (PV) antes de criar o personagem.');
                e.preventDefault();
                return;
            }
        });
    });
})();
</script>
@endsection
