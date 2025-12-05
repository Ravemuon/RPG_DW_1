@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- COLUNA DA BARRA DE PROGRESSO --}}
        <div class="col-md-4">
            @include('personagens.create._progress_bar', ['data' => $sessionData])
        </div>
        
        {{-- COLUNA DO FORMULÁRIO --}}
        <div class="col-md-8">
            <div class="card shadow mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">2. Raça, Classe & Origem</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('personagens.storeStep2') }}">
                        @csrf
                        
                        {{-- Raça --}}
                        <div class="mb-3">
                            <label for="raca_id" class="form-label">Raça <span class="text-danger">*</span></label>
                            <select class="form-select @error('raca_id') is-invalid @enderror" 
                                    id="raca_id" 
                                    name="raca_id" 
                                    required>
                                <option value="">Selecione a Raça...</option>
                                @foreach ($racas as $raca)
                                    <option value="{{ $raca->id }}" {{ old('raca_id', $sessionData['raca_id'] ?? '') == $raca->id ? 'selected' : '' }}>
                                        {{ $raca->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('raca_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Classe --}}
                        <div class="mb-3">
                            <label for="classe_id" class="form-label">Classe <span class="text-danger">*</span></label>
                            <select class="form-select @error('classe_id') is-invalid @enderror" 
                                    id="classe_id" 
                                    name="classe_id" 
                                    required>
                                <option value="">Selecione a Classe...</option>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ old('classe_id', $sessionData['classe_id'] ?? '') == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classe_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        {{-- Origem --}}
                        <div class="mb-3">
                            <label for="origem_id" class="form-label">Origem (Background)</label>
                            <select class="form-select @error('origem_id') is-invalid @enderror" 
                                    id="origem_id" 
                                    name="origem_id">
                                <option value="">Nenhuma (Opcional)</option>
                                @foreach ($origens as $origem)
                                    <option value="{{ $origem->id }}" {{ old('origem_id', $sessionData['origem_id'] ?? '') == $origem->id ? 'selected' : '' }}>
                                        {{ $origem->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('origem_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Bônus de Proficiência --}}
                        <div class="mb-3">
                            <label for="bonus_proficiencia" class="form-label">Bônus de Proficiência <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('bonus_proficiencia') is-invalid @enderror" 
                                   id="bonus_proficiencia" 
                                   name="bonus_proficiencia" 
                                   value="{{ old('bonus_proficiencia', $sessionData['bonus_proficiencia'] ?? 2) }}" 
                                   min="1" max="6" required>
                            @error('bonus_proficiencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Ajustado automaticamente com base no nível do personagem.</small>
                        </div>
                        
                        {{-- Navegação --}}
                        <a href="{{ route('personagens.create', ['campanha' => $sessionData['campanha_id']]) }}" class="btn btn-outline-secondary">
                            &laquo; Voltar (Passo 1)
                        </a>
                        <button type="submit" class="btn btn-warning float-end">Salvar e Próximo (Passo 3) &raquo;</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
