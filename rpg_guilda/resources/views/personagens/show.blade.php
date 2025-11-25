@extends('layouts.app')

@section('title', 'Detalhes e Edição de Personagem')

@section('content')
<div class="container py-4">

    {{-- CABEÇALHO E BOTÃO DE MODO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <img id="avatarPreview" src="{{ $personagem->imagem ? asset('storage/'.$personagem->imagem) : asset('images/default-avatar.png') }}" class="rounded-circle me-2" style="width:60px; height:60px; object-fit:cover;">
            <span id="personagemNomeDisplay">{{ $personagem->nome }}</span>
            <small class="text-muted fs-6">({{ $personagem->raca->nome ?? 'Raça Desconhecida' }})</small>
        </h1>
        <button id="toggleModeBtn" class="btn btn-warning shadow-sm">
            <i class="bi bi-pencil"></i> Editar Personagem
        </button>
    </div>

    {{-- MENSAGENS --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ===================================================== --}}
    {{-- MODO VISUALIZAÇÃO --}}
    {{-- ===================================================== --}}
    <div id="viewModeContent">
        {{-- Atributos Dinâmicos do Sistema --}}
        <div id="sistemaStatsViewSection" class="card shadow-sm mb-4" style="display:none;">
            <div class="card-header bg-danger text-white"><i class="bi bi-gear"></i> Estatísticas do Sistema</div>
            <div class="card-body" id="dynamicStatsViewContainer"></div>
        </div>

        {{-- Atributos Principais --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white"><i class="bi bi-shield-check"></i> Atributos Base</div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3 g-3" id="atributosViewContainer"></div>
            </div>
        </div>

        {{-- Perícias --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white"><i class="bi bi-person-arms-up"></i> Perícias</div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-4 g-3" id="periciasViewContainer"></div>
            </div>
        </div>

        {{-- Informações Textuais --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white"><i class="bi bi-journal-text"></i> Detalhes e Personalidade</div>
            <div class="card-body">
                <p><strong>Descrição / Personalidade:</strong> <span id="viewDescricao">{{ $personagem->descricao }}</span></p>
                <hr>
                <p><strong>História:</strong> <span id="viewHistoria">{{ $personagem->historia }}</span></p>
                <hr>
                <p><strong>Inventário:</strong> <span id="viewInventario">{{ $personagem->inventario }}</span></p>
            </div>
        </div>

        {{-- Botão Voltar --}}
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary btn-lg shadow">
                <i class="bi bi-arrow-left"></i> Voltar para a Campanha
            </a>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODO EDIÇÃO --}}
    {{-- ===================================================== --}}
    <form id="editForm" action="{{ route('personagens.update', $personagem->id) }}" method="POST" enctype="multipart/form-data" style="display:none;">
        @csrf
        @method('PUT')

        {{-- Campos hidden para JSON --}}
        <input type="hidden" name="atributos_json" id="atributosJsonInput">
        <input type="hidden" name="pericias_json" id="periciasJsonInput">
        <input type="hidden" name="opcoes_adicionais_json" id="opcoesAdicionaisJsonInput">

        {{-- Avatar e Nome --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                <label for="inputImagem" class="form-label">Avatar</label>
                <input type="file" id="inputImagem" name="imagem" class="form-control mb-2">
                <input type="text" id="inputNome" name="nome" class="form-control" value="{{ $personagem->nome }}" placeholder="Nome do Personagem" required>
            </div>
        </div>

        {{-- Atributos Dinâmicos do Sistema --}}
        <div id="sistemaStatsEditSection" class="card shadow-sm mb-4" style="display:none;">
            <div class="card-header bg-danger text-white"><i class="bi bi-gear"></i> Estatísticas do Sistema</div>
            <div class="card-body" id="dynamicStatsEditContainer"></div>
        </div>

        {{-- Atributos Principais --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white"><i class="bi bi-shield-check"></i> Atributos Base</div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3 g-3" id="atributosEditContainer"></div>
            </div>
        </div>

        {{-- Perícias --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white"><i class="bi bi-person-arms-up"></i> Perícias</div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-4 g-3" id="periciasEditContainer"></div>
            </div>
        </div>

        {{-- Informações Textuais --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white"><i class="bi bi-journal-text"></i> Detalhes e Personalidade</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Descrição / Personalidade</label>
                    <textarea id="inputDescricao" name="descricao" class="form-control" rows="3">{{ $personagem->descricao }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">História</label>
                    <textarea id="inputHistoria" name="historia" class="form-control" rows="5">{{ $personagem->historia }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Inventário</label>
                    <textarea id="inputInventario" name="inventario" class="form-control" rows="4">{{ $personagem->inventario }}</textarea>
                </div>
            </div>
        </div>

        {{-- Botões --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-success btn-lg shadow"><i class="bi bi-save"></i> Salvar Alterações</button>
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-lg shadow"><i class="bi bi-x-circle"></i> Cancelar</button>
        </div>
    </form>

</div>

<script>
const personagem = @json($personagem);
const sistema = @json($sistema);

let atributos = JSON.parse(personagem.atributos || '{}');
let pericias = JSON.parse(personagem.pericias || '{}');
let opcoesAdicionais = JSON.parse(personagem.opcoes_adicionais || '{}');
const atributosSistema = JSON.parse(sistema.atributos || '{}');
const originalOpcoesAdicionais = { ...opcoesAdicionais };

// DOM Elements
const toggleModeBtn = document.getElementById('toggleModeBtn');
const viewModeContent = document.getElementById('viewModeContent');
const editForm = document.getElementById('editForm');
const atributosViewContainer = document.getElementById('atributosViewContainer');
const atributosEditContainer = document.getElementById('atributosEditContainer');
const periciasViewContainer = document.getElementById('periciasViewContainer');
const periciasEditContainer = document.getElementById('periciasEditContainer');
const dynamicStatsViewContainer = document.getElementById('dynamicStatsViewContainer');
const dynamicStatsEditContainer = document.getElementById('dynamicStatsEditContainer');
const sistemaStatsViewSection = document.getElementById('sistemaStatsViewSection');
const sistemaStatsEditSection = document.getElementById('sistemaStatsEditSection');
const cancelBtn = document.getElementById('cancelBtn');

const inputNome = document.getElementById('inputNome');
const inputDescricao = document.getElementById('inputDescricao');
const inputHistoria = document.getElementById('inputHistoria');
const inputInventario = document.getElementById('inputInventario');
const atributosJsonInput = document.getElementById('atributosJsonInput');
const periciasJsonInput = document.getElementById('periciasJsonInput');
const opcoesAdicionaisJsonInput = document.getElementById('opcoesAdicionaisJsonInput');
const avatarPreview = document.getElementById('avatarPreview');
const personagemNomeDisplay = document.getElementById('personagemNomeDisplay');

// Preview da imagem
document.getElementById('inputImagem').addEventListener('change', e=>{
    const file = e.target.files[0];
    avatarPreview.src = file ? URL.createObjectURL(file) : "{{ asset('images/default-avatar.png') }}";
});

// Renderização de atributos/perícias
function renderStats(container, data, systemData, type='atributos') {
    container.innerHTML = '';
    for (let key in systemData) {
        const fullName = systemData[key];
        const currentValue = data[key] || 0;
        const col = document.createElement('div');
        col.className = 'col';
        col.innerHTML = `<div class="bg-light p-3 rounded shadow-sm border ${type==='atributos'?'border-primary':'border-info'}">
            <label class="form-label fw-bold ${type==='atributos'?'text-primary':'text-info'}">${fullName}</label>
            <p class="fs-4 mb-0 text-dark">${currentValue}</p>
        </div>`;
        container.appendChild(col);
    }
}

function renderEditStats(container, data, systemData, type='atributos') {
    container.innerHTML = '';
    for (let key in systemData) {
        const fullName = systemData[key];
        const value = data[key] || 0;
        const col = document.createElement('div');
        col.className = 'col';
        const inputId = `${type}-${key}-edit`;
        col.innerHTML = `<div class="bg-light p-3 rounded shadow-sm border ${type==='atributos'?'border-primary':'border-info'}">
            <label class="form-label fw-bold ${type==='atributos'?'text-primary':'text-info'}" for="${inputId}">${fullName}</label>
            <input type="number" id="${inputId}" class="form-control form-control-sm mt-1" value="${value}" min="0">
        </div>`;
        const input = col.querySelector('input');
        input.addEventListener('input', e=>{
            const val = parseInt(e.target.value) || 0;
            if(type==='atributos') atributos[key] = val;
            else pericias[key] = val;
        });
        container.appendChild(col);
    }
}

// Campos Dinâmicos
function createDynamicFieldView(key, label, colorClass){
    const val = opcoesAdicionais[key] ?? 100;
    const div = document.createElement('div');
    div.className = `d-flex justify-content-between align-items-center p-3 mb-2 rounded ${colorClass}`;
    div.innerHTML = `<span class="fw-bold">${label}</span><span class="fs-4">${val}%</span>`;
    return div;
}

function createDynamicFieldEdit(key, label, colorClass){
    const val = opcoesAdicionais[key] ?? 100;
    const div = document.createElement('div');
    div.className = `d-flex justify-content-between align-items-center p-3 mb-2 rounded ${colorClass}`;
    div.innerHTML = `<label class="fw-bold mb-0">${label}</label>
        <input type="number" min="0" max="100" class="form-control form-control-sm w-25 ms-2" value="${val}" data-key="${key}">
        <span class="ms-2">%</span>`;
    div.querySelector('input').addEventListener('input', e=>{
        let v = parseInt(e.target.value)||0;
        opcoesAdicionais[key] = Math.min(100, Math.max(0, v));
        e.target.value = opcoesAdicionais[key];
    });
    return div;
}

function renderDynamicStats(){
    dynamicStatsViewContainer.innerHTML='';
    dynamicStatsEditContainer.innerHTML='';
    let hasDynamic=false;
    if(sistema.usa_sanidade){
        hasDynamic=true;
        dynamicStatsViewContainer.appendChild(createDynamicFieldView('sanidade','Sanidade (0-100)','bg-danger text-white'));
        dynamicStatsEditContainer.appendChild(createDynamicFieldEdit('sanidade','Sanidade (0-100)','bg-danger text-white'));
    }
    if(sistema.usa_sorte){
        hasDynamic=true;
        dynamicStatsViewContainer.appendChild(createDynamicFieldView('sorte','Sorte (0-100)','bg-warning text-dark'));
        dynamicStatsEditContainer.appendChild(createDynamicFieldEdit('sorte','Sorte (0-100)','bg-warning text-dark'));
    }
    sistemaStatsViewSection.style.display = hasDynamic?'block':'none';
    sistemaStatsEditSection.style.display = hasDynamic?'block':'none';
}

// Alternar modos
function toggleMode(edit){
    if(edit){
        viewModeContent.style.display='none';
        editForm.style.display='block';
        toggleModeBtn.textContent='Modo de Visualização';
        toggleModeBtn.classList.replace('btn-warning','btn-danger');
    }else{
        viewModeContent.style.display='block';
        editForm.style.display='none';
        toggleModeBtn.textContent='Editar Personagem';
        toggleModeBtn.classList.replace('btn-danger','btn-warning');
    }
}

// Inicialização
function initialize(){
    renderStats(atributosViewContainer, atributos, atributosSistema,'atributos');
    renderStats(periciasViewContainer, pericias, personagem.pericias_sistema||{},'pericias');
    renderEditStats(atributosEditContainer, atributos, atributosSistema,'atributos');
    renderEditStats(periciasEditContainer, pericias, personagem.pericias_sistema||{},'pericias');
    renderDynamicStats();

    toggleModeBtn.addEventListener('click',()=>toggleMode(viewModeContent.style.display!=='none'));
    cancelBtn.addEventListener('click',()=>{
        opcoesAdicionais={...originalOpcoesAdicionais};
        inputNome.value = personagem.nome;
        inputDescricao.value = personagem.descricao;
        inputHistoria.value = personagem.historia;
        inputInventario.value = personagem.inventario;
        initialize();
        toggleMode(false);
    });

    editForm.addEventListener('submit',()=>{
        atributosJsonInput.value=JSON.stringify(atributos);
        periciasJsonInput.value=JSON.stringify(pericias);
        opcoesAdicionaisJsonInput.value=JSON.stringify(opcoesAdicionais);

        personagemNomeDisplay.textContent=inputNome.value;
        document.getElementById('viewDescricao').textContent=inputDescricao.value;
        document.getElementById('viewHistoria').textContent=inputHistoria.value;
        document.getElementById('viewInventario').textContent=inputInventario.value;
    });

    toggleMode(false);
}

window.onload = initialize;
</script>
@endsection
