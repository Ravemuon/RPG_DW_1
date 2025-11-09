@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1 class="fw-bold m-0">🧬 Classes de {{ $sistema->nome }}</h1>
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

    @if($sistema->classes->isEmpty())
        <div class="text-center p-5 bg-light rounded shadow-sm">
            <i class="bi bi-emoji-frown fs-3 text-muted"></i>
            <h4 class="mt-3 text-muted">Nenhuma classe cadastrada ainda.</h4>
        </div>
    @else
        <!-- Tabela de Classes -->
        <div class="table-responsive shadow-sm rounded mb-4">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Força</th>
                        <th>Destreza</th>
                        <th>Inteligência</th>
                        <th>Sabedoria</th>
                        <th>Carisma</th>
                        <th>Página</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sistema->classes as $classe)
                        <tr>
                            <td>{{ $classe->id }}</td>
                            <td class="fw-bold">{{ $classe->nome }}</td>
                            <td>{{ $classe->forca }}</td>
                            <td>{{ $classe->destreza }}</td>
                            <td>{{ $classe->inteligencia }}</td>
                            <td>{{ $classe->sabedoria }}</td>
                            <td>{{ $classe->carisma }}</td>
                            <td>{{ $classe->pagina ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('sistemas.classes.show', [$sistema->id, $classe->id]) }}" class="btn btn-sm btn-outline-info">
                                    👁️
                                </a>
                                <a href="{{ route('sistemas.classes.edit', [$sistema->id, $classe->id]) }}" class="btn btn-sm btn-outline-warning">
                                    ✏️
                                </a>
                                <form action="{{ route('sistemas.classes.destroy', [$sistema->id, $classe->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Tem certeza que deseja excluir esta classe?')" class="btn btn-sm btn-outline-danger">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @isset($classe)
    <!-- Formulário de Edição de Classe -->
    <div class="card shadow-sm rounded">
        <div class="card-header bg-warning text-white">
            <h4>Editar Classe: {{ $classe->nome }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sistemas.classes.update', [$sistema->id, $classe->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nome da Classe -->
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Classe</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="{{ old('nome', $classe->nome) }}">
                </div>

                <!-- Descrição -->
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="3">{{ old('descricao', $classe->descricao) }}</textarea>
                </div>

                <!-- Atributos da Classe -->
                <div class="row">
                    <div class="col-md-4">
                        <label for="forca" class="form-label">Força</label>
                        <input type="number" id="forca" name="forca" class="form-control" value="{{ old('forca', $classe->forca) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="destreza" class="form-label">Destreza</label>
                        <input type="number" id="destreza" name="destreza" class="form-control" value="{{ old('destreza', $classe->destreza) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="inteligencia" class="form-label">Inteligência</label>
                        <input type="number" id="inteligencia" name="inteligencia" class="form-control" value="{{ old('inteligencia', $classe->inteligencia) }}">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="sabedoria" class="form-label">Sabedoria</label>
                        <input type="number" id="sabedoria" name="sabedoria" class="form-control" value="{{ old('sabedoria', $classe->sabedoria) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="carisma" class="form-label">Carisma</label>
                        <input type="number" id="carisma" name="carisma" class="form-control" value="{{ old('carisma', $classe->carisma) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="pagina" class="form-label">Página</label>
                        <input type="text" id="pagina" name="pagina" class="form-control" value="{{ old('pagina', $classe->pagina) }}">
                    </div>
                </div>

                <!-- Botão de Atualização -->
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">Atualizar Classe</button>
                </div>
            </form>
        </div>
    </div>
    @endisset
</div>
@endsection
