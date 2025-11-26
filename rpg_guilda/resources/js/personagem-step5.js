document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('step5-form');
    const bonusProficienciaSelect = document.getElementById('bonus_proficiencia');
    const skillCheckboxes = document.querySelectorAll('.skill-checkbox');
    const skillFeedback = document.getElementById('skill-selection-feedback');
    const skillFeedbackText = document.getElementById('skill-feedback-text');
    const allSkillsList = document.getElementById('all-skills-list');

    let selectedSkills = new Set();
    let bonusProficiencia = BONUS_PROFICIENCIA_INICIAL;

    // Inicializar
    function initialize() {
        updateSkillSelection();
        updateAllSkillsDisplay();
        setupEventListeners();
    }

    function setupEventListeners() {
        // Bônus de proficência
        bonusProficienciaSelect.addEventListener('change', function() {
            bonusProficiencia = parseInt(this.value);
            updateAllSkillsDisplay();
        });

        // Checkboxes de perícias
        skillCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    selectedSkills.add(this.value);
                } else {
                    selectedSkills.delete(this.value);
                }
                updateSkillSelection();
                updateAllSkillsDisplay();
            });
        });
    }

    function updateSkillSelection() {
        const currentCount = selectedSkills.size;

        if (MAX_ESCOLHAS > 0) {
            if (currentCount === MAX_ESCOLHAS) {
                // Desabilitar checkboxes não selecionados
                skillCheckboxes.forEach(checkbox => {
                    if (!checkbox.checked) {
                        checkbox.disabled = true;
                        checkbox.parentElement.classList.add('text-muted');
                    }
                });
                showFeedback(`Perfeito! Você selecionou ${MAX_ESCOLHAS} perícias.`, 'success');
            } else if (currentCount > MAX_ESCOLHAS) {
                // Remover últimas seleções excedentes
                const excess = currentCount - MAX_ESCOLHAS;
                const selectedArray = Array.from(selectedSkills);
                for (let i = 0; i < excess; i++) {
                    const skillToRemove = selectedArray[selectedArray.length - 1 - i];
                    selectedSkills.delete(skillToRemove);

                    const checkbox = document.querySelector(`.skill-checkbox[value="${skillToRemove}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
                showFeedback(`Você só pode selecionar ${MAX_ESCOLHAS} perícias.`, 'warning');
            } else {
                // Habilitar todos os checkboxes
                skillCheckboxes.forEach(checkbox => {
                    checkbox.disabled = false;
                    checkbox.parentElement.classList.remove('text-muted');
                });
                showFeedback(`Selecione mais ${MAX_ESCOLHAS - currentCount} perícia(s).`, 'info');
            }
        }
    }

    function showFeedback(message, type) {
        if (!skillFeedback || !skillFeedbackText) return;

        skillFeedbackText.textContent = message;
        skillFeedback.className = `alert alert-${type} mt-3`;
        skillFeedback.classList.remove('d-none');

        // Auto-esconder mensagens de sucesso
        if (type === 'success') {
            setTimeout(() => {
                skillFeedback.classList.add('d-none');
            }, 3000);
        }
    }

    function calculateModifier(attributeValue) {
        return Math.floor((attributeValue - 10) / 2);
    }

    function getSkillModifier(skillName) {
        const attributeKey = PERICIAS_SISTEMA[skillName];
        if (!attributeKey || !ATRIBUTOS[attributeKey]) return 0;

        return calculateModifier(ATRIBUTOS[attributeKey]);
    }

    function isSkillProficient(skillName) {
        // Verificar se é fixa da classe
        if (PERICIAS_CLASSE.fixas && PERICIAS_CLASSE.fixas.includes(skillName)) {
            return true;
        }

        // Verificar se é da origem
        if (PERICIAS_ORIGEM && PERICIAS_ORIGEM[skillName]) {
            return true;
        }

        // Verificar se foi escolhida
        if (selectedSkills.has(skillName)) {
            return true;
        }

        return false;
    }

    function updateAllSkillsDisplay() {
        if (!allSkillsList) return;

        let skillsHtml = '';

        for (const [skillName, attribute] of Object.entries(PERICIAS_SISTEMA)) {
            const modifier = getSkillModifier(skillName);
            const isProficient = isSkillProficient(skillName);
            const totalBonus = modifier + (isProficient ? bonusProficiencia : 0);
            const totalDisplay = totalBonus >= 0 ? `+${totalBonus}` : totalBonus;
            const modifierDisplay = modifier >= 0 ? `+${modifier}` : modifier;

            const attributeName = attribute in MODIFICADORES ?
                Object.keys(PERICIAS_SISTEMA).find(key => PERICIAS_SISTEMA[key] === attribute) :
                attribute;

            skillsHtml += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card skill-card ${isProficient ? 'border-success bg-success-subtle' : ''}">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="${isProficient ? 'fw-bold text-success' : ''}">
                                        ${skillName}
                                    </span>
                                    <div class="small text-muted">
                                        ${attributeName}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold ${isProficient ? 'text-success' : 'text-dark'}">
                                        ${totalDisplay}
                                    </div>
                                    <div class="small text-muted">
                                        ${modifierDisplay} + ${isProficient ? bonusProficiencia : 0}
                                    </div>
                                </div>
                            </div>
                            ${isProficient ? `
                            <div class="text-center mt-1">
                                <small class="badge bg-success">Proficiente</small>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        allSkillsList.innerHTML = skillsHtml;
    }

    // Inicializar
    initialize();
});
