@extends('layouts.app')

@section('title', 'Criar Novo Personagem')

@section('content')
<div class="container py-5">
    <div class="theme-card p-5 rounded shadow-lg">
        <h1 class="text-warning fw-bold mb-4 border-bottom pb-2" style="text-shadow: 0 0 6px var(--btn-bg);">
            🛡️ Construir Novo Personagem
        </h1>

        <form action="{{ route('personagens.store') }}" method="POST">
            @csrf

            {{-- Informações básicas --}}
            <h2 class="text-warning fw-semibold mb-3">Informações Básicas</h2>

            <div class="mb-3">
                <label for="nome" class="form-label text-light">Nome Completo</label>
                <input type="text" name="nome" id="nome" required
                       class="form-control theme-input"
                       value="{{ old('nome') }}" placeholder="Ex: Elara, A Ladina Silenciosa">
                @error('nome') <p class="text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="classe" class="form-label text-light">Classe</label>
                    <select name="classe" id="classe" required class="form-select theme-input">
                        <option value="">Selecione a Classe</option>
                        @foreach ($classes as $classe)
                            <option value="{{ $classe->nome }}" {{ old('classe') == $classe->nome ? 'selected' : '' }}>
                                {{ $classe->nome }} ({{ $classe->sistemaRPG ?? 'Sistema Genérico' }})
                            </option>
                        @endforeach
                    </select>
                    @error('classe') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="col-md-6">
                    <label for="campanha_id" class="form-label text-light">Campanha</label>
                    <select name="campanha_id" id="campanha_id" required class="form-select theme-input">
                        <option value="">Selecione a Campanha</option>
                        @foreach ($campanhas as $campanha)
                            <option value="{{ $campanha->id }}" {{ old('campanha_id') == $campanha->id ? 'selected' : '' }}>
                                {{ $campanha->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('campanha_id') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Origens --}}
            <div class="mb-3 theme-section p-3 rounded">
                <label class="form-label text-light">Origens (Bônus de Atributos)</label>
                <div class="d-flex flex-wrap gap-2 max-vh-50 overflow-auto">
                    @foreach ($origens as $origem)
                        <div class="form-check text-light">
                            <input id="origem-{{ $origem->id }}" name="origens[]" type="checkbox" value="{{ $origem->id }}"
                                   class="form-check-input theme-input"
                                   {{ in_array($origem->id, (array)old('origens', [])) ? 'checked' : '' }}>
                            <label for="origem-{{ $origem->id }}" class="form-check-label">
                                {{ $origem->nome }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('origens') <p class="text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descrição --}}
            <div class="mb-3">
                <label for="descricao" class="form-label text-light">História de Fundo / Descrição</label>
                <textarea name="descricao" id="descricao" rows="5"
                          class="form-control theme-input"
                          placeholder="Descreva a história e a aparência física do seu personagem.">{{ old('descricao') }}</textarea>
                @error('descricao') <p class="text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Botões --}}
            <div class="d-flex justify-end gap-2 mt-4">
                <a href="{{ route('personagens.index') }}" class="btn theme-btn-outline">Cancelar</a>
                <button type="submit" class="btn theme-btn fw-bold">Criar Personagem</button>
            </div>
        </form>
    </div>
</div>
@endsection
