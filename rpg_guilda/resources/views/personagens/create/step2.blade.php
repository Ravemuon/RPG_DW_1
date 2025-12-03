@extends('layouts.app')

@section('title', 'Criação - Raça, Classe e Origem')

@section('content')
@php
    $sistema = $personagem->sistema;
    // IMPORTANTE: Adicione aqui as variáveis que o JS precisa e que não estão nos data-attributes das opções.
    // Simulação dos atributos base do personagem (geralmente definidos no Passo 1, ou 0 se for ponto de compra).
    $atributosBase = $personagem->atributos_base ?? ['for' => 10, 'des' => 10, 'con' => 10, 'int' => 10, 'sab' => 10, 'car' => 10];
    // Garantindo que $sistema->atributos seja um array associativo para o JS.
    $sistemaAtributos = is_string($sistema->atributos) ? json_decode($sistema->atributos, true) : ($sistema->atributos ?? []);

    // Definição das classes das seções para uso nas Tabs
    $sections = [
        'raca' => ['icon' => 'fas fa-dna', 'title' => 'Raça', 'color' => 'warning', 'tab_id' => 'raca-tab'],
        'classe' => ['icon' => 'fas fa-helmet-safety', 'title' => 'Classe', 'color' => 'primary', 'tab_id' => 'classe-tab'],
        'origem' => ['icon' => 'fas fa-house-chimney', 'title' => 'Origem', 'color' => 'success', 'tab_id' => 'origem-tab'],
    ];
@endphp

