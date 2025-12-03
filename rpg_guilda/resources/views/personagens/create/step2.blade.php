@extends('layouts.app')

@section('title', 'Criação - Raça, Classe e Origem')

@section('content')
@php
    $sistema = $personagem->sistema;
    // Simulação dos atributos base (Passo 1 ou ponto de compra)
    $atributosBase = $personagem->atributos_base ?? ['for' => 10, 'des' => 10, 'con' => 10, 'int' => 10, 'sab' => 10, 'car' => 10]; [cite_start]// [cite: 181]
    [cite_start]// Garantindo que $sistema->atributos seja um array associativo para o JS. [cite: 182]
    $sistemaAtributos = is_string($sistema->atributos) ?
        json_decode($sistema->atributos, true) : ($sistema->atributos ?? []); [cite_start]// [cite: 183]

    // Definição das classes das seções para uso nas Tabs
    $sections = [
        [cite_start]'raca' => ['icon' => 'fas fa-dna', 'title' => 'Raça', 'color' => 'warning', 'tab_id' => 'raca-tab'], // [cite: 183]
        [cite_start]'classe' => ['icon' => 'fas fa-helmet-safety', 'title' => 'Classe', 'color' => 'primary', 'tab_id' => 'classe-tab'], // [cite: 183]
        [cite_start]'origem' => ['icon' => 'fas fa-house-chimney', 'title' => 'Origem', 'color' => 'success', 'tab_id' => 'origem-tab'], // [cite: 183]
    ];
@endphp

