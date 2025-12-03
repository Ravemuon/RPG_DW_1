@extends('layouts.app')

@section('title', $personagem->nome . ' - Ficha Completa')

@section('content')
<<<<<<< HEAD

{{-- Mock de dados para demonstração, já que o modelo real é complexo --}}
@php
    // Definição do sistema (baseado no mock implícito nos outros arquivos)
    $sistema = $personagem->sistema ?? (object)['nome' => 'Sistema Desconhecido'];

    // Mock de Atributos, caso não estejam preenchidos (Exemplo D&D 5e)
    $atributos = $personagem->atributos ?? [
        'forca' => 15, 'destreza' => 14, 'constituicao' => 13,
        'inteligencia' => 10, 'sabedoria' => 12, 'carisma' => 8
    ];
    $modificadores = array_map(fn($val) => floor(($val - 10) / 2), $atributos);

    // Mock de Perícias
    $pericias = $personagem->pericias ?? [
        'Acrobacia' => ['atributo' => 'destreza', 'proficiente' => true],
        'Intimidação' => ['atributo' => 'carisma', 'proficiente' => true],
        'Percepção' => ['atributo' => 'sabedoria', 'proficiente' => false],
        'Furtividade' => ['atributo' => 'destreza', 'proficiente' => false],
    ];

    // Mock de Combate/Defesa
    $defesa = [
        'CA' => 10 + ($modificadores['destreza'] ?? 0) + 2, // Exemplo de armadura leve
        'Iniciativa' => $modificadores['destreza'] ?? 0,
        'Deslocamento' => '9m (30ft)',
        'Proficiência' => 2,
    ];
@endphp

<div class="container my-5">
    <div class="card shadow-xl border-0">

        {{-- Cabeçalho Principal --}}
        <div class="card-header bg-dark text-white p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="h2 mb-0 text-uppercase">{{ $personagem->nome }}</h1>
                    {{-- Adicionado: Linha de Raça e Classe com acesso seguro (CORREÇÃO DO ERRO) --}}
                    <p class="mb-1 text-info fw-bold">
                        <i class="fas fa-mask me-1"></i>
                        {{ $personagem->raca->nome ?? 'Raça Desconhecida' }} |
                        {{ $personagem->classe->nome ?? 'Classe Desconhecida' }}
                    </p>
                    {{-- Informações de Sistema e Campanha --}}
                    <p class="mb-0 text-secondary">
                        <i class="fas fa-book me-2"></i>Sistema: {{ $sistema->nome }} |
                        <i class="fas fa-users me-2"></i>Campanha: {{ $personagem->campanha->nome ?? 'N/A' }}
=======

