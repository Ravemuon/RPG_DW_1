@extends('layouts.app')

@section('title', 'Editar: ' . $personagem->nome)

@section('content')
<div class="container">
    <div class="row">
        
        {{-- COLUNA LATERAL DE DADOS GERAIS --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Personagem: {{ $personagem->nome }}</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $personagem->imagem_url ?? asset('img/default_avatar.png') }}" 
                         alt="Avatar" 
                         class="img-fluid rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <p class="mb-1"><strong>Nível:</strong> {{ $personagem->nivel }}</p>
                    <p class="mb-1"><strong>Raça:</strong> {{ $personagem->raca->nome ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Classe:</strong> {{ $personagem->classe->nome ?? 'N/A' }}</p>
                    
                    <a href="{{ route('personagens.show', $personagem) }}" class="btn btn-sm btn-outline-primary mt-3">
                        <i class="fas fa-eye"></i> Ver Ficha Final
                    </a>
                </div>
            </div>
            
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        {{-- COLUNA PRINCIPAL: LISTA DE PASSOS --}}
        <div class="col-md-8">
            <div class="card shadow border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Ajustar Personagem ({{ $personagem->nome }})</h4>
                    <p class="mb-0 small">Selecione o passo que deseja modificar.</p>
                </div>
                <div class="card-body">
                    
                    {{-- Estrutura de Lista de Passos --}}
                    <div class="list-group">
                        
                        {{-- Passo 1 --}}
                        <a href="{{ route('personagens.editStep1', $personagem) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-id-card me-2"></i> 
                            **1. Dados Básicos** <small class="text-muted">(Nome, Campanha, Sistema, Nível, Imagem)</small>
                        </a>

                        {{-- Passo 2 --}}
                        <a href="{{ route('personagens.editStep2', $personagem) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-mask me-2"></i> 
                            **2. Raça, Classe e Origem** <small class="text-muted">(Dados essenciais de cálculo)</small>
                        </a>

                        {{-- Passo 3 --}}
                        <a href="{{ route('personagens.editStep3', $personagem) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-dice-d20 me-2"></i> 
                            **3. Atributos** <small class="text-muted">(Pontuações de Força, Destreza, etc.)</small>
                        </a>

                        {{-- Passo 4 --}}
                        <a href="{{ route('personagens.editStep4', $personagem) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-heartbeat me-2"></i> 
                            **4. Vida e Recursos** <small class="text-muted">(Pontos de vida, Sanidade, Sorte)</small>
                        </a>

                        {{-- Passo 5 --}}
                        <a href="{{ route('personagens.editStep5', $personagem) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-cogs me-2"></i> 
                            **5. Perícias, Inventário e Equipamento** <small class="text-muted">(Habilidades e Itens de posse)</small>
                        </a>
                        
                    </div>
                    
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('personagens.show', $personagem) }}" class="btn btn-success btn-lg">
                    <i class="fas fa-check"></i> Finalizar Edição e Ver Ficha
                </a>
            </div>
        </div>
    </div>
</div>
@endsection