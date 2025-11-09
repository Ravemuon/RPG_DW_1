{{-- resources/views/missoes/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Criar Missão')

@section('content')
<div class="container py-4">
    <h2>📝 Criar Missão</h2>

    <form method="POST" action="{{ route('missoes.store', $campanha->id) }}">
        @csrf

        <div class="mb-3">
            <label for="titulo" class="form-label">Título da Missão</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao"></textarea>
        </div>

        <div class="mb-3">
            <label for="recompensa" class="form-label">Recompensa</label>
            <input type="text" class="form-control" id="recompensa" name="recompensa">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="pendente">Pendente</option>
                <option value="em_andamento">Em Andamento</option>
                <option value="concluida">Concluída</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Criar Missão</button>
    </form>
</div>
@endsection
