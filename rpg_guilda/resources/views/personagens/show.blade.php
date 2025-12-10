@extends('layouts.app')

@section('title', $personagem->nome)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('personagens.index') }}">Personagens</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $personagem->nome }}</li>
                </ol>
            </nav>
            <h1 class="display-4 fw-bold mb-0">
                {{ $personagem->nome }} 
                <small class="text-muted fs-5"> (Nível {{ $personagem->nivel }})</small>
                @if($personagem->ativo)
                    <span class="badge bg-success align-text-bottom ms-2">Ativo</span>
                @else
                    <span class="badge bg-secondary align-text-bottom ms-2">Inativo</span>
                @endif
            </h1>
        </div>
        <div class="btn-group shadow-sm">
            <a href="{{ route('personagens.edit', $personagem) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('personagens.exportar-pdf', $personagem) }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('personagens.pericias', $personagem) }}" class="btn btn-outline-info">
                <i class="fas fa-tasks me-1"></i> Perícias
            </a>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Excluir Personagem">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-4 mb-4">
            
            <div class="card shadow-lg mb-4 hover-lift">
                @if($personagem->imagem)
                    <img src="{{ Storage::url($personagem->imagem) }}" 
                          class="card-img-top img-fluid character-image" 
                          alt="{{ $personagem->nome }}">
                @else
                    <div class="card-img-top bg-gradient-primary text-white d-flex align-items-center justify-content-center character-image-placeholder">
                        <div class="text-center">
                            <i class="fas fa-user-circle fa-5x mb-3 opacity-75"></i>
                            <h4 class="mb-0">{{ $personagem->nome }}</h4>
                        </div>
                    </div>
                @endif
                
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="fas fa-info-circle me-2"></i> Detalhes Essenciais</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fas fa-campground text-primary me-2"></i> Campanha:
                            <span class="fw-bold">{{ $personagem->campanha->nome }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <i class="fas fa-dice-d20 text-secondary me-2"></i> Sistema:
                            <span class="fw-bold">{{ $personagem->sistema->nome }}</span>
                        </li>
                        @if($personagem->raca)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <i class="fas fa-dragon text-info me-2"></i> Raça:
                                <span class="fw-bold">{{ $personagem->raca->nome }}</span>
                            </li>
                        @endif
                        @if($personagem->classe)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <i class="fas fa-shield-alt text-warning me-2"></i> Classe:
                                <span class="fw-bold">{{ $personagem->classe->nome }}</span>
                            </li>
                        @endif
                        @if($personagem->origem)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <i class="fas fa-history text-success me-2"></i> Origem:
                                <span class="fw-bold">{{ $personagem->origem->nome }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="card-footer text-muted bg-light">
                    <small>
                        <i class="fas fa-user-circle me-1"></i> Criado por: {{ $personagem->user->name }} 
                        em {{ $personagem->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
            
            @if(isset($pontosVida))
            <div class="card shadow-sm bg-gradient-danger text-white mb-4 hover-lift">
                <div class="card-body text-center py-3">
                    <h6 class="mb-0 text-uppercase">
                        <i class="fas fa-heart me-2"></i> Pontos de Vida Máximos
                    </h6>
                    <div class="display-3 fw-bolder">{{ $pontosVida }}</div>
                    <small class="opacity-75">
                         Baseado em {{ $personagem->classe?->dado_vida ?? 'N/A' }} por nível
                    </small>
                </div>
            </div>
            @endif
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-gradient-dark text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Progresso do Nível</h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: {{ min($progressoNivel, 100) }}%"
                             aria-valuenow="{{ $progressoNivel }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <span class="fw-bolder fs-5">{{ number_format($progressoNivel, 1) }}%</span>
                        </div>
                    </div>
                    
                    <div class="row text-center g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">XP Atual</small>
                                <strong class="fs-5 text-primary">{{ number_format($personagem->xp) }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <small class="text-muted d-block">XP Próximo Nível</small>
                                <strong class="fs-5 text-success">{{ number_format($personagem->xpProximoNivel()) }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('personagens.adicionar-xp', $personagem) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <input type="number" name="xp" class="form-control form-control-lg" 
                                   placeholder="XP para adicionar" min="1" required>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-1"></i> Adicionar
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle"></i> Adicione XP para avançar.</small>
                    </form>
                </div>
            </div>
            
            <div class="row g-3 mt-1">
                @if($personagem->sorte)
                    <div class="col-6">
                        <div class="alert alert-warning py-3 mb-0 text-center shadow-sm hover-lift">
                            <i class="fas fa-clover fa-2x d-block mb-1"></i>
                            <small class="d-block text-uppercase fw-bold">Sorte</small>
                            <strong class="display-6 d-block">{{ $personagem->sorte }}</strong>
                        </div>
                    </div>
                @endif
                @if($personagem->sanidade)
                    <div class="col-6">
                        <div class="alert alert-info py-3 mb-0 text-center shadow-sm hover-lift">
                            <i class="fas fa-brain fa-2x d-block mb-1"></i>
                            <small class="d-block text-uppercase fw-bold">Sanidade</small>
                            <strong class="display-6 d-block">{{ $personagem->sanidade }}</strong>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <div class="col-lg-8">
            
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i> Atributos Base</h5>
                    <span class="badge bg-light text-dark fs-6 p-2">
                        <i class="fas fa-award me-1"></i> Bônus de Proficiência: **+{{ $personagem->bonus_proficiencia }}**
                    </span>
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
                        @foreach($atributosCompletos as $key => $atributo)
                            <div class="col">
                                <div class="text-center p-3 border rounded shadow-sm hover-lift attribute-card">
                                    <div class="text-muted small text-uppercase fw-bold attribute-name">
                                        {{ $personagem->sistema->atributos[$key] ?? $key }}
                                    </div>
                                    <div class="display-5 fw-bold my-1 attribute-value">{{ $atributo['valor'] }}</div>
                                    <div class="h3 mb-0 fw-bolder attribute-modifier {{ $atributo['modificador'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $atributo['modificador'] >= 0 ? '+' : '' }}{{ $atributo['modificador'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#tab-descricao" type="button" role="tab"><i class="fas fa-eye me-1"></i> Descrição</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="hist-tab" data-bs-toggle="tab" data-bs-target="#tab-historia" type="button" role="tab"><i class="fas fa-book me-1"></i> História</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="pers-tab" data-bs-toggle="tab" data-bs-target="#tab-personalidade" type="button" role="tab"><i class="fas fa-user-tie me-1"></i> Personalidade</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="tab-descricao" role="tabpanel">
                        @if($personagem->descricao)
                            <div class="text-content">
                                {!! nl2br(e($personagem->descricao)) !!}
                            </div>
                        @else
                            <div class="text-center text-muted py-3"><i class="fas fa-eye-slash fa-2x mb-2"></i><p class="mb-0">Nenhuma descrição fornecida.</p></div>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="tab-historia" role="tabpanel">
                        @if($personagem->historia)
                            <div class="text-content">
                                {!! nl2br(e($personagem->historia)) !!}
                            </div>
                        @else
                            <div class="text-center text-muted py-3"><i class="fas fa-book-open fa-2x mb-2"></i><p class="mb-0">Nenhuma história fornecida.</p></div>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="tab-personalidade" role="tabpanel">
                        @if($personagem->personalidade)
                            <div class="text-content">
                                {!! nl2br(e($personagem->personalidade)) !!}
                            </div>
                        @else
                            <div class="text-center text-muted py-3"><i class="fas fa-brain fa-2x mb-2"></i><p class="mb-0">Nenhuma descrição de personalidade fornecida.</p></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Perícias</h5>
                    <a href="{{ route('personagens.pericias', $personagem) }}" class="btn btn-light btn-sm shadow-sm">
                        <i class="fas fa-edit me-1"></i> Gerenciar
                    </a>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        @forelse($personagem->pericias as $personagemPericia)
                            @if($personagemPericia->pericia)
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded h-100 hover-lift skill-card">
                                        <div>
                                            <strong class="text-primary">{{ $personagemPericia->pericia->nome }}</strong>
                                            <div class="small text-muted">
                                                <i class="fas fa-fist-raised me-1"></i>
                                                Base: {{ $personagemPericia->pericia->atributo_nome ?? $personagemPericia->pericia->atributo_relacionado }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="h4 mb-0 fw-bolder skill-bonus {{ $personagemPericia->calcularBonus() >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $personagemPericia->calcularBonus() >= 0 ? '+' : '' }}{{ $personagemPericia->calcularBonus() }}
                                            </span>
                                            @if($personagemPericia->proficiente)
                                                <span class="badge bg-info mt-1 d-block">
                                                    <i class="fas fa-check-circle"></i> Proficiente
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-4 border rounded bg-light">
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
            
            @if($personagem->inventario && count($personagem->inventario) > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-backpack me-2"></i> Inventário</h5>
                        <span class="badge bg-light text-dark fs-6">{{ count($personagem->inventario) }} itens</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qtd.</th>
                                        <th class="text-center">Peso</th>
                                        <th>Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($personagem->inventario as $index => $item)
                                        <tr>
                                            <td>
                                                <i class="fas fa-gem text-info me-2"></i>
                                                <strong>{{ $item['nome'] ?? 'Item ' . ($index + 1) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill">{{ $item['quantidade'] ?? 1 }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if(isset($item['peso']))
                                                    <span class="badge bg-secondary">{{ $item['peso'] }} kg</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="small text-wrap item-description">{{ $item['descricao'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-backpack me-2"></i> Inventário</h5>
                    </div>
                    <div class="card-body">
                         <div class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-3"></i>
                            <p class="mb-0">O inventário está vazio.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

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
                <div class="alert alert-danger border-0">
                    <i class="fas fa-skull-crossbones fa-2x float-start me-3"></i>
                    <h5 class="alert-heading fw-bold">Atenção!</h5>
                    <p class="mb-0">Tem certeza que deseja excluir permanentemente o personagem **{{ $personagem->nome }}**?</p>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    **Esta ação não pode ser desfeita!** Todos os dados do personagem serão perdidos.
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
/* Estilos de gradientes e efeito lift mantidos e aprimorados */
.hover-lift {
    transition: all 0.3s ease;
    cursor: default; /* Indicar que é um elemento estático que reage ao hover */
}

.hover-lift:hover {
    transform: translateY(-3px); /* Movimento mais sutil */
    box-shadow: 0 8px 18px rgba(0,0,0,0.2) !important; /* Sombra mais destacada */
}

/* Gradientes */
.bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); } /* Um azul mais forte */
.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); } /* Um verde mais forte */
.bg-gradient-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda200 100%); } /* Um amarelo mais forte */
.bg-gradient-danger { background: linear-gradient(135deg, #e74a3b 0%, #cc372c 100%); } /* Um vermelho mais forte */
.bg-gradient-secondary { background: linear-gradient(135deg, #858796 0%, #6d7083 100%); }
.bg-gradient-dark { background: linear-gradient(135deg, #36b9cc 0%, #1e879c 100%); } /* Mudando para um tom mais aqua */

.text-content {
    line-height: 1.6;
    font-size: 0.95rem; /* Um pouco maior para melhor legibilidade */
}

/* Estilos Específicos do Personagem */
.character-image {
    height: 350px; /* Um pouco mais alto */
    object-fit: cover;
    border-top-left-radius: calc(0.25rem - 1px);
    border-top-right-radius: calc(0.25rem - 1px);
}

.character-image-placeholder {
    height: 350px;
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border-top-left-radius: calc(0.25rem - 1px);
    border-top-right-radius: calc(0.25rem - 1px);
}

/* Estilos da barra de progresso */
.progress {
    height: 30px;
    background-color: #e9ecef;
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% { background-position: 1rem 0; }
    100% { background-position: 0 0; }
}

/* Estilo para as abas */
.card-header-tabs .nav-link {
    color: #495057;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    border: 1px solid transparent;
    border-bottom: 0;
}

.card-header-tabs .nav-link.active {
    color: #fff;
    background-color: #4e73df; /* Cor do gradiente primário */
    border-color: #4e73df;
}

/* Pequeno estilo para as tabelas do inventário */
.table-hover > tbody > tr:hover {
    --bs-table-hover-bg: #e9f2ff; /* Um azul suave no hover */
}

.item-description {
    max-width: 250px; /* Limitar para que a coluna não fique muito larga */
}

/* Estilo para dar um toque de RPG nas perícias/atributos */
.skill-card, .attribute-card {
    border-width: 2px !important;
    border-color: #dee2e6 !important;
}

.attribute-card:hover, .skill-card:hover {
    border-color: #4e73df !important; /* Borda azul no hover */
}
</style>
@endpush