<div class="container my-5">
    <div class="card shadow-lg border-0">
        {{-- CABEÇALHO --}}
        <div class="card-header bg-light border-bottom border-warning border-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-1 text-dark"><i class="fas fa-scroll me-2 text-warning"></i>Escolha de Raça, Classe e Origem</h1>
                    [cite_start]<p class="mb-0 text-muted">Personagem: <strong>{{ $personagem->nome }}</strong> | [cite: 185]
                    [cite_start]Sistema: <span class="fw-bold text-primary">{{ $sistema->nome }}</span></p> [cite: 186]
                </div>
                <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        [cite_start]<form action="{{ route('personagens.store.step2', $personagem->id) }}" method="POST" id="step2-form"> [cite: 187]
            @csrf

            <input type="hidden" name="pericias_classe_selecionadas" id="pericias_classe_selecionadas" value="{{ $personagem->pericias_selecionadas ?? '[]' }}">

            <div class="card-body">
                {{-- SELETORES EM COLUNAS --}}
                [cite_start]<div class="row g-4 mb-4"> [cite: 188]
                    {{-- COLUNA RAÇA --}}
                    <div class="col-lg-4">
                        [cite_start]<label for="raca_id" class="form-label fw-bold text-warning"><i class="fas fa-dna me-2"></i>Raça</label> [cite: 189]
                        <select name="raca_id" id="raca_id" class="form-select form-select-lg" required>
                            <option value="">Selecione uma raça</option>
                            @foreach($racas as $raca)
                                @php
                                    $modificadores = is_string($raca->modificadores_atributos) ?
                                        json_decode($raca->modificadores_atributos, true) : ($raca->modificadores_atributos ?? []); [cite_start]// [cite: 190, 191]
                                @endphp
                                <option value="{{ $raca->id }}"
                                        data-descricao="{{ $raca->descricao ?? '' }}"
                                        [cite_start]data-modificadores='@json($modificadores)' [cite: 192]
                                        data-tipo-bonus="{{ $raca->tipo_bonus ?? 'flat' }}"
                                        [cite_start]data-bonus-livre="{{ $raca->bonus_livre ?? 0 }}" [cite: 193]
                                        data-pagina="{{ $raca->pagina ?? '' }}"
                                        {{ $personagem->raca_id == $raca->id ? [cite_start]'selected' : '' }}> [cite: 194]
                                    {{ $raca->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- COLUNA CLASSE --}}
                    <div class="col-lg-4">
                        [cite_start]<label for="classe_id" class="form-label fw-bold text-primary"><i class="fas fa-helmet-safety me-2"></i>Classe</label> [cite: 196]
                        <select name="classe_id" id="classe_id" class="form-select form-select-lg" required>
                            <option value="">Selecione uma classe</option>
                            @foreach($classes as $classe)
                                @php
                                    $periciasIniciais = is_string($classe->pericias_iniciais) ?
                                        json_decode($classe->pericias_iniciais, true) : ($classe->pericias_iniciais ?? []); [cite_start]// [cite: 198]
                                    $equipamentoInicial = is_string($classe->equipamento_inicial) ? json_decode($classe->equipamento_inicial, true) : ($classe->equipamento_inicial ?? []); [cite_start]// [cite: 198]
                                    $atributosBonus = is_string($classe->atributos_bonus) ?
                                        json_decode($classe->atributos_bonus, true) : ($classe->atributos_bonus ?? []); [cite_start]// [cite: 199]
                                    $poderes = is_string($classe->poderes) ? json_decode($classe->poderes, true) : ($classe->poderes ?? []); [cite_start]// [cite: 199]
                                @endphp
                                <option value="{{ $classe->id }}"
                                        [cite_start]data-descricao="{{ $classe->descricao ?? '' }}" [cite: 200]
                                        [cite_start]data-dado-vida="{{ $classe->dado_vida ?? 'd6' }}" [cite: 201]
                                        data-usa-magia="{{ $classe->usa_magia ? 'Sim' : 'Não' }}"
                                        [cite_start]data-pericias='@json($periciasIniciais)' [cite: 202]
                                        data-equipamento='@json($equipamentoInicial)'
                                        data-atributos-bonus='@json($atributosBonus)'
                                        [cite_start]data-poderes='@json($poderes)' [cite: 203]
                                        data-pagina="{{ $classe->pagina ?? '' }}"
                                        data-pericias-quantidade="{{ $periciasIniciais['quantidade'] ?? 0 }}"
                                        {{ $personagem->classe_id == $classe->id ? [cite_start]'selected' : '' }}> [cite: 204, 205]
                                    {{ $classe->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- COLUNA ORIGEM --}}
                    <div class="col-lg-4">
                        [cite_start]<label for="origem_id" class="form-label fw-bold text-success"><i class="fas fa-house-chimney me-2"></i>Origem</label> [cite: 207]
                        <select name="origem_id" id="origem_id" class="form-select form-select-lg" required>
                            <option value="">Selecione uma origem</option>
                            @foreach($origens as $origem)
                                @php
                                    $bonusPericias = is_string($origem->bonus_pericias) ?
                                        json_decode($origem->bonus_pericias, true) : ($origem->bonus_pericias ?? []); [cite_start]// [cite: 209]
                                    $recursosAdicionais = is_string($origem->recursos_adicionais) ? json_decode($origem->recursos_adicionais, true) : ($origem->recursos_adicionais ?? []); [cite_start]// [cite: 209]
                                @endphp
                                <option value="{{ $origem->id }}"
                                        [cite_start]data-descricao="{{ $origem->descricao ?? '' }}" [cite: 210]
                                        [cite_start]data-bonus-pericias='@json($bonusPericias)' [cite: 211]
                                        data-recursos-adicionais='@json($recursosAdicionais)'
                                        [cite_start]data-pagina="{{ $origem->pagina ?? '' }}" [cite: 212]
                                        {{ $personagem->origem_id == $origem->id ? [cite_start]'selected' : '' }}> [cite: 213]
                                    {{ $origem->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="mt-4 mb-4">

                {{-- GUIA DE NAVEGAÇÃO (MOBILE/TABLET) --}}
                [cite_start]<ul class="nav nav-tabs nav-justified d-lg-none mb-3" id="main-tabs" role="tablist"> [cite: 215]
                    @foreach($sections as $key => $section)
                        <li class="nav-item" role="presentation">
                            [cite_start]<a class="nav-link text-{{ $section['color'] }} fw-bold {{ $loop->first ? 'active' : '' }}" [cite: 216]
                                id="{{ $section['tab_id'] }}"
                                data-bs-toggle="tab"
                                [cite_start]href="#{{ $key }}-content-tab" [cite: 217]
                                role="tab"
                                aria-controls="{{ $key }}-content-tab"
                                [cite_start]aria-selected="{{ $loop->first ? 'true' : 'false' }}"> [cite: 218, 219]
                                <i class="{{ $section['icon'] }} me-1"></i> {{ $section['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- DETALHES EM COLUNAS (DESKTOP) --}}
                [cite_start]<div class="row g-4 d-none d-lg-flex" id="desktop-details-row"> [cite: 220]
                    {{-- DETALHES RAÇA (DESKTOP) --}}
                    [cite_start]<div class="col-lg-4"> [cite: 221]
                        <div class="card h-100 border-warning border-start border-4">
                            [cite_start]<div class="card-header bg-warning text-dark"><h4 class="mb-0"><i class="fas fa-dna me-2"></i> Detalhes da Raça</h4></div> [cite: 222]
                            <div class="card-body">
                                [cite_start]<div id="raca-placeholder-desktop" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma raça.</p></div> [cite: 222]
                                <div id="raca-content-desktop" style="display: none;">
                                    [cite_start]{{-- CONTEÚDO SERÁ INJETADO DO MOBILE/TEMPLATE --}} [cite: 223]
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- DETALHES CLASSE (DESKTOP) --}}
                    [cite_start]<div class="col-lg-4"> [cite: 225]
                        <div class="card h-100 border-primary border-start border-4">
                            [cite_start]<div class="card-header bg-primary text-white"><h4 class="mb-0"><i class="fas fa-helmet-safety me-2"></i> Detalhes da Classe</h4></div> [cite: 225]
                            <div class="card-body">
                                [cite_start]<div id="classe-placeholder-desktop" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma classe.</p></div> [cite: 226]
                                <div id="classe-content-desktop" style="display: none;">
                                    [cite_start]{{-- CONTEÚDO SERÁ INJETADO DO MOBILE/TEMPLATE --}} [cite: 227]
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- DETALHES ORIGEM (DESKTOP) --}}
                    [cite_start]<div class="col-lg-4"> [cite: 228]
                        <div class="card h-100 border-success border-start border-4">
                            [cite_start]<div class="card-header bg-success text-white"><h4 class="mb-0"><i class="fas fa-house-chimney me-2"></i> Detalhes da Origem</h4></div> [cite: 229]
                            <div class="card-body">
                                [cite_start]<div id="origem-placeholder-desktop" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma origem.</p></div> [cite: 230]
                                <div id="origem-content-desktop" style="display: none;">
                                    [cite_start]{{-- CONTEÚDO SERÁ INJETADO DO MOBILE/TEMPLATE --}} [cite: 230]
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                [cite_start]{{-- CONTEÚDO DAS TABS (MOBILE/TABLET - ONDE O CONTEÚDO É RENDERIZADO) --}} [cite: 232]
                <div class="tab-content d-lg-none" id="main-tabs-content">

                    {{-- TAB RAÇA --}}
                    <div class="tab-pane fade show active" id="raca-content-tab" role="tabpanel" aria-labelledby="raca-tab">
                        [cite_start]<div class="card border-warning border-start border-4"> [cite: 233]
                            <div class="card-body">
                                [cite_start]<div id="raca-placeholder-mobile" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma raça.</p></div> [cite: 234]
                                <div id="raca-content-mobile" class="fade" style="display: none;">
                                    <p class="text-muted small">
                                        [cite_start]<i class="fas fa-book me-1"></i>Página <span id="raca-pagina"></span> [cite: 235]
                                    </p>
                                    [cite_start]<p class="border-start border-warning border-3 ps-2 fst-italic" id="raca-descricao"></p> [cite: 235]

                                    [cite_start]<h6 class="mt-3 text-warning"><i class="fas fa-star me-1"></i> Bônus em Atributos</h6> [cite: 236]
                                    <table class="table table-sm table-striped table-bordered">
                                        [cite_start]<thead class="bg-warning text-dark"> [cite: 237]
                                            <tr>
                                                <th>Atributo</th>
                                                [cite_start]<th class="text-center">Base</th> [cite: 238]
                                                [cite_start]<th class="text-center">Bônus</th> [cite: 239]
                                                [cite_start]<th class="text-center">Total</th> [cite: 239]
                                            </tr>
                                        </thead>
                                        [cite_start]<tbody id="atributos-dashboard"></tbody> [cite: 240]
                                    </table>
                                    [cite_start]<p class="small fst-italic pt-1" id="raca-bonus-livre-info"></p> [cite: 241]
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB CLASSE --}}
                    [cite_start]<div class="tab-pane fade" id="classe-content-tab" role="tabpanel" aria-labelledby="classe-tab"> [cite: 242]
                        [cite_start]<div class="card border-primary border-start border-4"> [cite: 243]
                            <div class="card-body">
                                [cite_start]<div id="classe-placeholder-mobile" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma classe.</p></div> [cite: 244]
                                <div id="classe-content-mobile" class="fade" style="display: none;">
                                    <p class="text-muted small">
                                        [cite_start]<i class="fas fa-book me-1"></i>Página <span id="classe-pagina"></span> | [cite: 245]
                                        [cite_start]<i class="fas fa-hat-wizard me-1"></i>Usa Magia: <strong id="classe-usa-magia"></strong> [cite: 246]
                                    </p>
                                    [cite_start]<p class="border-start border-primary border-3 ps-2 fst-italic" id="classe-descricao"></p> [cite: 246]

                                    [cite_start]<div class="card bg-light p-2 mb-3"> [cite: 247]
                                        [cite_start]<p class="mb-1 small"><strong><i class="fas fa-heartbeat me-1 text-danger"></i> Dado de Vida:</strong> <span id="classe-dado-vida" class="fw-bold text-primary"></span></p> [cite: 247]
                                        [cite_start]<p class="mb-0 small"><strong><i class="fas fa-star me-1 text-warning"></i> Bônus em Atributos:</strong> <span id="classe-atributos-bonus" class="text-primary">Nenhum</span></p> [cite: 248]
                                    </div>

                                    [cite_start]<h6 class="mt-3 text-primary"><i class="fas fa-bolt me-1"></i> Poderes de Nível 1</h6> [cite: 249]
                                    <ul id="classe-poderes" class="list-unstyled small"></ul>

                                    [cite_start]<h6 class="mt-3 text-primary"><i class="fas fa-bullseye me-1"></i> Proficiência em Perícias</h6> [cite: 250]
                                    <div id="classe-pericias" class="mb-2 small"></div>

                                    [cite_start]<div id="classe-pericias-selecao-container"></div> [cite: 250]
                                    [cite_start]<div id="pericia-limit-alert" class="alert alert-danger p-2 small mt-2" style="display:none;"></div> [cite: 251]

                                    [cite_start]<h6 class="mt-3 text-primary"><i class="fas fa-boxes me-1"></i> Equipamento Inicial</h6> [cite: 251]
                                    <ul id="classe-equipamento" class="list-unstyled small"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB ORIGEM --}}
                    [cite_start]<div class="tab-pane fade" id="origem-content-tab" role="tabpanel" aria-labelledby="origem-tab"> [cite: 253]
                        [cite_start]<div class="card border-success border-start border-4"> [cite: 254]
                            <div class="card-body">
                                [cite_start]<div id="origem-placeholder-mobile" class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i><p class="mb-0">Selecione uma origem.</p></div> [cite: 254]
                                <div id="origem-content-mobile" class="fade" style="display: none;">
                                    <p class="text-muted small">
                                        [cite_start]<i class="fas fa-book me-1"></i>Página <span id="origem-pagina"></span> [cite: 255]
                                    </p>
                                    [cite_start]<p class="border-start border-success border-3 ps-2 fst-italic" id="origem-descricao"></p> [cite: 256]

                                    [cite_start]<h6 class="mt-3 text-success"><i class="fas fa-check-double me-1"></i> Bônus em Perícias</h6> [cite: 257]
                                    <div id="origem-pericias" class="small"></div>

                                    [cite_start]<h6 class="mt-3 text-success"><i class="fas fa-gift me-1"></i> Recursos Adicionais</h6> [cite: 257]
                                    [cite_start]<ul id="origem-recursos" class="list-unstyled small"></ul> [cite: 258]
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RESUMO DAS ESCOLHAS (FIXO) --}}
                [cite_start]<div class="row mt-5"> [cite: 259]
                    <div class="col-12">
                        [cite_start]<div class="card bg-light shadow-sm"> [cite: 260]
                            <div class="card-header bg-white">
                                [cite_start]<h5 class="mb-0 text-primary"><i class="fas fa-clipboard-list me-2"></i> Resumo das Escolhas</h5> [cite: 261]
                            </div>
                            <div class="card-body">
                                [cite_start]<div class="list-group list-group-horizontal-md text-center" id="resumo-escolhas"> [cite: 262]
                                    [cite_start]<div class="list-group-item flex-fill"> [cite: 262]
                                        <h6 class="mb-1 text-warning">🧬 Raça</h6>
                                        <div id="resumo-raca"
                                            class="fw-bold">{{ $personagem->raca_id ? ($racas->firstWhere('id', $personagem->raca_id)[cite_start]->nome ?? 'Carregando...') : 'Não selecionada' }}</div> [cite: 263]
                                    </div>
                                    <div class="list-group-item flex-fill">
                                        [cite_start]<h6 class="mb-1 text-primary">⚔️ Classe</h6> [cite: 264]
                                        <div id="resumo-classe" class="fw-bold">{{ $personagem->classe_id ?
                                            ($classes->firstWhere('id', $personagem->classe_id)[cite_start]->nome ?? 'Carregando...') : 'Não selecionada' }}</div> [cite: 265]
                                    </div>
                                    <div class="list-group-item flex-fill">
                                        [cite_start]<h6 class="mb-1 text-success">📖 Origem</h6> [cite: 266]
                                        <div id="resumo-origem" class="fw-bold">{{ $personagem->origem_id ?
                                            ($origens->firstWhere('id', $personagem->origem_id)[cite_start]->nome ?? 'Carregando...') : 'Não selecionada' }}</div> [cite: 267]
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            [cite_start]<div class="card-footer bg-light"> [cite: 268]
                [cite_start]<div class="d-flex justify-content-between"> [cite: 269]
                    <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar ao Overview
                    </a>
                    [cite_start]<button type="submit" class="btn btn-warning btn-lg shadow-sm"> [cite: 270]
                        <i class="fas fa-save me-2"></i>Salvar e Continuar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

