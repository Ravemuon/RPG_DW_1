@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2>✨ Editar Personagem: {{ $personagem->nome }} (ID: {{ $personagem->id }})</h2>
                    <p class="mb-0">Edição Simplificada (Nome, Raça, Classe, Origem)</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('personagens.updateSimpleEdit', $personagem->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="nome" class="form-label font-weight-bold">Nome do Personagem <span class="text-danger">*</span></label>
                            <input type="text" 
                                class="form-control @error('nome') is-invalid @enderror" 
                                id="nome" 
                                name="nome" 
                                value="{{ old('nome', $personagem->nome) }}" 
                                required
                                maxlength="100">
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr class="my-4">

                        <div class="form-group mb-3">
                            <label for="raca_id" class="form-label">Raça</label>
                            <select class="form-control @error('raca_id') is-invalid @enderror" id="raca_id" name="raca_id">
                                <option value="" {{ is_null(old('raca_id', $personagem->raca_id)) ? 'selected' : '' }}>-- Selecione uma Raça --</option>
                                @foreach($racas as $raca)
                                    <option value="{{ $raca->id }}" 
                                            {{ old('raca_id', $personagem->raca_id) == $raca->id ? 'selected' : '' }}>
                                        {{ $raca->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('raca_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="classe_id" class="form-label">Classe</label>
                            <select class="form-control @error('classe_id') is-invalid @enderror" id="classe_id" name="classe_id">
                                <option value="" {{ is_null(old('classe_id', $personagem->classe_id)) ? 'selected' : '' }}>-- Selecione uma Classe --</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}" 
                                            {{ old('classe_id', $personagem->classe_id) == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classe_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="origem_id" class="form-label">Origem</label>
                            <select class="form-control @error('origem_id') is-invalid @enderror" id="origem_id" name="origem_id">
                                <option value="" {{ is_null(old('origem_id', $personagem->origem_id)) ? 'selected' : '' }}>-- Selecione uma Origem --</option>
                                @foreach($origens as $origem)
                                    <option value="{{ $origem->id }}" 
                                            {{ old('origem_id', $personagem->origem_id) == $origem->id ? 'selected' : '' }}>
                                        {{ $origem->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('origem_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('personagens.editOverview', $personagem->id) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection