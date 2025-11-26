@extends('layouts.app')

@section('title', 'Criação de Personagem - Overview')

@section('content')
@php
    $sistema = $personagem->sistema;
@endphp

<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Criação de Personagem: {{ $personagem->nome }}</h1>
                    <p class="mb-0">Sistema: {{ $sistema->nome }} | Campanha: {{ $personagem->campanha->nome }}</p>
                </div>
                <div>
                    <span class="badge bg-light text-primary fs-6">Nível 1</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Progresso -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Progresso da Criação</h5>
                            <div class="progress mb-3" style="height: 25px;">
                                <div class="progress-bar bg-success" style="width: {{ $progresso['porcentagem'] }}%">
                                    {{ number_format($progresso['porcentagem'], 0) }}%
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-md-2 col-6 mb-2">
                                    <div class="card {{ $progresso['basico'] ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body py-2">
                                            <i class="fas fa-user {{ $progresso['basico'] ? 'text-success' : 'text-secondary' }} fa-2x mb-2"></i>
                                            <h6>Básico</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <div class="card {{ $progresso['raca_classe'] ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body py-2">
                                            <i class="fas fa-dragon {{ $progresso['raca_classe'] ? 'text-success' : 'text-secondary' }} fa-2x mb-2"></i>
                                            <h6>Raça & Classe</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <div class="card {{ $progresso['atributos'] ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body py-2">
                                            <i class="fas fa-chart-bar {{ $progresso['atributos'] ? 'text-success' : 'text-secondary' }} fa-2x mb-2"></i>
                                            <h6>Atributos</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <div class="card {{ $progresso['vida_equipamento'] ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body py-2">
                                            <i class="fas fa-heart {{ $progresso['vida_equipamento'] ? 'text-success' : 'text-secondary' }} fa-2x mb-2"></i>
                                            <h6>Vida & Equip.</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <div class="card {{ $progresso['pericias'] ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body py-2">
                                            <i class="fas fa-star {{ $progresso['pericias'] ? 'text-success' : 'text-secondary' }} fa-2x mb-2"></i>
                                            <h6>Perícias</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2">
                                    <div class="card {{ $progresso['completo'] ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body py-2">
                                            <i class="fas fa-flag {{ $progresso['completo'] ? 'text-success' : 'text-secondary' }} fa-2x mb-2"></i>
                                            <h6>Finalizar</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de Navegação -->
            <div class="row g-4">
                <!-- Dados Básicos -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center {{ $progresso['basico'] ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                            <h5 class="mb-0">1. Dados Básicos</h5>
                            @if($progresso['basico'])
                                <span class="badge bg-light text-success">✓ Completo</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="card-text">Defina o nome, descrição, história e personalidade do seu personagem.</p>
                            <div class="mb-3">
                                <strong>Nome:</strong> {{ $personagem->nome }}<br>
                                <strong>Descrição:</strong> {{ Str::limit($personagem->descricao ?? 'Não definida', 50) }}<br>
                                <strong>História:</strong> {{ Str::limit($personagem->historia ?? 'Não definida', 50) }}
                            </div>
                            <a href="{{ route('personagens.edit', $personagem->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar Básico
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Raça, Classe e Origem -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center {{ $progresso['raca_classe'] ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                            <h5 class="mb-0">2. Raça, Classe & Origem</h5>
                            @if($progresso['raca_classe'])
                                <span class="badge bg-light text-success">✓ Completo</span>
                            @else
                                <span class="badge bg-light text-warning">Pendente</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="card-text">Escolha a raça, classe e origem do seu personagem.</p>
                            <div class="mb-3">
                                <strong>Raça:</strong> {{ $personagem->raca->nome ?? 'Não definida' }}<br>
                                <strong>Classe:</strong> {{ $personagem->classe->nome ?? 'Não definida' }}<br>
                                <strong>Origem:</strong> {{ $personagem->origem->nome ?? 'Não definida' }}
                            </div>
                            <a href="{{ route('personagens.step2', $personagem->id) }}" class="btn btn-outline-warning">
                                <i class="fas fa-dragon me-2"></i>
                                {{ $progresso['raca_classe'] ? 'Alterar' : 'Definir' }} Raça & Classe
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Atributos -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center {{ $progresso['atributos'] ? 'bg-success text-white' : 'bg-info text-white' }}">
                            <h5 class="mb-0">3. Atributos</h5>
                            @if($progresso['atributos'])
                                <span class="badge bg-light text-success">✓ Completo</span>
                            @else
                                <span class="badge bg-light text-info">Pendente</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="card-text">Distribua os pontos de atributo ou sorteie valores aleatórios.</p>
                            @if($progresso['atributos'])
                                <div class="mb-3">
                                    @foreach($personagem->atributos as $atributo => $valor)
                                        <span class="badge bg-primary me-1">
                                            {{ $sistema->atributos[$atributo] ?? $atributo }}: {{ $valor }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ route('personagens.step3', $personagem->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-chart-bar me-2"></i>
                                {{ $progresso['atributos'] ? 'Alterar' : 'Definir' }} Atributos
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vida e Equipamento -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center {{ $progresso['vida_equipamento'] ? 'bg-success text-white' : 'bg-danger text-white' }}">
                            <h5 class="mb-0">4. Vida & Equipamento</h5>
                            @if($progresso['vida_equipamento'])
                                <span class="badge bg-light text-success">✓ Completo</span>
                            @else
                                <span class="badge bg-light text-danger">Pendente</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="card-text">Determine os pontos de vida e escolha o equipamento inicial.</p>
                            @if($progresso['vida_equipamento'])
                                <div class="mb-3">
                                    <strong>Vida:</strong> {{ $personagem->vida }}<br>
                                    <strong>Equipamento:</strong> {{ count($personagem->inventario['equipamento_inicial'] ?? []) }} itens
                                </div>
                            @endif
                            <a href="{{ route('personagens.step4', $personagem->id) }}" class="btn btn-outline-danger">
                                <i class="fas fa-heart me-2"></i>
                                {{ $progresso['vida_equipamento'] ? 'Alterar' : 'Definir' }} Vida & Equip.
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Perícias -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center {{ $progresso['pericias'] ? 'bg-success text-white' : 'bg-purple text-white' }}">
                            <h5 class="mb-0">5. Perícias</h5>
                            @if($progresso['pericias'])
                                <span class="badge bg-light text-success">✓ Completo</span>
                            @else
                                <span class="badge bg-light text-purple">Pendente</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="card-text">Escolha as perícias em que seu personagem é proficiente.</p>
                            @if($progresso['pericias'])
                                <div class="mb-3">
                                    <strong>Perícias:</strong> {{ count($personagem->pericias ?? []) }} proficientes
                                </div>
                            @endif
                            <a href="{{ route('personagens.step5', $personagem->id) }}" class="btn btn-outline-purple">
                                <i class="fas fa-star me-2"></i>
                                {{ $progresso['pericias'] ? 'Alterar' : 'Definir' }} Perícias
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Finalizar -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center {{ $progresso['completo'] ? 'bg-success text-white' : 'bg-dark text-white' }}">
                            <h5 class="mb-0">6. Finalizar</h5>
                            @if($progresso['completo'])
                                <span class="badge bg-light text-success">Pronto!</span>
                            @else
                                <span class="badge bg-light text-dark">Indisponível</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="card-text">Revise e finalize a criação do seu personagem.</p>
                            @if($progresso['completo'])
                                <div class="mb-3">
                                    <strong>Status:</strong> Pronto para aventura!<br>
                                    <strong>Progresso:</strong> 100% completo
                                </div>
                                <a href="{{ route('personagens.final', $personagem->id) }}" class="btn btn-success">
                                    <i class="fas fa-flag me-2"></i>Finalizar Personagem
                                </a>
                            @else
                                <div class="mb-3">
                                    <strong>Status:</strong> Complete todas as etapas<br>
                                    <strong>Progresso:</strong> {{ number_format($progresso['porcentagem'], 0) }}%
                                </div>
                                <button class="btn btn-dark" disabled>
                                    <i class="fas fa-lock me-2"></i>Complete as Etapas
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            @if($progresso['completo'])
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Personagem Pronto!</h5>
                            <p class="card-text">Seu personagem está completo e pronto para aventuras!</p>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <a href="{{ route('personagens.final', $personagem->id) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-tachometer-alt me-2"></i>Ver Dashboard
                                </a>
                                <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-scroll me-2"></i>Ver Ficha Completa
                                </a>
                                <a href="{{ route('campanhas.show', $personagem->campanha_id) }}" class="btn btn-info btn-lg">
                                    <i class="fas fa-users me-2"></i>Ir para Campanha
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}
.btn-outline-purple {
    color: #6f42c1;
    border-color: #6f42c1;
}
.btn-outline-purple:hover {
    color: #fff;
    background-color: #6f42c1;
    border-color: #6f42c1;
}
</style>
@endsection
