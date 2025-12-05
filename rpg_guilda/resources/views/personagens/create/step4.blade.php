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
            <div class="card shadow mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">4. Vida, Sanidade & Sorte</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('personagens.storeStep4') }}">
                        @csrf

                        {{-- Pontos de Vida --}}
                        <div class="mb-3">
                            <label for="vida" class="form-label">Pontos de Vida (PV) Máximo <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('vida') is-invalid @enderror" 
                                   id="vida" 
                                   name="vida" 
                                   value="{{ old('vida', $data['vida'] ?? 1) }}" 
                                   min="1" required>
                            @error('vida')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">
                                Geralmente é o máximo do dado de vida da classe + modificador de Constituição no Nível 1.
                            </small>
                            {{-- Opcional: botão AJAX para sorteio de vida --}}
                            {{-- <button type="button" class="btn btn-sm btn-outline-danger mt-2">Sortear Vida</button> --}}
                        </div>

                        {{-- Sanidade e Sorte --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sanidade" class="form-label">Sanidade Máxima (Opcional)</label>
                                <input type="number" 
                                       class="form-control @error('sanidade') is-invalid @enderror" 
                                       id="sanidade" 
                                       name="sanidade" 
                                       value="{{ old('sanidade', $data['sanidade'] ?? '') }}" 
                                       min="0" max="100">
                                @error('sanidade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sorte" class="form-label">Sorte/Felicidade Máxima (Opcional)</label>
                                <input type="number" 
                                       class="form-control @error('sorte') is-invalid @enderror" 
                                       id="sorte" 
                                       name="sorte" 
                                       value="{{ old('sorte', $data['sorte'] ?? '') }}" 
                                       min="0" max="100">
                                @error('sorte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Navegação --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('personagens.step3') }}" class="btn btn-outline-secondary">
                                &laquo; Voltar (Passo 3)
                            </a>
                            <button type="submit" class="btn btn-danger">Salvar e Próximo (Passo 5) &raquo;</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
