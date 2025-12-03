@extends('layouts.app')

@section('title', 'Criação de Personagem - 2. Raça, Classe e Origem')

@section('content')

@php
    // Modo Criação: Carrega dados da sessão
    $raca_id = old('raca_id', $sessionData['raca_id'] ?? null);
    $classe_id = old('classe_id', $sessionData['classe_id'] ?? null);
    $origem_id = old('origem_id', $sessionData['origem_id'] ?? null);
    $bonus_proficiencia = old('bonus_proficiencia', $sessionData['bonus_proficiencia'] ?? 2);
@endphp

<div class="container my-5">
    <div class="card shadow-xl border-0">
        <div class="card-header bg-success text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1"><i class="fas fa-sitemap me-2"></i> Etapa 2: Raça, Classe e Origem</h1>
                    <p class="mb-0 fs-6">
                        Campanha: <strong>{{ $campanha->nome }}</strong> | Personagem: <strong>{{ $sessionData['nome'] }}</strong>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-success fs-5 p-2">Sistema: {{ $campanha->sistema->nome ?? 'Padrão' }}</span>
                </div>
            </div>

            <div class="progress mt-3" style="height: 10px;">
                <div class="progress-bar bg-light" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="d-block mt-1 text-light">Progresso: 50% (Identidade)</small>
        </div>

        <div class="card-body p-5">
            {{-- Criação: Envia para storeStep2 para armazenar na sessão e avançar --}}
            <form action="{{ route('personagens.store.step2') }}" method="POST">
                @csrf

                <div class="row g-5">

                    {{-- Seleção de Raça --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                            <h3 class="mb-4 text-success"><i class="fas fa-paw me-2"></i> Raça</h3>
                            <div class="mb-4">
                                <label for="raca_id" class="form-label fs-5">
                                    <i class="fas fa-globe me-1"></i> Escolha a Raça
                                    <span class="badge bg-secondary ms-2">Opcional</span>
                                </label>
                                <select class="form-select form-select-lg @error('raca_id') is-invalid @enderror"
                                        id="raca_id"
                                        name="raca_id">
                                    <option value="" @if(empty($raca_id)) selected @endif>Nenhuma/Outra</option>
                                    @foreach ($racas as $raca)
                                        <option value="{{ $raca->id }}" @selected($raca->id == $raca_id)>
                                            {{ $raca->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('raca_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">A herança e natureza de seu personagem.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Seleção de Classe --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                            <h3 class="mb-4 text-success"><i class="fas fa-hat-wizard me-2"></i> Classe</h3>
                            <div class="mb-4">
                                <label for="classe_id" class="form-label fs-5">
                                    <i class="fas fa-bolt me-1"></i> Escolha a Classe
                                    <span class="badge bg-secondary ms-2">Opcional</span>
                                </label>
                                <select class="form-select form-select-lg @error('classe_id') is-invalid @enderror"
                                        id="classe_id"
                                        name="classe_id">
                                    <option value="" @if(empty($classe_id)) selected @endif>Nenhuma/Outra</option>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe->id }}" @selected($classe->id == $classe_id)>
                                            {{ $classe->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('classe_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Seu papel, habilidades e vocação.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Seleção de Origem --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                            <h3 class="mb-4 text-success"><i class="fas fa-map-marker-alt me-2"></i> Origem</h3>
                            <div class="mb-4">
                                <label for="origem_id" class="form-label fs-5">
                                    <i class="fas fa-home me-1"></i> Escolha a Origem (Background)
                                    <span class="badge bg-secondary ms-2">Opcional</span>
                                </label>
                                <select class="form-select form-select-lg @error('origem_id') is-invalid @enderror"
                                        id="origem_id"
                                        name="origem_id">
                                    <option value="" @if(empty($origem_id)) selected @endif>Nenhuma/Outra</option>
                                    @foreach ($origens as $origem)
                                        <option value="{{ $origem->id }}" @selected($origem->id == $origem_id)>
                                            {{ $origem->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('origem_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">A história pregressa e conexões sociais.</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bônus de Proficiência --}}
                <div class="row mt-5">
                    <div class="col-md-6 offset-md-3">
                         <div class="card border-0 shadow-sm p-4 text-center">
                            <h3 class="mb-4 text-success"><i class="fas fa-plus-circle me-2"></i> Bônus de Proficiência (BP)</h3>
                            <div class="mb-4">
                                <label for="bonus_proficiencia" class="form-label fs-5">
                                    <i class="fas fa-dice-d20 me-1"></i> Valor do Bônus <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                        class="form-control form-control-lg text-center @error('bonus_proficiencia') is-invalid @enderror"
                                        id="bonus_proficiencia"
                                        name="bonus_proficiencia"
                                        value="{{ $bonus_proficiencia }}"
                                        min="1"
                                        max="6"
                                        required>
                                @error('bonus_proficiencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Baseado no nível do seu personagem. Para nível 1-4 em D&D 5e, o valor é 2.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <a href="{{ route('personagens.create', ['campanha' => $campanha->id]) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-chevron-left me-2"></i> Voltar (Etapa 1)
                    </a>

                    <button type="submit" class="btn btn-success btn-lg shadow-lg">
                        Próxima Etapa: Atributos <i class="fas fa-chevron-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@endpush

@endsection