[cite_start]{{-- O script de interatividade (JS) OTIMIZADO --}} [cite: 271]
<script>
    document.addEventListener('DOMContentLoaded', function() {
        [cite_start]// Variáveis PHP passadas para o escopo JS [cite: 272]
        const sistemaAtributos = @json($sistemaAtributos);
        const atributosBase = @json($atributosBase);

        /**
         * Objeto para gerenciar toda a lógica de atualização do Passo 2.
         * @namespace PersonagemStep2
         */
        const PersonagemStep2 = {
            [cite_start]// Mapeamento de IDs para desktop/mobile/placeholders [cite: 272]
            contentIds: {
                raca: { desktop: 'raca-content-desktop', mobile: 'raca-content-mobile', placeholder: { desktop: 'raca-placeholder-desktop', mobile: 'raca-placeholder-mobile' }, tab: 'raca-tab', selectId: 'raca_id' },
                classe: { desktop: 'classe-content-desktop', mobile: 'classe-content-mobile', placeholder: { desktop: 'classe-placeholder-desktop', mobile: 'classe-placeholder-mobile' }, tab: 'classe-tab', selectId: 'classe_id' },
                origem: { desktop: 'origem-content-desktop', mobile: 'origem-content-mobile', placeholder: { desktop: 'origem-placeholder-desktop', mobile: 'origem-placeholder-mobile' }, tab: 'origem-tab', selectId: 'origem_id' },
            },

            [cite_start]// Variável para armazenar o estado atual das perícias selecionadas [cite: 314]
            periciasSelecionadas: @json(json_decode($personagem->pericias_selecionadas ?? '[]', true)),

            /**
             * [cite_start]Utilitário para exibir/esconder o conteúdo e placeholders em ambas as visualizações. [cite: 273, 274]
             * @param {string} sectionKey - 'raca', 'classe', ou 'origem'.
             * @param {boolean} hasContent - Se o conteúdo deve ser exibido (true) ou o placeholder (false).
             */
            toggleContent(sectionKey, hasContent) {
                const ids = this.contentIds[sectionKey]; [cite_start]// [cite: 275]

                [cite_start]// 1. Desktop [cite: 276]
                const contentD = document.getElementById(ids.desktop);
                const placeholderD = document.getElementById(ids.placeholder.desktop);
                [cite_start]if (contentD && placeholderD) { // [cite: 277]
                    contentD.style.display = hasContent ? 'block' : 'none'; [cite_start]// [cite: 278]
                    placeholderD.style.display = hasContent ? [cite_start]'none' : 'block'; [cite: 278]
                    [cite_start]if (hasContent) { // [cite: 279]
                        contentD.classList.add('active', 'show'); [cite_start]// [cite: 279]
                    } else {
                        contentD.classList.remove('active', 'show'); [cite_start]// [cite: 280, 281]
                    }
                }

                [cite_start]// 2. Mobile/Tablet (Tabs) [cite: 282]
                const contentM = document.getElementById(ids.mobile);
                const placeholderM = document.getElementById(ids.placeholder.mobile);
                const tabLink = document.getElementById(ids.tab);

                [cite_start]if (contentM && placeholderM) { // [cite: 282]
                    contentM.style.display = hasContent ? 'block' : 'none'; [cite_start]// [cite: 283]
                    placeholderM.style.display = hasContent ? 'none' : 'block'; [cite_start]// [cite: 283]
                    [cite_start]if (hasContent) { // [cite: 284]
                         // Ativa a tab correspondente (apenas se for mobile)
                        [cite_start]if (window.innerWidth < 992 && tabLink) { // 992px é o breakpoint do 'lg' do Bootstrap [cite: 284]
                            new bootstrap.Tab(tabLink).show(); [cite_start]// [cite: 285]
                        }
                        [cite_start]// Adiciona as classes 'active show' para o efeito fade do Bootstrap [cite: 286]
                        setTimeout(() => {
                            [cite_start]contentM.classList.add('active', 'show'); // [cite: 286]
                        }, 50);
                    } else {
                        contentM.classList.remove('active', 'show'); [cite_start]// [cite: 287]
                    }
                }
            },

            /**
             * [cite_start]Utilitário para renderizar JSON como lista/mapa. [cite: 287]
             */
            renderJsonAsList(data) {
                if (!data || (Array.isArray(data) && data.length === 0) || (typeof data === 'object' && Object.keys(data).length === 0)) {
                    return '<span class="text-muted fst-italic">Nenhum/Nenhuma.</span>'; [cite_start]// [cite: 288]
                }
                let html = '<ul class="list-unstyled small">'; [cite_start]// [cite: 288]
                [cite_start]if (Array.isArray(data)) { // [cite: 289]
                    data.forEach(item => {
                        [cite_start]html += `<li><i class="fas fa-box me-1 text-primary"></i> ${item}</li>`; // [cite: 289]
                    });
                [cite_start]} else if (typeof data === 'object') { // [cite: 290]
                    for (const key in data) {
                        const value = data[key]; [cite_start]// [cite: 290]
                        const icon = key.toLowerCase().includes('pericia') ? 'fas fa-check-double' : 'fas fa-star';
                        const color = key.toLowerCase().includes('pericia') ? 'text-success' : 'text-primary';

                        [cite_start]if (typeof value === 'number' && key.length > 3) { // [cite: 291]
                             html += `<li><i class="${icon} me-1 ${color}"></i> <strong>${key}</strong>: Bônus Fixo de ${value > 0 ? '+' : ''}${value}</li>`; [cite_start]// [cite: 292]
                        } else {
                             html += `<li><i class="${icon} me-1 ${color}"></i> <strong>${key}:</strong> ${value}</li>`; [cite_start]// [cite: 293]
                        }
                    }
                }
                html += '</ul>'; [cite_start]// [cite: 294]
                return html;
            },

            /**
             * Atualiza os detalhes da Raça.
             */
            updateRaca(selectedOption) {
                if (!selectedOption || !selectedOption.value) {
                    this.toggleContent('raca', false); [cite_start]// [cite: 295]
                    document.getElementById('resumo-raca').textContent = 'Não selecionada'; [cite_start]// [cite: 295]
                    return;
                }

                const data = selectedOption.dataset; [cite_start]// [cite: 296]
                // JSON.parse não é mais necessário, Laravel injeta data-attributes JSON diretamente como objeto/array se @json for usado
                const modificadores = JSON.parse(data.modificadores); [cite_start]// [cite: 296]
                const tipoBonus = data.tipoBonus; [cite_start]// [cite: 296]
                const bonusLivre = parseInt(data.bonusLivre); [cite_start]// [cite: 297]

                [cite_start]// Atualiza elementos fora do container (Resumo) [cite: 297]
                document.getElementById('raca-pagina').textContent = `${data.pagina}`; [cite_start]// [cite: 297]
                document.getElementById('raca-descricao').textContent = data.descricao; [cite_start]// [cite: 298]
                document.getElementById('resumo-raca').textContent = selectedOption.textContent.trim(); [cite_start]// [cite: 298]

                [cite_start]// 1. Renderizar o Dashboard de Atributos [cite: 298]
                let dashboardHtml = '';
                [cite_start]for (const [key, nome] of Object.entries(sistemaAtributos)) { // [cite: 299]
                    const bonus = modificadores[key] || 0; [cite_start]// [cite: 300]
                    const base = atributosBase[key] || 0; [cite_start]// [cite: 300]
                    const total = (typeof base === 'number') ? base + bonus : 'N/D'; [cite_start]// [cite: 300]
                    dashboardHtml += `
                        <tr>
                            <td class="fw-bold">${nome} (${key.toUpperCase()})</td>
                            <td class="text-center">${base}</td>
                            <td class="text-center text-warning fw-bold">${bonus > 0 ? [cite_start]'+' : ''}${bonus}</td> [cite: 302]
                            <td class="text-center fw-bold">${total}</td>
                        </tr>
                    `; [cite_start]// [cite: 303]
                }
                document.getElementById('atributos-dashboard').innerHTML = dashboardHtml; [cite_start]// [cite: 303]

                [cite_start]// 2. Info de Bônus Livre [cite: 304]
                let bonusLivreInfo = '';
                const bonusLivreElement = document.getElementById('raca-bonus-livre-info'); [cite_start]// [cite: 305]

                if (bonusLivre > 0) {
                    switch (tipoBonus) {
                        case 'flat':
                            bonusLivreInfo = `⚠️ <strong>Bônus Livre</strong>: Concede um bônus adicional e fixo de +${bonusLivre} em um Atributo à sua escolha (a ser definido no próximo passo).`; [cite_start]// [cite: 306]
                            break;
                        case 'escolha':
                            bonusLivreInfo = `⚠️ <strong>Bônus Livre</strong>: Você pode escolher ${bonusLivre} Atributo(s) para receber um bônus (valor a definir pelo sistema).`; [cite_start]// [cite: 307]
                            break;
                        default:
                            bonusLivreInfo = `⚠️ <strong>Bônus Livre</strong>: Esta raça tem um bônus livre especial: ${bonusLivre} (${tipoBonus}).`; [cite_start]// [cite: 308]
                    }
                    bonusLivreElement.classList.remove('text-muted'); [cite_start]// [cite: 308]
                    bonusLivreElement.classList.add('text-danger'); [cite_start]// [cite: 309]
                } else {
                    bonusLivreInfo = 'Não há bônus livres adicionais de Atributo nesta Raça.'; [cite_start]// [cite: 310]
                    bonusLivreElement.classList.add('text-muted'); [cite_start]// [cite: 310]
                    bonusLivreElement.classList.remove('text-danger'); [cite_start]// [cite: 310]
                }
                bonusLivreElement.innerHTML = bonusLivreInfo; [cite_start]// [cite: 311]

                [cite_start]// Transfere o conteúdo do mobile (template) para o desktop [cite: 311, 312]
                const contentM = document.getElementById(this.contentIds.raca.mobile).innerHTML;
                document.getElementById(this.contentIds.raca.desktop).innerHTML = contentM;

                this.toggleContent('raca', true); [cite_start]// [cite: 312]
            },

            /**
             * Lida com a seleção interativa de perícias da classe.
             * @param {number} quantidade - O número de perícias que podem ser selecionadas.
             */
            setupPericiaSelection(quantidade) {
                const container = document.getElementById('classe-pericias-selecao-container'); [cite_start]// [cite: 313]
                const checkboxes = container.querySelectorAll('.pericia-choice'); [cite_start]// [cite: 313]
                const limitAlert = document.getElementById('pericia-limit-alert'); [cite_start]// [cite: 313]
                const hiddenInput = document.getElementById('pericias_classe_selecionadas'); [cite_start]// [cite: 313]

                // Reinicia o estado e popula seleções iniciais
                limitAlert.style.display = 'none'; [cite_start]// [cite: 320]
                checkboxes.forEach(cb => {
                    cb.checked = this.periciasSelecionadas.includes(cb.value); // Usa o estado salvo
                    cb.removeEventListener('change', this.handlePericiaChange); // Remove listeners antigos
                });

                // Função de manipulação de evento separada para fácil remoção/adição
                this.handlePericiaChange = (e) => {
                    const selectedCount = container.querySelectorAll('.pericia-choice:checked').length; [cite_start]// [cite: 314]
                    const isChecked = e.target.checked; [cite_start]// [cite: 314]

                    if (isChecked && selectedCount > quantidade) { // Se ultrapassou o limite
                        e.target.checked = false; // Bloqueia a seleção
                        limitAlert.textContent = `Limite atingido: Você só pode selecionar ${quantidade} perícia(s).`; [cite_start]// [cite: 318]
                        limitAlert.style.display = 'block'; [cite_start]// [cite: 318]
                        return; [cite_start]// [cite: 319]
                    } else if (selectedCount === quantidade) {
                        limitAlert.textContent = `Máximo de ${quantidade} perícia(s) selecionado.`;
                        limitAlert.style.display = 'block';
                    } else if (selectedCount < quantidade) {
                        limitAlert.style.display = 'none'; // Esconde o alerta se estiver dentro do limite
                    }

                    [cite_start]// Atualiza o Array e o campo oculto após a seleção [cite: 314]
                    const choicesArray = Array.from(container.querySelectorAll('.pericia-choice:checked')).map(cb => cb.value); [cite_start]// [cite: 314]
                    hiddenInput.value = JSON.stringify(choicesArray); [cite_start]// [cite: 315]
                    this.periciasSelecionadas = choicesArray;
                };

                // Adiciona o novo listener
                checkboxes.forEach(checkbox => {
                    [cite_start]checkbox.addEventListener('change', this.handlePericiaChange); // [cite: 317]
                });

                // Dispara a atualização inicial para o caso de pré-seleção
                this.handlePericiaChange({ target: { checked: false } });
            },

            /**
             * Atualiza os detalhes da Classe.
             */
            updateClasse(selectedOption) {
                if (!selectedOption || !selectedOption.value) {
                    this.toggleContent('classe', false); [cite_start]// [cite: 322]
                    document.getElementById('resumo-classe').textContent = 'Não selecionada'; [cite_start]// [cite: 322]
                    return;
                }

                const data = selectedOption.dataset; [cite_start]// [cite: 323]
                const pericias = JSON.parse(data.pericias); [cite_start]// [cite: 323]
                const equipamento = JSON.parse(data.equipamento); [cite_start]// [cite: 323]
                const atributosBonus = JSON.parse(data.atributosBonus); [cite_start]// [cite: 323]
                const poderes = JSON.parse(data.poderes); [cite_start]// [cite: 324]
                const periciasQuantidade = parseInt(data.periciasQuantidade); // Nova variável para quantidade

                [cite_start]// Atualiza elementos fora do container (Resumo) [cite: 324]
                document.getElementById('classe-pagina').textContent = `${data.pagina}`; [cite_start]// [cite: 324]
                document.getElementById('classe-descricao').textContent = data.descricao; [cite_start]// [cite: 325]
                document.getElementById('classe-dado-vida').textContent = data.dadoVida; [cite_start]// [cite: 325]
                document.getElementById('classe-usa-magia').textContent = data.usaMagia; [cite_start]// [cite: 325]
                document.getElementById('resumo-classe').textContent = selectedOption.textContent.trim(); [cite_start]// [cite: 325]

                [cite_start]// Bônus em Atributos (da Classe) [cite: 326]
                let atributosHtml = 'Nenhum';
                [cite_start]if (atributosBonus && Object.keys(atributosBonus).length > 0) { // [cite: 326]
                    atributosHtml = Object.entries(atributosBonus).map(([key, value]) => {
                        const nomeAtributo = sistemaAtributos[key] || key.toUpperCase();
                        return `${nomeAtributo} ${value > 0 ? '+' : ''}${value}`;
                    }).join(', '); [cite_start]// [cite: 327]
                }
                document.getElementById('classe-atributos-bonus').textContent = atributosHtml; [cite_start]// [cite: 327]

                [cite_start]// Poderes [cite: 328]
                document.getElementById('classe-poderes').innerHTML = this.renderJsonAsList(poderes); [cite_start]// [cite: 328]

                [cite_start]// Perícias (Proficiência) - Seleção interativa [cite: 329]
                const periciasContainer = document.getElementById('classe-pericias'); [cite_start]// [cite: 329]
                const periciasSelecaoContainer = document.getElementById('classe-pericias-selecao-container'); [cite_start]// [cite: 330]
                const hiddenInput = document.getElementById('pericias_classe_selecionadas'); [cite_start]// [cite: 330]

                let periciasHtml = '';
                let periciasSelecaoHtml = '';

                // Limpa o estado da seleção de perícias a cada mudança de classe
                this.periciasSelecionadas = []; // Reseta o estado
                hiddenInput.value = '[]'; // Zera o campo oculto

                [cite_start]if (pericias.tipo === 'escolha' && Array.isArray(pericias.lista)) { // [cite: 331]
                    const quantidade = periciasQuantidade; [cite_start]// [cite: 332]
                    periciasHtml = `<p class="alert alert-primary p-2 mb-0"><i class="fas fa-hand-pointer me-1"></i> Você ganha <strong>Proficiência</strong> em <strong>${quantidade}</strong> Perícia(s) à sua escolha entre as listadas.
                    Marque suas opções:</p>`; [cite_start]// [cite: 332, 333]

                    periciasSelecaoHtml = `<div class="list-group small mb-2">`;
                    pericias.lista.forEach(pericia => {
                        // Pre-seleciona as perícias salvas anteriormente, se existirem e estiverem na lista
                        const isChecked = this.periciasSelecionadas.includes(pericia) ? 'checked' : '';
                        periciasSelecaoHtml += `
                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                ${pericia}
                                [cite_start]<input class="form-check-input me-1 pericia-choice" type="checkbox" value="${pericia}" ${isChecked}> [cite: 334]
                            </label>
                        `;
                    });
                    periciasSelecaoHtml += '</div>'; [cite_start]// [cite: 335]

                    periciasSelecaoContainer.innerHTML = periciasSelecaoHtml;
                    this.setupPericiaSelection(quantidade); [cite_start]// Chama a função para adicionar listeners [cite: 335]

                [cite_start]} else if (pericias.tipo === 'flat' && Array.isArray(pericias.lista)) { // [cite: 335]
                    periciasHtml = `<p class="alert alert-primary p-2 mb-0"><i class="fas fa-lock me-1"></i> Proficiência automática nas seguintes Perícias:</p>`; [cite_start]// [cite: 336]
                    periciasHtml += this.renderJsonAsList(pericias.lista); [cite_start]// [cite: 336]

                    this.periciasSelecionadas = pericias.lista;
                    hiddenInput.value = JSON.stringify(pericias.lista); [cite_start]// [cite: 336]
                    periciasSelecaoContainer.innerHTML = ''; // Limpa a seleção interativa
                } else {
                    periciasHtml = '<span class="text-muted fst-italic">Nenhuma Proficiência em perícia fornecida diretamente pela Classe no Nível 1.</span>'; [cite_start]// [cite: 337]
                    periciasSelecaoContainer.innerHTML = ''; // Limpa a seleção interativa
                }

                periciasContainer.innerHTML = periciasHtml; [cite_start]// [cite: 337]

                [cite_start]// Equipamento [cite: 338]
                document.getElementById('classe-equipamento').innerHTML = this.renderJsonAsList(equipamento); [cite_start]// [cite: 338]

                [cite_start]// Transfere o conteúdo do mobile (template) para o desktop [cite: 339, 340]
                const contentM = document.getElementById(this.contentIds.classe.mobile).innerHTML;
                document.getElementById(this.contentIds.classe.desktop).innerHTML = contentM;

                this.toggleContent('classe', true); [cite_start]// [cite: 340]
            },

            /**
             * Atualiza os detalhes da Origem.
             */
            updateOrigem(selectedOption) {
                if (!selectedOption || !selectedOption.value) {
                    this.toggleContent('origem', false); [cite_start]// [cite: 341]
                    document.getElementById('resumo-origem').textContent = 'Não selecionada'; [cite_start]// [cite: 341]
                    return;
                }

                const data = selectedOption.dataset; [cite_start]// [cite: 342]
                const bonusPericias = JSON.parse(data.bonusPericias); [cite_start]// [cite: 342]
                const recursosAdicionais = JSON.parse(data.recursosAdicionais); [cite_start]// [cite: 342]

                [cite_start]// Atualiza elementos fora do container (Resumo) [cite: 342]
                document.getElementById('origem-pagina').textContent = `${data.pagina}`; [cite_start]// [cite: 343]
                document.getElementById('origem-descricao').textContent = data.descricao; [cite_start]// [cite: 343]
                document.getElementById('resumo-origem').textContent = selectedOption.textContent.trim(); [cite_start]// [cite: 343]

                [cite_start]// Bônus em Perícias [cite: 344]
                let periciasHtml = '';
                [cite_start]if (bonusPericias && Object.keys(bonusPericias).length > 0) { // [cite: 344]
                    periciasHtml = `<p class="alert alert-success border-success border-start border-2 p-2 mb-0 text-dark"><i class="fas fa-user-check me-1"></i> <strong>Proficiência e/ou Bônus Fixo</strong> em Perícias específicas:</p>`; [cite_start]// [cite: 345]
                    periciasHtml += this.renderJsonAsList(bonusPericias); [cite_start]// [cite: 345]
                } else {
                    periciasHtml = '<span class="text-muted fst-italic">Nenhum bônus em Perícia fornecido pela Origem.</span>'; [cite_start]// [cite: 346]
                }
                document.getElementById('origem-pericias').innerHTML = periciasHtml; [cite_start]// [cite: 347]

                [cite_start]// Recursos Adicionais [cite: 347]
                document.getElementById('origem-recursos').innerHTML = this.renderJsonAsList(recursosAdicionais); [cite_start]// [cite: 347]

                [cite_start]// Transfere o conteúdo do mobile (template) para o desktop [cite: 348, 349]
                const contentM = document.getElementById(this.contentIds.origem.mobile).innerHTML;
                document.getElementById(this.contentIds.origem.desktop).innerHTML = contentM;

                this.toggleContent('origem', true); [cite_start]// [cite: 349]
            },

            /**
             * Configura os Event Listeners iniciais e o estado.
             */
            init() {
                const racaSelect = document.getElementById('raca_id');
                const classeSelect = document.getElementById('classe_id');
                const origemSelect = document.getElementById('origem_id');

                // Event Listeners (agora chamando os métodos do objeto)
                racaSelect.addEventListener('change', (e) => this.updateRaca(e.target.options[e.target.selectedIndex])); [cite_start]// [cite: 350]
                classeSelect.addEventListener('change', (e) => this.updateClasse(e.target.options[e.target.selectedIndex])); [cite_start]// [cite: 351]
                origemSelect.addEventListener('change', (e) => this.updateOrigem(e.target.options[e.target.selectedIndex])); [cite_start]// [cite: 352]

                [cite_start]// Inicialização (para pré-seleção) [cite: 352]
                if (racaSelect.value) {
                    this.updateRaca(racaSelect.options[racaSelect.selectedIndex]); [cite_start]// [cite: 352]
                } else {
                    [cite_start]// Garante que a primeira TAB (Raça) esteja ativa no mobile [cite: 353]
                    const firstTab = document.getElementById('raca-content-tab');
                    if (firstTab) {
                         firstTab.classList.add('active', 'show');
                    }
                }
                if (classeSelect.value) this.updateClasse(classeSelect.options[classeSelect.selectedIndex]); [cite_start]// [cite: 354]
                if (origemSelect.value) this.updateOrigem(origemSelect.options[origemSelect.selectedIndex]); [cite_start]// [cite: 354]
            }
        };

        // Inicia o objeto de gerenciamento
        PersonagemStep2.init();
    });
</script>
@endsection
