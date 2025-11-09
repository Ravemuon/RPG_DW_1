@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1 class="fw-bold m-0">🌍 Origens de {{ $sistema->nome }}</h1>
        <div class="d-flex gap-2 flex-wrap">
            <!-- Botão para a Página de Detalhes do Sistema -->
            <a href="{{ route('sistemas.show', $sistema->id) }}" class="btn btn-secondary">
                <i class="bi bi-book"></i> Ver Sistema
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($sistema->origens->isEmpty())
        <div class="text-center p-5 bg-light rounded shadow-sm">
            <i class="bi bi-emoji-frown fs-3 text-muted"></i>
            <h4 class="mt-3 text-muted">Nenhuma origem cadastrada ainda.</h4>
        </div>
    @else
        <!-- Tabela de Origens -->
        <div class="table-responsive shadow-sm rounded mb-4">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Página</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sistema->origens as $origem)
                        <tr>
                            <td>{{ $origem->id }}</td>
                            <td class="fw-bold">{{ $origem->nome }}</td>
                            <td>{{ $origem->descricao }}</td>
                            <td>{{ $origem->pagina ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('origens.show', [$sistema->id, $origem->id]) }}" class="btn btn-sm btn-outline-info">
                                    👁️
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#editOrigemModal{{ $origem->id }}" class="btn btn-sm btn-outline-warning">
                                    ✏️
                                </a>
                                <form action="{{ route('origens.destroy', [$sistema->id, $origem->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Tem certeza que deseja excluir esta origem?')" class="btn btn-sm btn-outline-danger">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal de Edição da Origem -->
                        <div class="modal fade" id="editOrigemModal{{ $origem->id }}" tabindex="-1" aria-labelledby="editOrigemModalLabel{{ $origem->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editOrigemModalLabel{{ $origem->id }}">Editar Origem: {{ $origem->nome }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('origens.update', [$sistema->id, $origem->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-3">
                                                <label for="nome" class="form-label">Nome</label>
                                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $origem->nome) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="descricao" class="form-label">Descrição</label>
                                                <textarea class="form-control" id="descricao" name="descricao">{{ old('descricao', $origem->descricao) }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="pagina" class="form-label">Página</label>
                                                <input type="text" class="form-control" id="pagina" name="pagina" value="{{ old('pagina', $origem->pagina) }}">
                                            </div>

                                            <button type="submit" class="btn btn-warning">Atualizar Origem</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Formulário de Criação de Origem -->
    <div class="card shadow-sm rounded">
        <div class="card-header bg-primary text-white">
            <h4>Nova Origem</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('origens.store', $sistema->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Origem</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="{{ old('nome') }}">
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="3">{{ old('descricao') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="pagina" class="form-label">Página</label>
                    <input type="text" id="pagina" name="pagina" class="form-control" value="{{ old('pagina') }}">
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">Criar Origem</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
