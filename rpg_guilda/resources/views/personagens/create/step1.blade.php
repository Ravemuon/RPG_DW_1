@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row">
        {{-- COLUNA DA BARRA DE PROGRESSO --}}
        <div class="col-md-4">
            @include('personagens.create._progress_bar', ['data' => $data])
        </div>
        
        {{-- COLUNA DO FORMULÁRIO --}}
        <div class="col-md-8">
            <div class="card shadow mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">1. Dados Básicos do Personagem</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('personagens.storeStep1') }}" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Campos ocultos essenciais --}}
                        <input type="hidden" name="campanha_id" value="{{ $campanha->id }}">
                        <input type="hidden" name="sistema_id" value="{{ $campanha->sistema_id }}">

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Personagem <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nome') is-invalid @enderror" 
                                   id="nome" 
                                   name="nome" 
                                   value="{{ old('nome', $data['nome'] ?? '') }}" 
                                   required>
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Nível e XP --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nivel" class="form-label">Nível</label>
                                <input type="number" 
                                       class="form-control @error('nivel') is-invalid @enderror" 
                                       id="nivel" 
                                       name="nivel" 
                                       value="{{ old('nivel', $data['nivel'] ?? 1) }}" 
                                       min="1" max="20" required>
                                @error('nivel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="xp" class="form-label">Experiência (XP)</label>
                                <input type="number" 
                                       class="form-control @error('xp') is-invalid @enderror" 
                                       id="xp" 
                                       name="xp" 
                                       value="{{ old('xp', $data['xp'] ?? 0) }}" 
                                       min="0" required>
                                @error('xp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        {{-- Descrição --}}
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição Física (Aparência)</label>
                            <textarea class="form-control" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="2">{{ old('descricao', $data['descricao'] ?? '') }}</textarea>
                        </div>

                        {{-- História --}}
                        <div class="mb-3">
                            <label for="historia" class="form-label">História e Background</label>
                            <textarea class="form-control" 
                                      id="historia" 
                                      name="historia" 
                                      rows="3">{{ old('historia', $data['historia'] ?? '') }}</textarea>
                        </div>

                        {{-- Imagem --}}
                        <div class="mb-3">
                            <label for="imagem_file" class="form-label">Imagem/Avatar (Opcional)</label>
                            <input class="form-control @error('imagem_file') is-invalid @enderror" 
                                   type="file" 
                                   id="imagem_file" 
                                   name="imagem_file">
                            @error('imagem_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if (!empty($data['imagem_temp_path']))
                                <small class="text-muted">Imagem temporária atual carregada.</small>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-success float-end">
                            Salvar e Próximo (Passo 2) &raquo;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
