@extends('layouts.app')

@section('title', 'Criação - Raça, Classe e Origem')

@section('content')
@php
// Variáveis do sistema e personagem
$sistema = $personagem->sistema;
$personagemId = $personagem->id;
$personagemNivel = $personagem->nivel ?? 1;

// Simulação dos atributos base do personagem (geralmente definidos no Passo 1).
$atributosBase = $personagem->atributos_base ?? ['for' => 10, 'des' => 10, 'con' => 10, 'int' => 10, 'sab' => 10, 'car' => 10];

// Garantindo que $sistema->atributos seja um array associativo para o JS.
$sistemaAtributos = is_string($sistema->atributos) ? json_decode($sistema->atributos, true) : ($sistema->atributos ?? []);

// Definição das classes das seções para uso nas Tabs
$sections = [
    'raca' => ['icon' => 'fas fa-dna', 'title' => 'Raça', 'color' => 'warning', 'tab_id' => 'raca-tab', 'data_list' => $racas ?? []],
    'classe' => ['icon' => 'fas fa-helmet-safety', 'title' => 'Classe', 'color' => 'primary', 'tab_id' => 'classe-tab', 'data_list' => $classes ?? []],
    'origem' => ['icon' => 'fas fa-house-chimney', 'title' => 'Origem', 'color' => 'success', 'tab_id' => 'origem-tab', 'data_list' => $origens ?? []],
];

// Mapeamento dos nomes dos atributos para exibição
$attributeLabels = [
    'for' => 'Força (FOR)', 'des' => 'Destreza (DES)', 'con' => 'Constituição (CON)',
    'int' => 'Inteligência (INT)', 'sab' => 'Sabedoria (SAB)', 'car' => 'Carisma (CAR)',
];

// URL para o próximo passo (Passo 3: Pontos de Habilidade, etc.)
$nextStepUrl = route('personagem.create.step3', ['personagem' => $personagemId]);
// URL para o passo anterior (Passo 1: Nome, Atributos Base, etc.)
$prevStepUrl = route('personagem.create.step1', ['personagem' => $personagemId]);


@endphp

