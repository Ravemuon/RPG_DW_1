@extends('layouts.app')

@section('title', 'Criar Personagem')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Criar Personagem</h2>

    <form action="{{ route('personagens.store') }}" method="POST">
        @csrf
        <input type="hidden" name="campanha_id" value="{{ $campanha_id }}">

        {{-- Nome --}}
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        {{-- NPC --}}
        <div class="mb-3 form-check">
            <input type="checkbox" name="npc" id="npc" class="form-check-input">
            <label for="npc" class="form-check-label">NPC?</label>
        </div>

        {{-- Sistema --}}
        <div class="mb-3">
            <label for="sistemaRPG" class="form-label">Sistema RPG</label>
            <select name="sistemaRPG" id="sistemaRPG" class="form-select" required>
                <option value="D&D">D&D</option>
                <option value="Ordem Paranormal">Ordem Paranormal</option>
                <option value="Call of Cthulhu">Call of Cthulhu</option>
                <option value="Fate Core">Fate Core</option>
                <option value="Cypher System">Cypher System</option>
                <option value="Apocalypse World">Apocalypse World</option>
                <option value="Cyberpunk 2093 - Arkana-RPG">Cyberpunk 2093 - Arkana-RPG</option>
            </select>
        </div>

        {{-- Classe --}}
        <div class="mb-3">
            <label for="classe_id" class="form-label">Classe</label>
            <select name="classe_id" id="classe_id" class="form-select">
                <option value="">Nenhuma</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->nome }} ({{ $c->sistemaRPG }})</option>
                @endforeach
            </select>
        </div>

        {{-- Origens --}}
        <div class="mb-3">
            <label for="origem_ids" class="form-label">Origens</label>
            <select name="origem_ids[]" id="origem_ids" class="form-select" multiple>
                @foreach($origens as $o)
                    <option value="{{ $o->id }}">{{ $o->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Atributos JSON --}}
        <div class="mb-3">
            <label for="atributos" class="form-label">Atributos (JSON)</label>
            <textarea name="atributos" id="atributos" class="form-control" rows="5" placeholder='Ex: {"forca":10,"destreza":12}'></textarea>
        </div>

        <button type="submit" class="btn btn-success">Criar Personagem</button>
        <a href="{{ route('personagens.index', $campanha_id) }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
