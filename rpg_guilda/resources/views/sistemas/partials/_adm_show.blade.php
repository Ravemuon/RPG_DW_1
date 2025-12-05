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

            @php
                $sections = [
                    'Atributos Configurados' => ['data' => $sistema->getAtributos(), 'route' => null],
                    'Raças' => ['data' => $sistema->racas, 'route' => route('sistemas.racas.index', $sistema->id)],
                    'Origens' => ['data' => $sistema->origens, 'route' => route('sistemas.origens.index', $sistema->id)],
                    'Classes' => ['data' => $sistema->classes, 'route' => route('sistemas.classes.index', $sistema->id)],
                    'Perícias' => ['data' => $sistema->pericias, 'route' => route('sistemas.pericias.index', $sistema->id)],
                ];
            @endphp

            <!-- Seções do Sistema -->
            @foreach($sections as $title => $section)
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="m-0">{{ $title }}</h5>
                    </div>
                    <div class="card-body">
                        @if(empty($section['data']) || $section['data']->isEmpty())
                            <p>Não há {{ strtolower($title) }} configurados para este sistema.</p>
                        @else
                            <ul class="list-group">
                                @foreach($section['data'] as $item)
                                    <li class="list-group-item">
                                        @if(is_object($item))
                                            <strong>{{ $item->nome }}</strong> - {{ $item->descricao ?? 'Descrição não disponível' }}
                                        @else
                                            {{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($section['route'])
                            <a href="{{ $section['route'] }}" class="btn btn-link mt-2">Ver todos</a>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Ações Administrativas -->
            @if(auth()->check() && auth()->user()->is_admin)
                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('sistemas.edit', $sistema->id) }}" class="btn btn-warning">
                        ✏️ Editar Sistema
                    </a>

                    <form action="{{ route('sistemas.destroy', $sistema->id) }}" method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir o sistema {{ addslashes($sistema->nome) }}? Esta ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            🗑️ Excluir Sistema
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
