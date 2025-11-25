@extends('layouts.app')

@section('title', "Nova Missão - {$campanha->nome}")

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">➕ Criar Nova Missão</h2>
        <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
    <hr>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('missoes.store', $campanha->id) }}" method="POST" class="card p-4 shadow-sm rounded-4">
        @csrf
        <div class="mb-3">
            <label for="titulo" class="form-label fw-bold">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label fw-bold">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" rows="4">{{ old('descricao') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="recompensa" class="form-label fw-bold">Recompensa</label>
            <input type="text" name="recompensa" id="recompensa" class="form-control" value="{{ old('recompensa') }}">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="prioridade" class="form-label fw-bold">Prioridade</label>
                <select name="prioridade" id="prioridade" class="form-select" required>
                    {{-- Usa 'baixa' como fallback se não houver old('prioridade') --}}
                    <option value="baixa" {{ old('prioridade', 'baixa') == 'baixa' ? 'selected' : '' }}>Baixa</option>
                    <option value="media" {{ old('prioridade') == 'media' ? 'selected' : '' }}>Média</option>
                    <option value="alta" {{ old('prioridade') == 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label fw-bold">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="pendente" {{ old('status', 'pendente') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="em_andamento" {{ old('status') == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                    <option value="concluida" {{ old('status') == 'concluida' ? 'selected' : '' }}>Concluída</option>
                    <option value="cancelada" {{ old('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success rounded-pill mt-3"><i class="bi bi-save"></i> Salvar Missão</button>
    </form>
</div>
@endsection
