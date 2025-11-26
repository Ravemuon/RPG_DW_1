@extends('layouts.app')

@section('title', $personagem->nome . ' - Dashboard')

@section('content')
@php
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
        <!-- Coluna Esquerda - Atributos e Perícias -->
        <div class="col-lg-8">
            <!-- Atributos -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">📊 Atributos</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @foreach($atributos as $chave => $valor)
                        @php
                            $modificador = $modificadores[$chave] ?? 0;
                            $modDisplay = $modificador >= 0 ? "+{$modificador}" : $modificador;
                            $label = $sistema->atributos[$chave] ?? ucfirst($chave);
                        @endphp
                        <div class="col-6 col-md-4 col-lg-2 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <div class="small text-muted mb-1">{{ $label }}</div>
                                <div class="h4 fw-bold text-primary mb-1">{{ $valor }}</div>
                                <div class="small text-secondary">{{ $modDisplay }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Perícias -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🎯 Perícias</h5>
                    <small class="opacity-75">Bônus de Proficiência: +{{ $bonusProficiencia }}</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($pericias as $pericia => $dados)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 {{ $dados['proficiente'] ? 'border-success' : 'border-light' }}">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="{{ $dados['proficiente'] ? 'fw-bold text-success' : '' }}">
                                                {{ $pericia }}
                                            </span>
                                            <div class="small text-muted">
                                                {{ $sistema->atributos[$dados['atributo']] ?? $dados['atributo'] }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold {{ $dados['proficiente'] ? 'text-success' : 'text-dark' }}">
                                                {{ $dados['bonus_display'] }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $dados['modificador'] >= 0 ? '+' : '' }}{{ $dados['modificador'] }} + {{ $dados['proficiente'] ? $bonusProficiencia : 0 }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($dados['proficiente'])
                                    <div class="text-center mt-1">
                                        <small class="badge bg-success">Proficiente</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna Direita - Informações -->
        <div class="col-lg-4">
            <!-- Informações Básicas -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Informações</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>🧬 Raça:</strong>
                        <div>{{ $raca->nome }}</div>
                        @if($raca->descricao)
                        <small class="text-muted">{{ Str::limit($raca->descricao, 100) }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>⚔️ Classe:</strong>
                        <div>{{ $classe->nome }}</div>
                        <small class="text-muted">
                            Dado de Vida: {{ strtoupper($classe->dado_vida) }} |
                            Magia: {{ $classe->usa_magia ? 'Sim' : 'Não' }}
                        </small>
                    </div>

                    @if($origem)
                    <div class="mb-3">
                        <strong>📖 Origem:</strong>
                        <div>{{ $origem->nome }}</div>
                        @if($origem->descricao)
                        <small class="text-muted">{{ Str::limit($origem->descricao, 100) }}</small>
                        @endif
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>❤️ Pontos de Vida:</strong>
                        <div class="progress mb-1" style="height: 20px;">
                            <div class="progress-bar bg-danger" style="width: 100%">
                                <strong>{{ $personagem->vida }}/{{ $personagem->vida_maxima }}</strong>
                            </div>
                        </div>
                    </div>

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
                        <a href="{{ route('personagens.show', $personagem->id) }}"
                           class="btn btn-primary">
                            <i class="fas fa-scroll me-2"></i>Ver Ficha Completa
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

    <!-- Gráfico de Atributos -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">📈 Gráfico de Atributos</h5>
        </div>
        <div class="card-body">
            <div style="height: 400px;">
                <canvas id="attributeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const atributos = @json($atributos);
    const sistemaAtributos = @json($sistema->atributos);

    // Preparar dados para o gráfico
    const labels = Object.keys(atributos).map(key => sistemaAtributos[key] || key);
    const data = Object.values(atributos);

    // Criar gráfico radar
    const ctx = document.getElementById('attributeChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Atributos',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 20,
                    ticks: {
                        stepSize: 2
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.r;
                            const modifier = Math.floor((value - 10) / 2);
                            return `Valor: ${value} (Mod: ${modifier >= 0 ? '+' : ''}${modifier})`;
                        }
                    }
                }
            }
        }
    });
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
