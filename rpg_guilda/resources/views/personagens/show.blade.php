@extends('layouts.app')

@section('title', $personagem->nome . ' - Ficha Completa')

@section('content')
@php
    // Mesmas variáveis do dashboard
    $sistema = $personagem->sistema;
    $classe = $personagem->classe;
    $raca = $personagem->raca;
    $origem = $personagem->origem;
    $campanha = $personagem->campanha;

    $atributos = $personagem->atributos ?? [];
    $modificadores = $dadosCalculados['modificadores'] ?? [];
    $pericias = $dadosCalculados['pericias'] ?? [];
    $bonusProficiencia = $dadosCalculados['bonus_proficiencia'] ?? 2;
@endphp

<div class="container my-5">
    <!-- Header -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body text-center py-5 bg-gradient-primary text-white rounded">
            <h1 class="display-4 fw-bold mb-2">{{ $personagem->nome }}</h1>
            <p class="lead mb-3">
                {{ $raca->nome }} {{ $classe->nome }}
                @if($origem)
                    • {{ $origem->nome }}
                @endif
            </p>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <span class="badge bg-light text-dark fs-6">Nível {{ $personagem->nivel }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-dark fs-6">{{ $sistema->nome }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-dark fs-6">XP: {{ $personagem->xp }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-dark fs-6">PV: {{ $personagem->vida }}/{{ $personagem->vida_maxima }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Coluna Esquerda -->
        <div class="col-lg-8">
            <!-- Atributos e Modificadores -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">📊 Atributos e Modificadores</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Atributo</th>
                                    <th>Valor</th>
                                    <th>Modificador</th>
                                    <th>Mod. Temp</th>
                                    <th>Valor Temp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atributos as $chave => $valor)
                                @php
                                    $modificador = $modificadores[$chave] ?? 0;
                                    $modDisplay = $modificador >= 0 ? "+{$modificador}" : $modificador;
                                    $label = $sistema->atributos[$chave] ?? ucfirst($chave);
                                @endphp
                                <tr>
                                    <td><strong>{{ $label }}</strong></td>
                                    <td class="text-center h5 fw-bold text-primary">{{ $valor }}</td>
                                    <td class="text-center h5">{{ $modDisplay }}</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Perícias -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🎯 Perícias</h5>
                    <small class="opacity-75">Bônus de Proficiência: +{{ $bonusProficiencia }}</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Perícia</th>
                                    <th>Atributo</th>
                                    <th>Proficiente</th>
                                    <th>Bônus</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pericias as $pericia => $dados)
                                <tr>
                                    <td><strong>{{ $pericia }}</strong></td>
                                    <td>{{ $sistema->atributos[$dados['atributo']] ?? $dados['atributo'] }}</td>
                                    <td class="text-center">
                                        @if($dados['proficiente'])
                                            <span class="badge bg-success">Sim</span>
                                        @else
                                            <span class="badge bg-secondary">Não</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $dados['modificador'] >= 0 ? '+' : '' }}{{ $dados['modificador'] }}
                                        @if($dados['proficiente'])
                                            + {{ $bonusProficiencia }}
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold {{ $dados['proficiente'] ? 'text-success' : 'text-dark' }}">
                                        {{ $dados['bonus_display'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Descrição e História -->
            @if($personagem->descricao || $personagem->historia || $personagem->personalidade)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📖 Descrição e História</h5>
                </div>
                <div class="card-body">
                    @if($personagem->descricao)
                    <div class="mb-4">
                        <h6>Descrição</h6>
                        <p class="mb-0">{{ $personagem->descricao }}</p>
                    </div>
                    @endif

                    @if($personagem->personalidade)
                    <div class="mb-4">
                        <h6>Personalidade</h6>
                        <p class="mb-0">{{ $personagem->personalidade }}</p>
                    </div>
                    @endif

                    @if($personagem->historia)
                    <div>
                        <h6>História</h6>
                        <p class="mb-0">{{ $personagem->historia }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Coluna Direita -->
        <div class="col-lg-4">
            <!-- Informações Básicas -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Informações Básicas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nome:</strong></td>
                            <td>{{ $personagem->nome }}</td>
                        </tr>
                        <tr>
                            <td><strong>Raça:</strong></td>
                            <td>{{ $raca->nome }}</td>
                        </tr>
                        <tr>
                            <td><strong>Classe:</strong></td>
                            <td>{{ $classe->nome }}</td>
                        </tr>
                        @if($origem)
                        <tr>
                            <td><strong>Origem:</strong></td>
                            <td>{{ $origem->nome }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Nível:</strong></td>
                            <td>{{ $personagem->nivel }}</td>
                        </tr>
                        <tr>
                            <td><strong>Experiência:</strong></td>
                            <td>{{ $personagem->xp }}</td>
                        </tr>
                        <tr>
                            <td><strong>Campanha:</strong></td>
                            <td>{{ $campanha->nome }}</td>
                        </tr>
                        <tr>
                            <td><strong>Sistema:</strong></td>
                            <td>{{ $sistema->nome }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Pontos de Vida -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">❤️ Pontos de Vida</h5>
                </div>
                <div class="card-body text-center">
                    <div class="h1 fw-bold text-danger">{{ $personagem->vida }}/{{ $personagem->vida_maxima }}</div>
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-danger" style="width: {{ ($personagem->vida / $personagem->vida_maxima) * 100 }}%">
                            <strong>{{ $personagem->vida }} PV</strong>
                        </div>
                    </div>
                    <small class="text-muted">
                        Dado de Vida: {{ strtoupper($classe->dado_vida) }} |
                        Mod. Constituição: {{ $modificadores['constituicao'] >= 0 ? '+' : '' }}{{ $modificadores['constituicao'] }}
                    </small>
                </div>
            </div>

            <!-- Atributos Especiais -->
            @if($personagem->sistema->usa_sanidade || $personagem->sistema->usa_sorte)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">✨ Atributos Especiais</h5>
                </div>
                <div class="card-body">
                    @if($personagem->sistema->usa_sanidade)
                    <div class="mb-3">
                        <strong>🧠 Sanidade:</strong>
                        <div class="progress mb-1" style="height: 20px;">
                            <div class="progress-bar bg-info" style="width: {{ $personagem->sanidade }}%">
                                <strong>{{ $personagem->sanidade }}/100</strong>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($personagem->sistema->usa_sorte)
                    <div class="mb-3">
                        <strong>🍀 Sorte:</strong>
                        <div class="progress mb-1" style="height: 20px;">
                            <div class="progress-bar bg-warning" style="width: {{ $personagem->sorte }}%">
                                <strong>{{ $personagem->sorte }}/100</strong>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Equipamento -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🎒 Equipamento</h5>
                </div>
                <div class="card-body">
                    @if($personagem->inventario && !empty($personagem->inventario['equipamento_inicial']))
                    <ul class="list-unstyled mb-0">
                        @foreach($personagem->inventario['equipamento_inicial'] as $item)
                        <li class="mb-1">• {{ $item }}</li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted mb-0">Nenhum equipamento definido.</p>
                    @endif
                </div>
            </div>

            <!-- Ações -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">🚀 Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('personagens.final', $personagem->id) }}"
                           class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a href="{{ route('personagens.edit', $personagem->id) }}"
                           class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-2"></i>Editar Personagem
                        </a>
                        <a href="{{ route('campanhas.show', $campanha->id) }}"
                           class="btn btn-outline-info">
                            <i class="fas fa-users me-2"></i>Ir para Campanha
                        </a>
                        <a href="{{ route('personagens.index') }}"
                           class="btn btn-outline-success">
                            <i class="fas fa-list me-2"></i>Meus Personagens
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