<div class="container my-5">
    <div class="card shadow-lg border-0">
        {{-- CABEÇALHO APRIMORADO --}}
        <div class="card-header bg-light border-bottom border-warning border-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-1 text-dark"><i class="fas fa-scroll me-2 text-warning"></i>Escolha de Raça, Classe e Origem</h1>
                    <p class="mb-0 text-muted">Personagem: <strong>{{ $personagem->nome }}</strong> | Sistema: <span class="fw-bold text-primary">{{ $sistema->nome }}</span></p>
                </div>
                <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <form action="{{ route('personagens.store.step2', $personagem->id) }}" method="POST" id="step2-form">
            @csrf

            {{-- ALERTA GERAL DE ERROS DE VALIDAÇÃO (SUBSTITUI ALERT()) --}}
            <div id="general-alert" class="alert alert-danger d-none m-3 mb-0" role="alert"></div>

            <input type="hidden" name="pericias_classe_selecionadas" id="pericias_classe_selecionadas" value="[]">

            <div class="card-body">
                {{-- SELETORES EM COLUNAS (VISÍVEL EM TODAS AS TELAS) --}}
                <div class="row g-4 mb-4">
                    {{-- COLUNA RAÇA --}}
                    <div class="col-lg-4">
                        <label for="raca_id" class="form-label fw-bold text-warning"><i class="fas fa-dna me-2"></i>Raça</label>
                        <select name="raca_id" id="raca_id" class="form-select form-select-lg" required>
                            <option value="">Selecione uma raça</option>
                            @foreach($racas as $raca)
                                @php
                                    $modificadores = is_string($raca->modificadores_atributos) ? json_decode($raca->modificadores_atributos, true) : ($raca->modificadores_atributos ?? []);
                                @endphp
                                <option value="{{ $raca->id }}"
                                        data-descricao="{{ $raca->descricao ?? '' }}"
                                        data-modificadores='@json($modificadores)'
                                        data-tipo-bonus="{{ $raca->tipo_bonus ?? 'flat' }}"
                                        data-bonus-livre="{{ $raca->bonus_livre ?? 0 }}"
                                        data-pagina="{{ $raca->pagina ?? '' }}"
                                        {{ $personagem->raca_id == $raca->id ? 'selected' : '' }}>
                                    {{ $raca->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- COLUNA CLASSE --}}
                    <div class="col-lg-4">
                        <label for="classe_id" class="form-label fw-bold text-primary"><i class="fas fa-helmet-safety me-2"></i>Classe</label>
                        <select name="classe_id" id="classe_id" class="form-select form-select-lg" required>
                            <option value="">Selecione uma classe</option>
                            @foreach($classes as $classe)
                                @php
                                    $periciasIniciais = is_string($classe->pericias_iniciais) ? json_decode($classe->pericias_iniciais, true) : ($classe->pericias_iniciais ?? []);
                                    $equipamentoInicial = is_string($classe->equipamento_inicial) ? json_decode($classe->equipamento_inicial, true) : ($classe->equipamento_inicial ?? []);
                                    $atributosBonus = is_string($classe->atributos_bonus) ? json_decode($classe->atributos_bonus, true) : ($classe->atributos_bonus ?? []);
                                    $poderes = is_string($classe->poderes) ? json_decode($classe->poderes, true) : ($classe->poderes ?? []);
                                @endphp
                                <option value="{{ $classe->id }}"
                                        data-descricao="{{ $classe->descricao ?? '' }}"
                                        data-dado-vida="{{ $classe->dado_vida ?? 'd6' }}"
                                        data-usa-magia="{{ $classe->usa_magia ? 'Sim' : 'Não' }}"
                                        data-pericias='@json($periciasIniciais)'
                                        data-equipamento='@json($equipamentoInicial)'
                                        data-atributos-bonus='@json($atributosBonus)'
                                        data-poderes='@json($poderes)'
                                        data-pagina="{{ $classe->pagina ?? '' }}"
                                        {{ $personagem->classe_id == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- COLUNA ORIGEM --}}
                    <div class="col-lg-4">
                        <label for="origem_id" class="form-label fw-bold text-success"><i class="fas fa-house-chimney me-2"></i>Origem</label>
                        <select name="origem_id" id="origem_id" class="form-select form-select-lg" required>
                            <option value="">Selecione uma origem</option>
                            @foreach($origens as $origem)
                                @php
                                    $bonusPericias = is_string($origem->bonus_pericias) ? json_decode($origem->bonus_pericias, true) : ($origem->bonus_pericias ?? []);
                                    $recursosAdicionais = is_string($origem->recursos_adicionais) ? json_decode($origem->recursos_adicionais, true) : ($origem->recursos_adicionais ?? []);
                                @endphp
                                <option value="{{ $origem->id }}"
                                        data-descricao="{{ $origem->descricao ?? '' }}"
                                        data-bonus-pericias='@json($bonusPericias)'
                                        data-recursos-adicionais='@json($recursosAdicionais)'
                                        data-pagina="{{ $origem->pagina ?? '' }}"
                                        {{ $personagem->origem_id == $origem->id ? 'selected' : '' }}>
                                    {{ $origem->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="mt-4 mb-4">

                {{-- GUIA DE NAVEGAÇÃO (VISÍVEL APENAS EM MOBILE/TABLET) --}}
                <ul class="nav nav-tabs nav-justified d-lg-none mb-3" id="main-tabs" role="tablist">
                    @foreach($sections as $key => $section)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link text-{{ $section['color'] }} fw-bold {{ $loop->first ? 'active' : '' }}"
                                id="{{ $section['tab_id'] }}"
                                data-bs-toggle="tab"
                                href="#{{ $key }}-content-tab"
                                role="tab"
                                aria-controls="{{ $key }}-content-tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <i class="{{ $section['icon'] }} me-1"></i> {{ $section['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- DETALHES EM COLUNAS (VISÍVEL APENAS EM DESKTOP) --}}
                <div class="row g-4 d-none d-lg-flex" id="desktop-details-row">
                    {{-- DETALHES RAÇA (DESKTOP) --}}
                    <div class="col-lg-4">
                        <div class="card h-100 border-warning border-start border-4">
                            <div class="card-header bg-warning text-dark"><h4 class="mb-0"><i class="fas fa-dna me-2"></i> Detalhes da Raça</h4></div>
                            <div class="card-body">
                                <div id="raca-placeholder-desktop" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma raça.</p></div>
                                <div id="raca-content-desktop" style="display: none;">
                                    {{-- CONTEÚDO REPLICADO ABAIXO (Ver TAB CONTENT) --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- DETALHES CLASSE (DESKTOP) --}}
                    <div class="col-lg-4">
                        <div class="card h-100 border-primary border-start border-4">
                            <div class="card-header bg-primary text-white"><h4 class="mb-0"><i class="fas fa-helmet-safety me-2"></i> Detalhes da Classe</h4></div>
                            <div class="card-body">
                                <div id="classe-placeholder-desktop" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma classe.</p></div>
                                <div id="classe-content-desktop" style="display: none;">
                                    {{-- CONTEÚDO REPLICADO ABAIXO (Ver TAB CONTENT) --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- DETALHES ORIGEM (DESKTOP) --}}
                    <div class="col-lg-4">
                        <div class="card h-100 border-success border-start border-4">
                            <div class="card-header bg-success text-white"><h4 class="mb-0"><i class="fas fa-house-chimney me-2"></i> Detalhes da Origem</h4></div>
                            <div class="card-body">
                                <div id="origem-placeholder-desktop" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma origem.</p></div>
                                <div id="origem-content-desktop" style="display: none;">
                                    {{-- CONTEÚDO REPLICADO ABAIXO (Ver TAB CONTENT) --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CONTEÚDO DAS TABS (VISÍVEL APENAS EM MOBILE/TABLET) --}}
                <div class="tab-content d-lg-none" id="main-tabs-content">

                    {{-- TAB RAÇA --}}
                    <div class="tab-pane fade show active" id="raca-content-tab" role="tabpanel" aria-labelledby="raca-tab">
                        <div class="card border-warning border-start border-4">
                            <div class="card-body">
                                <div id="raca-placeholder-mobile" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma raça.</p></div>
                                <div id="raca-content-mobile" class="fade" style="display: none;">
                                    <p class="text-muted small">
                                        <i class="fas fa-book me-1"></i>Página <span id="raca-pagina"></span>
                                    </p>
                                    <p class="border-start border-warning border-3 ps-2 fst-italic" id="raca-descricao"></p>

                                    <h6 class="mt-3 text-warning"><i class="fas fa-star me-1"></i> Bônus em Atributos</h6>
                                    <table class="table table-sm table-striped table-bordered">
                                        <thead class="bg-warning text-dark">
                                            <tr>
                                                <th>Atributo</th>
                                                <th class="text-center">Base</th>
                                                <th class="text-center">Bônus</th>
                                                <th class="text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="atributos-dashboard"></tbody>
                                    </table>
                                    <p class="small fst-italic pt-1" id="raca-bonus-livre-info"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB CLASSE --}}
                    <div class="tab-pane fade" id="classe-content-tab" role="tabpanel" aria-labelledby="classe-tab">
                        <div class="card border-primary border-start border-4">
                            <div class="card-body">
                                <div id="classe-placeholder-mobile" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma classe.</p></div>
                                <div id="classe-content-mobile" class="fade" style="display: none;">
                                    <p class="text-muted small">
                                        <i class="fas fa-book me-1"></i>Página <span id="classe-pagina"></span> | <i class="fas fa-hat-wizard me-1"></i>Usa Magia: <strong id="classe-usa-magia"></strong>
                                    </p>
                                    <p class="border-start border-primary border-3 ps-2 fst-italic" id="classe-descricao"></p>

                                    <div class="card bg-light p-2 mb-3">
                                        <p class="mb-1 small"><strong><i class="fas fa-heartbeat me-1 text-danger"></i> Dado de Vida:</strong> <span id="classe-dado-vida" class="fw-bold text-primary"></span></p>
                                        <p class="mb-0 small"><strong><i class="fas fa-star me-1 text-warning"></i> Bônus em Atributos:</strong> <span id="classe-atributos-bonus" class="text-primary">Nenhum</span></p>
                                    </div>

                                    <h6 class="mt-3 text-primary"><i class="fas fa-bolt me-1"></i> Poderes de Nível 1</h6>
                                    <ul id="classe-poderes" class="list-unstyled small"></ul>

                                    <h6 class="mt-3 text-primary"><i class="fas fa-bullseye me-1"></i> Proficiência em Perícias</h6>
                                    <div id="classe-pericias" class="mb-2 small"></div>

                                    <div id="classe-pericias-selecao-container"></div>
                                    <div id="pericia-limit-alert" class="alert alert-danger p-2 small mt-2" style="display:none;"></div>

                                    <h6 class="mt-3 text-primary"><i class="fas fa-boxes me-1"></i> Equipamento Inicial</h6>
                                    <ul id="classe-equipamento" class="list-unstyled small"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB ORIGEM --}}
                    <div class="tab-pane fade" id="origem-content-tab" role="tabpanel" aria-labelledby="origem-tab">
                         <div class="card border-success border-start border-4">
                            <div class="card-body">
                                <div id="origem-placeholder-mobile" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma origem.</p></div>
                                <div id="origem-content-mobile" class="fade" style="display: none;">
                                    <p class="text-muted small">
                                        <i class="fas fa-book me-1"></i>Página <span id="origem-pagina"></span>
                                    </p>
                                    <p class="border-start border-success border-3 ps-2 fst-italic" id="origem-descricao"></p>

                                    <h6 class="mt-3 text-success"><i class="fas fa-check-double me-1"></i> Bônus em Perícias</h6>
                                    <div id="origem-pericias" class="small"></div>

                                    <h6 class="mt-3 text-success"><i class="fas fa-gift me-1"></i> Recursos Adicionais</h6>
                                    <ul id="origem-recursos" class="list-unstyled small"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RESUMO DAS ESCOLHAS (FIXO) --}}
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card bg-light shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0 text-primary"><i class="fas fa-clipboard-list me-2"></i> Resumo das Escolhas</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-horizontal-md text-center" id="resumo-escolhas">
                                    <div class="list-group-item flex-fill">
                                        <h6 class="mb-1 text-warning">🧬 Raça</h6>
                                        <div id="resumo-raca" class="fw-bold">{{ $personagem->raca_id ? ($racas->firstWhere('id', $personagem->raca_id)->nome ?? 'Carregando...') : 'Não selecionada' }}</div>
                                    </div>
                                    <div class="list-group-item flex-fill">
                                        <h6 class="mb-1 text-primary">⚔️ Classe</h6>
                                        <div id="resumo-classe" class="fw-bold">{{ $personagem->classe_id ? ($classes->firstWhere('id', $personagem->classe_id)->nome ?? 'Carregando...') : 'Não selecionada' }}</div>
                                    </div>
                                    <div class="list-group-item flex-fill">
                                        <h6 class="mb-1 text-success">📖 Origem</h6>
                                        <div id="resumo-origem" class="fw-bold">{{ $personagem->origem_id ? ($origens->firstWhere('id', $personagem->origem_id)->nome ?? 'Carregando...') : 'Não selecionada' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar ao Overview
                    </a>
                    <button type="submit" class="btn btn-warning btn-lg shadow-sm">
                        <i class="fas fa-save me-2"></i>Salvar e Continuar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- O script de interatividade (JS) OTIMIZADO --}}
