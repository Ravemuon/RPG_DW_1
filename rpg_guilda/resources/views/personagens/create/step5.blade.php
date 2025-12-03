<?php
// =========================================================================================
// SIMULAÇÃO DE VARIÁVEIS DO PHP/FRAMEWORK (Laravel/Blade)
// No ambiente real, essas variáveis seriam passadas do Controller para a View.
// =========================================================================================

// Dados do Personagem e Sistema
$PERSONAGEM_NOME = "Gandalf, O Cinzento";
$SISTEMA_NOME = "D&D 5e (PHP Simulado)";

// Mapeamento de Perícias para Atributos
$PERICIAS_SISTEMA = [
    'Acrobacia' => 'destreza',
    'Arcanismo' => 'inteligencia',
    'Atletismo' => 'forca',
    'Enganação' => 'carisma',
    'Intuição' => 'sabedoria',
    'Medicina' => 'sabedoria',
    'Percepção' => 'sabedoria',
    'Persuasão' => 'carisma',
    'Sobrevivência' => 'sabedoria',
    'Prestidigitação' => 'destreza',
    'Religião' => 'inteligencia',
    'Furtividade' => 'destreza',
    'Fidelidade' => 'carisma',
    'Historia' => 'inteligencia',
    'Intimidação' => 'carisma',
];

// Valores de Atributos do Personagem
$ATRIBUTOS = ['forca' => 10, 'destreza' => 14, 'constituicao' => 12, 'inteligencia' => 8, 'sabedoria' => 16, 'carisma' => 13];

// Modificadores (podem ser calculados no JS)
$MODIFICADORES = ['forca' => 0, 'destreza' => 2, 'constituicao' => 1, 'inteligencia' => -1, 'sabedoria' => 3, 'carisma' => 1];

// Perícias Fornecidas pela Classe
$PERICIAS_CLASSE = [
    'escolha' => 2, // Número de escolhas permitidas
    'lista' => ['Arcanismo', 'Enganação', 'Intuição', 'Medicina', 'Persuasão', 'Fidelidade'],
    'fixas' => ['Atletismo'], // Perícias que a classe fornece automaticamente
];

// Perícias Fornecidas pela Origem/Antecedente
$PERICIAS_ORIGEM = ['Percepção' => 1];

$BONUS_PROFICIENCIA_INICIAL = 2;
$MAX_ESCOLHAS = $PERICIAS_CLASSE['escolha'];

// Nomes de Atributos para exibição
$PERICIAS_SISTEMA_NAMES = [
    'forca' => 'Força',
    'destreza' => 'Destreza',
    'constituicao' => 'Constituição',
    'inteligencia' => 'Inteligência',
    'sabedoria' => 'Sabedoria',
    'carisma' => 'Carisma',
];

