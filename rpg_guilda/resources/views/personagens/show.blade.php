@extends('layouts.app')

@section('title', $personagem->nome)

@section('content')
<div class="container py-4">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('personagens.index') }}">Personagens</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $personagem->nome }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('personagens.edit', $personagem) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('personagens.exportar-pdf', $personagem) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
                <a href="{{ route('personagens.pericias', $personagem) }}" class="btn btn-outline-info">
                    <i class="fas fa-tasks"></i> Perícias
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Coluna Esquerda - Informações Básicas -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                @if($personagem->imagem)
                    <img src="{{ Storage::url($personagem->imagem) }}" 
                         class="card-img-top" 
                         alt="{{ $personagem->nome }}"
                         style="height: 300px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-gradient-primary text-white d-flex align-items-center justify-content-center" 
                         style="height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="text-center">
                            <i class="fas fa-user fa-5x mb-3"></i>
                            <h4>{{ $personagem->nome }}</h4>
                        </div>
                    </div>
                @endif
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="card-title mb-0">{{ $personagem->nome }}</h3>
                        @if($personagem->ativo)
                            <span class="badge bg-success fs-6">Ativo</span>
                        @else
                            <span class="badge bg-secondary fs-6">Inativo</span>
                        @endif
                    </div>
                    
                    <!-- Badges de Informação -->
                    <div class="mb-3">
                        <span class="badge bg-primary fs-6 mb-2">
                            <i class="fas fa-campground me-1"></i> {{ $personagem->campanha->nome }}
                        </span>
                        <span class="badge bg-secondary fs-6 mb-2">
                            <i class="fas fa-dice-d20 me-1"></i> {{ $personagem->sistema->nome }}
                        </span>
                    </div>
                    
                    <!-- Grid de Detalhes -->
                    <div class="row g-2 mb-3">
                        @if($personagem->raca)
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-dragon text-info me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Raça</small>
                                        <strong class="fs-5">{{ $personagem->raca->nome }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($personagem->classe)
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-warning me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Classe</small>
                                        <strong class="fs-5">{{ $personagem->classe->nome }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($personagem->origem)
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-history text-success me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Origem</small>
                                        <strong class="fs-5">{{ $personagem->origem->nome }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Nível</small>
                                    <strong class="fs-5">{{ $personagem->nivel }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Atributos Especiais -->
                    <div class="row g-2">
                        @if($personagem->sorte)
                            <div class="col-6">
                                <div class="alert alert-warning py-2 mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clover me-2"></i>
                                        <div>
                                            <small class="d-block">Sorte</small>
                                            <strong class="fs-5">{{ $personagem->sorte }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($personagem->sanidade)
                            <div class="col-6">
                                <div class="alert alert-info py-2 mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-brain me-2"></i>
                                        <div>
                                            <small class="d-block">Sanidade</small>
                                            <strong class="fs-5">{{ $personagem->sanidade }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="card-footer bg-transparent">
                    <small class="text-muted">
                        <i class="fas fa-user-circle me-1"></i> Criado por: {{ $personagem->user->name }} 
                        em {{ $personagem->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
            
            <!-- Progresso de Nível -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Progresso do Nível
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: {{ min($progressoNivel, 100) }}%"
                             aria-valuenow="{{ $progressoNivel }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <span class="fw-bold">{{ number_format($progressoNivel, 1) }}%</span>
                        </div>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">XP Atual</small>
                                <strong class="fs-5">{{ number_format($personagem->xp) }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Próximo Nível</small>
                                <strong class="fs-5">{{ number_format($personagem->xpProximoNivel()) }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form para adicionar XP -->
                    <form action="{{ route('personagens.adicionar-xp', $personagem) }}" 
                          method="POST" class="mt-2">
                        @csrf
                        <div class="input-group">
                            <input type="number" name="xp" class="form-control" 
                                   placeholder="XP para adicionar" min="1" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle"></i> Adicione XP para subir de nível
                        </small>
                    </form>
                </div>
            </div>
            
            <!-- Pontos de Vida -->
            @if($pontosVida)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-gradient-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-heart me-2"></i>Pontos de Vida
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="display-1 text-danger fw-bold">{{ $pontosVida }}</div>
                    <p class="text-muted">
                        <i class="fas fa-dice me-1"></i>
                        Baseado em {{ $personagem->classe?->dado_vida ?? 'N/A' }} por nível
                    </p>
                </div>
            </div>
            @endif
        </div>

        <!-- Coluna Direita - Detalhes -->
        <div class="col-lg-8">
            <!-- Atributos -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Atributos
                    </h5>
                    <span class="badge bg-light text-dark fs-6">
                        <i class="fas fa-award me-1"></i> Bônus de Proficiência: +{{ $personagem->bonus_proficiencia }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
                        @foreach($atributosCompletos as $key => $atributo)
                            <div class="col">
                                <div class="text-center p-3 border rounded shadow-sm hover-lift">
                                    <div class="text-muted small text-uppercase fw-bold">
                                        {{ $personagem->sistema->atributos[$key] ?? $key }}
                                    </div>
                                    <div class="display-4 fw-bold my-2">{{ $atributo['valor'] }}</div>
                                    <div class="h3 mb-0 {{ $atributo['modificador'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $atributo['modificador'] >= 0 ? '+' : '' }}{{ $atributo['modificador'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- CONTAINERS SEPARADOS: Descrição, História e Personalidade -->
            <div class="row mb-4">
                <!-- Descrição -->
                <div class="col-lg-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye me-2"></i>Descrição
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($personagem->descricao)
                                <div class="text-content" style="max-height: 200px; overflow-y: auto;">
                                    {!! nl2br(e($personagem->descricao)) !!}
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-eye-slash fa-2x mb-3"></i>
                                    <p class="mb-0">Nenhuma descrição fornecida.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- História -->
                <div class="col-lg-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-book me-2"></i>História
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($personagem->historia)
                                <div class="text-content" style="max-height: 200px; overflow-y: auto;">
                                    {!! nl2br(e($personagem->historia)) !!}
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-book fa-2x mb-3"></i>
                                    <p class="mb-0">Nenhuma história fornecida.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Personalidade -->
                <div class="col-lg-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-warning text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-brain me-2"></i>Personalidade
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($personagem->personalidade)
                                <div class="text-content" style="max-height: 200px; overflow-y: auto;">
                                    {!! nl2br(e($personagem->personalidade)) !!}
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-user fa-2x mb-3"></i>
                                    <p class="mb-0">Nenhuma descrição de personalidade fornecida.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventário -->
            @if($personagem->inventario && count($personagem->inventario) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-backpack me-2"></i>Inventário
                        </h5>
                        <span class="badge bg-light text-dark">
                            {{ count($personagem->inventario) }} itens
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Item</th>
                                        <th width="15%">Quantidade</th>
                                        <th width="15%">Peso</th>
                                        <th width="30%">Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($personagem->inventario as $index => $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item['nome'] ?? 'Item ' . ($index + 1) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $item['quantidade'] ?? 1 }}</span>
                                            </td>
                                            <td>
                                                @if(isset($item['peso']))
                                                    <span class="badge bg-info">{{ $item['peso'] }} kg</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $item['descricao'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Perícias -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>Perícias
                    </h5>
                    <a href="{{ route('personagens.pericias', $personagem) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i> Gerenciar
                    </a>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        @forelse($personagem->pericias as $personagemPericia)
                            @if($personagemPericia->pericia)
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded hover-lift">
                                        <div>
                                            <strong>{{ $personagemPericia->pericia->nome }}</strong>
                                            <div class="small text-muted">
                                                <i class="fas fa-fist-raised me-1"></i>
                                                {{ $personagemPericia->pericia->atributo_nome ?? $personagemPericia->pericia->atributo_relacionado }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="h4 mb-0 {{ $personagemPericia->calcularBonus() >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $personagemPericia->calcularBonus() >= 0 ? '+' : '' }}{{ $personagemPericia->calcularBonus() }}
                                            </span>
                                            @if($personagemPericia->proficiente)
                                                <span class="badge bg-info mt-1">
                                                    <i class="fas fa-check-circle me-1"></i> Proficiente
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-tasks fa-2x mb-3"></i>
                                    <p class="mb-0">Nenhuma perícia configurada.</p>
                                    <a href="{{ route('personagens.pericias', $personagem) }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> Adicionar Perícias
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-skull-crossbones fa-2x float-start me-3"></i>
                    <h5 class="alert-heading">Atenção!</h5>
                    <p class="mb-0">Tem certeza que deseja excluir permanentemente o personagem <strong>{{ $personagem->nome }}</strong>?</p>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Esta ação não pode ser desfeita!</strong> Todos os dados do personagem serão perdidos.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <form action="{{ route('personagens.destroy', $personagem) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Excluir Permanentemente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #434343 0%, #000000 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #141e30 0%, #243b55 100%);
}

.text-content {
    line-height: 1.6;
    font-size: 0.9rem;
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% { background-position: 1rem 0; }
    100% { background-position: 0 0; }
}
</style>
@endpush