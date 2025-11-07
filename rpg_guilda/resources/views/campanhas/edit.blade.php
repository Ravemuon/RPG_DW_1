@extends('layouts.app')

@section('title', 'Editar Campanha - ' . $campanha->nome)

@section('content')
<div class="container py-5 bg-dark text-light min-vh-100">
    <h1 class="fw-bold text-warning mb-4">✏️ Editar Campanha: {{ $campanha->nome }}</h1>

    <div class="card bg-secondary text-light shadow-lg">
        <div class="card-body">
            <form action="{{ route('campanhas.update', $campanha->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Método para atualização --}}

                {{-- Nome da Campanha --}}
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Campanha <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-dark text-light border-warning" id="nome" name="nome" required value="{{ old('nome', $campanha->nome) }}">
                    @error('nome') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- Sistema --}}
                <div class="mb-3">
                    <label for="sistema_id" class="form-label">Sistema de RPG <span class="text-danger">*</span></label>
                    <select class="form-select bg-dark text-light border-warning" id="sistema_id" name="sistema_id" required>
                        <option value="">Selecione um Sistema</option>
                        {{-- Assumindo que $sistemas está disponível na view --}}
                        @foreach($sistemas as $sistema)
                            <option value="{{ $sistema->id }}" {{ old('sistema_id', $campanha->sistema_id) == $sistema->id ? 'selected' : '' }}>{{ $sistema->nome }}</option>
                        @endforeach
                    </select>
                    @error('sistema_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- Descrição --}}
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição (Enredo, Tom, Regras)</label>
                    <textarea class="form-control bg-dark text-light border-warning" id="descricao" name="descricao" rows="5">{{ old('descricao', $campanha->descricao) }}</textarea>
                    @error('descricao') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <label for="status" class="form-label">Status Atual</label>
                    <select class="form-select bg-dark text-light border-warning" id="status" name="status" required>
                        <option value="ativa" {{ old('status', $campanha->status) == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="pausada" {{ old('status', $campanha->status) == 'pausada' ? 'selected' : '' }}>Pausada</option>
                        <option value="encerrada" {{ old('status', $campanha->status) == 'encerrada' ? 'selected' : '' }}>Encerrada</option>
                    </select>
                    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success fw-bold">Salvar Alterações</button>
                    <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-light">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
