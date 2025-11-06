@extends('layouts.app')

@section('title', 'Editar Personagem')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Editar Personagem: {{ $personagem->nome }}</h2>

    <form action="{{ route('personagens.update', $personagem->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ $personagem->nome }}" required>
        </div>

        {{-- NPC --}}
        <div class="mb-3 form-check">
            <input type="checkbox" name="npc" id="npc" class="form-check-input" {{ $personagem->npc ? 'checked' : '' }}>
            <label for="npc" class="form-check-label">NPC?</label>
        </div>

        {{-- Sistema --}}
        <div class="mb-3">
            <label for="sistemaRPG" class="form-label">Sistema RPG</label>
            <select name="sistemaRPG" id="sistemaRPG" class="form-select" required>
                @foreach(['D&D','Ordem Paranormal','Call of Cthulhu','Fate Core','Cypher System','Apocalypse World','Cyberpunk 2093 - Arkana-RPG'] as $sistema)
                    <option value="{{ $sistema }}" {{ $personagem->sistemaRPG==$sistema ? 'selected' : '' }}>{{ $sistema }}</option>
                @endforeach
            </select>
        </div>

        {{-- Classe --}}
        <div class="mb-3">
            <label for="classe_id" class="form-label">Classe</label>
            <select name="classe_id" id="classe_id" class="form-select">
                <option value="">Nenhuma</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $personagem->classe_id==$c->id ? 'selected' : '' }}>{{ $c->nome }} ({{ $c->sistemaRPG }})</option>
                @endforeach
            </select>
        </div>

        {{-- Origens --}}
        <div class="mb-3">
            <label for="origem_ids" class="form-label">Origens</label>
            <select name="origem_ids[]" id="origem_ids" class="form-select" multiple>
                @foreach($origens as $o)
                    <option value="{{ $o->id }}" {{ in_array($o->id, $personagem->origens->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $o->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Atributos JSON --}}
        <div class="mb-3">
            <label for="atributos" class="form-label">Atributos (JSON)</label>
            <textarea name="atributos" id="atributos" class="form-control" rows="5">{{ json_encode($personagem->atributos()) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Personagem</button>
        <a href="{{ route('personagens.index', $personagem->campanha_id) }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
