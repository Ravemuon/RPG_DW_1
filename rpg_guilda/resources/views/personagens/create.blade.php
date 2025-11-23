@extends('layouts.app')

@section('title', 'Novo Personagem')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">🧙 Criar Personagem</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('personagens.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="campanha_id" value="{{ $campanha->id }}">

        {{-- Etapa 1: Raça e Classe --}}
        <div class="mb-3">
            <label class="form-label">Raça</label>
            <select name="raca_id" class="form-select" required>
                <option value="">Selecione</option>
                @foreach($racas as $r)
                    <option value="{{ $r->id }}" {{ old('raca_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Classe</label>
            <select name="classe_id" class="form-select" required>
                <option value="">Selecione</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ old('classe_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Exibição de bônus de atributos (somente leitura) --}}
        @if(old('raca_id') || old('classe_id'))
            <div class="mb-3">
                <label class="form-label">Bônus de Atributos (somente leitura)</label>
                <ul class="list-group">
                    @foreach($racas->firstWhere('id', old('raca_id'))->atributos_bonus ?? [] as $attr => $valor)
                        <li class="list-group-item">{{ ucfirst($attr) }}: +{{ $valor }}</li>
                    @endforeach
                    @foreach($classes->firstWhere('id', old('classe_id'))->atributos_bonus ?? [] as $attr => $valor)
                        <li class="list-group-item">{{ ucfirst($attr) }}: +{{ $valor }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Etapa 2: Origem --}}
        <div class="mb-3">
            <label class="form-label">Origem</label>
            <select name="origem_id" class="form-select">
                <option value="">Selecione</option>
                @foreach($origens as $o)
                    <option value="{{ $o->id }}" {{ old('origem_id') == $o->id ? 'selected' : '' }}>
                        {{ $o->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Etapa 3: Personalizações opcionais --}}
        <hr>
        <h5>Personalizações (opcional)</h5>

        <div class="mb-3">
            <label class="form-label">Nome do Personagem</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem / Aparência</label>
            <input type="file" name="imagem" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">História</label>
            <textarea name="historia" class="form-control" rows="3">{{ old('historia') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição / Personalidade</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Inventário</label>
            <textarea name="inventario" class="form-control" rows="3">{{ old('inventario') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Criar Personagem</button>
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
    