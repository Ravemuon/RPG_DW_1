@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold">Sistemas de RPG</h1>

        <div class="d-flex gap-2 flex-wrap">
            @if(auth()->user()->is_admin)
                <a href="{{ route('sistemas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Criar Novo Sistema
                </a>
            @endif

            <a href="{{ route('sistemas.exportar-pdf') }}" target="_blank" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Gerar PDF
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                    <th>Perícias</th>
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
                        <td>
                            <a href="{{ route('sistemas.classes', $sistema->id) }}" class="btn btn-sm btn-outline-primary">
                                Ver Classes
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('sistemas.origens', $sistema->id) }}" class="btn btn-sm btn-outline-primary">
                                Ver Origens
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('sistemas.racas', $sistema->id) }}" class="btn btn-sm btn-outline-primary">
                                Ver Raças
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('sistemas.pericias', $sistema->id) }}" class="btn btn-sm btn-outline-primary">
                                Ver Perícias
                            </a>
                        </td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('sistemas.show', $sistema) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('sistemas.edit', $sistema) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('sistemas.destroy', $sistema) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este sistema?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Nenhum sistema encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
