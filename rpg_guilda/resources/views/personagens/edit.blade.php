@extends('layouts.app')

@section('title', 'Editar ' . $personagem->nome)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Personagem: {{ $personagem->nome }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('personagens.update', $personagem) }}" method="POST" enctype="multipart/form-data" id="formPersonagem">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informações do Sistema e Campanha -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                    <div>
                                        <h5 class="mb-1">Editando personagem na campanha:</h5>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="badge bg-primary p-2">
                                                <i class="fas fa-campground me-1"></i>
                                                <strong>Campanha:</strong> {{ $personagem->campanha->nome }}
                                            </div>
                                            <div class="badge bg-secondary p-2">
                                                <i class="fas fa-dice-d20 me-1"></i>
                                                <strong>Sistema:</strong> {{ $personagem->sistema->nome }}
                                            </div>
                                            <div class="badge bg-success p-2">
                                                <i class="fas fa-dragon me-1"></i>
                                                <strong>Raça:</strong> {{ $personagem->raca->nome ?? 'Não definida' }}
                                            </div>
                                            <div class="badge bg-danger p-2">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                <strong>Classe:</strong> {{ $personagem->classe->nome ?? 'Não definida' }}
                                            </div>
                                            @if($personagem->origem)
                                                <div class="badge bg-info p-2">
                                                    <i class="fas fa-history me-1"></i>
                                                    <strong>Origem:</strong> {{ $personagem->origem->nome }}
                                                </div>
                                            @endif
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            Nota: Sistema, campanha, raça, classe e origem não podem ser alterados aqui.
                                        </small>
                                    </div>
                                </div>
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
                                           id="nome" name="nome" value="{{ old('nome', $personagem->nome) }}" 
                                           placeholder="Ex: Aragorn, Gandalf, Conan" required>
                                </div>
                                @error('nome')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Nível -->
                            <div class="col-md-3 mb-3">
                                <label for="nivel" class="form-label">
                                    <i class="fas fa-chart-line me-1"></i> Nível *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-level-up-alt"></i></span>
                                    <input type="number" class="form-control @error('nivel') is-invalid @enderror" 
                                           id="nivel" name="nivel" value="{{ old('nivel', $personagem->nivel) }}" 
                                           min="1" max="20" onchange="atualizarBonusProficiencia()" required>
                                </div>
                                @error('nivel')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    Bônus de Proficiência: 
                                    <span id="bonus-proficiencia" class="badge bg-dark">
                                        +{{ $personagem->bonus_proficiencia }}
                                    </span>
                                </small>
                            </div>
                            
                            <!-- XP -->
                            <div class="col-md-3 mb-3">
                                <label for="xp" class="form-label">
                                    <i class="fas fa-star me-1"></i> XP *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bolt"></i></span>
                                    <input type="number" class="form-control @error('xp') is-invalid @enderror" 
                                           id="xp" name="xp" value="{{ old('xp', $personagem->xp) }}" min="0" required>
                                </div>
                                @error('xp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    XP para próximo nível: 
                                    <span id="xp-proximo" class="badge bg-secondary">
                                        {{ number_format($personagem->xpProximoNivel()) }}
                                    </span>
                                </small>
                            </div>
                            
                            <!-- Bônus de Proficiência -->
                            <div class="col-md-3 mb-3">
                                <label for="bonus_proficiencia" class="form-label">
                                    <i class="fas fa-award me-1"></i> Bônus de Proficiência *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">+</span>
                                    <input type="number" class="form-control @error('bonus_proficiencia') is-invalid @enderror" 
                                           id="bonus_proficiencia" name="bonus_proficiencia" 
                                           value="{{ old('bonus_proficiencia', $personagem->bonus_proficiencia) }}" 
                                           min="0" max="10" required>
                                </div>
                                @error('bonus_proficiencia')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Sanidade -->
                            @if($personagem->sistema && $personagem->sistema->usa_sanidade)
                            <div class="col-md-3 mb-3">
                                <label for="sanidade" class="form-label">
                                    <i class="fas fa-brain me-1"></i> Sanidade
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-mental-health"></i></span>
                                    <input type="number" class="form-control @error('sanidade') is-invalid @enderror" 
                                           id="sanidade" name="sanidade" 
                                           value="{{ old('sanidade', $personagem->sanidade) }}" 
                                           min="0" max="100">
                                </div>
                                @error('sanidade')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                            
                            <!-- Sorte -->
                            <div class="col-md-3 mb-3">
                                <label for="sorte" class="form-label">
                                    <i class="fas fa-clover me-1"></i> Sorte
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dice"></i></span>
                                    <input type="number" class="form-control @error('sorte') is-invalid @enderror" 
                                           id="sorte" name="sorte" 
                                           value="{{ old('sorte', $personagem->sorte) }}" 
                                           min="0" max="100">
                                </div>
                                @error('sorte')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Status Ativo -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i> Status
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="ativo" name="ativo" value="1"
                                           {{ old('ativo', $personagem->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Personagem Ativo
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Imagem -->
                            <div class="col-md-6 mb-3">
                                <label for="imagem" class="form-label">
                                    <i class="fas fa-image me-1"></i> Imagem do Personagem
                                </label>
                                
                                <!-- Pré-visualização da imagem atual -->
                                @if($personagem->imagem)
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ Storage::url($personagem->imagem) }}" 
                                                 alt="{{ $personagem->nome }}" 
                                                 class="img-thumbnail me-3" 
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                            <div>
                                                <p class="mb-1"><strong>Imagem atual:</strong></p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="remover_imagem" name="remover_imagem" value="1">
                                                    <label class="form-check-label" for="remover_imagem">
                                                        Remover imagem atual
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <input type="file" class="form-control @error('imagem') is-invalid @enderror" 
                                       id="imagem" name="imagem" accept="image/*">
                                @error('imagem')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Deixe em branco para manter a imagem atual.
                                    Formatos: JPG, PNG, GIF. Tamanho máximo: 2MB
                                </small>
                            </div>
                        </div>

                        <!-- ATRIBUTOS -->
                        @if($personagem->sistema && $personagem->sistema->atributos)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">
                                            <i class="fas fa-tachometer-alt me-2"></i>Atributos
                                            <small class="text-muted">(Sistema: {{ $personagem->sistema->nome }})</small>
                                        </h5>
                                        
                                        <!-- Exibir bônus da raça se existir -->
                                        @if($personagem->raca && $personagem->raca->modificadores_atributos)
                                            <div class="badge bg-info">
                                                <i class="fas fa-dragon me-1"></i>
                                                Bônus de Raça: {{ $personagem->raca->nome }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Resumo dos Atributos -->
                                    @php
                                        // Obter atributos completos do personagem
                                        $atributosCompletos = $personagem->atributosCompletos();
                                        
                                        // Extrair apenas valores base (sem bônus de raça)
                                        $valoresBase = [];
                                        $bonusRaca = [];
                                        
                                        if ($personagem->raca && $personagem->raca->modificadores_atributos) {
                                            $bonusRaca = is_string($personagem->raca->modificadores_atributos) 
                                                ? json_decode($personagem->raca->modificadores_atributos, true)
                                                : $personagem->raca->modificadores_atributos;
                                        }
                                        
                                        foreach ($atributosCompletos as $key => $atributo) {
                                            $valorTotal = $atributo['valor'];
                                            $bonus = $bonusRaca[$key] ?? 0;
                                            $valoresBase[$key] = $valorTotal - $bonus;
                                        }
                                        
                                        $totalPontos = array_sum($valoresBase);
                                        $media = count($valoresBase) > 0 ? $totalPontos / count($valoresBase) : 0;
                                        $maisAlto = !empty($valoresBase) ? max($valoresBase) : 0;
                                        $maisBaixo = !empty($valoresBase) ? min($valoresBase) : 0;
                                    @endphp
                                    
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="card border-primary">
                                                <div class="card-body py-2">
                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <small class="text-muted">Total de Pontos</small>
                                                            <h5 class="mb-0" id="total-pontos">{{ $totalPontos }}</h5>
                                                        </div>
                                                        <div class="col">
                                                            <small class="text-muted">Média</small>
                                                            <h5 class="mb-0" id="media-atributos">{{ number_format($media, 1) }}</h5>
                                                        </div>
                                                        <div class="col">
                                                            <small class="text-muted">Mais Alto</small>
                                                            <h5 class="mb-0 text-success" id="mais-alto">{{ $maisAlto }}</h5>
                                                        </div>
                                                        <div class="col">
                                                            <small class="text-muted">Mais Baixo</small>
                                                            <h5 class="mb-0 text-danger" id="mais-baixo">{{ $maisBaixo }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Grid de Atributos -->
                                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3" id="grid-atributos">
                                        @foreach(json_decode($personagem->sistema->atributos, true) as $key => $nome)
                                            @php
                                                // Obter valor base (sem bônus de raça)
                                                $valorBase = $valoresBase[$key] ?? 10;
                                                
                                                // Calcular bônus da raça
                                                $bonusRacaValor = 0;
                                                if ($personagem->raca && $personagem->raca->modificadores_atributos) {
                                                    $bonusData = is_string($personagem->raca->modificadores_atributos) 
                                                        ? json_decode($personagem->raca->modificadores_atributos, true)
                                                        : $personagem->raca->modificadores_atributos;
                                                    $bonusRacaValor = $bonusData[$key] ?? 0;
                                                }
                                                
                                                // Calcular valor total e modificador
                                                $valorTotal = $valorBase + $bonusRacaValor;
                                                $modificador = floor(($valorTotal - 10) / 2);
                                            @endphp
                                            <div class="col">
                                                <div class="card h-100 border atributo-card">
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
                                                                   value="{{ old('atributos.' . $key, $valorBase) }}" 
                                                                   min="1" max="20"
                                                                   data-key="{{ $key }}"
                                                                   data-bonus-raca="{{ $bonusRacaValor }}"
                                                                   onchange="atualizarAtributo('{{ $key }}')">
                                                        </div>
                                                        
                                                        <!-- Modificador -->
                                                        <div class="mb-1">
                                                            <span class="h5 mb-0 {{ $modificador >= 0 ? 'text-success' : 'text-danger' }}" 
                                                                  id="mod_{{ $key }}">
                                                                {{ $modificador >= 0 ? '+' : '' }}{{ $modificador }}
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- Bônus de Raça (se houver) -->
                                                        @if($bonusRacaValor > 0)
                                                            <div class="small text-info" id="bonus_raca_{{ $key }}">
                                                                <i class="fas fa-dragon me-1"></i>
                                                                +<span id="valor_bonus_raca_{{ $key }}">{{ $bonusRacaValor }}</span>
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Total com Bônus -->
                                                        <div class="small text-dark mt-1" id="total_{{ $key }}">
                                                            Total: <span id="valor_total_{{ $key }}">{{ $valorTotal }}</span>
                                                            (<span id="mod_total_{{ $key }}" class="{{ $modificador >= 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ $modificador >= 0 ? '+' : '' }}{{ $modificador }}
                                                            </span>)
                                                        </div>
                                                    </div>
                                                    <div class="card-footer text-center py-1">
                                                        <small class="text-muted">{{ strtoupper(substr($nome, 0, 3)) }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="distribuirPontosPadrao()">
                                            <i class="fas fa-dice"></i> Distribuição Padrão (15,14,13,12,10,8)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="atualizarTodosAtributos()">
                                            <i class="fas fa-sync-alt"></i> Recalcular Todos
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Os atributos não estão configurados para este sistema.
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
                                          placeholder="Descreva a aparência física do personagem...">{{ old('descricao', $personagem->descricao) }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="historia" class="form-label">História de Fundo</label>
                                <textarea class="form-control @error('historia') is-invalid @enderror" 
                                          id="historia" name="historia" rows="4"
                                          placeholder="Conte a história do personagem...">{{ old('historia', $personagem->historia) }}</textarea>
                                @error('historia')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="personalidade" class="form-label">Personalidade</label>
                                <textarea class="form-control @error('personalidade') is-invalid @enderror" 
                                          id="personalidade" name="personalidade" rows="3"
                                          placeholder="Descreva a personalidade, objetivos, medos...">{{ old('personalidade', $personagem->personalidade) }}</textarea>
                                @error('personalidade')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Inventário -->
                            <div class="col-12 mb-3">
                                <label for="inventario" class="form-label">
                                    <i class="fas fa-backpack me-1"></i> Inventário
                                </label>
                                <textarea class="form-control @error('inventario') is-invalid @enderror" 
                                          id="inventario" name="inventario" rows="5"
                                          placeholder="Liste os itens do personagem, um por linha ou em formato JSON. Exemplo:
• Espada longa
• Armadura de couro
• 50 po
• 3 poções de cura

Ou em JSON:
[&quot;Espada longa&quot;, &quot;Armadura de couro&quot;, &quot;50 po&quot;, &quot;3 poções de cura&quot;]">{{ old('inventario', is_array($personagem->inventario) ? json_encode($personagem->inventario, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($personagem->inventario ?? '')) }}</textarea>
                                @error('inventario')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Você pode digitar itens simples (um por linha) ou usar formato JSON. O sistema converterá automaticamente.
                                </small>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('personagens.show', $personagem) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Cancelar
                                        </a>
                                        <a href="{{ route('campanhas.show', $personagem->campanha_id) }}" 
                                           class="btn btn-outline-info ms-2">
                                            <i class="fas fa-campground me-1"></i> Voltar para Campanha
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-warning" id="btn-submit">
                                            <i class="fas fa-save me-1"></i> Atualizar Personagem
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
    text-align: center;
}

.atributo-input:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

.atributo-card {
    transition: all 0.3s ease;
}

.atributo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.atributo-card .card-header {
    background-color: #f8f9fa;
    font-weight: 600;
}

#inventario {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}
</style>
@endpush

@push('scripts')
<script>
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

$(document).ready(function() {
    // Inicializar cálculos
    atualizarBonusProficiencia();
    atualizarTodosAtributos();
    atualizarResumo();
    
    // Event listeners
    $('#nivel').on('input change', atualizarBonusProficiencia);
    $('#xp').on('input change', function() {
        atualizarBonusProficiencia();
    });
});

function atualizarBonusProficiencia() {
    const nivel = parseInt($('#nivel').val()) || 1;
    const xpAtual = parseInt($('#xp').val()) || 0;
    
    // Calcular bônus de proficiência
    const bonus = BONUS_PROFICIENCIA[nivel] || 2;
    $('#bonus-proficiencia').text('+' + bonus);
    $('#bonus_proficiencia').val(bonus);
    
    // Atualizar XP para próximo nível
    const xpProximo = XP_POR_NIVEL[Math.min(nivel + 1, 20)] || XP_POR_NIVEL[20];
    $('#xp-proximo').text(xpProximo.toLocaleString('pt-BR'));
    
    // Verificar se subiu de nível
    if (xpAtual >= xpProximo && nivel < 20) {
        $('#xp-proximo').addClass('text-success');
    } else {
        $('#xp-proximo').removeClass('text-success');
    }
}

function atualizarAtributo(key) {
    const input = $(`#atributos_${key}`);
    const valorBase = parseInt(input.val()) || 1;
    
    // Limitar entre 1 e 20
    if (valorBase < 1) input.val(1);
    if (valorBase > 20) input.val(20);
    
    const valorBaseFinal = parseInt(input.val());
    const bonusRaca = parseInt(input.data('bonus-raca')) || 0;
    const valorTotal = valorBaseFinal + bonusRaca;
    const modificador = Math.floor((valorTotal - 10) / 2);
    const textoMod = modificador >= 0 ? '+' + modificador : modificador.toString();
    
    // Atualizar display
    $(`#mod_${key}`).text(textoMod);
    $(`#mod_${key}`).removeClass('text-success text-danger')
        .addClass(modificador >= 0 ? 'text-success' : 'text-danger');
    
    $(`#valor_total_${key}`).text(valorTotal);
    $(`#mod_total_${key}`).text(textoMod);
    $(`#mod_total_${key}`).removeClass('text-success text-danger')
        .addClass(modificador >= 0 ? 'text-success' : 'text-danger');
    
    atualizarResumo();
}

function atualizarTodosAtributos() {
    $('.atributo-input').each(function() {
        const key = $(this).data('key');
        if (key) {
            atualizarAtributo(key);
        }
    });
}

function atualizarResumo() {
    let total = 0;
    let maisAlto = 0;
    let maisBaixo = 20;
    let count = 0;
    let valores = [];
    
    $('.atributo-input').each(function() {
        const valorBase = parseInt($(this).val()) || 1;
        const bonusRaca = parseInt($(this).data('bonus-raca')) || 0;
        const valorTotal = valorBase + bonusRaca;
        
        total += valorTotal;
        count++;
        valores.push(valorTotal);
        
        if (valorTotal > maisAlto) maisAlto = valorTotal;
        if (valorTotal < maisBaixo) maisBaixo = valorTotal;
    });
    
    if (count > 0) {
        const media = (total / count).toFixed(1);
        $('#total-pontos').text(total);
        $('#media-atributos').text(media);
        $('#mais-alto').text(maisAlto);
        $('#mais-baixo').text(maisBaixo);
        
        // Destacar se algum atributo está muito baixo ou alto
        if (maisAlto >= 18) {
            $('#mais-alto').addClass('fw-bold');
        } else {
            $('#mais-alto').removeClass('fw-bold');
        }
        
        if (maisBaixo <= 6) {
            $('#mais-baixo').addClass('fw-bold');
        } else {
            $('#mais-baixo').removeClass('fw-bold');
        }
    }
}

function distribuirPontosPadrao() {
    if (confirm('Isso substituirá os valores atuais dos atributos pela distribuição padrão (15,14,13,12,10,8). Deseja continuar?')) {
        const valores = [15, 14, 13, 12, 10, 8];
        let index = 0;
        
        $('.atributo-input').each(function() {
            if (index < valores.length) {
                $(this).val(valores[index]);
                const key = $(this).data('key');
                if (key) {
                    atualizarAtributo(key);
                }
                index++;
            }
        });
        
        atualizarTodosAtributos();
        atualizarResumo();
    }
}

// Formatar inventário antes de enviar
function formatarInventarioParaJSON(texto) {
    if (!texto.trim()) {
        return null;
    }
    
    // Tentar parsear como JSON
    try {
        const json = JSON.parse(texto);
        return JSON.stringify(json);
    } catch (e) {
        // Se não for JSON válido, converter de texto simples para array
        const linhas = texto.split('\n')
            .map(linha => linha.trim())
            .filter(linha => linha.length > 0)
            .map(linha => {
                // Remover marcadores de lista
                return linha.replace(/^[\-•*]\s*/, '');
            });
        
        return JSON.stringify(linhas);
    }
}

// Validação do formulário
$('#formPersonagem').on('submit', function(e) {
    e.preventDefault();
    
    // Validar nome
    const nome = $('#nome').val().trim();
    if (!nome) {
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
        alert('Por favor, insira valores válidos para os atributos (1-20).');
        return false;
    }
    
    // Formatar inventário
    const inventarioText = $('#inventario').val();
    $('#inventario').val(formatarInventarioParaJSON(inventarioText));
    
    // Mostrar loading no botão
    const btnSubmit = $('#btn-submit');
    btnSubmit.html('<i class="fas fa-spinner fa-spin me-1"></i> Atualizando...');
    btnSubmit.prop('disabled', true);
    
    // Enviar formulário
    this.submit();
});

// Mostrar alerta se houver erros no formulário
@if($errors->any())
    $(document).ready(function() {
        const errorCount = {{ $errors->count() }};
        if (errorCount > 0) {
            alert('Por favor, corrija os ' + errorCount + ' erro(s) no formulário antes de enviar novamente.');
        }
    });
@endif
</script>
@endpush