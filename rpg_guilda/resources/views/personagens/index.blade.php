@extends('layouts.app')

@section('title', 'Meus Personagens')

@push('styles')
<style>
    .character-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid #0d6efd;
    }
    
    .character-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }
    
    .character-avatar {
        width: 60px;
        height: 60px;
        border: 3px solid #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    
    {{-- Cabeçalho da Página --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="text-primary mb-1">
                        <i class="fas fa-users me-2"></i>Meus Personagens
                    </h1>
                    <p class="text-muted mb-0">
                        Gerencie todos os seus personagens de RPG em um só lugar
                    </p>
                </div>
                <a href="{{ route('personagens.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-plus-circle me-2"></i> Novo Personagem
                </a>
            </div>
        </div>
    </div>

    {{-- Cards de Estatísticas --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">TOTAL</h6>
                            <h2 class="mb-0">{{ $personagens->count() }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">EM CAMPANHA</h6>
                            <h2 class="mb-0">{{ $personagens->where('campanha_id', '!=', null)->count() }}</h2>
                        </div>
                        <i class="fas fa-map-marked-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">MÉDIA DE NÍVEL</h6>
                            <h2 class="mb-0">{{ round($personagens->avg('nivel') ?? 1, 1) }}</h2>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">SISTEMAS</h6>
                            <h2 class="mb-0">{{ $personagens->pluck('sistema_id')->unique()->count() }}</h2>
                        </div>
                        <i class="fas fa-dice-d20 fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($personagens->isEmpty())
        {{-- Estado Vazio --}}
        <div class="card shadow-lg border-0">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Nenhum Personagem Encontrado</h3>
                    <p class="text-muted mb-4">Comece criando seu primeiro personagem para ver a magia acontecer!</p>
                </div>
                <a href="{{ route('personagens.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-magic me-2"></i>Criar Primeiro Personagem
                </a>
            </div>
        </div>
    @else
        {{-- Filtros e Pesquisa --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Buscar personagem por nome..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="systemFilter">
                            <option value="">Todos os sistemas</option>
                            @foreach($personagens->pluck('sistema.nome')->unique()->filter() as $sistema)
                                <option value="{{ $sistema }}">{{ $sistema }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="campaignFilter">
                            <option value="">Todas as campanhas</option>
                            <option value="solo">Solo</option>
                            @foreach($personagens->pluck('campanha.nome')->unique()->filter() as $campanha)
                                <option value="{{ $campanha }}">{{ $campanha }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de Personagens --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="characterGrid">
            @foreach ($personagens as $personagem)
                <div class="col" data-name="{{ strtolower($personagem->nome) }}" 
                     data-system="{{ $personagem->sistema?->nome ?? '' }}" 
                     data-campaign="{{ $personagem->campanha?->nome ?? 'solo' }}">
                    <div class="card character-card shadow-sm h-100">
                        <div class="card-body">
                            {{-- Cabeçalho do Card --}}
                            <div class="d-flex align-items-start mb-3">
                                <img src="{{ $personagem->image_url ?? asset('storage/default/avatar.png') }}" 
                                     alt="{{ $personagem->nome }}" 
                                     class="character-avatar rounded-circle me-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title mb-1">{{ $personagem->nome }}</h5>
                                        <span class="badge bg-primary fs-6">{{ $personagem->nivel ?? 1 }}</span>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $personagem->classe_raca ?? 'Aventureiro' }}
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Informações do Personagem --}}
                            <div class="mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-dice-d20 text-primary me-2"></i>
                                            <small>
                                                <strong>Sistema:</strong><br>
                                                <span class="badge bg-secondary status-badge">
                                                    {{ $personagem->sistema?->nome ?? 'N/A' }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marked-alt text-success me-2"></i>
                                            <small>
                                                <strong>Campanha:</strong><br>
                                                {{ $personagem->campanha?->nome ?? 'Solo' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Status e Atributos Rápidos --}}
                            @if($personagem->vida_atual || $personagem->vida_maxima)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-heart text-danger me-1"></i> Vida
                                </small>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $vidaPercent = $personagem->vida_maxima ? 
                                            ($personagem->vida_atual / $personagem->vida_maxima) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-danger" 
                                         role="progressbar" 
                                         style="width: {{ $vidaPercent }}%"
                                         aria-valuenow="{{ $vidaPercent }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $personagem->vida_atual ?? 0 }}/{{ $personagem->vida_maxima ?? 0 }}
                                </small>
                            </div>
                            @endif
                            
                            {{-- Ações --}}
                            <div class="d-flex justify-content-between pt-3 border-top">
                                <a href="{{ route('personagens.show', $personagem) }}" 
                                   class="btn btn-outline-info btn-sm action-btn" 
                                   title="Visualizar Ficha">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('personagens.simpleEdit', $personagem) }}" 
                                   class="btn btn-outline-warning btn-sm action-btn" 
                                   title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                
                                <a href="#" 
                                   class="btn btn-outline-success btn-sm action-btn" 
                                   title="Ficha Completa">
                                    <i class="fas fa-scroll"></i>
                                </a>
                                
                                <button class="btn btn-outline-danger btn-sm action-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal-{{ $personagem->id }}" 
                                        title="Deletar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm action-btn" 
                                            type="button" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false"
                                            title="Mais opções">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-copy me-2"></i>Duplicar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" 
                                               href="#" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#deleteModal-{{ $personagem->id }}">
                                                <i class="fas fa-trash me-2"></i>Excluir
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Rodapé do Card --}}
                        <div class="card-footer bg-transparent border-top-0 py-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Criado em {{ $personagem->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Modal de Confirmação de Exclusão --}}
                <div class="modal fade" id="deleteModal-{{ $personagem->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Excluir Personagem
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="mb-4">
                                    <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                                    <h5>Tem certeza que deseja excluir?</h5>
                                    <p class="mb-0">
                                        <strong>"{{ $personagem->nome }}"</strong> será permanentemente removido.
                                    </p>
                                    <small class="text-muted">Esta ação não pode ser desfeita.</small>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </button>
                                <form action="{{ route('personagens.destroy', $personagem) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const systemFilter = document.getElementById('systemFilter');
    const campaignFilter = document.getElementById('campaignFilter');
    const characterCards = document.querySelectorAll('#characterGrid .col');
    
    function filterCharacters() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedSystem = systemFilter.value.toLowerCase();
        const selectedCampaign = campaignFilter.value.toLowerCase();
        
        characterCards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const system = card.dataset.system.toLowerCase();
            const campaign = card.dataset.campaign.toLowerCase();
            
            const matchesSearch = name.includes(searchTerm);
            const matchesSystem = !selectedSystem || system.includes(selectedSystem);
            const matchesCampaign = !selectedCampaign || 
                (selectedCampaign === 'solo' ? campaign === 'solo' : campaign.includes(selectedCampaign));
            
            if (matchesSearch && matchesSystem && matchesCampaign) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterCharacters);
    systemFilter.addEventListener('change', filterCharacters);
    campaignFilter.addEventListener('change', filterCharacters);
});
</script>
@endpush