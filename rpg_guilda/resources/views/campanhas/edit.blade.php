@extends('layouts.app')

@section('title', 'Editar Campanha: ' . $campanha->nome)

@section('content')
<div class="container py-4">
    <div class="card border-warning shadow-lg bg-body-tertiary">
        <div class="card-header bg-warning bg-opacity-25 border-warning">
            <h2 class="fw-bold text-warning mb-0">✏️ Editar Campanha</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('campanhas.update', $campanha->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nome da Campanha --}}
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome da Campanha *</label>
                    <input type="text" id="nome" name="nome"
                        class="form-control"
                        value="{{ old('nome', $campanha->nome) }}" required>
                </div>

                {{-- Sistema --}}
                <div class="mb-3">
                    <label for="sistema_id" class="form-label fw-semibold">Sistema de RPG *</label>
                    <select id="sistema_id" name="sistema_id" class="form-select" required>
                        @foreach($sistemas as $sistema)
                            <option value="{{ $sistema->id }}" {{ old('sistema_id', $campanha->sistema_id) == $sistema->id ? 'selected' : '' }}>
                                {{ $sistema->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Descrição --}}
                <div class="mb-3">
                    <label for="descricao" class="form-label fw-semibold">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4" class="form-control">{{ old('descricao', $campanha->descricao) }}</textarea>
                </div>

                {{-- Privada --}}
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="privada" name="privada" value="1" {{ old('privada', $campanha->privada) ? 'checked' : '' }}>
                    <label class="form-check-label" for="privada">Campanha Privada</label>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status *</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="ativa" {{ old('status', $campanha->status) === 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="pausada" {{ old('status', $campanha->status) === 'pausada' ? 'selected' : '' }}>Pausada</option>
                        <option value="encerrada" {{ old('status', $campanha->status) === 'encerrada' ? 'selected' : '' }}>Encerrada</option>
                    </select>
                </div>

                {{-- Código de Convite --}}
                <div class="mb-4">
                    <label for="codigo_convite" class="form-label fw-semibold">Código de Convite</label>
                    <input type="text" id="codigo_convite" name="codigo_convite"
                        class="form-control"
                        value="{{ old('codigo_convite', $campanha->codigo_convite) }}" maxlength="10">
                    <small class="text-muted">Use este código para convidar jogadores. Deixe vazio para manter o atual.</small>
                </div>

                {{-- Botões --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-secondary">
                        ⬅️ Voltar
                    </a>
                    <button type="submit" class="btn btn-warning fw-bold shadow-sm">
                        💾 Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
