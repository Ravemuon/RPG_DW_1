@extends('layouts.app')

@section('title', 'Criar Personagem')

@section('content')
<div class="container py-5">

    <h2>🧝‍♂️ Criar Novo Personagem</h2>

    {{-- Formulário para criar o personagem --}}
    <form action="{{ route('personagens.store', $campanha->id) }}" method="POST" class="mt-4">
        @csrf
        {{-- Nome --}}
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Personagem</label>
            <input type="text" id="nome" name="nome" class="form-control" value="{{ old('nome') }}" required>
        </div>

        {{-- Raça --}}
        <div class="mb-3">
            <label for="raca" class="form-label">Raça</label>
            <select id="raca" name="raca_id" class="form-select">
                <option value="">Selecione a Raça</option>
                @foreach($racas as $raca)
                    <option value="{{ $raca->id }}" {{ old('raca_id') == $raca->id ? 'selected' : '' }}>{{ $raca->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Classe --}}
        <div class="mb-3">
            <label for="classe" class="form-label">Classe</label>
            <input type="text" id="classe" name="classe" class="form-control" value="{{ old('classe') }}">
        </div>

        {{-- Origem --}}
        <div class="mb-3">
            <label for="origem" class="form-label">Origem</label>
            <input type="text" id="origem" name="origem" class="form-control" value="{{ old('origem') }}">
        </div>

        {{-- Sistema RPG --}}
        <div class="mb-3">
            <label for="sistema_rpg" class="form-label">Sistema RPG</label>
            <input type="text" id="sistema_rpg" name="sistema_rpg" class="form-control" value="{{ old('sistema_rpg') }}">
        </div>

        {{-- Atributos --}}
        <div class="mb-3">
            <label for="atributos" class="form-label">Atributos (JSON)</label>
            <textarea id="atributos" name="atributos" class="form-control" rows="4">{{ old('atributos') }}</textarea>
            <small class="form-text text-muted">Exemplo: {"forca": 15, "destreza": 14, "inteligencia": 12}</small>
        </div>

        {{-- Descrição --}}
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="4">{{ old('descricao') }}</textarea>
        </div>

        {{-- Página --}}
        <div class="mb-3">
            <label for="pagina" class="form-label">Página (opcional)</label>
            <input type="text" id="pagina" name="pagina" class="form-control" value="{{ old('pagina') }}">
        </div>

        {{-- Botões --}}
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Criar Personagem</button>
            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

</div>
@endsection
