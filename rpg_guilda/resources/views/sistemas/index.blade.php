@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Sistemas de RPG</h1>

        @if(auth()->user()->is_admin)
            <a href="{{ route('sistemas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Criar Novo Sistema
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(auth()->user()->is_admin)
        <!-- Tabela para administradores -->
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Foco</th>
                        <th>Complexidade</th>
                        <th>Classes</th>
                        <th>Origens</th>
                        <th>Raças</th>
                        <th>Atributos (Raças)</th>
                        <th>Perícias</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sistemas as $sistema)
                        <tr>
                            <td>{{ $sistema->id }}</td>
                            <td>{{ $sistema->nome }}</td>
                            <td>{{ $sistema->foco ?? 'N/A' }}</td>
                            <td>{{ $sistema->complexidade ?? 'N/A' }}</td>

                            <!-- Classes -->
                            <td>
                                @if($sistema->classes?->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach($sistema->classes as $classe)
                                            <li>{{ $classe->nome }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Origens -->
                            <td>
                                @if($sistema->origens?->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach($sistema->origens as $origem)
                                            <li>{{ $origem->nome }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Raças -->
                            <td>
                                @if($sistema->racas?->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach($sistema->racas as $raca)
                                            <li>{{ $raca->nome }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Atributos das Raças -->
                            <td>
                                @if($sistema->racas?->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach($sistema->racas as $raca)
                                            <li>
                                                {{ $raca->nome }}:
                                                @php
                                                    $attrs = $raca->atributosBase() ?? [];
                                                @endphp
                                                @if(!empty($attrs))
                                                    {{ implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($attrs), $attrs)) }}
                                                @else
                                                    N/A
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Perícias -->
                            <td>
                                @if($sistema->pericias?->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach($sistema->pericias as $pericia)
                                            <li>{{ $pericia->nome }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Criado em -->
                            <td>{{ $sistema->created_at->format('d/m/Y') }}</td>

                            <!-- Ações -->
                            <td class="d-flex gap-1">
                                <a href="{{ route('sistemas.show', $sistema) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sistemas.edit', $sistema) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('sistemas.destroy', $sistema) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este sistema?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Nenhum sistema encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        <!-- Cards para usuários comuns com prévia da descrição -->
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @forelse ($sistemas as $sistema)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold">{{ $sistema->nome }}</h5>
                            <p class="card-text text-secondary">
                                {{ Str::limit($sistema->descricao, 100, '...') }}
                            </p>
                            <a href="{{ route('sistemas.show', $sistema) }}" class="btn btn-sm btn-outline-primary">
                                Ver Mais
                            </a>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-end">
                            <small class="text-muted">Criado em: {{ $sistema->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Nenhum sistema disponível.</p>
            @endforelse
        </div>
    @endif
</div>
@endsection