{{-- TOASTS --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    @if(session('success'))
    <div class="toast show text-white bg-success border-0">
        <div class="d-flex">
            <div class="toast-body fw-bold">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast show text-white bg-danger border-0">
        <div class="d-flex">
            <div class="toast-body fw-bold">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>

<div class="container mt-5 text-light">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">

            <div class="card bg-dark border-0 shadow-lg rounded-4">

                {{-- HEADER --}}
                <div class="card-header bg-primary text-white rounded-top-4 p-4">
                    <h1 class="h2 fw-bold mb-1">Sessão: {{ $sessao->titulo }}</h1>
                    <p class="mb-1 opacity-75">
                        Mestre: <strong class="text-warning">{{ optional($mestre)->nome ?? 'Desconhecido' }}</strong>
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
                    </p>
                </div>
<<<<<<< HEAD
                <div class="text-end mt-2 mt-md-0">
                    <span class="badge bg-primary fs-5 p-2 rounded-pill shadow-sm">
                        Nível {{ $personagem->nivel ?? 1 }}
                    </span>
                    <a href="{{ route('personagens.edit', $personagem->id) }}" class="btn btn-outline-light ms-3 btn-sm">
                        <i class="fas fa-pencil-alt me-2"></i>Editar Ficha
                    </a>
=======

                <div class="card-body p-4">

                    {{-- DETALHES --}}
                    <h3 class="h5 text-info mb-3">Detalhes Principais</h3>

                    <div class="bg-secondary-subtle p-3 rounded mb-4">
                        <dl class="row mb-0 small">

                            <dt class="col-sm-4 text-warning">Sistema:</dt>
                            <dd class="col-sm-8 text-light fw-bold">
                                {{ $campanha->sistema->nome ?? 'Desconhecido' }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Status:</dt>
                            <dd class="col-sm-8 text-light fw-bold">
                                {{ ucfirst(str_replace('_', ' ', $sessao->status)) }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Agendada para:</dt>
                            <dd class="col-sm-8 text-light">
                                {{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Não definida' }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Resumo:</dt>
                            <dd class="col-sm-8 text-secondary fst-italic">
                                {{ $sessao->resumo ?: 'Nenhum resumo disponível.' }}
                            </dd>
                        </dl>
                    </div>

                    <hr class="border-secondary">

                    {{-- CONFIRMADOS --}}
                    <h3 class="h5 text-success mb-3">
                        Jogadores Confirmados ({{ count($confirmados) }})
                    </h3>

                    <div class="p-3 border border-success rounded mb-4 d-flex flex-wrap gap-2">

                        @forelse($confirmados as $c)
                            <span class="badge {{ $c['isMestre'] ? 'bg-warning text-dark' : 'bg-success' }}
                                          p-2 fw-bold shadow-sm">
                                {{ $c['nome'] }} @if($c['isMestre']) (Mestre) @endif
                            </span>
                        @empty
                            <p class="text-warning mb-0">Ninguém confirmou ainda.</p>
                        @endforelse

                    </div>

                    <hr class="border-secondary">

                    {{-- OUTROS --}}
                    <h3 class="h5 text-warning mb-3">
                        Jogadores Ativos ({{ $outrosJogadores->count() }})
                    </h3>

                    <p class="text-muted small">Jogadores sem confirmação.</p>

                    <div class="p-3 border border-warning rounded mb-4 d-flex flex-wrap gap-2">
                        @forelse($outrosJogadores as $j)
                            <span class="badge bg-secondary p-2 fw-bold shadow-sm">
                                {{ $j->nome }}
                            </span>
                        @empty
                            <p class="text-info mb-0">Todos já confirmaram.</p>
                        @endforelse
                    </div>

                    <hr class="border-secondary">

                    {{-- NOTAS --}}
                    <h3 class="h5 text-info mb-3">Notas do Mestre</h3>

                    <div class="bg-secondary p-4 rounded text-light shadow-inner" style="min-height: 180px;">
                        {!! nl2br(e($sessao->descricao_detalhada ?? 'Nenhuma anotação.')) !!}
                    </div>

                    <hr class="border-secondary my-4">

                    {{-- GRÁFICO CHARTZ --}}
                    <h3 class="h5 text-primary mb-3">
                        <i class="fa-solid fa-chart-bar"></i> Gráfico de Confirmações
                    </h3>

                    <div id="sessao-presencas-chart" style="height: 260px;"></div>

                    {!! $chart->container() !!}

                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-dark border-top border-secondary p-3
                            d-flex flex-wrap justify-content-between align-items-center rounded-bottom-4">

                    <small class="text-muted">ID Sessão: {{ $sessao->id }}</small>

                    <div class="d-flex gap-2 flex-wrap">

                        @if($podeMarcarPresenca)
                            <form action="{{ route('sessoes.marcar_presenca', [$campanha->id, $sessao->id]) }}"
                                  method="POST">
                                @csrf
                                <button class="btn btn-success rounded-pill fw-bold px-4 shadow-sm">
                                    Marcar Presença
                                </button>
                            </form>
                        @elseif($jaMarqueiPresenca)
                            <span class="badge bg-success p-2 fw-bold shadow-sm">
                                PRESENÇA CONFIRMADA
                            </span>
                        @endif

                        @if($isMestre)
                            <a href="{{ route('sessoes.edit', [$campanha->id, $sessao->id]) }}"
                               class="btn btn-warning rounded-pill px-4 shadow-sm">
                                Editar Sessão
                            </a>
                        @endif

                        <a href="{{ route('sessoes.index', $campanha->id) }}"
                           class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                            Todas as Sessões
                        </a>
                    </div>

>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
                </div>

            </div>
        </div>

        <div class="card-body p-4 p-md-5">

            <div class="row g-4">
                {{-- Coluna de Atributos (Pequena) --}}
                <div class="col-lg-3 order-lg-1 order-2">
                    <div class="card border-primary shadow-sm h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h5 class="mb-0">Atributos</h5>
                        </div>
                        <ul class="list-group list-group-flush">
                            @foreach($atributos as $nome => $valor)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong class="text-capitalize">{{ $nome }}</strong>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-dark rounded-pill me-2 fs-6 p-2">{{ $valor }}</span>
                                        <span class="text-muted fw-bold">
                                            ({{ $modificadores[$nome] >= 0 ? '+' : '' }}{{ $modificadores[$nome] ?? 0 }})
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Coluna Principal (Maior) --}}
                <div class="col-lg-9 order-lg-2 order-1">
                    <div class="row g-4 mb-4">
                        {{-- Resumo Rápido --}}
                        <div class="col-md-6">
                            <div class="card bg-light border-0 shadow-sm h-100 p-3">
                                <h4 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Resumo do Personagem</h4>
                                <ul class="list-unstyled mb-0">
                                    {{-- Usando o null coalescing operator (??) para evitar o erro se a relação for NULL --}}
                                    <li><strong>Raça:</strong> <span class="badge bg-secondary">{{ $personagem->raca->nome ?? 'N/A' }}</span></li>
                                    <li><strong>Classe:</strong> <span class="badge bg-secondary">{{ $personagem->classe->nome ?? 'N/A' }}</span></li>
                                    <li><strong>Origem:</strong> <span class="badge bg-secondary">{{ $personagem->origem->nome ?? 'N/A' }}</span></li>
                                    <li><strong>Pontos de Vida (Máx):</strong> <span class="badge bg-danger">{{ $personagem->vida ?? 'N/A' }}</span></li>
                                </ul>
                            </div>
                        </div>

                        {{-- Defesas e Combate --}}
                        <div class="col-md-6">
                            <div class="card bg-light border-0 shadow-sm h-100 p-3">
                                <h4 class="text-danger mb-3"><i class="fas fa-shield-alt me-2"></i>Defesas & Combate</h4>
                                <div class="row text-center">
                                    @foreach($defesa as $key => $value)
                                        <div class="col-4">
                                            <div class="p-2 border rounded bg-white shadow-xs">
                                                <h6 class="text-muted mb-0">{{ $key }}</h6>
                                                <strong class="fs-4 text-dark">{{ $value }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Perícias (Skills) --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Perícias</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($pericias as $nome => $detalhes)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="{{ $detalhes['proficiente'] ? 'fw-bold text-success' : 'text-muted' }}">
                                                {!! $detalhes['proficiente'] ? '<i class="fas fa-check-circle me-1"></i>' : '<i class="far fa-circle me-1"></i>' !!}
                                                {{ $nome }}
                                            </span>
                                            <small class="text-end">
                                                (Mod. {{ ucfirst($detalhes['atributo']) }})
                                                <span class="ms-1 fw-bold text-dark">
                                                    {{ $modificadores[$detalhes['atributo']] + ($detalhes['proficiente'] ? $defesa['Proficiência'] : 0) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detalhes de Background --}}
            <div class="row g-4 mt-3">
                <div class="col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-signature me-2"></i>Personalidade</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted">{{ $personagem->personalidade ?? 'Não definida.' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>História & Descrição</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold">Descrição:</h6>
                            <p class="text-muted">{{ $personagem->descricao ?? 'Não definida.' }}</p>
                            <hr>
                            <h6 class="fw-bold">História:</h6>
                            <p class="text-muted">{{ $personagem->historia ?? 'Não definida.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Equipamento (Simplificado) --}}
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Equipamento & Inventário</h5>
                </div>
                <div class="card-body">
                    @if(isset($personagem->inventario) && count($personagem->inventario['equipamento_inicial'] ?? []) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($personagem->inventario['equipamento_inicial'] as $item)
                                <li class="list-group-item">{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-warning mb-0">Nenhum equipamento inicial definido.</div>
                    @endif
                </div>
            </div>

            {{-- Botão de Retorno --}}
            <div class="text-center mt-5">
                <a href="{{ route('personagens.overview', $personagem->id) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Voltar para o Overview de Criação
                </a>
            </div>

        </div>
    </div>
</div>

<<<<<<< HEAD
=======
{{-- CHARTZ SCRIPTS --}}
<script src="https://unpkg.com/chart.js"></script>
<script src="https://unpkg.com/@chartisan/chartjs"></script>

{!! $chart->script() !!}

<style>
.bg-dark { background-color: #1a1e23 !important; }
.bg-secondary-subtle { background-color: #24292e !important; }
.shadow-inner { box-shadow: inset 0 1px 3px rgba(0,0,0,0.6); }
</style>

>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
@endsection
