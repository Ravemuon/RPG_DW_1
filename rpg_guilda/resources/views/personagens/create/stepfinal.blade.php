@extends('layouts.app')

@section('title', $personagem->nome . ' - Ficha de Personagem')

@section('content')
@php
    // Definições de variáveis (mantendo a lógica original)
    $sistema = $personagem->sistema;
    $classe = $personagem->classe;
    $raca = $personagem->raca;
    $origem = $personagem->origem;
    $campanha = $personagem->campanha;

    $atributos = $personagem->atributos ?? [];
    $modificadores = $dadosCalculados['modificadores'] ?? [];
    $pericias = $dadosCalculados['pericias'] ?? [];
    $bonusProficiencia = $dadosCalculados['bonus_proficiencia'] ?? 2;

    // Novos dados assumidos para a ficha (pode precisar de cálculo real na sua controller)
    $ca = $personagem->ca ?? 10 + ($modificadores['destreza'] ?? 0);
    $iniciativa = $dadosCalculados['iniciativa'] ?? ($modificadores['destreza'] ?? 0);
    $deslocamento = $personagem->deslocamento ?? '9m (30ft)';
    $salvaguardas = $dadosCalculados['salvaguardas'] ?? ['forca' => false, 'destreza' => true, 'constituicao' => false, 'inteligencia' => false, 'sabedoria' => true, 'carisma' => false];
    $caracteristicas = $personagem->caracteristicas ?? [
        ['nome' => 'Visão no Escuro', 'descricao' => 'Você consegue ver na penumbra em um raio de 18 metros.'],
        ['nome' => 'Fúria da Classe', 'descricao' => 'Você pode entrar em fúria como uma ação bônus.'],
    ];
@endphp

