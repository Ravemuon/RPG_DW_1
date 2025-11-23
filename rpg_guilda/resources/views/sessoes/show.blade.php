@extends('layouts.app')

@section('title', "Editar Sessão - {$sessao->titulo}")

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Editar Sessão</h1>

    <form action="{{ route('sessoes.update', $sessao->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo', $sessao->titulo) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Data e Hora</label>
            <input type="datetime-local" name="data_hora" class="form-control" value="{{ old('data_hora', $sessao->data_hora->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Resumo</label>
            <textarea name="resumo" class="form-control" rows="4">{{ old('resumo', $sessao->resumo) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="agendada" {{ $sessao->status=='agendada' ? 'selected' : '' }}>Agendada</option>
                <option value="em_andamento" {{ $sessao->status=='em_andamento' ? 'selected' : '' }}>Em andamento</option>
                <option value="concluida" {{ $sessao->status=='concluida' ? 'selected' : '' }}>Concluída</option>
                <option value="cancelada" {{ $sessao->status=='cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill">Atualizar Sessão</button>
        <a href="{{ route('sessoes.index', $sessao->campanha->id) }}" class="btn btn-secondary rounded-pill">Cancelar</a>
    </form>
</div>
@endsection
