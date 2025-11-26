// character-creation.js - Sistema Aprimorado de Criação de Personagens

class CharacterCreator {
    constructor() {
        this.systemData = window.characterCreationData;
        this.atributos = this.systemData.ATRIBUTOS_JSON;
        this.selectedSkills = [];
        this.finalAttributes = {};
        this.raceBonus = {};
        this.classBonus = {};
        this.originBonus = {};
        this.equipmentSelections = {};
        this.skillSelections = {};
        this.intelligenceSkills = [];
        this.proficienciaBonus = 2;
        this.chart = null;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.renderAttributeDistribution();
        this.updateProficienciaBonus();
        this.updateModFormulaDisplay();
    }

    setupEventListeners() {
        document.getElementById('raca_id').addEventListener('change', (e) => {
            this.handleRaceChange(e.target);
        });

        document.getElementById('classe_id').addEventListener('change', (e) => {
            this.handleClassChange(e.target);
        });

        document.getElementById('origem_id').addEventListener('change', (e) => {
            this.handleOriginChange(e.target);
        });

        document.querySelectorAll('input[name="distribution_method"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.handleDistributionMethodChange(e.target.value);
            });
        });

        document.getElementById('proficiencia_bonus_select').addEventListener('change', (e) => {
            this.proficienciaBonus = parseInt(e.target.value);
            this.updateProficienciaBonus();
            this.updateFinalAttributes();
            this.updateAllSkillsDisplay();
        });

        document.getElementById('roll-hp-button').addEventListener('click', () => {
            this.rollHP();
        });

        document.getElementById('randomize-button').addEventListener('click', () => {
            this.randomizeCharacter();
        });

        document.getElementById('character-form').addEventListener('submit', (e) => {
            this.handleFormSubmit(e);
        });

        document.getElementById('nome').addEventListener('input', () => {
            this.updateSubmitButton();
        });
    }

    handleRaceChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const descricao = selectedOption.getAttribute('data-descricao');
        const bonus = JSON.parse(selectedOption.getAttribute('data-bonus') || '{}');
        const tipoBonus = selectedOption.getAttribute('data-tipo-bonus');
        const bonusLivre = parseInt(selectedOption.getAttribute('data-bonus-livre') || 0);

        document.getElementById('raca-descricao-display').textContent = descricao || '';

        this.raceBonus = bonus;
        this.updateFinalAttributes();
        this.updateRaceChoicesUI(tipoBonus, bonusLivre);
    }

    handleClassChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const descricao = selectedOption.getAttribute('data-descricao');
        const classBases = JSON.parse(selectedOption.getAttribute('data-class-bases') || '{}');
        const classSkills = JSON.parse(selectedOption.getAttribute('data-class-skills') || '{}');
        const classEquipment = JSON.parse(selectedOption.getAttribute('data-class-equipment') || '{}');
        const dadoVida = selectedOption.getAttribute('data-dado-vida');
        const usaMagia = selectedOption.getAttribute('data-usa-magia');

        document.getElementById('classe-descricao-display').textContent = descricao || '';
        document.getElementById('classe-magia-display').textContent = usaMagia === 'Sim' ? '🪄 Esta classe usa magia' : '';
        document.getElementById('dado-vida-display').textContent = dadoVida;

        this.classBonus = classBases;
        this.updateFinalAttributes();
        this.updateClassOptionsUI(classSkills, classEquipment);

        const optionsCard = document.getElementById('class-options-card');
        if (selectedOption.value) {
            optionsCard.classList.remove('d-none');
        } else {
            optionsCard.classList.add('d-none');
        }
    }

    handleOriginChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const descricao = selectedOption.getAttribute('data-descricao');
        const skills = JSON.parse(selectedOption.getAttribute('data-skills') || '{}');
        const resources = JSON.parse(selectedOption.getAttribute('data-resources') || '{}');

        document.getElementById('origem-descricao-display').textContent = descricao || '';

        this.originBonus = skills;
        this.updateFinalAttributes();
        this.updateOriginResourcesUI(resources);
    }

    handleDistributionMethodChange(method) {
        const pointBuyUI = document.getElementById('point-buy-ui');
        const manualUI = document.getElementById('manual-ui');

        if (method === 'point_buy') {
            pointBuyUI.classList.remove('d-none');
            manualUI.classList.add('d-none');
            this.renderPointBuyUI();
        } else if (method === 'manual') {
            pointBuyUI.classList.add('d-none');
            manualUI.classList.remove('d-none');
            this.renderManualUI();
        } else if (method === 'random') {
            pointBuyUI.classList.add('d-none');
            manualUI.classList.add('d-none');
            this.rollRandomAttributes();
        }
    }

    renderAttributeDistribution() {
        this.renderPointBuyUI();
        this.renderManualUI();
    }

    renderPointBuyUI() {
        const container = document.getElementById('attribute-list-container');
        container.innerHTML = '';

        Object.entries(this.atributos).forEach(([key, nome]) => {
            const attrDiv = document.createElement('div');
            attrDiv.className = 'd-flex align-items-center justify-content-between border-bottom pb-2';
            attrDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <span class="fw-bold me-3" style="min-width: 120px">${nome}</span>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary decrease-attr" data-attr="${key}">-</button>
                        <span class="btn btn-outline-dark disabled" style="min-width: 40px" id="attr-value-${key}">8</span>
                        <button type="button" class="btn btn-outline-secondary increase-attr" data-attr="${key}">+</button>
                    </div>
                </div>
                <div>
                    <small class="text-muted">Mod: <span id="attr-mod-${key}">-1</span></small>
                    <small class="text-muted ms-2">Custo: <span id="attr-cost-${key}">0</span></small>
                </div>
            `;
            container.appendChild(attrDiv);
        });

        container.querySelectorAll('.increase-attr').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.changeAttributeValue(e.target.dataset.attr, 1);
            });
        });

        container.querySelectorAll('.decrease-attr').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.changeAttributeValue(e.target.dataset.attr, -1);
            });
        });

        this.updatePointsRemaining();
    }

    renderManualUI() {
        const container = document.getElementById('attribute-manual-list-container');
        container.innerHTML = '';

        Object.entries(this.atributos).forEach(([key, nome]) => {
            const attrDiv = document.createElement('div');
            attrDiv.className = 'd-flex align-items-center justify-content-between';
            attrDiv.innerHTML = `
                <label class="form-label mb-1" style="min-width: 120px">${nome}</label>
                <input type="number" class="form-control form-control-sm manual-attr-input"
                       data-attr="${key}" min="1" max="20" value="10"
                       style="width: 80px">
            `;
            container.appendChild(attrDiv);
        });

        container.querySelectorAll('.manual-attr-input').forEach(input => {
            input.addEventListener('change', (e) => {
                this.updateManualAttribute(e.target.dataset.attr, parseInt(e.target.value));
            });
            input.addEventListener('input', (e) => {
                this.updateManualAttribute(e.target.dataset.attr, parseInt(e.target.value));
            });
        });
    }

    rollRandomAttributes() {
        Object.keys(this.atributos).forEach(attr => {
            const rolls = Array.from({length: 4}, () => Math.floor(Math.random() * 6) + 1);
            rolls.sort((a, b) => a - b);
            rolls.shift();
            const total = rolls.reduce((sum, val) => sum + val, 0);

            const valueElement = document.getElementById(`attr-value-${attr}`);
            if (valueElement) {
                valueElement.textContent = total;
                this.updateAttributeCost(attr, total);
                this.updateAttributeModifier(attr, total);
            }

            const manualInput = document.querySelector(`.manual-attr-input[data-attr="${attr}"]`);
            if (manualInput) {
                manualInput.value = total;
            }
        });

        this.updatePointsRemaining();
        this.updateFinalAttributes();
    }

    changeAttributeValue(attr, change) {
        const valueElement = document.getElementById(`attr-value-${attr}`);
        let currentValue = parseInt(valueElement.textContent);
        let newValue = currentValue + change;

        if (newValue >= 8 && newValue <= 15) {
            valueElement.textContent = newValue;
            this.updateAttributeCost(attr, newValue);
            this.updateAttributeModifier(attr, newValue);
            this.updatePointsRemaining();
            this.updateFinalAttributes();
        }
    }

    updateAttributeCost(attr, value) {
        const costMap = {8:0, 9:1, 10:2, 11:3, 12:4, 13:5, 14:7, 15:9};
        const costElement = document.getElementById(`attr-cost-${attr}`);
        costElement.textContent = costMap[value] || 0;
    }

    updateAttributeModifier(attr, value) {
        const modElement = document.getElementById(`attr-mod-${attr}`);
        const modifier = this.calculateModifier(value);
        modElement.textContent = modifier >= 0 ? `+${modifier}` : modifier;
    }

    calculateModifier(value) {
        return Math.floor((value - 10) / 2);
    }

    updatePointsRemaining() {
        const pointsElement = document.getElementById('points-remaining');
        let totalCost = 0;

        Object.keys(this.atributos).forEach(attr => {
            const value = parseInt(document.getElementById(`attr-value-${attr}`).textContent);
            const costMap = {8:0, 9:1, 10:2, 11:3, 12:4, 13:5, 14:7, 15:9};
            totalCost += costMap[value] || 0;
        });

        const remaining = 27 - totalCost;
        pointsElement.textContent = remaining;
        pointsElement.className = `fw-bold ${remaining >= 0 ? 'text-success' : 'text-danger'}`;

        this.updateSubmitButton();
    }

    updateManualAttribute(attr, value) {
        if (value < 1) value = 1;
        if (value > 20) value = 20;
        this.updateFinalAttributes();
    }

    updateFinalAttributes() {
        const method = document.querySelector('input[name="distribution_method"]:checked').value;
        const finalAttributes = {};

        if (method === 'point_buy') {
            Object.keys(this.atributos).forEach(attr => {
                finalAttributes[attr] = parseInt(document.getElementById(`attr-value-${attr}`).textContent);
            });
        } else if (method === 'manual') {
            document.querySelectorAll('.manual-attr-input').forEach(input => {
                finalAttributes[input.dataset.attr] = parseInt(input.value) || 10;
            });
        } else if (method === 'random') {
            Object.keys(this.atributos).forEach(attr => {
                finalAttributes[attr] = parseInt(document.getElementById(`attr-value-${attr}`).textContent);
            });
        }

        Object.keys(finalAttributes).forEach(attr => {
            let bonus = 0;
            bonus += this.raceBonus[attr] || 0;
            bonus += this.classBonus[attr] || 0;
            bonus += this.originBonus[attr] || 0;

            finalAttributes[attr] += bonus;
        });

        this.finalAttributes = finalAttributes;
        this.updateFinalAttributesDisplay();
        this.updateAllSkillsDisplay();
        this.updateIntelligenceSkills();
    }

    updateFinalAttributesDisplay() {
        const container = document.getElementById('final-attributes-display');
        container.innerHTML = '';

        Object.entries(this.atributos).forEach(([key, nome]) => {
            const value = this.finalAttributes[key] || 10;
            const modifier = this.calculateModifier(value);
            const modDisplay = modifier >= 0 ? `+${modifier}` : modifier;

            const attrDiv = document.createElement('div');
            attrDiv.className = 'd-flex justify-content-between align-items-center border-bottom pb-1';
            attrDiv.innerHTML = `
                <span class="fw-semibold">${nome}</span>
                <div>
                    <span class="badge bg-primary me-2">${value}</span>
                    <span class="badge bg-secondary">${modDisplay}</span>
                </div>
            `;
            container.appendChild(attrDiv);
        });

        this.updateChart();
        this.updateSubmitButton();
    }

    updateChart() {
        const ctx = document.getElementById('attribute-chart').getContext('2d');

        if (this.chart) {
            this.chart.destroy();
        }

        const labels = Object.values(this.atributos);
        const data = Object.keys(this.atributos).map(key => this.finalAttributes[key] || 10);

        this.chart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Atributos',
                    data: data,
                    fill: true,
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
                        beginAtZero: true,
                        max: 20,
                        min: 0
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                maintainAspectRatio: false
            }
        });
    }

    updateModFormulaDisplay() {
        const container = document.getElementById('mod-formula-display');
        container.textContent = `Fórmula do Modificador: (Valor - 10) / 2 (arredondado para baixo)`;
    }

    updateClassOptionsUI(classSkills, classEquipment) {
        this.updateEquipmentUI(classEquipment);
        this.updateSkillsUI(classSkills);
    }

    updateEquipmentUI(equipment) {
        const fixedContainer = document.getElementById('fixed-equipment-display');
        const optionsContainer = document.getElementById('equipment-options-container');

        fixedContainer.innerHTML = '<strong>Equipamento Fixo:</strong><br>' +
            (equipment.fixas?.join(', ') || 'Nenhum');

        optionsContainer.innerHTML = '';

        if (equipment.opcoes && equipment.opcoes.length > 0) {
            equipment.opcoes.forEach((opcao, index) => {
                const opcaoDiv = document.createElement('div');
                opcaoDiv.className = 'border p-2 rounded';
                opcaoDiv.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input equipment-option" type="radio"
                               name="equipment_option_${index}" value='${JSON.stringify(opcao)}'
                               id="equip_opt_${index}">
                        <label class="form-check-label" for="equip_opt_${index}">
                            ${opcao.join(' + ')}
                        </label>
                    </div>
                `;
                optionsContainer.appendChild(opcaoDiv);
            });

            optionsContainer.querySelectorAll('.equipment-option').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    this.equipmentSelections = JSON.parse(e.target.value);
                    this.updateSubmitButton();
                });
            });
        } else {
            optionsContainer.innerHTML = '<p class="text-muted small">Nenhuma opção de equipamento disponível.</p>';
        }
    }

    updateSkillsUI(classSkills) {
        const fixedContainer = document.getElementById('fixed-skills-display');
        const optionsContainer = document.getElementById('skill-options-container');
        const checkboxesContainer = document.getElementById('skill-choice-checkboxes');

        fixedContainer.textContent = classSkills.fixas?.length > 0 ?
            `Perícias fixas: ${classSkills.fixas.join(', ')}` : 'Nenhuma perícia fixa';

        if (classSkills.lista && classSkills.lista.length > 0 && classSkills.escolha > 0) {
            optionsContainer.classList.remove('d-none');
            document.getElementById('skill-choice-instructions').textContent =
                `Escolha ${classSkills.escolha} perícia(s) da lista abaixo:`;

            checkboxesContainer.innerHTML = '';
            classSkills.lista.forEach(skill => {
                const skillDiv = document.createElement('div');
                skillDiv.className = 'form-check';
                skillDiv.innerHTML = `
                    <input class="form-check-input skill-checkbox" type="checkbox"
                           value="${skill}" id="skill_${skill}">
                    <label class="form-check-label" for="skill_${skill}">
                        ${skill}
                    </label>
                `;
                checkboxesContainer.appendChild(skillDiv);
            });

            checkboxesContainer.querySelectorAll('.skill-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    this.handleSkillSelection(classSkills.escolha);
                });
            });
        } else {
            optionsContainer.classList.add('d-none');
        }
    }

    handleSkillSelection(maxSelections) {
        const selected = document.querySelectorAll('.skill-checkbox:checked');
        const alert = document.getElementById('skill-choice-alert');

        if (selected.length > maxSelections) {
            alert.textContent = `Você só pode escolher ${maxSelections} perícia(s).`;
            alert.classList.remove('d-none');
            event.target.checked = false;
        } else {
            alert.classList.add('d-none');
            this.skillSelections = Array.from(selected).map(cb => cb.value);
        }

        this.updateSubmitButton();
        this.updateAllSkillsDisplay();
    }

    updateAllSkillsDisplay() {
        const container = document.getElementById('all-skills-display');

        if (!this.systemData.PERICIAS_MAPEAMENTO_JSON || this.systemData.PERICIAS_MAPEAMENTO_JSON.length === 0) {
            container.innerHTML = '<p class="text-muted small">Nenhuma perícia definida no sistema.</p>';
            return;
        }

        let html = '';
        this.systemData.PERICIAS_MAPEAMENTO_JSON.forEach(pericia => {
            const atributoBase = pericia.atributo_relacionado || 'inteligencia';
            const valorAtributo = this.finalAttributes[atributoBase] || 10;
            const modificadorBase = this.calculateModifier(valorAtributo);

            const isProficient = this.skillSelections.includes(pericia.nome) || this.intelligenceSkills.includes(pericia.nome);
            const bonusProficiencia = isProficient ? this.proficienciaBonus : 0;
            const totalBonus = modificadorBase + bonusProficiencia;

            html += `
                <div class="mb-2 d-flex justify-content-between align-items-center border-bottom pb-1">
                    <div>
                        <span class="fw-semibold">${pericia.nome}</span>
                        <small class="text-muted ms-2">(${this.atributos[atributoBase]})</small>
                    </div>
                    <span class="badge ${isProficient ? 'bg-success' : 'bg-secondary'}">
                        ${totalBonus >= 0 ? '+' : ''}${totalBonus}
                    </span>
                </div>
            `;
        });

        container.innerHTML = html;

        this.updatePericiasListDisplay();
    }

    updatePericiasListDisplay() {
        const container = document.getElementById('pericias-list-display');

        if (!this.systemData.PERICIAS_MAPEAMENTO_JSON || this.systemData.PERICIAS_MAPEAMENTO_JSON.length === 0) {
            container.innerHTML = '<p class="text-muted small">Nenhuma perícia definida no sistema.</p>';
            return;
        }

        let html = '';
        this.systemData.PERICIAS_MAPEAMENTO_JSON.forEach(pericia => {
            const atributoBase = pericia.atributo_relacionado || 'inteligencia';
            const valorAtributo = this.finalAttributes[atributoBase] || 10;
            const modificadorBase = this.calculateModifier(valorAtributo);

            const isProficient = this.skillSelections.includes(pericia.nome) || this.intelligenceSkills.includes(pericia.nome);
            const bonusProficiencia = isProficient ? this.proficienciaBonus : 0;
            const totalBonus = modificadorBase + bonusProficiencia;

            html += `
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">${pericia.nome}</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">${this.atributos[atributoBase]}</small>
                                <span class="badge ${isProficient ? 'bg-success' : 'bg-secondary'}">
                                    ${totalBonus >= 0 ? '+' : ''}${totalBonus}
                                </span>
                            </div>
                            ${isProficient ? '<small class="text-success">✓ Proficiente</small>' : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    updateIntelligenceSkills() {
        const intValue = this.finalAttributes['inteligencia'] || 10;
        const intModifier = this.calculateModifier(intValue);
        const container = document.getElementById('intelligence-skills-container');
        const countElement = document.getElementById('intelligence-skill-count');
        const modDisplay = document.getElementById('int-mod-display');

        modDisplay.textContent = intModifier >= 0 ? `+${intModifier}` : intModifier;

        const additionalSkillsCount = Math.max(0, intModifier);

        if (additionalSkillsCount > 0) {
            container.classList.remove('d-none');
            countElement.textContent = additionalSkillsCount;

            const checkboxesContainer = document.getElementById('intelligence-skills-checkboxes');
            checkboxesContainer.innerHTML = '';

            this.systemData.PERICIAS_MAPEAMENTO_JSON.forEach(pericia => {
                if (!this.skillSelections.includes(pericia.nome)) {
                    const skillDiv = document.createElement('div');
                    skillDiv.className = 'form-check';
                    skillDiv.innerHTML = `
                        <input class="form-check-input intelligence-skill-checkbox" type="checkbox"
                               value="${pericia.nome}" id="int_skill_${pericia.nome}">
                        <label class="form-check-label" for="int_skill_${pericia.nome}">
                            ${pericia.nome}
                        </label>
                    `;
                    checkboxesContainer.appendChild(skillDiv);
                }
            });

            checkboxesContainer.querySelectorAll('.intelligence-skill-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    this.handleIntelligenceSkillSelection(additionalSkillsCount);
                });
            });
        } else {
            container.classList.add('d-none');
            this.intelligenceSkills = [];
        }
    }

    handleIntelligenceSkillSelection(maxSelections) {
        const selected = document.querySelectorAll('.intelligence-skill-checkbox:checked');

        if (selected.length > maxSelections) {
            alert(`Você só pode escolher ${maxSelections} perícia(s) adicional(ais) por inteligência.`);
            event.target.checked = false;
        } else {
            this.intelligenceSkills = Array.from(selected).map(cb => cb.value);
        }

        this.updateAllSkillsDisplay();
        document.getElementById('intelligenceSkillsInput').value = JSON.stringify(this.intelligenceSkills);
    }

    updateRaceChoicesUI(tipoBonus, bonusLivre) {
        const container = document.getElementById('race-choice-container');
        const area = document.getElementById('race-choices-area');

        if (tipoBonus === 'choice' && bonusLivre > 0) {
            container.classList.remove('d-none');
            area.innerHTML = `
                <p class="small">Você tem ${bonusLivre} ponto(s) de bônus para distribuir livremente.</p>
                <div class="d-flex gap-2 flex-wrap">
                    ${Object.entries(this.atributos).map(([key, nome]) => `
                        <button type="button" class="btn btn-sm btn-outline-warning race-bonus-btn" data-attr="${key}">
                            +1 ${nome}
                        </button>
                    `).join('')}
                </div>
                <div class="mt-2">
                    <small>Bônus aplicados: <span id="race-bonus-applied">0/${bonusLivre}</span></small>
                </div>
            `;
        } else {
            container.classList.add('d-none');
        }
    }

    updateOriginResourcesUI(resources) {
        const container = document.getElementById('origem-resources-display');

        if (Object.keys(resources).length > 0) {
            container.classList.remove('d-none');
            let html = '<strong>Recursos da Origem:</strong><br>';
            Object.entries(resources).forEach(([key, value]) => {
                html += `<strong>${key}:</strong> ${value}<br>`;
            });
            container.innerHTML = html;
        } else {
            container.classList.add('d-none');
        }
    }

    updateProficienciaBonus() {
        document.getElementById('proficienciaBonusInput').value = this.proficienciaBonus;
    }

    rollHP() {
        const dadoVida = document.getElementById('dado-vida-display').textContent;
        const match = dadoVida.match(/d(\d+)/);

        if (match) {
            const faces = parseInt(match[1]);
            const roll = Math.floor(Math.random() * faces) + 1;
            document.getElementById('hp-roll-result').textContent = roll;
            document.getElementById('rolledHpInput').value = roll;
            this.updateSubmitButton();
        }
    }

    randomizeCharacter() {
        const racas = document.getElementById('raca_id').options;
        const randomRaca = racas[Math.floor(Math.random() * (racas.length - 1)) + 1];
        document.getElementById('raca_id').value = randomRaca.value;
        this.handleRaceChange(document.getElementById('raca_id'));

        const classes = document.getElementById('classe_id').options;
        const randomClasse = classes[Math.floor(Math.random() * (classes.length - 1)) + 1];
        document.getElementById('classe_id').value = randomClasse.value;
        this.handleClassChange(document.getElementById('classe_id'));

        const origens = document.getElementById('origem_id').options;
        if (origens.length > 1) {
            const randomOrigem = origens[Math.floor(Math.random() * (origens.length - 1)) + 1];
            document.getElementById('origem_id').value = randomOrigem.value;
            this.handleOriginChange(document.getElementById('origem_id'));
        }

        document.getElementById('method-random').click();

        const nomes = ['Aragorn', 'Legolas', 'Gimli', 'Gandalf', 'Frodo', 'Samwise', 'Merry', 'Pippin', 'Boromir', 'Faramir'];
        document.getElementById('nome').value = nomes[Math.floor(Math.random() * nomes.length)];

        this.rollHP();
    }

    updateSubmitButton() {
        const submitButton = document.getElementById('submit-button');
        const hasName = document.getElementById('nome').value.trim() !== '';
        const hasRace = document.getElementById('raca_id').value !== '';
        const hasClass = document.getElementById('classe_id').value !== '';
        const hasHpRoll = document.getElementById('rolledHpInput').value !== '';

        const hasRequiredSelections = hasHpRoll;

        submitButton.disabled = !(hasName && hasRace && hasClass && hasRequiredSelections);
    }

    handleFormSubmit(e) {
        document.getElementById('finalAttributesJsonInput').value = JSON.stringify(this.finalAttributes);
        document.getElementById('selectedSkillsInput').value = JSON.stringify(this.skillSelections);
        document.getElementById('selectedEquipmentInput').value = JSON.stringify(this.equipmentSelections);
        document.getElementById('intelligenceSkillsInput').value = JSON.stringify(this.intelligenceSkills);

        if (!this.validateForm()) {
            e.preventDefault();
            alert('Por favor, complete todas as seleções necessárias antes de criar o personagem.');
        }
    }

    validateForm() {
        const hasName = document.getElementById('nome').value.trim() !== '';
        const hasRace = document.getElementById('raca_id').value !== '';
        const hasClass = document.getElementById('classe_id').value !== '';
        const hasHpRoll = document.getElementById('rolledHpInput').value !== '';

        return hasName && hasRace && hasClass && hasHpRoll;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new CharacterCreator();
});