<div class="container mx-auto px-4 py-8">
<div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6 lg:p-8">
<h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">
Passo 2: Raça, Classe e Origem
</h1>
<p class="mb-6 text-gray-600 dark:text-gray-300">
Escolha os pilares do seu personagem. Os bônus de cada escolha serão exibidos abaixo.
</p>

    <form id="step2Form" action="{{ $nextStepUrl }}" method="POST">
        @csrf
        @method('PUT') {{-- Usamos PUT para edição, mesmo que seja criação sequencial --}}

        {{-- Tabs de Navegação (Mobile e Desktop) --}}
        <ul class="nav nav-tabs flex flex-wrap -mb-px" id="myTab" role="tablist">
            @foreach ($sections as $key => $section)
                <li class="nav-item flex-auto text-center" role="presentation">
                    <button class="nav-link w-full py-3 px-2 text-sm font-medium text-center text-gray-500 rounded-t-lg border-b-2 border-transparent hover:text-{{ $section['color'] }}-600 dark:hover:text-{{ $section['color'] }}-500 transition duration-150 ease-in-out {{ $key === 'raca' ? 'active border-b-{{ $section['color'] }}-600' : '' }}"
                        id="{{ $section['tab_id'] }}" data-bs-toggle="tab" data-bs-target="#{{ $key }}-content-tab" type="button" role="tab"
                        aria-controls="{{ $key }}-content-tab" aria-selected="{{ $key === 'raca' ? 'true' : 'false' }}">
                        <i class="{{ $section['icon'] }} mr-1"></i> {{ $section['title'] }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="myTabContent">
            {{-- RAÇA --}}
            <div class="tab-pane fade show active" id="raca-content-tab" role="tabpanel" aria-labelledby="raca-tab">
                <div class="mt-4">
                    <label for="raca_id" class="block text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">Selecione a Raça:</label>
                    <select name="raca_id" id="raca_id" required
                        class="mt-1 block w-full py-3 px-4 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-warning-500 focus:border-warning-500 sm:text-lg text-gray-900 dark:text-white">
                        <option value="">-- Selecione uma Raça --</option>
                        @foreach ($racas as $raca)
                            <option value="{{ $raca->id }}"
                                data-descricao="{{ $raca->descricao ?? 'Sem descrição detalhada.' }}"
                                data-atributos="{{ $raca->atributos_json ?? '{}' }}"
                                data-habilidades="{{ $raca->habilidades_json ?? '[]' }}"
                                {{ $personagem->raca_id == $raca->id ? 'selected' : '' }}>
                                {{ $raca->nome }}
                            </option>
                        @endforeach
                    </select>

                    <div id="raca-details" class="mt-6 bg-yellow-50 dark:bg-gray-750 p-4 border-l-4 border-warning-500 rounded-r-md transition-all duration-300 ease-in-out">
                        <h3 class="text-xl font-semibold text-warning-700 dark:text-warning-400 mb-3">Detalhes da Raça</h3>
                        <div id="raca-description" class="text-gray-700 dark:text-gray-300 mb-4">
                            Selecione uma raça para ver sua descrição, bônus de atributos e habilidades.
                        </div>
                        <div id="raca-bonuses" class="mb-4"></div>
                        <div id="raca-abilities"></div>
                    </div>
                </div>
            </div>

            {{-- CLASSE --}}
            <div class="tab-pane fade" id="classe-content-tab" role="tabpanel" aria-labelledby="classe-tab">
                <div class="mt-4">
                    <label for="classe_id" class="block text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">Selecione a Classe:</label>
                    <select name="classe_id" id="classe_id" required
                        class="mt-1 block w-full py-3 px-4 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-lg text-gray-900 dark:text-white">
                        <option value="">-- Selecione uma Classe --</option>
                        @foreach ($classes as $classe)
                            <option value="{{ $classe->id }}"
                                data-descricao="{{ $classe->descricao ?? 'Sem descrição detalhada.' }}"
                                data-atributos="{{ $classe->atributos_json ?? '{}' }}"
                                data-habilidades="{{ $classe->habilidades_json ?? '[]' }}"
                                data-pv-inicial="{{ $classe->pv_inicial ?? 0 }}"
                                data-pericias="{{ $classe->pericias_json ?? '[]' }}"
                                {{ $personagem->classe_id == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nome }} (PV Inicial: {{ $classe->pv_inicial ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>

                    <div id="classe-details" class="mt-6 bg-blue-50 dark:bg-gray-750 p-4 border-l-4 border-primary-500 rounded-r-md transition-all duration-300 ease-in-out">
                        <h3 class="text-xl font-semibold text-primary-700 dark:text-primary-400 mb-3">Detalhes da Classe</h3>
                        <div id="classe-description" class="text-gray-700 dark:text-gray-300 mb-4">
                            Selecione uma classe para ver sua descrição, bônus (se houver) e habilidades iniciais.
                        </div>
                        <div id="classe-bonuses" class="mb-4"></div>
                        <div id="classe-abilities" class="mb-4"></div>
                        <div id="classe-initial-hp"></div>
                    </div>
                </div>
            </div>

            {{-- ORIGEM --}}
            <div class="tab-pane fade" id="origem-content-tab" role="tabpanel" aria-labelledby="origem-tab">
                <div class="mt-4">
                    <label for="origem_id" class="block text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">Selecione a Origem:</label>
                    <select name="origem_id" id="origem_id" required
                        class="mt-1 block w-full py-3 px-4 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-success-500 focus:border-success-500 sm:text-lg text-gray-900 dark:text-white">
                        <option value="">-- Selecione uma Origem --</option>
                        @foreach ($origens as $origem)
                            <option value="{{ $origem->id }}"
                                data-descricao="{{ $origem->descricao ?? 'Sem descrição detalhada.' }}"
                                data-habilidades="{{ $origem->habilidades_json ?? '[]' }}"
                                {{ $personagem->origem_id == $origem->id ? 'selected' : '' }}>
                                {{ $origem->nome }}
                            </option>
                        @endforeach
                    </select>

                    <div id="origem-details" class="mt-6 bg-green-50 dark:bg-gray-750 p-4 border-l-4 border-success-500 rounded-r-md transition-all duration-300 ease-in-out">
                        <h3 class="text-xl font-semibold text-success-700 dark:text-success-400 mb-3">Detalhes da Origem</h3>
                        <div id="origem-description" class="text-gray-700 dark:text-gray-300 mb-4">
                            Selecione uma origem para ver sua descrição e a habilidade única que ela concede.
                        </div>
                        <div id="origem-abilities"></div>
                    </div>
                </div>
            </div>

            {{-- Seção de Visualização de Atributos Totais --}}
            <div class="mt-8 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-inner">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">Visualização de Atributos (Total)</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Seus atributos base ({{ implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($atributosBase), $atributosBase)) }})
                    ajustados pelos bônus de Raça e Classe.
                </p>
                <div id="total-attributes-preview" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    {{-- O JS preencherá aqui --}}
                </div>
            </div>

        </div> {{-- Fim do tab-content --}}

        {{-- Botões de Navegação --}}
        <div class="flex justify-between mt-8">
            <a href="{{ $prevStepUrl }}" class="flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Anterior
            </a>
            <button type="submit" id="nextStepButton"
                class="flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Próximo Passo <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </form>

</div>


</div>

@endsection

@push('scripts')

<script>
// JSON dos atributos base e sistema (para uso no JS)
const ATRIBUTOS_BASE = @json($atributosBase);
const ATRIBUTOS_SISTEMA = @json($sistemaAtributos);
const ATTRIBUTE_LABELS = @json($attributeLabels);
let currentRacaBonus = {};
let currentClasseBonus = {};

// Função auxiliar para formatar a lista de habilidades
function formatHabilidades(habilidades, title) {
    if (!habilidades || habilidades.length === 0) {
        return &#39;&#39;;
    }

    let html = `&lt;h4 class=&quot;font-semibold text-lg text-gray-800 dark:text-gray-100 mt-4&quot;&gt;${title}:&lt;/h4&gt;&lt;ul class=&quot;list-disc ml-5 space-y-2 text-gray-700 dark:text-gray-300&quot;&gt;`;
    habilidades.forEach(hab =&gt; {
        html += `&lt;li&gt;&lt;strong&gt;${hab.nome}&lt;/strong&gt;: ${hab.descricao || &#39;Sem descrição.&#39;} (${hab.tipo || &#39;Passiva&#39;})&lt;/li&gt;`;
    });
    html += `&lt;/ul&gt;`;
    return html;
}

// Função para calcular e exibir os atributos totais
function updateAttriTotalPreview() {
    const totalAttributes = { ...ATRIBUTOS_BASE };
    const allBonus = { ...currentRacaBonus, ...currentClasseBonus };

    // Aplica todos os bônus
    for (const [attr, bonus] of Object.entries(allBonus)) {
        if (totalAttributes.hasOwnProperty(attr)) {
            totalAttributes[attr] += bonus;
        }
    }

    let previewHtml = &#39;&#39;;
    for (const [attrKey, baseValue] of Object.entries(ATRIBUTOS_BASE)) {
        const label = ATTRIBUTE_LABELS[attrKey] || attrKey.toUpperCase();
        const totalValue = totalAttributes[attrKey];
        const bonus = totalValue - baseValue;

        previewHtml += `
            &lt;div class=&quot;p-3 border rounded-lg bg-white dark:bg-gray-800 shadow-sm transition duration-150 ease-in-out&quot;&gt;
                &lt;p class=&quot;text-sm font-medium text-gray-500 dark:text-gray-400&quot;&gt;${label}&lt;/p&gt;
                &lt;div class=&quot;flex items-center mt-1&quot;&gt;
                    &lt;span class=&quot;text-2xl font-bold text-indigo-600 dark:text-indigo-400&quot;&gt;${totalValue}&lt;/span&gt;
                    &lt;span class=&quot;ml-2 text-sm ${bonus &gt;= 0 ? &#39;text-green-500&#39; : &#39;text-red-500&#39;} font-semibold&quot;&gt;
                        (${bonus &gt;= 0 ? &#39;+&#39; : &#39;&#39;}${bonus})
                    &lt;/span&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        `;
    }
    document.getElementById(&#39;total-attributes-preview&#39;).innerHTML = previewHtml;
}

// --- Funções de Atualização de Detalhes ---

function displayAttributeBonus(bonusObj, targetId) {
    const targetElement = document.getElementById(targetId);
    targetElement.innerHTML = &#39;&#39;;

    const attributes = Object.entries(bonusObj).filter(([, value]) =&gt; value !== 0);

    if (attributes.length === 0) {
        targetElement.innerHTML = &#39;&lt;p class=&quot;text-gray-600 dark:text-gray-400&quot;&gt;Nenhum bônus de atributo.&lt;/p&gt;&#39;;
        return;
    }

    let html = &#39;&lt;h4 class=&quot;font-semibold text-lg text-gray-800 dark:text-gray-100 mb-2&quot;&gt;Bônus de Atributos:&lt;/h4&gt;&#39;;
    html += &#39;&lt;ul class=&quot;space-y-1&quot;&gt;&#39;;
    attributes.forEach(([attrKey, bonus]) =&gt; {
        const label = ATTRIBUTE_LABELS[attrKey] || attrKey.toUpperCase();
        html += `&lt;li class=&quot;flex items-center text-sm font-medium ${bonus &gt; 0 ? &#39;text-green-600&#39; : &#39;text-red-600&#39;}&quot;&gt;
            &lt;i class=&quot;fas fa-${bonus &gt; 0 ? &#39;plus&#39; : &#39;minus&#39;} mr-2&quot;&gt;&lt;/i&gt;
            ${label}: &lt;strong&gt;${bonus}&lt;/strong&gt;
        &lt;/li&gt;`;
    });
    html += &#39;&lt;/ul&gt;&#39;;
    targetElement.innerHTML = html;
}

// Raça
function updateRacaDetails(option) {
    const detailsContainer = document.getElementById(&#39;raca-details&#39;);
    const descElement = document.getElementById(&#39;raca-description&#39;);
    const bonusesElement = document.getElementById(&#39;raca-bonuses&#39;);
    const abilitiesElement = document.getElementById(&#39;raca-abilities&#39;);

    if (!option.value) {
        // Caso nenhum item selecionado
        descElement.innerHTML = &#39;Selecione uma raça para ver sua descrição, bônus de atributos e habilidades.&#39;;
        bonusesElement.innerHTML = &#39;&#39;;
        abilitiesElement.innerHTML = &#39;&#39;;
        detailsContainer.classList.remove(&#39;p-4&#39;);
        currentRacaBonus = {};
        updateAttriTotalPreview();
        return;
    }

    detailsContainer.classList.add(&#39;p-4&#39;);
    const descricao = option.getAttribute(&#39;data-descricao&#39;) || &#39;Sem descrição.&#39;;
    const atributos = JSON.parse(option.getAttribute(&#39;data-atributos&#39;) || &#39;{}&#39;);
    const habilidades = JSON.parse(option.getAttribute(&#39;data-habilidades&#39;) || &#39;[]&#39;);

    currentRacaBonus = atributos; // Salva o bônus atual

    descElement.innerHTML = `&lt;p&gt;${descricao}&lt;/p&gt;`;

    displayAttributeBonus(atributos, &#39;raca-bonuses&#39;);

    abilitiesElement.innerHTML = formatHabilidades(habilidades, &#39;Habilidades Raciais&#39;);

    updateAttriTotalPreview();
}

// Classe
function updateClasseDetails(option) {
    const detailsContainer = document.getElementById(&#39;classe-details&#39;);
    const descElement = document.getElementById(&#39;classe-description&#39;);
    const bonusesElement = document.getElementById(&#39;classe-bonuses&#39;);
    const abilitiesElement = document.getElementById(&#39;classe-abilities&#39;);
    const hpElement = document.getElementById(&#39;classe-initial-hp&#39;);

    if (!option.value) {
         descElement.innerHTML = &#39;Selecione uma classe para ver sua descrição, bônus (se houver) e habilidades iniciais.&#39;;
        bonusesElement.innerHTML = &#39;&#39;;
        abilitiesElement.innerHTML = &#39;&#39;;
        hpElement.innerHTML = &#39;&#39;;
        detailsContainer.classList.remove(&#39;p-4&#39;);
        currentClasseBonus = {};
        updateAttriTotalPreview();
        return;
    }

    detailsContainer.classList.add(&#39;p-4&#39;);
    const descricao = option.getAttribute(&#39;data-descricao&#39;) || &#39;Sem descrição.&#39;;
    const atributos = JSON.parse(option.getAttribute(&#39;data-atributos&#39;) || &#39;{}&#39;);
    const habilidades = JSON.parse(option.getAttribute(&#39;data-habilidades&#39;) || &#39;[]&#39;);
    const pvInicial = option.getAttribute(&#39;data-pv-inicial&#39;) || &#39;N/A&#39;;
    // const pericias = JSON.parse(option.getAttribute(&#39;data-pericias&#39;) || &#39;[]&#39;); // Não usado na prévia simples

    currentClasseBonus = atributos; // Salva o bônus atual

    descElement.innerHTML = `&lt;p&gt;${descricao}&lt;/p&gt;`;

    displayAttributeBonus(atributos, &#39;classe-bonuses&#39;);

    abilitiesElement.innerHTML = formatHabilidades(habilidades, &#39;Habilidades de Classe (Nível 1)&#39;);

    hpElement.innerHTML = `&lt;h4 class=&quot;font-semibold text-lg text-gray-800 dark:text-gray-100 mt-4&quot;&gt;Pontos de Vida Iniciais:&lt;/h4&gt;&lt;p class=&quot;text-primary-600 dark:text-primary-400 font-bold text-xl&quot;&gt;${pvInicial}&lt;/p&gt;`;

    updateAttriTotalPreview();
}

// Origem
function updateOrigemDetails(option) {
    const detailsContainer = document.getElementById(&#39;origem-details&#39;);
    const descElement = document.getElementById(&#39;origem-description&#39;);
    const abilitiesElement = document.getElementById(&#39;origem-abilities&#39;);

    if (!option.value) {
        descElement.innerHTML = &#39;Selecione uma origem para ver sua descrição e a habilidade única que ela concede.&#39;;
        abilitiesElement.innerHTML = &#39;&#39;;
        detailsContainer.classList.remove(&#39;p-4&#39;);
        return;
    }

    detailsContainer.classList.add(&#39;p-4&#39;);
    const descricao = option.getAttribute(&#39;data-descricao&#39;) || &#39;Sem descrição.&#39;;
    const habilidades = JSON.parse(option.getAttribute(&#39;data-habilidades&#39;) || &#39;[]&#39;);

    descElement.innerHTML = `&lt;p&gt;${descricao}&lt;/p&gt;`;
    abilitiesElement.innerHTML = formatHabilidades(habilidades, &#39;Habilidade de Origem&#39;);
}

// --- Listeners e Inicialização ---

document.getElementById(&#39;raca_id&#39;).addEventListener(&#39;change&#39;, (e) =&gt; {
    updateRacaDetails(e.target.options[e.target.selectedIndex]);
});

document.getElementById(&#39;classe_id&#39;).addEventListener(&#39;change&#39;, (e) =&gt; {
    updateClasseDetails(e.target.options[e.target.selectedIndex]);
});

document.getElementById(&#39;origem_id&#39;).addEventListener(&#39;change&#39;, (e) =&gt; {
    updateOrigemDetails(e.target.options[e.target.selectedIndex]);
});

// Inicialização ao carregar a página (para pré-seleção)
window.addEventListener(&#39;load&#39;, () =&gt; {
    const racaSelect = document.getElementById(&#39;raca_id&#39;);
    const classeSelect = document.getElementById(&#39;classe_id&#39;);
    const origemSelect = document.getElementById(&#39;origem_id&#39;);

    // Dispara a atualização dos detalhes se houver seleção inicial
    if (racaSelect.value) {
        updateRacaDetails(racaSelect.options[racaSelect.selectedIndex]);
    } else {
        // Se não houver seleção inicial, garantir que a primeira TAB (Raça) esteja ativa no mobile
        // Este bloco é mais complexo em sistemas de frontend que não usam jQuery ou Bootstrap puro,
        // mas o foco é garantir que o estado visual e de dados esteja correto.
    }

    if (classeSelect.value) updateClasseDetails(classeSelect.options[classeSelect.selectedIndex]);
    if (origemSelect.value) updateOrigemDetails(origemSelect.options[origemSelect.selectedIndex]);

    // Garante que o preview total seja calculado mesmo que um dos campos não esteja selecionado,
    // usando apenas os que estão. Se ambos estiverem vazios, mostrará os atributos base.
    updateAttriTotalPreview();
});


</script>

@endpush
