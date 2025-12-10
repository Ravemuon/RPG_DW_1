@extends('layouts.app')

@section('title', 'Meus Personagens')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Meus Personagens</h1>
            <p class="text-muted">Gerencie seus personagens de RPG</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('personagens.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Personagem
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome ou descrição...">
                </div>
                <div class="col-md-3">
                    <label for="campanha_id" class="form-label">Campanha</label>
                    <select class="form-select" id="campanha_id" name="campanha_id">
                        <option value="">Todas</option>
                        @foreach($campanhas as $campanha)
                            <option value="{{ $campanha->id }}" {{ request('campanha_id') == $campanha->id ? 'selected' : '' }}>
                                {{ $campanha->nome }}
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
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('personagens.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Personagens -->
    @if($personagens->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-user-slash fa-3x text-muted"></i>
            </div>
            <h4 class="text-muted">Nenhum personagem encontrado</h4>
            <p class="text-muted">Comece criando seu primeiro personagem!</p>
            <a href="{{ route('personagens.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Criar Personagem
            </a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($personagens as $personagem)
                <div class="col">
                    <div class="card h-100">
                        @if($personagem->imagem)
                            <img src="{{ Storage::url($personagem->imagem) }}" 
                                 class="card-img-top" 
                                 alt="{{ $personagem->nome }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-dark text-white d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $personagem->nome }}</h5>
                                <span class="badge bg-{{ $personagem->ativo ? 'success' : 'secondary' }}">
                                    {{ $personagem->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small mb-2">
                                <i class="fas fa-campground"></i> {{ $personagem->campanha->nome }}
                            </p>
                            
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($personagem->raca)
                                    <span class="badge bg-info">{{ $personagem->raca->nome }}</span>
                                @endif
                                @if($personagem->classe)
                                    <span class="badge bg-warning text-dark">{{ $personagem->classe->nome }}</span>
                                @endif
                                <span class="badge bg-dark">Nível {{ $personagem->nivel }}</span>
                            </div>
                            
                            @if($personagem->descricao)
                                <p class="card-text small">{{ Str::limit($personagem->descricao, 100) }}</p>
                            @endif
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('personagens.show', $personagem) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                
                                <div class="btn-group">
                                    <a href="{{ route('personagens.edit', $personagem) }}" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $personagem->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal de Confirmação -->
                    <div class="modal fade" id="deleteModal{{ $personagem->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Exclusão</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Tem certeza que deseja excluir o personagem <strong>{{ $personagem->nome }}</strong>?</p>
                                    <p class="text-danger small">Esta ação não pode ser desfeita.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form action="{{ route('personagens.destroy', $personagem) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Excluir</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Paginação -->
        <div class="mt-4">
            {{ $personagens->links() }}
        </div>
    @endif
</div>
@endsection