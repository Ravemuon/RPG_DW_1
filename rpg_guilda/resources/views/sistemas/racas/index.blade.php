@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1 class="fw-bold m-0">🧬 Raças de {{ $sistema->nome }}</h1>
        <div class="d-flex gap-2 flex-wrap">
            <!-- Apenas o botão para criar uma nova raça -->
            <a href="{{ route('sistemas.racas.create', $sistema->id) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Raça
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($sistema->racas->isEmpty())
        <div class="text-center p-5 bg-light rounded shadow-sm">
            <i class="bi bi-emoji-frown fs-3 text-muted"></i>
            <h4 class="mt-3 text-muted">Nenhuma raça cadastrada ainda.</h4>
        </div>
    @else
        <!-- Tabela de Raças -->
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
                    @foreach($sistema->racas as $raca)
                        <tr>
                            <td>{{ $raca->id }}</td>
                            <td class="fw-bold">{{ $raca->nome }}</td>
                            <td>{{ $raca->forca_bonus }}</td>
                            <td>{{ $raca->destreza_bonus }}</td>
                            <td>{{ $raca->inteligencia_bonus }}</td>
                            <td>{{ $raca->sabedoria_bonus }}</td>
                            <td>{{ $raca->carisma_bonus }}</td>
                            <td>{{ $raca->pagina ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('sistemas.racas.edit', [$sistema->id, $raca->id]) }}" class="btn btn-sm btn-outline-warning">
                                    ✏️
                                </a>
                                <form action="{{ route('sistemas.racas.destroy', [$sistema->id, $raca->id]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Tem certeza que deseja excluir esta raça?')" class="btn btn-sm btn-outline-danger">
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

    @isset($raca)
    <!-- Formulário de Edição de Raça -->
    <div class="card shadow-sm rounded">
        <div class="card-header bg-warning text-white">
            <h4>Editar Raça: {{ $raca->nome }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sistemas.racas.update', [$sistema->id, $raca->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nome da Raça -->
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Raça</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="{{ old('nome', $raca->nome) }}">
                </div>

                <!-- Descrição -->
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="3">{{ old('descricao', $raca->descricao) }}</textarea>
                </div>

                <!-- Atributos da Raça -->
                <div class="row">
                    <div class="col-md-4">
                        <label for="forca_bonus" class="form-label">Força</label>
                        <input type="number" id="forca_bonus" name="forca_bonus" class="form-control" value="{{ old('forca_bonus', $raca->forca_bonus) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="destreza_bonus" class="form-label">Destreza</label>
                        <input type="number" id="destreza_bonus" name="destreza_bonus" class="form-control" value="{{ old('destreza_bonus', $raca->destreza_bonus) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="inteligencia_bonus" class="form-label">Inteligência</label>
                        <input type="number" id="inteligencia_bonus" name="inteligencia_bonus" class="form-control" value="{{ old('inteligencia_bonus', $raca->inteligencia_bonus) }}">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="sabedoria_bonus" class="form-label">Sabedoria</label>
                        <input type="number" id="sabedoria_bonus" name="sabedoria_bonus" class="form-control" value="{{ old('sabedoria_bonus', $raca->sabedoria_bonus) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="carisma_bonus" class="form-label">Carisma</label>
                        <input type="number" id="carisma_bonus" name="carisma_bonus" class="form-control" value="{{ old('carisma_bonus', $raca->carisma_bonus) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="pagina" class="form-label">Página</label>
                        <input type="text" id="pagina" name="pagina" class="form-control" value="{{ old('pagina', $raca->pagina) }}">
                    </div>
                </div>

                <!-- Botão de Atualização -->
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">Atualizar Raça</button>
                </div>
            </form>
        </div>
    </div>
    @endisset
</div>
@endsection
