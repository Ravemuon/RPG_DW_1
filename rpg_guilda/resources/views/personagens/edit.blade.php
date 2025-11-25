@extends('layouts.app')

@section('title', 'Editar Personagem')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">✏️ Editar Personagem</h1>

    <form action="{{ route('personagens.update', $personagem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $personagem->nome) }}">
        </div>

        {{-- Raça --}}
        <div class="mb-3">
            <label class="form-label">Raça</label>
            <select name="raca_id" class="form-select">
                @foreach($racas as $r)
                    <option value="{{ $r->id }}" {{ $personagem->raca_id == $r->id ? 'selected' : '' }}>
                        {{ $r->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Classe --}}
        <div class="mb-3">
            <label class="form-label">Classe</label>
            <select name="classe_id" class="form-select">
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $personagem->classe_id == $c->id ? 'selected' : '' }}>
                        {{ $c->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Origem --}}
        <div class="mb-3">
            <label class="form-label">Origem</label>
            <select name="origem_id" class="form-select">
                @foreach($origens as $o)
                    <option value="{{ $o->id }}" {{ $personagem->origem_id == $o->id ? 'selected' : '' }}>
                        {{ $o->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- História --}}
        <div class="mb-3">
            <label class="form-label">História</label>
            <textarea name="historia" class="form-control" rows="3">{{ old('historia', $personagem->historia) }}</textarea>
        </div>

        {{-- Descrição --}}
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $personagem->descricao) }}</textarea>
        </div>

        {{-- Inventário --}}
        <div class="mb-3">
            <label class="form-label">Inventário</label>
            <textarea name="inventario" class="form-control" rows="3">{{ old('inventario', $personagem->inventario) }}</textarea>
        </div>

        {{-- Imagem --}}
        <div class="mb-3">
            <label class="form-label">Imagem (opcional)</label>
            <input type="file" name="imagem" class="form-control">
        </div>

        <button class="btn btn-success">Salvar Alterações</button>
        <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
