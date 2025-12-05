@extends('layouts.app')

@section('title', "Editar Sistema - {$sistema->nome}")

@section('content')
<div class="container">

    <h1 class="mb-4">Editar Sistema: <strong>{{ $sistema->nome }}</strong></h1>

    <form action="{{ route('sistemas.update', $sistema->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome:</label>
            <input type="text" name="nome" class="form-control"
                   value="{{ old('nome', $sistema->nome) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição:</label>
            <textarea name="descricao" class="form-control" rows="4">{{ old('descricao', $sistema->descricao) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Foco:</label>
            <input type="text" name="foco" class="form-control"
                   value="{{ old('foco', $sistema->foco) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Mecânica Principal:</label>
            <input type="text" name="mecanica_principal" class="form-control"
                   value="{{ old('mecanica_principal', $sistema->mecanica_principal) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Complexidade:</label>
            <input type="text" name="complexidade" class="form-control"
                   value="{{ old('complexidade', $sistema->complexidade) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Atributos (JSON):</label>
            <textarea name="atributos" class="form-control" rows="3">{{ old('atributos', json_encode($sistema->atributos)) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Usa Sanidade?</label>
            <select name="usa_sanidade" class="form-select">
                <option value="0" {{ old('usa_sanidade', $sistema->usa_sanidade) == 0 ? 'selected' : '' }}>Não</option>
                <option value="1" {{ old('usa_sanidade', $sistema->usa_sanidade) == 1 ? 'selected' : '' }}>Sim</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Fórmula dos Pontos de Vida:</label>
            <input type="text" name="formula_pontos_vida" class="form-control"
                   value="{{ old('formula_pontos_vida', $sistema->formula_pontos_vida) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Recursos (JSON):</label>
            <textarea name="recursos" class="form-control" rows="3">{{ old('recursos', json_encode($sistema->recursos)) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Regras Opcionais (JSON):</label>
            <textarea name="regras_opcionais" class="form-control" rows="3">{{ old('regras_opcionais', json_encode($sistema->regras_opcionais)) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Salvar Alterações</button>
        <a href="{{ route('sistemas.index') }}" class="btn btn-secondary mt-3">Voltar</a>
    </form>
</div>
@endsection
