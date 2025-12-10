{{-- resources/views/personagens/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Meus Personagens')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>Meus Personagens
                    @if($personagens->total() > 0)
                        <span class="badge bg-primary ms-2">{{ $personagens->total() }}</span>
                    @endif
                </h1>
                @can('create', \App\Models\Personagem::class)
                    <a href="{{ route('personagens.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Novo Personagem
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros
                <button class="btn btn-sm btn-outline-secondary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </h5>
        </div>
        <div class="collapse show" id="filtrosCollapse">
            <div class="card-body">
                <form action="{{ route('personagens.index') }}" method="GET" class="row g-3">
                    {{-- Filtros básicos --}}
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nome, história, descrição...">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="campanha_id" class="form-label">Campanha</label>
                        <select class="form-select" id="campanha_id" name="campanha_id">
                            <option value="">Todas</option>
                            @foreach($campanhas as $campanha)
                                <option value="{{ $campanha->id }}" {{ request('campanha_id') == $campanha->id ? 'selected' : '' }}>
                                    {{ $campanha->nome }} ({{ $campanha->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="sistema_id" class="form-label">Sistema</label>
                        <select class="form-select" id="sistema_id" name="sistema_id">
                            <option value="">Todos</option>
                            @foreach($sistemasDisponiveis as $sistema)
                                <option value="{{ $sistema->id }}" {{ request('sistema_id') == $sistema->id ? 'selected' : '' }}>
                                    {{ $sistema->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="ativo" class="form-label">Status</label>
                        <select class="form-select" id="ativo" name="ativo">
                            <option value="">Todos</option>
                            <option value="true" {{ request('ativo') === 'true' ? 'selected' : '' }}>Ativos</option>
                            <option value="false" {{ request('ativo') === 'false' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    
                    {{-- Filtros avançados --}}
                    <div class="col-md-2">
                        <label for="raca_id" class="form-label">Raça</label>
                        <select class="form-select" id="raca_id" name="raca_id">
                            <option value="">Todas</option>
                            @foreach($racasDisponiveis as $raca)
                                <option value="{{ $raca->id }}" {{ request('raca_id') == $raca->id ? 'selected' : '' }}>
                                    {{ $raca->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="classe_id" class="form-label">Classe</label>
                        <select class="form-select" id="classe_id" name="classe_id">
                            <option value="">Todas</option>
                            @foreach($classesDisponiveis as $classe)
                                <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="origem_id" class="form-label">Origem</label>
                        <select class="form-select" id="origem_id" name="origem_id">
                            <option value="">Todas</option>
                            @foreach($origensDisponiveis as $origem)
                                <option value="{{ $origem->id }}" {{ request('origem_id') == $origem->id ? 'selected' : '' }}>
                                    {{ $origem->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="nivel_min" class="form-label">Nível Mín</label>
                        <input type="number" class="form-control" id="nivel_min" name="nivel_min" 
                               value="{{ request('nivel_min') }}" min="1" max="20">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="nivel_max" class="form-label">Nível Máx</label>
                        <input type="number" class="form-control" id="nivel_max" name="nivel_max" 
                               value="{{ request('nivel_max') }}" min="1" max="20">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="ordenar" class="form-label">Ordenar por</label>
                        <select class="form-select" id="ordenar" name="ordenar">
                            @foreach($opcoesOrdenacao as $valor => $texto)
                                <option value="{{ $valor }}" {{ request('ordenar', 'created_at_desc') == $valor ? 'selected' : '' }}>
                                    {{ $texto }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="por_pagina" class="form-label">Por página</label>
                        <select class="form-select" id="por_pagina" name="por_pagina">
                            @foreach($opcoesPorPagina as $valor)
                                <option value="{{ $valor }}" {{ request('por_pagina', 12) == $valor ? 'selected' : '' }}>
                                    {{ $valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('personagens.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Estatísticas --}}
    @if($estatisticas['total'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Estatísticas
                        <small class="text-muted ms-2">{{ $estatisticas['total'] }} personagem(s)</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-success bg-opacity-10 border-success">
                                <div class="card-body text-center">
                                    <h3 class="text-success">{{ $estatisticas['ativos'] }}</h3>
                                    <p class="mb-0 text-success">Ativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-warning bg-opacity-10 border-warning">
                                <div class="card-body text-center">
                                    <h3 class="text-warning">{{ $estatisticas['inativos'] }}</h3>
                                    <p class="mb-0 text-warning">Inativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-info bg-opacity-10 border-info">
                                <div class="card-body text-center">
                                    <h3 class="text-info">{{ $estatisticas['media_nivel'] }}</h3>
                                    <p class="mb-0 text-info">Nível Médio</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-primary bg-opacity-10 border-primary">
                                <div class="card-body text-center">
                                    <h3 class="text-primary">{{ number_format($estatisticas['total_xp']) }}</h3>
                                    <p class="mb-0 text-primary">XP Total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($estatisticas['personagem_maior_nivel'])
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-crown me-2"></i>
                        <strong>Personagem de Maior Nível:</strong> 
                        {{ $estatisticas['personagem_maior_nivel']->nome }} (Nível {{ $estatisticas['personagem_maior_nivel']->nivel }})
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Lista de personagens --}}
    <div class="card">
        <div class="card-body">
            @if($personagens->count() > 0)
                <div class="row">
                    @foreach($personagens as $personagem)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 border-{{ $personagem->ativo ? 'success' : 'warning' }} border-2">
                                <div class="card-header bg-{{ $personagem->ativo ? 'success' : 'warning' }} bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">
                                            {{ $personagem->nome }}
                                            @if(!$personagem->ativo)
                                                <span class="badge bg-warning ms-1">Inativo</span>
                                            @endif
                                        </h5>
                                        <small class="text-muted">
                                            Nível {{ $personagem->nivel }} • {{ $personagem->xp }} XP
                                        </small>
                                    </div>
                                    <span class="badge bg-info">{{ $personagem->sistema->nome ?? 'Sistema' }}</span>
                                </div>
                                
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            @if($personagem->imagem)
                                                <img src="{{ Storage::url($personagem->imagem) }}" 
                                                     alt="{{ $personagem->nome }}" 
                                                     class="img-fluid rounded mb-3" 
                                                     style="max-height: 150px; object-fit: cover; width: 100%;">
                                            @else
                                                <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center mb-3" 
                                                     style="height: 150px;">
                                                    <i class="fas fa-user fa-3x text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong><i class="fas fa-dungeon me-1"></i>Campanha:</strong><br>
                                            @if($personagem->campanha)
                                                <span class="badge bg-primary">{{ $personagem->campanha->nome }}</span>
                                            @else
                                                <span class="text-muted">Sem campanha</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong><i class="fas fa-dragon me-1"></i>Raça:</strong><br>
                                            {{ $personagem->raca->nome ?? 'Não definida' }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong><i class="fas fa-shield-alt me-1"></i>Classe:</strong><br>
                                            {{ $personagem->classe->nome ?? 'Não definida' }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong><i class="fas fa-book me-1"></i>Origem:</strong><br>
                                            {{ $personagem->origem->nome ?? 'Não definida' }}
                                        </div>
                                    </div>
                                    
                                    @if($personagem->descricao)
                                        <div class="mt-3">
                                            <p class="mb-0" style="font-size: 0.9rem;">
                                                {{ Str::limit($personagem->descricao, 100) }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('personagens.show', $personagem) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                        @can('update', $personagem)
                                            <a href="{{ route('personagens.edit', $personagem) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </a>
                                            @can('delete', $personagem)
                                                <form action="{{ route('personagens.destroy', $personagem) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este personagem?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash me-1"></i>Excluir
                                                    </button>
                                                </form>
                                            @endcan
                                        @endcan
                                    </div>
                                    
                                    <div class="mt-2 text-muted text-end">
                                        <small>
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $personagem->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Paginação --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Mostrando {{ $personagens->firstItem() }} a {{ $personagens->lastItem() }} 
                        de {{ $personagens->total() }} resultados
                    </div>
                    <div>
                        {{ $personagens->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-users fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted">Nenhum personagem encontrado</h4>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'campanha_id', 'sistema_id', 'ativo']))
                            Tente ajustar os filtros de busca
                        @else
                            Você ainda não criou nenhum personagem
                        @endif
                    </p>
                    @can('create', \App\Models\Personagem::class)
                        <a href="{{ route('personagens.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Criar Primeiro Personagem
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-submit ao mudar ordenação ou itens por página
    document.addEventListener('DOMContentLoaded', function() {
        const ordenarSelect = document.getElementById('ordenar');
        const porPaginaSelect = document.getElementById('por_pagina');
        
        if (ordenarSelect) {
            ordenarSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }
        
        if (porPaginaSelect) {
            porPaginaSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }
        
        // Botão para mostrar/esconder filtros avançados
        const filtrosBtn = document.querySelector('[data-bs-target="#filtrosCollapse"]');
        if (filtrosBtn) {
            filtrosBtn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('fa-chevron-down')) {
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                } else {
                    icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                }
            });
        }
    });
</script>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .border-success {
        border-width: 2px !important;
    }
    
    .border-warning {
        border-width: 2px !important;
    }
    
    .badge {
        font-size: 0.75em;
    }
</style>
@endpush