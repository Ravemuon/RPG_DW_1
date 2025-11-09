@extends('layouts.app')

@section('title', 'Detalhes do Sistema')

@section('content')
<div class="container py-4">

    <!-- Botão de Voltar -->
    <button onclick="window.history.back()" class="btn btn-outline-primary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </button>

    <!-- Botão Novo Sistema (Visível apenas para administradores) -->
    @if(auth()->check() && auth()->user()->is_admin)
        <a href="{{ route('sistemas.create') }}" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle me-1"></i> Novo Sistema
        </a>
    @endif

    <!-- Exibição do Sistema -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="m-0">📜 Sistema: {{ $sistema->nome }} (#{{ $sistema->id }})</h4>
        </div>
        <div class="card-body">
            <!-- Informações Gerais -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Descrição:</strong> {{ $sistema->descricao ?? 'Não disponível' }}</p>
                    <p><strong>Foco:</strong> {{ $sistema->foco ?? 'Não especificado' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Mecânica Principal:</strong> {{ $sistema->mecanica_principal ?? 'Não especificada' }}</p>
                    <p><strong>Complexidade:</strong> {{ $sistema->complexidade ?? 'Não definida' }}</p>
                    <p><strong>Regras Opcionais:</strong> {{ $sistema->regras_opcionais ?? 'Não definidas' }}</p>
                    <p><strong>Página:</strong> <a href="{{ $sistema->pagina ?? '#' }}" target="_blank">Link</a></p>
                </div>
            </div>

            <!-- Atributos Configurados -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="m-0">Atributos Configurados</h5>
                </div>
                <div class="card-body">
                    @if(empty($sistema->getAtributos()))
                        <p>Não há atributos configurados para este sistema.</p>
                    @else
                        <ul class="list-group">
                            @foreach($sistema->getAtributos() as $atributo)
                                <li class="list-group-item">{{ $atributo }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Raças do Sistema -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="m-0">Raças</h5>
                </div>
                <div class="card-body">
                    @if($sistema->racas->isEmpty())
                        <p>Não há raças configuradas para este sistema.</p>
                    @else
                        <ul class="list-group">
                            @foreach($sistema->racas as $raca)
                                <li class="list-group-item">
                                    <strong>{{ $raca->nome }}</strong> - {{ $raca->descricao ?? 'Descrição não disponível' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <a href="{{ route('sistemas.racas.index', $sistema->id) }}" class="btn btn-link">Ver todas as Raças</a>
                </div>
            </div>

            <!-- Origens do Sistema -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="m-0">Origens</h5>
                </div>
                <div class="card-body">
                    @if($sistema->origens->isEmpty())
                        <p>Não há origens configuradas para este sistema.</p>
                    @else
                        <ul class="list-group">
                            @foreach($sistema->origens as $origem)
                                <li class="list-group-item">
                                    <strong>{{ $origem->nome }}</strong> - {{ $origem->descricao ?? 'Descrição não disponível' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <a href="{{ route('sistemas.origens.index', $sistema->id) }}" class="btn btn-link">Ver todas as Origens</a>
                </div>
            </div>

            <!-- Classes do Sistema -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="m-0">Classes</h5>
                </div>
                <div class="card-body">
                    @if($sistema->classes->isEmpty())
                        <p>Não há classes configuradas para este sistema.</p>
                    @else
                        <ul class="list-group">
                            @foreach($sistema->classes as $classe)
                                <li class="list-group-item">
                                    <strong>{{ $classe->nome }}</strong> - {{ $classe->descricao ?? 'Descrição não disponível' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <a href="{{ route('sistemas.classes.index', $sistema->id) }}" class="btn btn-link">Ver todas as Classes</a>
                </div>
            </div>

            <!-- Perícias do Sistema -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="m-0">Perícias</h5>
                </div>
                <div class="card-body">
                    @if($sistema->pericias->isEmpty())
                        <p>Não há perícias configuradas para este sistema.</p>
                    @else
                        <ul class="list-group">
                            @foreach($sistema->pericias as $pericia)
                                <li class="list-group-item">
                                    <strong>{{ $pericia->nome }}</strong> - {{ $pericia->descricao ?? 'Descrição não disponível' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <a href="{{ route('sistemas.pericias.index', $sistema->id) }}" class="btn btn-link">Ver todas as Perícias</a>
                </div>
            </div>

            <!-- Ações Administrativas (Visível apenas para Administradores) -->
            @if(auth()->check() && auth()->user()->is_admin)
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('sistemas.edit', $sistema->id) }}" class="btn btn-warning">
                        ✏️ Editar Sistema
                    </a>
                    <form action="{{ route('sistemas.destroy', $sistema->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este sistema?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">🗑️ Excluir Sistema</button>
                    </form>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