<div class="container my-5">
    <!-- Header e Dados Vitais -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body text-center py-5 bg-gradient-primary text-white rounded">
            <h1 class="display-4 fw-bold mb-2">{{ $personagem->nome }}</h1>
            <p class="lead mb-3">
                {{ $raca->nome }} | {{ $classe->nome }} (Nível {{ $personagem->nivel }})
                @if($origem)
                    • {{ $origem->nome }}
                @endif
            </p>
            <div class="row justify-content-center g-2">
                <div class="col-auto"><span class="badge bg-light text-dark fs-6 shadow-sm">Sistema: {{ $sistema->nome }}</span></div>
                <div class="col-auto"><span class="badge bg-light text-dark fs-6 shadow-sm">Bônus Proficiência: +{{ $bonusProficiencia }}</span></div>
                <div class="col-auto"><span class="badge bg-light text-dark fs-6 shadow-sm">XP: {{ number_format($personagem->xp) }}</span></div>
            </div>
        </div>
    </div>


    <div class="row g-4">
        <!-- COLUNA PRINCIPAL (CA, Atributos, Perícias) -->
        <div class="col-lg-8">

            <!-- Estatísticas de Combate e Movimento -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">🔥 Combate e Movimento</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center align-items-center">
                        <!-- CA -->
                        <div class="col-4">
                            <div class="border rounded-circle p-3 d-inline-block bg-light shadow-sm" style="width: 100px; height: 100px;">
                                <div class="small text-muted">CA</div>
                                <div class="h2 fw-bold text-danger">{{ $ca }}</div>
                            </div>
                            <div class="small mt-1 text-danger fw-bold">Classe de Armadura</div>
                        </div>
                        <!-- Iniciativa -->
                        <div class="col-4">
                            <div class="border rounded-circle p-3 d-inline-block bg-light shadow-sm" style="width: 100px; height: 100px;">
                                <div class="small text-muted">Inic.</div>
                                <div class="h2 fw-bold text-success">{{ $iniciativa >= 0 ? '+' : '' }}{{ $iniciativa }}</div>
                            </div>
                            <div class="small mt-1 text-success fw-bold">Iniciativa</div>
                        </div>
                        <!-- Deslocamento -->
                        <div class="col-4">
                            <div class="border rounded-circle p-3 d-inline-block bg-light shadow-sm" style="width: 100px; height: 100px;">
                                <div class="small text-muted">Desloc.</div>
                                <div class="h5 fw-bold text-dark mt-2">{{ $deslocamento }}</div>
                            </div>
                            <div class="small mt-1 text-dark fw-bold">Deslocamento</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atributos -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">📊 Atributos</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        @foreach($atributos as $chave => $valor)
                        @php
                            $modificador = $modificadores[$chave] ?? 0;
                            $modDisplay = $modificador >= 0 ? "+{$modificador}" : $modificador;
                            $label = $sistema->atributos[$chave] ?? ucfirst($chave);
                            $colorClass = match($chave) {
                                'forca' => 'bg-danger-subtle',
                                'destreza' => 'bg-warning-subtle',
                                'constituicao' => 'bg-success-subtle',
                                'inteligencia' => 'bg-info-subtle',
                                'sabedoria' => 'bg-primary-subtle',
                                'carisma' => 'bg-secondary-subtle',
                                default => 'bg-light'
                            };
                        @endphp
                        <div class="col-6 col-md-4 col-lg-2">
                            <div class="border rounded p-3 {{ $colorClass }} h-100 shadow-sm">
                                <div class="small text-muted mb-1">{{ $label }}</div>
                                <div class="h4 fw-bold text-dark mb-1">{{ $valor }}</div>
                                <div class="badge {{ $modificador >= 0 ? 'bg-primary' : 'bg-secondary' }}">{{ $modDisplay }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Perícias e Salvaguardas -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🎯 Perícias & Salvaguardas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Salvaguardas -->
                        <div class="col-md-4 mb-3">
                            <h6 class="border-bottom pb-2 mb-3">Testes de Resistência</h6>
                            @foreach($salvaguardas as $atributoChave => $proficiente)
                                @php
                                    $modAtributo = $modificadores[$atributoChave] ?? 0;
                                    $bonusTotal = $modAtributo + ($proficiente ? $bonusProficiencia : 0);
                                    $bonusDisplay = $bonusTotal >= 0 ? '+' . $bonusTotal : $bonusTotal;
                                    $label = $sistema->atributos[$atributoChave] ?? ucfirst($atributoChave);
                                @endphp
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="small {{ $proficiente ? 'fw-bold text-primary' : 'text-dark' }}">
                                        {!! $proficiente ? '<i class="fas fa-check-circle me-1"></i>' : '<i class="far fa-circle me-1"></i>' !!}
                                        {{ $label }}
                                    </span>
                                    <span class="badge {{ $proficiente ? 'bg-primary' : 'bg-secondary' }}">{{ $bonusDisplay }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Perícias -->
                        <div class="col-md-8">
                            <h6 class="border-bottom pb-2 mb-3">Perícias (Baseado em Atributo)</h6>
                            <div class="row g-2">
                                @foreach($pericias as $pericia => $dados)
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded {{ $dados['proficiente'] ? 'bg-success-subtle border border-success' : 'bg-light border border-secondary' }}">
                                        <div>
                                            <span class="small {{ $dados['proficiente'] ? 'fw-bold text-success' : 'text-dark' }}">
                                                {!! $dados['proficiente'] ? '<i class="fas fa-star me-1"></i>' : '<i class="far fa-star me-1"></i>' !!}
                                                {{ $pericia }}
                                            </span>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                ({{ $sistema->atributos[$dados['atributo']] ?? $dados['atributo'] }})
                                            </div>
                                        </div>
                                        <div class="fw-bold {{ $dados['proficiente'] ? 'text-success' : 'text-dark' }}">
                                            {{ $dados['bonus_display'] }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- COLUNA DE DETALHES (Vida, Infos, Features, Ações) -->
        <div class="col-lg-4">
            <!-- Barra de Vida/Recursos -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">❤️ Pontos de Vida</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>PV Atual:</strong>
                        <div class="progress mb-1" style="height: 25px;">
                            @php
                                $pvPorcentagem = ($personagem->vida / $personagem->vida_maxima) * 100;
                            @endphp
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pvPorcentagem }}%">
                                <strong>{{ $personagem->vida }}/{{ $personagem->vida_maxima }}</strong>
                            </div>
                        </div>
                    </div>

                    @if($personagem->sistema->usa_sanidade)
                    <div class="mb-3">
                        <strong>🧠 Sanidade:</strong>
                        <div class="progress mb-1" style="height: 25px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $personagem->sanidade }}%">
                                <strong>{{ $personagem->sanidade }}/100</strong>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($personagem->sistema->usa_sorte)
                    <div class="mb-3">
                        <strong>🍀 Sorte:</strong>
                        <div class="progress mb-1" style="height: 25px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $personagem->sorte }}%">
                                <strong>{{ $personagem->sorte }}/100</strong>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Características de Classe/Raça -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">✨ Características & Traços</h5>
                </div>
                <div class="card-body p-2">
                    <div class="list-group list-group-flush">
                        @forelse($caracteristicas as $feature)
                        <div class="list-group-item">
                            <h6 class="mb-1 text-info fw-bold">{{ $feature['nome'] }}</h6>
                            <p class="small text-muted mb-0">{{ $feature['descricao'] }}</p>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted">Nenhuma característica especial.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Equipamento Principal -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🎒 Equipamento Inicial</h5>
                </div>
                <div class="card-body">
                    @if($personagem->inventario && !empty($personagem->inventario['equipamento_inicial']))
                    <ul class="list-unstyled mb-0 small">
                        @foreach($personagem->inventario['equipamento_inicial'] as $item)
                        <li class="mb-1 d-flex align-items-center">
                            <i class="fas fa-hand-point-right me-2 text-warning"></i>{{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted mb-0 small">Nenhum equipamento inicial definido.</p>
                    @endif
                </div>
            </div>

            <!-- Ações Finais -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">🚀 Ações Finais</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-dice-d20 me-2"></i>Começar a Jogar!
                        </a>
                        <a href="{{ route('personagens.edit', $personagem->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-2"></i>Revisar Criação
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Atributos (Mantido) -->
    <div class="card shadow-lg border-0 mt-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">📈 Perfil de Atributos (Radar)</h5>
        </div>
        <div class="card-body">
            <div style="height: 450px;">
                <canvas id="attributeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Garantindo que 'Chart' está carregado antes de usar
    if (typeof Chart === 'undefined') {
        console.error("Chart.js não está carregado. Verifique a URL do script.");
        return;
    }

    // Assumindo que as variáveis JSON são injetadas corretamente
    const atributos = @json($atributos);
    const sistemaAtributos = @json($sistema->atributos);

    // Preparar dados para o gráfico
    const labels = Object.keys(atributos).map(key => sistemaAtributos[key] || key);
    const data = Object.values(atributos);

    // Encontrando o valor máximo e definindo um máximo um pouco maior para o radar
    const maxValue = Math.max(...data, 10); // Garante que o maximo não é menor que 10
    const chartMax = Math.ceil(maxValue / 2) * 2; // Arredonda para o próximo par (ex: 17 -> 18)

    // Criar gráfico radar
    const ctx = document.getElementById('attributeChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Valores de Atributo',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.4)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
                line: {
                    tension: 0.1
                }
            },
            scales: {
                r: {
                    angleLines: { color: 'rgba(0, 0, 0, 0.1)' },
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                    pointLabels: {
                        font: { size: 14, weight: 'bold' },
                        color: '#333'
                    },
                    beginAtZero: true,
                    // Garante que o gráfico se ajuste dinamicamente
                    min: 0,
                    max: chartMax,
                    ticks: {
                        stepSize: 2,
                        backdropColor: 'rgba(255, 255, 255, 0.8)',
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.r;
                            const modifier = Math.floor((value - 10) / 2);
                            return `${context.label}: ${value} (Mod: ${modifier >= 0 ? '+' : ''}${modifier})`;
                        }
                    }
                }
            }
        }
    });
});
</script>

<style>
/* Estilos personalizados */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Roxo/Azul */
}
.bg-danger-subtle { background-color: #f8d7da; }
.bg-warning-subtle { background-color: #fff3cd; }
.bg-success-subtle { background-color: #d1e7dd; }
.bg-info-subtle { background-color: #cff4fc; }
.bg-primary-subtle { background-color: #cfe2ff; }
.bg-secondary-subtle { background-color: #e2e3e5; }
</style>
@endsection
