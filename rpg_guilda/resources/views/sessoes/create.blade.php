@extends('layouts.app')

@section('title', 'Criar Sessão - ' . $campanha->titulo)

@section('content')
<div class="container py-4">
    <div class="card bg-dark text-light border-secondary shadow-lg">
        <div class="card-header">
            <h4 class="mb-0 fw-bold">➕ Criar Sessão para: {{ $campanha->titulo }}</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('sessoes.store', $campanha->id) }}">
                @csrf

                {{-- Título --}}
                <div class="mb-3">
                    <label for="titulo" class="form-label fw-semibold">Título da Sessão</label>
                    <input type="text" name="titulo" id="titulo" class="form-control bg-dark text-light border-secondary"
                           placeholder="Ex: O Cerco de Ravemuon" value="{{ old('titulo') }}" required>
                    @error('titulo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Data e hora --}}
                <div class="mb-3">
                    <label for="data_hora" class="form-label fw-semibold">Data e Hora</label>
                    <input type="datetime-local" name="data_hora" id="data_hora"
                           class="form-control bg-dark text-light border-secondary"
                           value="{{ old('data_hora') }}" required>
                    @error('data_hora') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Resumo --}}
                <div class="mb-3">
                    <label for="resumo" class="form-label fw-semibold">Resumo (opcional)</label>
                    <textarea name="resumo" id="resumo" rows="4"
                              class="form-control bg-dark text-light border-secondary"
                              placeholder="Breve descrição do que ocorrerá na sessão...">{{ old('resumo') }}</textarea>
                    @error('resumo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Botões --}}
                <div class="text-end mt-4">
                    <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-secondary me-2">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-warning fw-semibold text-dark">
                        Criar Sessão
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
