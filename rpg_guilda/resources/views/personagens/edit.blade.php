@extends('layouts.app')

@section('title', 'Editar Personagem')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">✏️ Editar Personagem: {{ $personagem->nome }}</h1>

    <form action="{{ route('personagens.update', $personagem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $personagem->nome) }}" required>
        </div>

        {{-- Raça --}}
        <div class="mb-3">
            <label class="form-label">Raça</label>
            <select name="raca_id" class="form-select" required>
                @foreach($racas as $r)
                    <option value="{{ $r->id }}" {{ $personagem->raca_id == $r->id ? 'selected' : '' }}>{{ $r->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Classe --}}
        <div class="mb-3">
            <label class="form-label">Classe</label>
            <input type="text" name="classe" class="form-control" value="{{ old('classe', $personagem->classe) }}" required>
        </div>

        {{-- Origem --}}
        <div class="mb-3">
            <label class="form-label">Origem</label>
            <input type="text" name="origem" class="form-control" value="{{ old('origem', $personagem->origem) }}">
        </div>

        {{-- Sistema RPG --}}
        <div class="mb-3">
            <label class="form-label">Sistema RPG</label>
            <select name="sistema_rpg" class="form-select" required>
                @foreach($sistemas as $s)
                    <option value="{{ $s->id }}" {{ $personagem->sistema_rpg == $s->id ? 'selected' : '' }}>{{ $s->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Perícias --}}
        <div class="mb-3">
            <label class="form-label">Perícias</label>
            <select name="pericias[]" class="form-select" multiple>
                @foreach($pericias as $p)
                    <option value="{{ $p->id }}" {{ $personagem->pericias->contains($p->id) ? 'selected' : '' }}>{{ $p->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Atributos --}}
        <div class="mb-3">
            <label class="form-label">Atributos (JSON)</label>
            <textarea name="atributos" class="form-control" rows="3">{{ old('atributos', json_encode($personagem->atributos)) }}</textarea>
        </div>

        {{-- Imagem --}}
        <div class="mb-3">
            <label class="form-label">Imagem do Personagem</label>
            <input type="file" name="imagem" class="form-control">
            @if($personagem->imagem)
                <img src="{{ asset('storage/'.$personagem->imagem) }}" alt="{{ $personagem->nome }}" class="img-thumbnail mt-2" style="max-width: 150px;">
            @endif
        </div>

        {{-- Descrição --}}
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $personagem->descricao) }}</textarea>
        </div>

        {{-- História --}}
        <div class="mb-3">
            <label class="form-label">História</label>
            <textarea name="historia" class="form-control" rows="3">{{ old('historia', $personagem->historia) }}</textarea>
        </div>

        {{-- Personalidade --}}
        <div class="mb-3">
            <label class="form-label">Personalidade</label>
            <textarea name="personalidade" class="form-control" rows="3">{{ old('personalidade', $personagem->personalidade) }}</textarea>
        </div>

        {{-- Inventário --}}
        <div class="mb-3">
            <label class="form-label">Inventário</label>
            <textarea name="inventario" class="form-control" rows="3">{{ old('inventario', $personagem->inventario) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Personagem</button>
        <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
