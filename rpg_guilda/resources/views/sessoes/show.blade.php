@extends('layouts.app')

@section('title', $personagem->nome . ' - Ficha Completa')

@section('content')

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
                    </p>
                </div>
                <div class="text-end mt-2 mt-md-0">
                    <span class="badge bg-primary fs-5 p-2 rounded-pill shadow-sm">
                        Nível {{ $personagem->nivel ?? 1 }}
                    </span>
                    <a href="{{ route('personagens.edit', $personagem->id) }}" class="btn btn-outline-light ms-3 btn-sm">
                        <i class="fas fa-pencil-alt me-2"></i>Editar Ficha
                    </a>
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

@endsection