<script>
    // Variáveis PHP passadas para o escopo JS
    const sistemaAtributos = @json($sistemaAtributos);
    const atributosBase = @json($atributosBase);

    // Variável global para armazenar os IDs de conteúdo de desktop e mobile
    const contentIds = {
        raca: { desktop: 'raca-content-desktop', mobile: 'raca-content-mobile', placeholder: { desktop: 'raca-placeholder-desktop', mobile: 'raca-placeholder-mobile' }, tab: 'raca-tab' },
        classe: { desktop: 'classe-content-desktop', mobile: 'classe-content-mobile', placeholder: { desktop: 'classe-placeholder-desktop', mobile: 'classe-placeholder-mobile' }, tab: 'classe-tab' },
        origem: { desktop: 'origem-content-desktop', mobile: 'origem-content-mobile', placeholder: { desktop: 'origem-placeholder-desktop', mobile: 'origem-placeholder-mobile' }, tab: 'origem-tab' },
    };

    /**
     * Função utilitária para exibir/esconder o conteúdo e placeholders em ambas as visualizações (desktop e mobile).
     * @param {string} sectionKey - 'raca', 'classe', ou 'origem'.
     * @param {boolean} hasContent - Se o conteúdo deve ser exibido (true) ou o placeholder (false).
     */
    function toggleContent(sectionKey, hasContent) {
        const ids = contentIds[sectionKey];

        // 1. Desktop
        const contentD = document.getElementById(ids.desktop);
        const placeholderD = document.getElementById(ids.placeholder.desktop);

        if (contentD && placeholderD) {
            contentD.style.display = hasContent ? 'block' : 'none';
            placeholderD.style.display = hasContent ? 'none' : 'block';
            if (hasContent) {
                // Adiciona a classe 'active show' manualmente para desktop (não usa fade)
                contentD.classList.add('active', 'show');
            } else {
                 contentD.classList.remove('active', 'show');
            }
        }

        // 2. Mobile/Tablet (Tabs)
        const contentM = document.getElementById(ids.mobile);
        const placeholderM = document.getElementById(ids.placeholder.mobile);
        const tabLink = document.getElementById(ids.tab);

        if (contentM && placeholderM) {
            contentM.style.display = hasContent ? 'block' : 'none';
            placeholderM.style.display = hasContent ? 'none' : 'block';

            if (hasContent) {
                 // Ativa a tab correspondente (apenas se for mobile e o elemento existir)
                if (window.innerWidth < 992 && tabLink) { // 992px é o breakpoint do 'lg' do Bootstrap
                    new bootstrap.Tab(tabLink).show();
                }
                // Adiciona as classes 'active show' para o efeito fade do Bootstrap
                setTimeout(() => {
                    contentM.classList.add('active', 'show');
                }, 50); // Pequeno delay para garantir que o 'fade' funcione
            } else {
                contentM.classList.remove('active', 'show');
            }
        }
    }

    // Função utilitária para renderizar JSON como lista/mapa (mantida)
    function renderJsonAsList(data) {
        if (!data || Object.keys(data).length === 0) {
            return '<span class="text-muted fst-italic">Nenhum/Nenhuma.</span>';
        }
        let html = '<ul class="list-unstyled small">';
        if (Array.isArray(data)) {
            data.forEach(item => {
                html += `<li><i class="fas fa-box me-1 text-primary"></i> ${item}</li>`;
            });
        } else if (typeof data === 'object') {
            for (const key in data) {
                const value = data[key];
                if (typeof value === 'number' && key.length > 3) {
                     html += `<li><i class="fas fa-check-double me-1 text-success"></i> <strong>${key}</strong>: Bônus Fixo de ${value > 0 ? '+' : ''}${value}</li>`;
                } else {
                     html += `<li><i class="fas fa-star me-1 text-primary"></i> <strong>${key}:</strong> ${value}</li>`;
                }
            }
        }
        html += '</ul>';
        return html;
    }

    // Função para renderizar detalhes da Raça
    function updateRacaDetails(selectedOption) {
        if (!selectedOption || !selectedOption.value) {
            toggleContent('raca', false);
            document.getElementById('resumo-raca').textContent = 'Não selecionada';
            return;
        }

        const data = selectedOption.dataset;
        const modificadores = JSON.parse(data.modificadores);
        const tipoBonus = data.tipoBonus;
        const bonusLivre = parseInt(data.bonusLivre);

        // Atualiza elementos fora do container (Resumo)
        document.getElementById('raca-pagina').textContent = `${data.pagina}`;
        document.getElementById('raca-descricao').textContent = data.descricao;
        document.getElementById('resumo-raca').textContent = selectedOption.textContent.trim();

        // 1. Renderizar o Dashboard de Atributos (precisa ser feito apenas uma vez, pois o elemento é compartilhado - #atributos-dashboard)
        let dashboardHtml = '';
        for (const [key, nome] of Object.entries(sistemaAtributos)) {
            const bonus = modificadores[key] || 0;
            const base = atributosBase[key] || 0;
            const total = (typeof base === 'number') ? base + bonus : 'N/D';

            dashboardHtml += `
                <tr>
                    <td class="fw-bold">${nome} (${key.toUpperCase()})</td>
                    <td class="text-center">${base}</td>
                    <td class="text-center text-warning fw-bold">${bonus > 0 ? '+' : ''}${bonus}</td>
                    <td class="text-center fw-bold">${total}</td>
                </tr>
            `;
        }
        document.getElementById('atributos-dashboard').innerHTML = dashboardHtml;

        // 2. Info de Bônus Livre (precisa ser feito apenas uma vez)
        let bonusLivreInfo = '';
        const bonusLivreElement = document.getElementById('raca-bonus-livre-info');

        if (bonusLivre > 0) {
            switch (tipoBonus) {
                case 'flat':
                    bonusLivreInfo = `⚠️ <strong>Bônus Livre</strong>: Concede um bônus adicional e fixo de +${bonusLivre} em um Atributo à sua escolha (a ser definido no próximo passo).`;
                    break;
                case 'escolha':
                    bonusLivreInfo = `⚠️ <strong>Bônus Livre</strong>: Você pode escolher ${bonusLivre} Atributo(s) para receber um bônus (valor a definir pelo sistema).`;
                    break;
                default:
                    bonusLivreInfo = `⚠️ <strong>Bônus Livre</strong>: Esta raça tem um bônus livre especial: ${bonusLivre} (${tipoBonus}).`;
            }
            bonusLivreElement.classList.remove('text-muted');
            bonusLivreElement.classList.add('text-danger');
        } else {
            bonusLivreInfo = 'Não há bônus livres adicionais de Atributo nesta Raça.';
            bonusLivreElement.classList.add('text-muted');
            bonusLivreElement.classList.remove('text-danger');
        }
        bonusLivreElement.innerHTML = bonusLivreInfo;

        // Transfere o conteúdo para o desktop (pois os IDs são diferentes)
        const contentM = document.getElementById(contentIds.raca.mobile).innerHTML;
        document.getElementById(contentIds.raca.desktop).innerHTML = contentM;

        toggleContent('raca', true);
    }

    // Função para lidar com a seleção de perícias da classe
    function setupPericiaSelection(quantidade) {
        const checkboxes = document.querySelectorAll('#classe-pericias-selecao-container .pericia-choice');
        const limitAlert = document.getElementById('pericia-limit-alert');
        const hiddenInput = document.getElementById('pericias_classe_selecionadas');

        const updateState = () => {
            const selected = document.querySelectorAll('#classe-pericias-selecao-container .pericia-choice:checked').length;
            const choicesArray = Array.from(document.querySelectorAll('#classe-pericias-selecao-container .pericia-choice:checked')).map(cb => cb.value);
            hiddenInput.value = JSON.stringify(choicesArray);

            if (selected > quantidade) {
                // Isso nunca deve acontecer devido à lógica abaixo, mas é um fallback
                limitAlert.textContent = `Erro: Você só pode selecionar ${quantidade} perícia(s).`;
                limitAlert.style.display = 'block';
            } else if (selected === quantidade) {
                limitAlert.textContent = `✅ Limite de perícias atingido. Você selecionou ${quantidade} perícia(s) de Classe.`;
                limitAlert.classList.remove('alert-danger');
                limitAlert.classList.add('alert-success');
                limitAlert.style.display = 'block';
            } else {
                limitAlert.textContent = `Selecione mais ${quantidade - selected} perícia(s) de Classe.`;
                limitAlert.classList.remove('alert-success');
                limitAlert.classList.add('alert-danger');
                limitAlert.style.display = 'block';
            }
        };

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const selected = document.querySelectorAll('#classe-pericias-selecao-container .pericia-choice:checked').length;
                if (selected > quantidade && e.target.checked) {
                    e.target.checked = false; // Desmarcar se exceder
                }
                updateState();
            });
        });

        // Estado inicial
        hiddenInput.value = '[]';
        checkboxes.forEach(cb => cb.checked = false);
        updateState();
    }

    // Função para renderizar detalhes da Classe
    function updateClasseDetails(selectedOption) {
        if (!selectedOption || !selectedOption.value) {
            toggleContent('classe', false);
            document.getElementById('resumo-classe').textContent = 'Não selecionada';
            return;
        }

        const data = selectedOption.dataset;
        const pericias = JSON.parse(data.pericias);
        const equipamento = JSON.parse(data.equipamento);
        const atributosBonus = JSON.parse(data.atributosBonus);
        const poderes = JSON.parse(data.poderes);

        // Atualiza elementos fora do container (Resumo)
        document.getElementById('classe-pagina').textContent = `${data.pagina}`;
        document.getElementById('classe-descricao').textContent = data.descricao;
        document.getElementById('classe-dado-vida').textContent = data.dadoVida;
        document.getElementById('classe-usa-magia').textContent = data.usaMagia;
        document.getElementById('resumo-classe').textContent = selectedOption.textContent.trim();

        // Bônus em Atributos (da Classe)
        let atributosHtml = 'Nenhum';
        if (atributosBonus && Object.keys(atributosBonus).length > 0) {
            atributosHtml = Object.entries(atributosBonus).map(([key, value]) => {
                const nomeAtributo = sistemaAtributos[key] || key.toUpperCase();
                return `${nomeAtributo} ${value > 0 ? '+' : ''}${value}`;
            }).join(', ');
        }
        document.getElementById('classe-atributos-bonus').textContent = atributosHtml;

        // Poderes
        document.getElementById('classe-poderes').innerHTML = renderJsonAsList(poderes);

        // Perícias (Proficiência) - Seleção interativa
        const periciasContainer = document.getElementById('classe-pericias');
        const periciasSelecaoContainer = document.getElementById('classe-pericias-selecao-container');
        const limitAlert = document.getElementById('pericia-limit-alert');
        const hiddenInput = document.getElementById('pericias_classe_selecionadas');

        let periciasHtml = '';
        let periciasSelecaoHtml = '';
        limitAlert.style.display = 'none';
        hiddenInput.value = '[]'; // Resetar a lista de perícias selecionadas

        if (pericias.tipo === 'escolha' && Array.isArray(pericias.lista)) {
            const quantidade = parseInt(pericias.quantidade);
            periciasHtml = `<p class="alert alert-primary p-2 mb-0"><i class="fas fa-hand-pointer me-1"></i> Você ganha <strong>Proficiência</strong> em <strong>${quantidade}</strong> Perícia(s) à sua escolha entre as listadas. Marque suas opções:</p>`;

            periciasSelecaoHtml = `<div class="list-group small mb-2">`;
            pericias.lista.forEach(pericia => {
                periciasSelecaoHtml += `
                    <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                        ${pericia}
                        <input class="form-check-input me-1 pericia-choice" type="checkbox" value="${pericia}">
                    </label>
                `;
            });
            periciasSelecaoHtml += '</div>';

            setTimeout(() => setupPericiaSelection(quantidade), 0);

        } else if (pericias.tipo === 'flat' && Array.isArray(pericias.lista)) {
            periciasHtml = `<p class="alert alert-primary p-2 mb-0"><i class="fas fa-lock me-1"></i> Proficiência automática nas seguintes Perícias:</p>`;
            periciasHtml += renderJsonAsList(pericias.lista);
            hiddenInput.value = JSON.stringify(pericias.lista); // Salva as perícias automáticas
        } else {
            periciasHtml = '<span class="text-muted fst-italic">Nenhuma Proficiência em perícia fornecida diretamente pela Classe no Nível 1.</span>';
        }

        periciasContainer.innerHTML = periciasHtml;
        periciasSelecaoContainer.innerHTML = periciasSelecaoHtml;

        // Equipamento
        document.getElementById('classe-equipamento').innerHTML = renderJsonAsList(equipamento);

         // Transfere o conteúdo para o desktop (pois os IDs são diferentes)
        const contentM = document.getElementById(contentIds.classe.mobile).innerHTML;
        document.getElementById(contentIds.classe.desktop).innerHTML = contentM;

        toggleContent('classe', true);
    }

    // Função para renderizar detalhes da Origem
    function updateOrigemDetails(selectedOption) {
        if (!selectedOption || !selectedOption.value) {
            toggleContent('origem', false);
            document.getElementById('resumo-origem').textContent = 'Não selecionada';
            return;
        }

        const data = selectedOption.dataset;
        const bonusPericias = JSON.parse(data.bonusPericias);
        const recursosAdicionais = JSON.parse(data.recursosAdicionais);

        // Atualiza elementos fora do container (Resumo)
        document.getElementById('origem-pagina').textContent = `${data.pagina}`;
        document.getElementById('origem-descricao').textContent = data.descricao;
        document.getElementById('resumo-origem').textContent = selectedOption.textContent.trim();

        // Bônus em Perícias
        let periciasHtml = '';
        if (bonusPericias && Object.keys(bonusPericias).length > 0) {
            periciasHtml = `<p class="alert alert-success border-success border-start border-2 p-2 mb-0 text-dark"><i class="fas fa-user-check me-1"></i> <strong>Proficiência e/ou Bônus Fixo</strong> em Perícias específicas:</p>`;
            periciasHtml += renderJsonAsList(bonusPericias);
        } else {
            periciasHtml = '<span class="text-muted fst-italic">Nenhum bônus em Perícia fornecido pela Origem.</span>';
        }
        document.getElementById('origem-pericias').innerHTML = periciasHtml;

        // Recursos Adicionais
        document.getElementById('origem-recursos').innerHTML = renderJsonAsList(recursosAdicionais);

        // Transfere o conteúdo para o desktop (pois os IDs são diferentes)
        const contentM = document.getElementById(contentIds.origem.mobile).innerHTML;
        document.getElementById(contentIds.origem.desktop).innerHTML = contentM;

        toggleContent('origem', true);
    }

    // Função para lidar com a submissão do formulário e validação das perícias da classe
    function handleSubmit(event) {
        const racaSelect = document.getElementById('raca_id');
        const classeSelect = document.getElementById('classe_id');
        const origemSelect = document.getElementById('origem_id');
        const generalAlert = document.getElementById('general-alert');
        const limitAlert = document.getElementById('pericia-limit-alert');

        // Limpar alertas anteriores
        generalAlert.classList.add('d-none');
        generalAlert.innerHTML = '';

        // 1. Validação básica de seleção
        if (!racaSelect.value || !classeSelect.value || !origemSelect.value) {
            generalAlert.innerHTML = '⚠️ Por favor, selecione uma **Raça**, uma **Classe** e uma **Origem** antes de continuar.';
            generalAlert.classList.remove('d-none');
            racaSelect.scrollIntoView({ behavior: 'smooth' });
            event.preventDefault();
            return;
        }

        // 2. Validação das perícias da Classe
        const selectedOption = classeSelect.options[classeSelect.selectedIndex];
        const periciasData = JSON.parse(selectedOption.dataset.pericias || '{}');

        if (periciasData.tipo === 'escolha' && Array.isArray(periciasData.lista)) {
            const quantidadeRequerida = parseInt(periciasData.quantidade);
            const selectedCount = document.querySelectorAll('#classe-pericias-selecao-container .pericia-choice:checked').length;

            if (selectedCount !== quantidadeRequerida) {
                const message = `⚠️ Você deve selecionar exatamente **${quantidadeRequerida} perícia(s)** de Classe. Selecionadas: ${selectedCount}.`;
                generalAlert.innerHTML = message;
                generalAlert.classList.remove('d-none');

                // Exibe o alerta específico da perícia e foca na seção
                limitAlert.textContent = message.replace('⚠️ ', '');
                limitAlert.style.display = 'block';
                limitAlert.classList.remove('alert-success');
                limitAlert.classList.add('alert-danger');

                document.getElementById('classe-pericias-selecao-container').scrollIntoView({ behavior: 'smooth' });
                event.preventDefault();
                return;
            }
        }

        // Se a validação for bem-sucedida
        limitAlert.style.display = 'none';
        console.log('Formulário validado e pronto para envio.');
    }

    // Event Listeners
    document.getElementById('raca_id').addEventListener('change', (e) => {
        updateRacaDetails(e.target.options[e.target.selectedIndex]);
    });

    document.getElementById('classe_id').addEventListener('change', (e) => {
        updateClasseDetails(e.target.options[e.target.selectedIndex]);
    });

    document.getElementById('origem_id').addEventListener('change', (e) => {
        updateOrigemDetails(e.target.options[e.target.selectedIndex]);
    });

    document.getElementById('step2-form').addEventListener('submit', handleSubmit);


    // Inicialização ao carregar a página (para pré-seleção)
    window.addEventListener('load', () => {
        const racaSelect = document.getElementById('raca_id');
        const classeSelect = document.getElementById('classe_id');
        const origemSelect = document.getElementById('origem_id');

        // Dispara a atualização dos detalhes se houver seleção inicial
        if (racaSelect.value) {
            updateRacaDetails(racaSelect.options[racaSelect.selectedIndex]);
        } else {
            // Se não houver seleção inicial, garantir que a primeira TAB (Raça) esteja ativa no mobile
            const firstTab = document.getElementById('raca-content-tab');
            if (firstTab) {
                 firstTab.classList.add('active', 'show');
            }
        }
        if (classeSelect.value) updateClasseDetails(classeSelect.options[classeSelect.selectedIndex]);
        if (origemSelect.value) updateOrigemDetails(origemSelect.options[origemSelect.selectedIndex]);
    });
</script>
@endsection
