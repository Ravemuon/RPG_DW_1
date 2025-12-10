@extends('layouts.app')

@section('title', 'Gerenciar Perícias - ' . $personagem->nome)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('personagens.index') }}">Personagens</a></li>
            <li class="breadcrumb-item"><a href="{{ route('personagens.show', $personagem) }}">{{ $personagem->nome }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gerenciar Perícias</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">
        <i class="fas fa-tasks me-2"></i>Gerenciar Perícias de **{{ $personagem->nome }}**
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações Essenciais</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Bônus de Proficiência (BP):</strong> <span class="badge bg-success fs-5">+{{ $personagem->bonus_proficiencia }}</span></p>
                    <p class="mb-0 text-muted">O Bônus Total é calculado como: (Modificador de Atributo) + (BP se Proficiente) + (Bônus Customizado).</p>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Adicionar Perícia</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('personagens.adicionar-pericia', $personagem) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <select name="pericia_id" class="form-select" required>
                                <option value="">Selecione a Perícia</option>
                                @foreach($periciasNaoAdicionadas as $pericia)
                                    <option value="{{ $pericia->id }}">
                                        {{ $pericia->nome }} ({{ strtoupper($personagem->sistema->atributos[$pericia->atributo_relacionado] ?? $pericia->atributo_relacionado) }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-dark" title="Adicionar Perícia">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Lista de Perícias do Personagem</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($personagemPericias as $personagemPericia)
                        <li class="list-group-item">
                            <form action="{{ route('personagens.atualizar-pericia', [$personagem, $personagemPericia->pericia_id]) }}" 
                                  method="POST" 
                                  class="d-flex align-items-center justify-content-between form-pericia-update">
                                @csrf
                                @method('PUT')
                                
                                <div class="pericia-info me-3" style="min-width: 200px;">
                                    <h6 class="mb-0 fw-bold">{{ $personagemPericia->pericia->nome }}</h6>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-fist-raised me-1"></i>
                                        {{ $personagem->sistema->atributos[$personagemPericia->pericia->atributo_relacionado] ?? $personagemPericia->pericia->atributo_relacionado }} 
                                        (Mod: {{ $personagemPericia->modificadorAtributo }})
                                    </small>
                                </div>

                                <div class="pericia-controles d-flex align-items-center flex-grow-1">
                                    
                                    <div class="form-check form-switch me-3" data-bs-toggle="tooltip" title="Proficiência (Bônus de Proficiência +{{ $personagem->bonus_proficiencia }})">
                                        <input class="form-check-input" type="checkbox" id="proficiencia-{{ $personagemPericia->pericia_id }}" 
                                               name="proficiente" 
                                               value="1" 
                                               {{ $personagemPericia->proficiente ? 'checked' : '' }} 
                                               onchange="this.form.submit()">
                                        <label class="form-check-label" for="proficiencia-{{ $personagemPericia->pericia_id }}">
                                            Prof.
                                        </label>
                                    </div>
                                    
                                    <div class="input-group input-group-sm me-3" style="max-width: 150px;">
                                        <span class="input-group-text">Bônus Adicional</span>
                                        <input type="number" name="valor_customizado" class="form-control text-center" 
                                               value="{{ $personagemPericia->valor_customizado }}" 
                                               min="-99" max="99" 
                                               onchange="this.form.submit()"
                                               style="width: 50px;">
                                    </div>
                                    
                                    <div class="text-end ms-auto" style="min-width: 80px;">
                                        <strong class="fs-4 
                                            {{ $personagemPericia->calcularBonus() >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $personagemPericia->calcularBonus() >= 0 ? '+' : '' }}{{ $personagemPericia->calcularBonus() }}
                                        </strong>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-outline-danger btn-sm ms-3" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#removerPericiaModal-{{ $personagemPericia->pericia_id }}"
                                        title="Remover Perícia do Personagem">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-tasks fa-2x mb-3"></i>
                            <p class="mb-0">Este personagem não tem perícias configuradas. Use o formulário ao lado para adicionar!</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@foreach($personagemPericias as $personagemPericia)
<div class="modal fade" id="removerPericiaModal-{{ $personagemPericia->pericia_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Remoção</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja remover a perícia **{{ $personagemPericia->pericia->nome }}** do personagem?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('personagens.remover-pericia', [$personagem, $personagemPericia->pericia_id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remover</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    // Inicializar Tooltips do Bootstrap
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush