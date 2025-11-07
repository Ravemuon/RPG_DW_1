@extends('layouts.app')

@section('title', 'Editar Campanha: ' . $campanha->nome)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4 text-warning fw-bold text-center" style="text-shadow: 0 0 6px var(--btn-bg);">
                ✏️ Editando: {{ $campanha->nome }}
            </h2>
            <p class="text-center text-secondary mb-4">Apenas o mestre pode fazer alterações.</p>

            {{-- Formulário de Edição --}}
            <div class="card border-warning shadow-lg bg-dark p-4 mb-5">
                <form action="{{ route('campanhas.update', $campanha->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Nome da Campanha --}}
                    <div class="mb-4">
                        <label for="nome" class="form-label text-light fw-bold">Nome da Campanha <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-secondary text-light @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $campanha->nome) }}" required maxlength="100">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Sistema RPG --}}
                    <div class="mb-4">
                        <label for="sistema_id" class="form-label text-light fw-bold">Sistema de RPG <span class="text-danger">*</span></label>
                        <select class="form-select bg-secondary text-light @error('sistema_id') is-invalid @enderror" id="sistema_id" name="sistema_id" required>
                            <option value="">Selecione um Sistema</option>
                            @foreach($sistemas as $sistema)
                                <option value="{{ $sistema->id }}" {{ old('sistema_id', $campanha->sistema_id) == $sistema->id ? 'selected' : '' }}>
                                    {{ $sistema->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('sistema_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="mb-4">
                        <label for="descricao" class="form-label text-light fw-bold">Descrição / Sinopse</label>
                        <textarea class="form-control bg-secondary text-light @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="5">{{ old('descricao', $campanha->descricao) }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="text-warning my-4">

                    {{-- Privacidade --}}
                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input bg-warning border-warning" type="checkbox" id="privada" name="privada" value="1" {{ old('privada', $campanha->privada) ? 'checked' : '' }}>
                        <label class="form-check-label text-light fw-bold" for="privada">
                            Campanha Privada (Requer código para entrar)
                        </label>
                        @error('privada')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Código de Convite --}}
                    <div class="mb-4">
                        <label for="codigo_convite" class="form-label text-light fw-bold">
                            Código de Convite Atual (Se privada)
                        </label>
                        <input type="text" class="form-control bg-secondary text-light @error('codigo_convite') is-invalid @enderror" id="codigo_convite" name="codigo_convite" value="{{ old('codigo_convite', $campanha->codigo_convite) }}" maxlength="20">
                        @error('codigo_convite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($campanha->privada)
                            <small class="text-info">Código atual: **{{ $campanha->codigo_convite }}**</small>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold shadow-sm mt-3">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            {{-- Formulário de Exclusão --}}
            <div class="card border-danger shadow-lg bg-dark p-4">
                <h4 class="text-danger fw-bold">⚠️ Zona de Perigo</h4>
                <p class="text-light">A exclusão da campanha é irreversível e removerá todos os dados, missões e sessões relacionadas.</p>
                <form action="{{ route('campanhas.destroy', $campanha->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja DELETAR a campanha {{ $campanha->nome }}? Esta ação é irreversível!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold">
                        Deletar Campanha
                    </button>
                </form>
            </div>

            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-info mt-4">
                &larr; Voltar para a Campanha
            </a>
        </div>
    </div>
</div>
@endsection
