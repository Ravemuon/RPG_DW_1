@extends('layouts.app')

@section('title', 'Criar Personagem')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Criar Novo Personagem</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('personagens.store') }}" method="POST" enctype="multipart/form-data" id="formPersonagem">
                        @csrf

                        <!-- Informações do Sistema e Campanha -->
                        <div class="row mb-4">
                            <div class="col-12">
                                @if(request()->has('campanha_id') && $campanha)
                                    <input type="hidden" name="campanha_id" value="{{ $campanha->id }}">
                                    <input type="hidden" name="sistema_id" value="{{ $sistema->id }}">
                                    
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="fas fa-info-circle fa-2x me-3"></i>
                                        <div>
                                            <h5 class="mb-1">Criando personagem para:</h5>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="badge bg-primary p-2">
                                                    <i class="fas fa-campground me-1"></i>
                                                    <strong>Campanha:</strong> {{ $campanha->nome }}
                                                </div>
                                                <div class="badge bg-secondary p-2">
                                                    <i class="fas fa-dice-d20 me-1"></i>
                                                    <strong>Sistema:</strong> {{ $sistema->nome }}
                                                </div>
                                                @if($campanha->criador)
                                                    <div class="badge bg-success p-2">
                                                        <i class="fas fa-crown me-1"></i>
                                                        <strong>Mestre:</strong> {{ $campanha->criador->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Selecione uma campanha para começar a criar seu personagem.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Dados Básicos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-id-card me-2"></i>Dados Básicos
                                </h5>
                            </div>
                            
                            <!-- Nome do Personagem -->
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome do Personagem *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                           id="nome" name="nome" value="{{ old('nome') }}" 
                                           placeholder="Ex: Aragorn, Gandalf, Conan" required>
                                </div>
                                @error('nome')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Seletor de Campanha -->
                            @if(!request()->has('campanha_id'))
                                <div class="col-md-6 mb-3">
                                    <label for="campanha_id" class="form-label">Campanha *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-campground"></i></span>
                                        <select class="form-select @error('campanha_id') is-invalid @enderror" 
                                                id="campanha_id" name="campanha_id" required
                                                onchange="window.location.href = '{{ route('personagens.create') }}?campanha=' + this.value;">
                                            <option value="">Selecione uma campanha...</option>
                                            @foreach($campanhas as $camp)
                                                @php
                                                    $sistemaCamp = $camp->sistema;
                                                    $isMestre = $camp->criador_id === Auth::id();
                                                    $isJogador = $camp->jogadores()
                                                        ->where('user_id', Auth::id())
                                                        ->whereIn('campanha_usuario.status', ['ativo', 'mestre'])
                                                        ->exists();
                                                    $temPermissao = $isMestre || $isJogador || !$camp->privada;
                                                @endphp
                                                
                                                @if($temPermissao)
                                                    <option value="{{ $camp->id }}" 
                                                            data-sistema="{{ $sistemaCamp->id }}"
                                                            data-sistema-nome="{{ $sistemaCamp->nome }}"
                                                            {{ old('campanha_id') == $camp->id ? 'selected' : '' }}>
                                                        {{ $camp->nome }}
                                                        <small class="text-muted">
                                                            ({{ $sistemaCamp->nome }})
                                                            @if($camp->privada)
                                                                <i class="fas fa-lock ms-1" title="Campanha Privada"></i>
                                                            @else
                                                                <i class="fas fa-globe ms-1" title="Campanha Pública"></i>
                                                            @endif
                                                            @if($isMestre)
                                                                <i class="fas fa-crown ms-1 text-warning" title="Você é o Mestre"></i>
                                                            @elseif($isJogador)
                                                                <i class="fas fa-user-check ms-1 text-success" title="Você é Jogador"></i>
                                                            @endif
                                                        </small>
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('campanha_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-info-circle"></i> Ao selecionar uma campanha, as opções de raça, classe e origem serão filtradas pelo sistema.
                                    </small>
                                </div>
                            @endif
                            
                            <!-- Raça -->
                            <div class="col-md-4 mb-3">
                                <label for="raca_id" class="form-label">
                                    <i class="fas fa-dragon me-1"></i> Raça
                                    @if($sistema)
                                        <span class="badge bg-info float-end" id="total-racas">
                                            {{ $racas->count() }} disponíveis
                                        </span>
                                    @endif
                                </label>
                                <select class="form-select @error('raca_id') is-invalid @enderror" 
                                        id="raca_id" name="raca_id"
                                        onchange="atualizarBonusRaca()">
                                    <option value="">Selecione uma raça...</option>
                                    @if($sistema && $racas->count() > 0)
                                        @foreach($racas as $raca)
                                            <option value="{{ $raca->id }}" 
                                                    data-bonus="{{ $raca->modificadores_atributos }}"
                                                    data-descricao="{{ $raca->descricao }}"
                                                    {{ old('raca_id') == $raca->id ? 'selected' : '' }}>
                                                {{ $raca->nome }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('raca_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                
                                <!-- Informações da Raça Selecionada -->
                                <div id="raca-info" class="mt-2" style="display: none;">
                                    <div class="card border-info">
                                        <div class="card-body py-2">
                                            <small class="text-muted" id="raca-descricao"></small>
                                            <div id="raca-bonus" class="mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Classe -->
                            <div class="col-md-4 mb-3">
                                <label for="classe_id" class="form-label">
                                    <i class="fas fa-shield-alt me-1"></i> Classe
                                    @if($sistema)
                                        <span class="badge bg-info float-end" id="total-classes">
                                            {{ $classes->count() }} disponíveis
                                        </span>
                                    @endif
                                </label>
                                <select class="form-select @error('classe_id') is-invalid @enderror" 
                                        id="classe_id" name="classe_id"
                                        onchange="atualizarInfoClasse()">
                                    <option value="">Selecione uma classe...</option>
                                    @if($sistema && $classes->count() > 0)
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}" 
                                                    data-dado-vida="{{ $classe->dado_vida }}"
                                                    data-descricao="{{ $classe->descricao }}"
                                                    {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                                {{ $classe->nome }}
                                                @if($classe->usa_magia)
                                                    <i class="fas fa-magic ms-1 text-purple" title="Usa Magia"></i>
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('classe_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                
                                <!-- Informações da Classe Selecionada -->
                                <div id="classe-info" class="mt-2" style="display: none;">
                                    <div class="card border-warning">
                                        <div class="card-body py-2">
                                            <small class="text-muted" id="classe-descricao"></small>
                                            <div class="mt-1">
                                                <span class="badge bg-dark" id="classe-dado-vida"></span>
                                                <span class="badge bg-purple ms-1" id="classe-magia" style="display: none;">
                                                    <i class="fas fa-magic"></i> Usa Magia
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Origem -->
                            <div class="col-md-4 mb-3">
                                <label for="origem_id" class="form-label">
                                    <i class="fas fa-history me-1"></i> Origem
                                    @if($sistema)
                                        <span class="badge bg-info float-end" id="total-origens">
                                            {{ $origens->count() }} disponíveis
                                        </span>
                                    @endif
                                </label>
                                <select class="form-select @error('origem_id') is-invalid @enderror" 
                                        id="origem_id" name="origem_id"
                                        onchange="atualizarInfoOrigem()">
                                    <option value="">Selecione uma origem...</option>
                                    @if($sistema && $origens->count() > 0)
                                        @foreach($origens as $origem)
                                            <option value="{{ $origem->id }}" 
                                                    data-descricao="{{ $origem->descricao }}"
                                                    {{ old('origem_id') == $origem->id ? 'selected' : '' }}>
                                                {{ $origem->nome }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('origem_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                
                                <!-- Informações da Origem Selecionada -->
                                <div id="origem-info" class="mt-2" style="display: none;">
                                    <div class="card border-success">
                                        <div class="card-body py-2">
                                            <small class="text-muted" id="origem-descricao"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Nível e XP -->
                            <div class="col-md-3 mb-3">
                                <label for="nivel" class="form-label">
                                    <i class="fas fa-chart-line me-1"></i> Nível
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-level-up-alt"></i></span>
                                    <input type="number" class="form-control @error('nivel') is-invalid @enderror" 
                                           id="nivel" name="nivel" value="{{ old('nivel', 1) }}" 
                                           min="1" max="20" onchange="atualizarBonusProficiencia()">
                                </div>
                                @error('nivel')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    Bônus de Proficiência: <span id="bonus-proficiencia" class="badge bg-dark">+2</span>
                                </small>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="xp" class="form-label">
                                    <i class="fas fa-star me-1"></i> XP
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bolt"></i></span>
                                    <input type="number" class="form-control @error('xp') is-invalid @enderror" 
                                           id="xp" name="xp" value="{{ old('xp', 0) }}" min="0">
                                </div>
                                @error('xp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    XP para próximo nível: <span id="xp-proximo" class="badge bg-secondary">300</span>
                                </small>
                            </div>
                            
                            <!-- Imagem (Minimizada) -->
                            <div class="col-md-6 mb-3">
                                <label for="imagem" class="form-label">
                                    <i class="fas fa-image me-1"></i> Imagem do Personagem
                                </label>
                                <input type="file" class="form-control @error('imagem') is-invalid @enderror" 
                                       id="imagem" name="imagem" accept="image/*">
                                @error('imagem')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Formatos: JPG, PNG, GIF. Tamanho máximo: 2MB
                                </small>
                            </div>
                        </div>

                        <!-- ATRIBUTOS - Agora com visualização dos bônus -->
                        @if($sistema && $sistema->atributos)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">
                                            <i class="fas fa-tachometer-alt me-2"></i>Atributos
                                            <small class="text-muted">(Sistema: {{ $sistema->nome }})</small>
                                        </h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="distribuirPontosPadrao()">
                                            <i class="fas fa-dice"></i> Distribuição Padrão
                                        </button>
                                    </div>
                                    <p class="text-muted small">
                                        Insira os valores para cada atributo. Os bônus de raça serão aplicados automaticamente.
                                    </p>
                                    
                                    <!-- Resumo dos Atributos -->
                                    <div class="row mb-3" id="resumo-atributos" style="display: none;">
                                        <div class="col-12">
                                            <div class="card border-primary">
                                                <div class="card-body py-2">
                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <small class="text-muted">Total de Pontos</small>
                                                            <h5 class="mb-0" id="total-pontos">72</h5>
                                                        </div>
                                                        <div class="col">
                                                            <small class="text-muted">Média</small>
                                                            <h5 class="mb-0" id="media-atributos">12.0</h5>
                                                        </div>
                                                        <div class="col">
                                                            <small class="text-muted">Mais Alto</small>
                                                            <h5 class="mb-0 text-success" id="mais-alto">15</h5>
                                                        </div>
                                                        <div class="col">
                                                            <small class="text-muted">Mais Baixo</small>
                                                            <h5 class="mb-0 text-danger" id="mais-baixo">8</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Grid de Atributos -->
                                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3" id="grid-atributos">
                                        @foreach(json_decode($sistema->atributos, true) as $key => $nome)
                                            @php
                                                $valorPadrao = old('atributos.' . $key, 10);
                                                $modificador = floor(($valorPadrao - 10) / 2);
                                            @endphp
                                            <div class="col">
                                                <div class="card h-100 border">
                                                    <div class="card-header text-center py-2">
                                                        <small class="text-uppercase text-muted">{{ $nome }}</small>
                                                    </div>
                                                    <div class="card-body text-center py-3">
                                                        <!-- Valor Base -->
                                                        <div class="input-group input-group-sm mb-2">
                                                            <input type="number" 
                                                                   class="form-control form-control-lg text-center atributo-input" 
                                                                   id="atributos_{{ $key }}" 
                                                                   name="atributos[{{ $key }}]" 
                                                                   value="{{ $valorPadrao }}" 
                                                                   min="1" max="20"
                                                                   onchange="calcularModificador('{{ $key }}'); atualizarResumo()">
                                                        </div>
                                                        
                                                        <!-- Modificador -->
                                                        <div class="mb-1">
                                                            <span class="h5 mb-0 {{ $modificador >= 0 ? 'text-success' : 'text-danger' }}" 
                                                                  id="mod_{{ $key }}">
                                                                {{ $modificador >= 0 ? '+' : '' }}{{ $modificador }}
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- Bônus de Raça -->
                                                        <div class="small text-info" id="bonus_raca_{{ $key }}" style="display: none;">
                                                            +<span id="valor_bonus_raca_{{ $key }}">0</span>
                                                        </div>
                                                        
                                                        <!-- Total com Bônus -->
                                                        <div class="small text-dark mt-1" id="total_{{ $key }}" style="display: none;">
                                                            Total: <span id="valor_total_{{ $key }}">10</span>
                                                            (<span id="mod_total_{{ $key }}" class="{{ $modificador >= 0 ? 'text-success' : 'text-danger' }}">+0</span>)
                                                        </div>
                                                    </div>
                                                    <div class="card-footer text-center py-1">
                                                        <small class="text-muted">{{ strtoupper(substr($nome, 0, 3)) }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @elseif($sistema)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                O sistema <strong>{{ $sistema->nome }}</strong> não tem atributos configurados.
                                Você poderá configurar os atributos depois na edição do personagem.
                            </div>
                        @endif

                        <!-- Descrição, História e Personalidade -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-book me-2"></i>História e Personalidade
                                </h5>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="descricao" class="form-label">Descrição Física</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                          id="descricao" name="descricao" rows="3" 
                                          placeholder="Descreva a aparência física do personagem...">{{ old('descricao') }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="historia" class="form-label">História de Fundo</label>
                                <textarea class="form-control @error('historia') is-invalid @enderror" 
                                          id="historia" name="historia" rows="4"
                                          placeholder="Conte a história do personagem...">{{ old('historia') }}</textarea>
                                @error('historia')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="personalidade" class="form-label">Personalidade</label>
                                <textarea class="form-control @error('personalidade') is-invalid @enderror" 
                                          id="personalidade" name="personalidade" rows="3"
                                          placeholder="Descreva a personalidade, objetivos, medos...">{{ old('personalidade') }}</textarea>
                                @error('personalidade')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('personagens.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Cancelar
                                        </a>
                                        @if(request()->has('campanha_id'))
                                            <a href="{{ route('campanhas.show', request('campanha_id')) }}" 
                                               class="btn btn-outline-info ms-2">
                                                <i class="fas fa-campground me-1"></i> Voltar para Campanha
                                            </a>
                                        @endif
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-warning me-2" onclick="previsualizarPersonagem()">
                                            <i class="fas fa-eye me-1"></i> Pré-visualizar
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="btn-submit">
                                            <i class="fas fa-save me-1"></i> Criar Personagem
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Modal de Pré-visualização -->
            <div class="modal fade" id="previewModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Pré-visualização do Personagem</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="previewContent">
                            <!-- Conteúdo será preenchido via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.atributo-input {
    font-size: 1.25rem;
    font-weight: bold;
    height: 50px;
}

.atributo-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.card.atributo {
    transition: all 0.3s ease;
}

.card.atributo:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.badge.bg-purple {
    background-color: #6f42c1;
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar cálculos
    atualizarBonusProficiencia();
    calcularTodosModificadores();
    atualizarResumo();
    
    // Verificar se há raça, classe ou origem selecionada
    if ($('#raca_id').val()) {
        atualizarBonusRaca();
    }
    if ($('#classe_id').val()) {
        atualizarInfoClasse();
    }
    if ($('#origem_id').val()) {
        atualizarInfoOrigem();
    }
});

// Sistema de XP por nível (D&D 5e padrão)
const XP_POR_NIVEL = {
    1: 0, 2: 300, 3: 900, 4: 2700, 5: 6500,
    6: 14000, 7: 23000, 8: 34000, 9: 48000,
    10: 64000, 11: 85000, 12: 100000, 13: 120000,
    14: 140000, 15: 165000, 16: 195000,
    17: 225000, 18: 265000, 19: 305000, 20: 355000
};

// Bônus de proficiência por nível
const BONUS_PROFICIENCIA = {
    1: 2, 2: 2, 3: 2, 4: 2, 5: 3,
    6: 3, 7: 3, 8: 3, 9: 4, 10: 4,
    11: 4, 12: 4, 13: 5, 14: 5, 15: 5,
    16: 5, 17: 6, 18: 6, 19: 6, 20: 6
};

function atualizarBonusProficiencia() {
    const nivel = parseInt($('#nivel').val()) || 1;
    const bonus = BONUS_PROFICIENCIA[nivel] || 2;
    $('#bonus-proficiencia').text('+' + bonus);
    
    // Atualizar XP para próximo nível
    const xpAtual = parseInt($('#xp').val()) || 0;
    const xpProximo = XP_POR_NIVEL[Math.min(nivel + 1, 20)] || XP_POR_NIVEL[20];
    $('#xp-proximo').text(xpProximo.toLocaleString());
}

function calcularModificador(chave) {
    const valor = parseInt($('#atributos_' + chave).val()) || 10;
    const modificador = Math.floor((valor - 10) / 2);
    const textoMod = modificador >= 0 ? '+' + modificador : modificador.toString();
    
    $('#mod_' + chave).text(textoMod);
    $('#mod_' + chave).removeClass('text-success text-danger');
    $('#mod_' + chave).addClass(modificador >= 0 ? 'text-success' : 'text-danger');
    
    // Atualizar total se houver bônus de raça
    atualizarTotalAtributo(chave);
}

function calcularTodosModificadores() {
    $('.atributo-input').each(function() {
        const id = $(this).attr('id');
        if (id) {
            const chave = id.replace('atributos_', '');
            calcularModificador(chave);
        }
    });
}

function atualizarBonusRaca() {
    const racaId = $('#raca_id').val();
    const racaOption = $('#raca_id option:selected');
    
    // Limpar bônus anteriores
    $('[id^="bonus_raca_"]').hide();
    $('[id^="total_"]').hide();
    
    if (racaId && racaOption.length > 0) {
        const bonusData = racaOption.data('bonus');
        const descricao = racaOption.data('descricao');
        
        // Mostrar informações da raça
        $('#raca-info').show();
        $('#raca-descricao').text(descricao || 'Sem descrição disponível.');
        
        if (bonusData) {
            try {
                const bonus = typeof bonusData === 'string' ? JSON.parse(bonusData) : bonusData;
                let htmlBonus = '<div class="mt-2"><strong>Bônus de Atributos:</strong><br>';
                
                Object.entries(bonus).forEach(([atributo, valor]) => {
                    // Atualizar display do bônus
                    const bonusElement = $('#bonus_raca_atributos_' + atributo);
                    if (bonusElement.length) {
                        $('#valor_bonus_raca_atributos_' + atributo).text(valor);
                        bonusElement.show();
                    }
                    
                    // Atualizar total
                    atualizarTotalAtributo(atributo, valor);
                    
                    // Adicionar ao resumo
                    const sinal = valor >= 0 ? '+' : '';
                    htmlBonus += `<span class="badge bg-info me-1">${atributo}: ${sinal}${valor}</span>`;
                });
                
                htmlBonus += '</div>';
                $('#raca-bonus').html(htmlBonus);
            } catch (e) {
                console.error('Erro ao processar bônus da raça:', e);
            }
        }
    } else {
        $('#raca-info').hide();
    }
}

function atualizarTotalAtributo(chave, bonusRaca = 0) {
    const valorBase = parseInt($('#atributos_' + chave).val()) || 10;
    const valorTotal = valorBase + (bonusRaca || 0);
    const modificadorTotal = Math.floor((valorTotal - 10) / 2);
    const textoModTotal = modificadorTotal >= 0 ? '+' + modificadorTotal : modificadorTotal.toString();
    
    $('#valor_total_' + chave).text(valorTotal);
    $('#mod_total_' + chave).text(textoModTotal);
    $('#mod_total_' + chave).removeClass('text-success text-danger');
    $('#mod_total_' + chave).addClass(modificadorTotal >= 0 ? 'text-success' : 'text-danger');
    
    // Se houver algum bônus aplicado, mostrar o total
    if (bonusRaca !== 0) {
        $('#total_' + chave).show();
    }
}

function atualizarInfoClasse() {
    const classeId = $('#classe_id').val();
    const classeOption = $('#classe_id option:selected');
    
    if (classeId && classeOption.length > 0) {
        const dadoVida = classeOption.data('dado-vida');
        const descricao = classeOption.data('descricao');
        const usaMagia = classeOption.find('i.fa-magic').length > 0;
        
        $('#classe-info').show();
        $('#classe-descricao').text(descricao || 'Sem descrição disponível.');
        $('#classe-dado-vida').text('Dado de Vida: ' + (dadoVida || 'N/A'));
        
        if (usaMagia) {
            $('#classe-magia').show();
        } else {
            $('#classe-magia').hide();
        }
    } else {
        $('#classe-info').hide();
    }
}

function atualizarInfoOrigem() {
    const origemId = $('#origem_id').val();
    const origemOption = $('#origem_id option:selected');
    
    if (origemId && origemOption.length > 0) {
        const descricao = origemOption.data('descricao');
        
        $('#origem-info').show();
        $('#origem-descricao').text(descricao || 'Sem descrição disponível.');
    } else {
        $('#origem-info').hide();
    }
}

function atualizarResumo() {
    let total = 0;
    let maisAlto = 0;
    let maisBaixo = 20;
    let count = 0;
    
    $('.atributo-input').each(function() {
        const valor = parseInt($(this).val()) || 10;
        total += valor;
        count++;
        
        if (valor > maisAlto) maisAlto = valor;
        if (valor < maisBaixo) maisBaixo = valor;
    });
    
    if (count > 0) {
        const media = (total / count).toFixed(1);
        $('#total-pontos').text(total);
        $('#media-atributos').text(media);
        $('#mais-alto').text(maisAlto);
        $('#mais-baixo').text(maisBaixo);
        $('#resumo-atributos').show();
    }
}

function distribuirPontosPadrao() {
    // Distribuição padrão do D&D: 15, 14, 13, 12, 10, 8
    const valores = [15, 14, 13, 12, 10, 8];
    let index = 0;
    
    $('.atributo-input').each(function() {
        if (index < valores.length) {
            $(this).val(valores[index]);
            const id = $(this).attr('id');
            if (id) {
                const chave = id.replace('atributos_', '');
                calcularModificador(chave);
            }
            index++;
        }
    });
    
    atualizarResumo();
}

function previsualizarPersonagem() {
    const formData = new FormData(document.getElementById('formPersonagem'));
    let previewHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0">Informações Básicas</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> ${$('#nome').val() || 'Não informado'}</p>
                        <p><strong>Nível:</strong> ${$('#nivel').val() || 1}</p>
                        <p><strong>XP:</strong> ${$('#xp').val() || 0}</p>
    `;
    
    // Raça
    const racaOption = $('#raca_id option:selected');
    if (racaOption.val()) {
        previewHTML += `<p><strong>Raça:</strong> ${racaOption.text()}</p>`;
    }
    
    // Classe
    const classeOption = $('#classe_id option:selected');
    if (classeOption.val()) {
        previewHTML += `<p><strong>Classe:</strong> ${classeOption.text()}</p>`;
    }
    
    // Origem
    const origemOption = $('#origem_id option:selected');
    if (origemOption.val()) {
        previewHTML += `<p><strong>Origem:</strong> ${origemOption.text()}</p>`;
    }
    
    previewHTML += `
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0">Atributos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
    `;
    
    // Atributos
    $('.atributo-input').each(function() {
        const id = $(this).attr('id');
        if (id) {
            const chave = id.replace('atributos_', '');
            const label = $(`label[for="${id}"]`).text() || chave;
            const valor = $(this).val() || 10;
            const mod = $('#mod_' + chave).text();
            
            previewHTML += `
                <div class="col-md-4 mb-2">
                    <div class="border p-2 text-center rounded">
                        <small class="text-muted">${label}</small>
                        <div class="h4 mb-0">${valor}</div>
                        <div class="h6 ${mod.includes('+') ? 'text-success' : 'text-danger'}">${mod}</div>
                    </div>
                </div>
            `;
        }
    });
    
    previewHTML += `
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#previewContent').html(previewHTML);
    $('#previewModal').modal('show');
}

// Validação do formulário
$('#formPersonagem').on('submit', function(e) {
    if (!$('#nome').val().trim()) {
        e.preventDefault();
        alert('Por favor, insira um nome para o personagem.');
        $('#nome').focus();
        return false;
    }
    
    // Validar atributos
    let atributosValidos = true;
    $('.atributo-input').each(function() {
        const valor = parseInt($(this).val());
        if (isNaN(valor) || valor < 1 || valor > 20) {
            atributosValidos = false;
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    if (!atributosValidos) {
        e.preventDefault();
        alert('Por favor, insira valores válidos para os atributos (1-20).');
        return false;
    }
    
    // Mostrar loading no botão
    $('#btn-submit').html('<i class="fas fa-spinner fa-spin me-1"></i> Criando...');
    $('#btn-submit').prop('disabled', true);
});
</script>
@endpush