// =========================================================================================
// HTML E JAVASCRIPT
// =========================================================================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criação - Perícias</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple: #6f42c1;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* bg-gray-100 */
        }
        .bg-purple-custom {
            background-color: var(--purple);
        }
        .text-purple-custom {
            color: var(--purple);
        }
        .btn-purple {
            background-color: var(--purple);
            border-color: var(--purple);
            color: white;
            transition: background-color 0.15s ease-in-out;
        }
        .btn-purple:hover {
            background-color: #5a2d91;
        }
        .border-warning-custom { border-color: #ffc107 !important; }
        .bg-warning-custom { background-color: #ffc107 !important; }

        /* Custom style for the skill selection box */
        .skill-option {
            border: 1px solid #d1d5db; /* border-gray-300 */
            padding: 0.75rem;
            border-radius: 0.5rem; /* rounded-lg */
            cursor: pointer;
            transition: all 0.2s;
        }
        .skill-option:hover {
            background-color: #f3f4f6;
        }
        /* Não precisa de input:checked + label se o JS gerencia a classe skill-option-selected */
        .skill-option-selected {
            background-color: #e5e7eb; /* bg-gray-200 */
            border-color: var(--purple);
            border-width: 2px; /* Destaque maior na seleção */
        }
        .skill-card {
            border-left-width: 4px;
        }
    </style>
</head>
<body>

    <!-- Bloco PHP para injetar dados no JavaScript -->
    <script>
        const PERICIAS_SISTEMA = <?php echo json_encode($PERICIAS_SISTEMA); ?>;
        const ATRIBUTOS = <?php echo json_encode($ATRIBUTOS); ?>;
        const PERICIAS_CLASSE = <?php echo json_encode($PERICIAS_CLASSE); ?>;
        const PERICIAS_ORIGEM = <?php echo json_encode($PERICIAS_ORIGEM); ?>;
        const PERICIAS_SISTEMA_NAMES = <?php echo json_encode($PERICIAS_SISTEMA_NAMES); ?>;
        const MAX_ESCOLHAS = <?php echo $MAX_ESCOLHAS; ?>;
        const BONUS_PROFICIENCIA_INICIAL = <?php echo $BONUS_PROFICIENCIA_INICIAL; ?>;
        const PERSONAGEM_NOME = "<?php echo htmlspecialchars($PERSONAGEM_NOME); ?>";
        const SISTEMA_NOME = "<?php echo htmlspecialchars($SISTEMA_NOME); ?>";
    </script>

    <!-- Estrutura Principal -->
    <div class="container mx-auto p-4 md:p-8 max-w-7xl">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <!-- Card Header -->
            <div class="p-4 md:p-6 bg-purple-custom text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-1">Perícias e Proficiências</h1>
                        <p class="text-sm">Personagem: <span id="personagem-nome"><?php echo htmlspecialchars($PERSONAGEM_NOME); ?></span> | Sistema: <span id="sistema-nome"><?php echo htmlspecialchars($SISTEMA_NOME); ?></span></p>
                    </div>
                    <a href="#" class="px-4 py-2 text-white border border-white rounded-lg hover:bg-white hover:text-purple-custom transition duration-150 text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="#" method="POST" id="step5-form">
                <!-- Conteúdo do Card -->
                <div class="p-4 md:p-6 space-y-8">

                    <!-- Configurações e Perícias Automáticas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Configurações -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                            <h5 class="text-lg font-semibold mb-3 border-b pb-2">Configurações</h5>

                            <div class="mb-4">
                                <label for="bonus_proficiencia" class="block text-sm font-medium text-gray-700 mb-1">Bônus de Proficiência</label>
                                <select name="bonus_proficiencia" id="bonus_proficiencia" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-purple-custom focus:border-purple-custom">
                                    <option value="2" <?php if ($BONUS_PROFICIENCIA_INICIAL == 2) echo 'selected'; ?>>+2 (Nível 1-4)</option>
                                    <option value="3" <?php if ($BONUS_PROFICIENCIA_INICIAL == 3) echo 'selected'; ?>>+3 (Nível 5-8)</option>
                                    <option value="4" <?php if ($BONUS_PROFICIENCIA_INICIAL == 4) echo 'selected'; ?>>+4 (Nível 9-12)</option>
                                    <option value="5" <?php if ($BONUS_PROFICIENCIA_INICIAL == 5) echo 'selected'; ?>>+5 (Nível 13-16)</option>
                                    <option value="6" <?php if ($BONUS_PROFICIENCIA_INICIAL == 6) echo 'selected'; ?>>+6 (Nível 17-20)</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1">Este valor é somado às perícias em que você é proficiente.</div>
                            </div>
                        </div>

                        <!-- Perícias Automáticas -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                            <h5 class="text-lg font-semibold mb-3 border-b pb-2">Perícias Automáticas</h5>

                            <!-- Perícias Fixas da Classe -->
                            <div class="mb-4">
                                <strong class="text-gray-700 block mb-1">Classe:</strong>
                                <div class="flex flex-wrap">
                                    <?php foreach ($PERICIAS_CLASSE['fixas'] as $pericia): ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-2 mb-1">
                                            <?php echo htmlspecialchars($pericia); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Perícias da Origem -->
                            <div>
                                <strong class="text-gray-700 block mb-1">Origem:</strong>
                                <div class="flex flex-wrap">
                                    <?php foreach (array_keys($PERICIAS_ORIGEM) as $pericia): ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2 mb-1">
                                            <?php echo htmlspecialchars($pericia); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Perícias da Classe para Escolher -->
                    <div class="border border-yellow-500 rounded-xl overflow-hidden">
                        <div class="p-4 bg-yellow-400 text-gray-900 font-semibold">
                            <h5 class="text-xl mb-0"><i class="fas fa-bullseye mr-2"></i> Escolha de Perícias da Classe</h5>
                        </div>
                        <div class="p-4 bg-yellow-50">
                            <p class="mb-4 text-gray-700">
                                Sua classe permite escolher <strong class="text-lg text-yellow-800" id="max-escolhas-display"><?php echo $MAX_ESCOLHAS; ?> perícias</strong> da lista abaixo:
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="skill-selection-area">
                                <!-- Os itens de perícia serão renderizados aqui pelo JS -->
                            </div>

                            <div id="skill-selection-feedback" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg mt-4 hidden" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span id="skill-feedback-text"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Lista Completa de Perícias -->
                    <div class="border border-blue-600 rounded-xl overflow-hidden">
                        <div class="p-4 bg-blue-600 text-white font-semibold">
                            <h5 class="text-xl mb-0"><i class="fas fa-chart-bar mr-2"></i> Todas as Perícias do Sistema</h5>
                        </div>
                        <div class="p-4">
                            <p class="text-gray-500 mb-4 text-sm">
                                Lista completa de perícias disponíveis no sistema. As perícias em que você é proficiente são destacadas.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="all-skills-list">
                                <!-- Lista completa de perícias renderizada pelo JS -->
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Card Footer -->
                <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <a href="#" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Voltar ao Overview
                        </a>
                        <button type="submit" class="btn-purple px-6 py-3 rounded-lg font-bold shadow-md hover:shadow-lg transition duration-150 disabled:opacity-50" id="save-button">
                            <i class="fas fa-save mr-2"></i>Salvar Perícias
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript para a lógica de seleção de perícias -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skillSelectionArea = document.getElementById('skill-selection-area');
            const allSkillsList = document.getElementById('all-skills-list');
            const feedbackDiv = document.getElementById('skill-selection-feedback');
            const feedbackText = document.getElementById('skill-feedback-text');
            const form = document.getElementById('step5-form');
            const saveButton = document.getElementById('save-button');
            const proficienciaSelect = document.getElementById('bonus_proficiencia');

            let currentBonusProficiencia = BONUS_PROFICIENCIA_INICIAL;
            const periciasAutomaticas = PERICIAS_CLASSE.fixas.concat(Object.keys(PERICIAS_ORIGEM));

            /**
             * Calcula o modificador de atributo.
             * @param {string} atributo - O nome do atributo (ex: 'destreza').
             * @returns {number} O valor do modificador.
             */
            const getModificador = (atributo) => {
                const valor = ATRIBUTOS[atributo] || 10;
                // Fórmula padrão D&D: (Valor - 10) / 2, arredondado para baixo
                return Math.floor((valor - 10) / 2);
            };

            /**
             * Formata um número para exibir um sinal de '+' se for positivo.
             * @param {number} valor
             * @returns {string}
             */
            const formatValue = (valor) => {
                return valor >= 0 ? `+${valor}` : `${valor}`;
            };

            /**
             * Renderiza os checkboxes para a lista de escolha de classe.
             */
            const renderSkillSelection = () => {
                skillSelectionArea.innerHTML = '';

                // Filtra as perícias da lista de escolha que já não são automáticas (fixas ou de origem)
                const periciasParaEscolher = PERICIAS_CLASSE.lista.filter(pericia => !periciasAutomaticas.includes(pericia));

                periciasParaEscolher.forEach(pericia => {
                    const atributoRelacionado = PERICIAS_SISTEMA[pericia];
                    const modificador = getModificador(atributoRelacionado);
                    const total = modificador + currentBonusProficiencia;

                    const html = `
                        <label class="block cursor-pointer">
                            <div class="skill-option transition duration-150">
                                <input class="hidden skill-checkbox"
                                    type="checkbox"
                                    name="pericias_escolhidas[]"
                                    value="${pericia}">

                                <div class="flex justify-between items-start">
                                    <span class="font-medium text-gray-800">${pericia}</span>
                                    <div class="text-right">
                                        <small class="text-sm text-gray-500">${PERICIAS_SISTEMA_NAMES[atributoRelacionado]}</small>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1 flex justify-between">
                                    <span>Mod. Base: ${formatValue(modificador)}</span>
                                    <span class="font-bold text-purple-custom">Proficiente: ${formatValue(total)}</span>
                                </div>
                            </div>
                        </label>
                    `;
                    skillSelectionArea.innerHTML += html;
                });

                // Adiciona listeners após a renderização
                document.querySelectorAll('.skill-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', handleSkillSelection);

                    // Adiciona o listener ao pai (label) para o efeito visual e clique em qualquer lugar do card
                    checkbox.closest('label').addEventListener('click', (e) => {
                        // Garante que o clique no label ative/desative o checkbox
                        if (e.target.tagName !== 'INPUT') {
                            checkbox.checked = !checkbox.checked;
                            handleSkillSelection({target: checkbox});
                        }
                    });
                });

                // Atualiza o display do botão e estado inicial
                handleSkillSelection();
            };

            /**
             * Renderiza a lista completa de perícias, calculando o total final.
             */
            const renderAllSkills = () => {
                allSkillsList.innerHTML = '';
                const escolhidas = Array.from(document.querySelectorAll('.skill-checkbox:checked')).map(cb => cb.value);

                for (const pericia in PERICIAS_SISTEMA) {
                    const atributo = PERICIAS_SISTEMA[pericia];
                    const modificador = getModificador(atributo);

                    // Verifica se é proficiente (automática ou escolhida)
                    let proficiente = periciasAutomaticas.includes(pericia) || escolhidas.includes(pericia);

                    const bonus = proficiente ? currentBonusProficiencia : 0;
                    const total = modificador + bonus;
                    const totalDisplay = formatValue(total);
                    const modificadorDisplay = formatValue(modificador);
                    const bonusDisplay = formatValue(bonus);

                    const cardClasses = proficiente ? 'border-l-green-500 bg-green-50 shadow-md' : 'border-l-gray-300 bg-white';
                    const textClasses = proficiente ? 'font-bold text-green-700' : 'text-gray-800';

                    let profType = '';
                    if (PERICIAS_CLASSE.fixas.includes(pericia)) {
                        profType = 'Classe (Fixa)';
                    } else if (Object.keys(PERICIAS_ORIGEM).includes(pericia)) {
                        profType = 'Origem';
                    } else if (escolhidas.includes(pericia)) {
                        profType = 'Classe (Escolha)';
                    }


                    const html = `
                        <div class="skill-card border-l-4 rounded-lg ${cardClasses} transition duration-150">
                            <div class="p-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-base ${textClasses}">${pericia}</span>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            ${PERICIAS_SISTEMA_NAMES[atributo]} (${modificadorDisplay})
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl ${textClasses} font-extrabold">${totalDisplay}</div>
                                        <div class="text-xs text-gray-500">${modificadorDisplay} ${bonus >= 0 ? '+' : ''} ${bonus} (Prof.)</div>
                                    </div>
                                </div>
                                ${proficiente ? `<div class="text-center mt-1"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-500 text-white">${profType}</span></div>` : ''}
                            </div>
                        </div>
                    `;
                    allSkillsList.innerHTML += html;
                }
            };

            /**
             * Lida com a seleção de checkboxes, limitando a quantidade.
             */
            const handleSkillSelection = (e) => {
                const checked = document.querySelectorAll('.skill-checkbox:checked');
                let count = checked.length;

                // Atualiza a visualização no card de escolha (selecionado/não selecionado)
                if (e && e.target) {
                    const optionDiv = e.target.closest('.skill-option');
                    if (e.target.checked) {
                        if (count > MAX_ESCOLHAS) {
                            // Se exceder, desmarca o que acabou de ser clicado
                            e.target.checked = false;
                            count--;
                        } else {
                            optionDiv.classList.add('skill-option-selected');
                        }
                    } else {
                        optionDiv.classList.remove('skill-option-selected');
                    }
                }

                // Aplica a classe visual para seleções persistidas após uma potencial reversão
                document.querySelectorAll('.skill-checkbox').forEach(checkbox => {
                    const optionDiv = checkbox.closest('.skill-option');
                    if (checkbox.checked) {
                         optionDiv.classList.add('skill-option-selected');
                    } else {
                        optionDiv.classList.remove('skill-option-selected');
                    }
                });


                const remaining = MAX_ESCOLHAS - count;

                // Atualiza o feedback e o estado do botão
                if (count < MAX_ESCOLHAS) {
                    feedbackDiv.classList.remove('hidden', 'bg-red-100', 'border-red-400', 'text-red-700');
                    feedbackDiv.classList.add('bg-yellow-100', 'border-yellow-400', 'text-yellow-700');
                    feedbackText.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> Você precisa escolher mais ${remaining} perícia(s).`;
                    saveButton.disabled = true;
                } else if (count > MAX_ESCOLHAS) {
                    feedbackDiv.classList.remove('hidden', 'bg-yellow-100', 'border-yellow-400', 'text-yellow-700');
                    feedbackDiv.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
                    feedbackText.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> Limite excedido! Desmarque ${count - MAX_ESCOLHAS} perícia(s).`;
                    saveButton.disabled = true;
                } else {
                    feedbackDiv.classList.add('hidden');
                    feedbackText.textContent = '';
                    saveButton.disabled = false;
                }

                // Garante que checkboxes restantes fiquem desabilitados se o limite foi atingido
                document.querySelectorAll('.skill-checkbox:not(:checked)').forEach(cb => {
                    cb.disabled = (count >= MAX_ESCOLHAS);
                    // Adiciona um estilo visual de desabilitado ao card
                    cb.closest('label').classList.toggle('opacity-50', count >= MAX_ESCOLHAS);
                    cb.closest('label').classList.toggle('cursor-not-allowed', count >= MAX_ESCOLHAS);
                });

                // Re-renderiza a lista completa de perícias para refletir as escolhas
                renderAllSkills();
            };

            /**
             * Lida com a mudança do Bônus de Proficiência.
             */
            const handleProficienciaChange = (e) => {
                currentBonusProficiencia = parseInt(e.target.value, 10);
                // Re-renderiza ambas as listas
                renderSkillSelection(); // Isso também chama handleSkillSelection()
                renderAllSkills();
            };

            // Listeners
            proficienciaSelect.addEventListener('change', handleProficienciaChange);

            // Inicialização
            // Define o valor inicial do select (necessário para que o JS saiba o valor padrão, mesmo que já esteja 'selected')
            currentBonusProficiencia = parseInt(proficienciaSelect.value, 10);
            renderSkillSelection();
            renderAllSkills();

            // Intercepta o submit do formulário para validação final
            form.addEventListener('submit', (e) => {
                const finalCount = document.querySelectorAll('.skill-checkbox:checked').length;
                if (finalCount !== MAX_ESCOLHAS) {
                    e.preventDefault();
                    feedbackDiv.classList.remove('hidden', 'bg-yellow-100', 'border-yellow-400', 'text-yellow-700');
                    feedbackDiv.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
                    feedbackText.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> Atenção! Você deve escolher exatamente ${MAX_ESCOLHAS} perícias da classe.`;

                    // Scrolla para a mensagem de feedback
                    feedbackDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                // Se tudo estiver OK, o formulário será enviado.
            });
        });
    </script>
</body>
</html>
