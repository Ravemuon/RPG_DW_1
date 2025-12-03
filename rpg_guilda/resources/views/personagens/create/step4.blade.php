<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criação - Atributos</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome (para ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        .card {
            border-radius: 1rem;
        }
        .card-header {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }
        .atributo-card .card-body {
            padding: 1rem;
        }
        .atributo-input {
            font-size: 1.5rem;
            font-weight: bold;
            height: 3.5rem;
            border-radius: 0.5rem;
        }
        .btn-group-sm .btn {
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
        }
        .point-buy-valid {
            background-color: #d1e7dd; /* green-light */
            color: #0f5132; /* green-dark */
        }
        .point-buy-invalid {
            background-color: #f8d7da; /* red-light */
            color: #842029; /* red-dark */
        }
    </style>
</head>
<body>

<!-- Simulação de variáveis injetadas pelo PHP/Blade -->
<script>
    // Configuração simulada do sistema
    const PERSONAGEM = { nome: 'Ayla', id: 123, overviewRoute: '#overview', atributos: {} };
    const SISTEMA = {
        nome: 'D&D 5e Simplificado',
        formula_modificador_atributo: '(valor - 10) / 2',
        atributos: { for: 'Força', des: 'Destreza', con: 'Constituição', int: 'Inteligência', sab: 'Sabedoria', car: 'Carisma' },
        usa_sanidade: true,
        recursos: [{ nome: 'Sorte' }],
        point_buy_total: 27,
        point_buy_min: 8,
        point_buy_max: 15,
        base_value: 10 // Valor inicial para o modo manual/rolagem
    };

    const ATRIBUTOS_SISTEMA = Object.keys(SISTEMA.atributos);
    const USA_SANIDADE = SISTEMA.usa_sanidade;
    const USA_SORTE = SISTEMA.recursos.some(r => r.nome === 'Sorte');

    const POINT_BUY_COSTS = {
        8: 0, 9: 1, 10: 2, 11: 3, 12: 4, 13: 5, 14: 7, 15: 9
    };

    let currentAttributes = ATRIBUTOS_SISTEMA.reduce((acc, key) => ({ ...acc, [key]: SISTEMA.base_value }), {});
    let specialAttributes = {
        sanidade: 50,
        sorte: 50,
    };
    let distributionMethod = 'rolagem';
    let totalPointsRemaining = SISTEMA.point_buy_total;
</script>

<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Distribuição de Atributos</h1>
                    <p class="mb-0">Personagem: <span id="personagem-nome">{PERSONAGEM.nome}</span> | Sistema: <span id="sistema-nome">{SISTEMA.nome}</span></p>
                </div>
                <a href="{PERSONAGEM.overviewRoute}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <form action="#salvar-atributos" method="POST" id="step3-form" onsubmit="handleFormSubmission(event)">
            <div class="card-body">
                <!-- Método de Distribuição -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Método de Distribuição</h5>
                                <div class="d-flex flex-wrap gap-3 align-items-center">

                                    <!-- Radio Buttons -->
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metodo_distribuicao" id="metodo_rolagem" value="rolagem" checked onchange="handleMethodChange(this.value)">
                                        <label class="form-check-label" for="metodo_rolagem">Rolagem (4d6k3)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metodo_distribuicao" id="metodo_pontos" value="pontos" onchange="handleMethodChange(this.value)">
                                        <label class="form-check-label" for="metodo_pontos">Compra de Pontos ({SISTEMA.point_buy_total})</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metodo_distribuicao" id="metodo_manual" value="manual" onchange="handleMethodChange(this.value)">
                                        <label class="form-check-label" for="metodo_manual">Manual</label>
                                    </div>

                                    <!-- Botão Sortear Tudo -->
                                    <button type="button" id="sortear-atributos" class="btn btn-sm btn-outline-info ms-lg-3" onclick="rollAllAttributes()">
                                        <i class="fas fa-dice me-1"></i>Sortear Tudo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Atributos do Sistema -->
                    <div class="col-lg-<%= USA_SANIDADE || USA_SORTE ? '8' : '12' %>">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Atributos Principais</h5>
                            </div>
                            <div class="card-body">
                                <div class="row" id="atributos-principais">
                                    <!-- Cards de Atributos são injetados aqui via JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atributos Especiais (Sanidade/Sorte) -->
                    <div class="col-lg-4 <%= USA_SANIDADE || USA_SORTE ? '' : 'd-none' %>">
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Atributos Especiais</h5>
                            </div>
                            <div class="card-body">

                                <% if (USA_SANIDADE) { %>
                                <div class="mb-3" id="sanidade-group">
                                    <label for="sanidade" class="form-label">Sanidade (Base)</label>
                                    <input type="number" name="sanidade" id="sanidade"
                                            class="form-control"
                                            value="50"
                                            min="0" max="100" onchange="handleSpecialAttrChange('sanidade', this.value)">
                                    <div class="form-text">Saúde mental base do personagem.</div>
                                </div>
                                <% } %>

                                <% if (USA_SORTE) { %>
                                <div class="mb-3" id="sorte-group">
                                    <label for="sorte" class="form-label">Sorte (Base)</label>
                                    <div class="input-group">
                                        <input type="number" name="sorte" id="sorte"
                                                class="form-control"
                                                value="50"
                                                min="1" max="100" onchange="handleSpecialAttrChange('sorte', this.value)">
                                        <button type="button" class="btn btn-outline-secondary" id="sortear-sorte" onclick="rollSorte()">
                                            <i class="fas fa-dice me-1"></i> Sortear (3d6x5)
                                        </button>
                                    </div>
                                    <div class="form-text">Sorte do personagem (1-100), geralmente rolado.</div>
                                </div>
                                <% } %>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumo dos Atributos -->
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Resumo dos Atributos</h5>
                                <div class="row" id="resumo-atributos">
                                    <!-- Resumo dos Atributos é injetado aqui via JS -->
                                </div>
                                <div class="mt-3 text-center d-none p-3 rounded" id="point-buy-summary">
                                    <strong>Pontos Restantes: <span id="pontos-restantes" class="text-success">27</span></strong>
                                    (Custo Total: <span id="total-pontos">0</span> de {SISTEMA.point_buy_total})
                                    <div id="point-buy-error" class="small mt-1 text-danger d-none">
                                        ERRO: Você excedeu o total de pontos permitidos na compra de pontos!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="{PERSONAGEM.overviewRoute}" class="btn btn-secondary">
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
    // ============== FUNÇÕES DE LÓGICA GERAL ==============

    /**
     * Calcula o modificador D&D 5e: floor((valor - 10) / 2)
     * @param {number} value
     * @returns {number}
     */
    function calculateModifier(value) {
        value = parseInt(value);
        if (isNaN(value)) return 0;
        return Math.floor((value - 10) / 2);
    }

    /**
     * Rolagem de Atributo (4d6k3: rola 4, mantém 3 maiores)
     * @returns {number}
     */
    function roll4d6k3() {
        const rolls = [];
        for (let i = 0; i < 4; i++) {
            rolls.push(Math.floor(Math.random() * 6) + 1);
        }
        rolls.sort((a, b) => b - a);
        return rolls[0] + rolls[1] + rolls[2];
    }

    /**
     * Rolagem de Sorte (3d6x5) - Simulação CoC
     * @returns {number}
     */
    function roll3d6x5() {
        let sum = 0;
        for (let i = 0; i < 3; i++) {
            sum += Math.floor(Math.random() * 6) + 1;
        }
        return sum * 5;
    }

    /**
     * Calcula o custo total dos atributos no modo Point Buy
     * @returns {number}
     */
    function calculateTotalCost() {
        let cost = 0;
        for (const key in currentAttributes) {
            const value = currentAttributes[key];
            cost += POINT_BUY_COSTS[value] || 0;
        }
        return cost;
    }

    // ============== FUNÇÕES DE MANIPULAÇÃO DE UI/ESTADO ==============

    /**
     * Renderiza o cartão de um único atributo.
     */
    function renderAttributeCard(chave, nome, value) {
        const modifier = calculateModifier(value);
        const cost = POINT_BUY_COSTS[value] || 0;
        const isPointBuy = distributionMethod === 'pontos';
        const min = isPointBuy ? SISTEMA.point_buy_min : 1;
        const max = isPointBuy ? SISTEMA.point_buy_max : 20;
        const isDisabled = isPointBuy && (value === SISTEMA.point_buy_max || totalPointsRemaining <= 0);

        return `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card atributo-card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title">${nome}</h6>
                        <input type="number"
                                name="${chave}"
                                id="atributo-${chave}"
                                class="form-control form-control-lg text-center atributo-input mb-2"
                                value="${value}"
                                min="${min}" max="${max}"
                                data-atributo="${chave}"
                                ${isPointBuy ? 'readonly' : ''}
                                required
                                onchange="handleAttributeInput('${chave}', this.value)">
                        <div class="mt-2">
                            <small class="text-muted">Modificador: <span id="mod-${chave}" class="${modifier >= 0 ? 'text-success' : 'text-danger'}">${modifier >= 0 ? '+' : ''}${modifier}</span></small>
                            ${isPointBuy ? `<small class="d-block text-primary">Custo: ${cost}</small>` : ''}
                        </div>
                        <div class="btn-group btn-group-sm mt-2" role="group">
                            <button type="button" class="btn btn-outline-secondary decrementar" onclick="changeAttributeValue('${chave}', -1)" ${value <= min ? 'disabled' : ''}>
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary incrementar" onclick="changeAttributeValue('${chave}', 1)" ${value >= max || (isPointBuy && totalPointsRemaining <= 0) ? 'disabled' : ''}>
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Atualiza o resumo de atributos.
     */
    function updateAttributeSummary() {
        const resumoDiv = document.getElementById('resumo-atributos');
        if (!resumoDiv) return;

        resumoDiv.innerHTML = ATRIBUTOS_SISTEMA.map(chave => {
            const value = currentAttributes[chave];
            const nome = SISTEMA.atributos[chave];
            const modifier = calculateModifier(value);
            return `
                <div class="col-md-3 col-6 text-center mb-3">
                    <div class="border rounded p-2 bg-white shadow-sm">
                        <strong class="text-sm">${nome}</strong>
                        <div class="h5 mb-0 text-info" id="resumo-${chave}">${value}</div>
                        <small class="text-muted" id="resumo-mod-${chave}">Mod: <span class="${modifier >= 0 ? 'text-success' : 'text-danger'}">${modifier >= 0 ? '+' : ''}${modifier}</span></small>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Renderiza e atualiza todos os atributos.
     */
    function renderAttributes() {
        const container = document.getElementById('atributos-principais');
        if (!container) return;

        container.innerHTML = ATRIBUTOS_SISTEMA.map(chave =>
            renderAttributeCard(chave, SISTEMA.atributos[chave], currentAttributes[chave])
        ).join('');

        updateAttributeSummary();
        updatePointBuySummary();
    }

    /**
     * Atualiza o estado de um atributo após manipulação.
     */
    function updateAttributeState(chave, newValue) {
        currentAttributes[chave] = parseInt(newValue);
        renderAttributes(); // Re-renderiza tudo para atualizar botões, custos e modificadores
    }

    /**
     * Manipula a mudança de valor de um atributo (botões + e -).
     */
    function changeAttributeValue(chave, delta) {
        const currentValue = currentAttributes[chave];
        let newValue = currentValue + delta;

        const isPointBuy = distributionMethod === 'pontos';
        const min = isPointBuy ? SISTEMA.point_buy_min : 1;
        const max = isPointBuy ? SISTEMA.point_buy_max : 20;

        if (newValue < min || newValue > max) return;

        if (isPointBuy) {
            const currentCost = POINT_BUY_COSTS[currentValue] || 0;
            const newCost = POINT_BUY_COSTS[newValue] || 0;
            const costChange = newCost - currentCost;

            if (totalPointsRemaining - costChange < 0) return; // Não há pontos suficientes

            totalPointsRemaining -= costChange;
        }

        updateAttributeState(chave, newValue);
    }

    /**
     * Manipula o input manual de um atributo (apenas no modo manual/rolagem).
     */
    function handleAttributeInput(chave, value) {
        if (distributionMethod !== 'pontos') {
            updateAttributeState(chave, value);
        }
    }

    /**
     * Manipula a mudança de método de distribuição.
     */
    function handleMethodChange(method) {
        distributionMethod = method;
        totalPointsRemaining = SISTEMA.point_buy_total;

        if (method === 'pontos') {
            // Garante que o valor esteja no range 8-15
            for (const key in currentAttributes) {
                let val = currentAttributes[key];
                if (val < SISTEMA.point_buy_min) val = SISTEMA.point_buy_min;
                if (val > SISTEMA.point_buy_max) val = SISTEMA.point_buy_max;
                currentAttributes[key] = val;
            }
        } else {
            // Garante valor base no modo rolagem/manual
            for (const key in currentAttributes) {
                 // Apenas ajusta se estiver fora de um valor razoável, se não, mantém o último valor
                 if (currentAttributes[key] < 3 || currentAttributes[key] > 20) {
                     currentAttributes[key] = SISTEMA.base_value;
                 }
            }
        }
        renderAttributes();
    }

    /**
     * Rola todos os atributos (4d6k3).
     */
    function rollAllAttributes() {
        distributionMethod = 'rolagem';
        document.getElementById('metodo_rolagem').checked = true;

        for (const key of ATRIBUTOS_SISTEMA) {
            currentAttributes[key] = roll4d6k3();
        }
        renderAttributes();
    }

    /**
     * Rola a Sorte (3d6x5).
     */
    function rollSorte() {
        const newSorte = roll3d6x5();
        specialAttributes.sorte = newSorte;
        document.getElementById('sorte').value = newSorte;
    }

    /**
     * Atualiza o resumo de pontos de compra (custo e pontos restantes).
     */
    function updatePointBuySummary() {
        const summaryDiv = document.getElementById('point-buy-summary');
        const errorDiv = document.getElementById('point-buy-error');

        if (distributionMethod === 'pontos') {
            const totalCost = calculateTotalCost();
            totalPointsRemaining = SISTEMA.point_buy_total - totalCost;

            summaryDiv.classList.remove('d-none', 'point-buy-valid', 'point-buy-invalid');

            document.getElementById('pontos-restantes').textContent = totalPointsRemaining;
            document.getElementById('total-pontos').textContent = totalCost;

            const isTooExpensive = totalPointsRemaining < 0;

            if (isTooExpensive) {
                summaryDiv.classList.add('point-buy-invalid');
                document.getElementById('pontos-restantes').classList.remove('text-success');
                document.getElementById('pontos-restantes').classList.add('text-danger');
                errorDiv.classList.remove('d-none');
                document.querySelector('button[type="submit"]').disabled = true;
            } else {
                summaryDiv.classList.add('point-buy-valid');
                document.getElementById('pontos-restantes').classList.remove('text-danger');
                document.getElementById('pontos-restantes').classList.add('text-success');
                errorDiv.classList.add('d-none');
                document.querySelector('button[type="submit"]').disabled = false;
            }
        } else {
            summaryDiv.classList.add('d-none');
            document.querySelector('button[type="submit"]').disabled = false;
        }
    }

    function handleSpecialAttrChange(key, value) {
        specialAttributes[key] = parseInt(value) || 0;
    }

    /**
     * Simula a submissão do formulário.
     */
    function handleFormSubmission(event) {
        event.preventDefault(); // Impede o envio real

        if (distributionMethod === 'pontos' && totalPointsRemaining < 0) {
            // Este caso já deveria ser evitado pelo botão disabled e erro na tela, mas é uma proteção
            alert('Erro: Você excedeu o total de pontos permitidos na compra de pontos!');
            return;
        }

        // Simula a coleta de todos os dados do formulário para envio (incluindo atributos principais e especiais)
        const finalData = {
            metodo_distribuicao: distributionMethod,
            atributos_principais: currentAttributes,
            sanidade: specialAttributes.sanidade,
            sorte: specialAttributes.sorte,
            // ... outros dados do formulário que seriam enviados
        };

        console.log("==========================================");
        console.log("SIMULAÇÃO DE REQUEST POST:");
        console.log("URL: /personagens/store/step3/" + PERSONAGEM.id);
        console.log("PAYLOAD (Dados do Formulário):");
        console.log(JSON.stringify(finalData, null, 2));
        console.log("==========================================");

        alert(`Atributos salvos com sucesso (Simulação)! Método: ${distributionMethod}`);
    }


    // ============== INICIALIZAÇÃO ==============

    window.onload = function() {
        // Inicializa o nome do personagem e sistema
        document.getElementById('personagem-nome').textContent = PERSONAGEM.nome;
        document.getElementById('sistema-nome').textContent = SISTEMA.nome;

        // Inicializa o valor da Sanidade e Sorte se os campos existirem no DOM
        if (USA_SANIDADE) document.getElementById('sanidade').value = specialAttributes.sanidade;
        if (USA_SORTE) document.getElementById('sorte').value = specialAttributes.sorte;

        // Garante que o método de rolagem está ativo por padrão (conforme o Blade original)
        document.getElementById('metodo_rolagem').checked = true;

        // Renderiza a interface
        renderAttributes();
    };

</script>

<!-- Bootstrap JS Bundle CDN (for features like dropdowns, if needed) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
