@extends('layouts.app')

@section('title', "Nova Sessão - {$campanha->nome}")

@section('content')
<div class="container py-5">
    <div class="card bg-dark text-light border-warning shadow">
        <div class="card-header bg-warning text-dark fw-bold">
            Criar Nova Sessão - {{ $campanha->nome }}
        </div>

        <div class="card-body">

            {{-- Mensagem de erro geral --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulário --}}
            <form action="{{ route('sessoes.store', ['campanha' => $campanha->id]) }}" method="POST">
                @csrf

                {{-- Título --}}
                <div class="mb-3">
                    <label for="titulo" class="form-label text-warning">Título da Sessão</label>
                    <input type="text" name="titulo" id="titulo"
                        class="form-control bg-dark text-light border-warning @error('titulo') is-invalid @enderror"
                        value="{{ old('titulo') }}" placeholder="Digite o título da sessão" required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Data e Hora --}}
                <div class="mb-3">
                    <label for="data_hora" class="form-label text-warning">Data e Hora</label>
                    <input type="datetime-local" name="data_hora" id="data_hora"
                        class="form-control bg-dark text-light border-warning @error('data_hora') is-invalid @enderror"
                        value="{{ old('data_hora') }}" required>
                    @error('data_hora')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Resumo --}}
                <div class="mb-3">
                    <label for="resumo" class="form-label text-warning">Resumo (opcional)</label>
                    <textarea name="resumo" id="resumo" rows="4"
                        class="form-control bg-dark text-light border-warning @error('resumo') is-invalid @enderror"
                        placeholder="Escreva um resumo breve da sessão">{{ old('resumo') }}</textarea>
                    @error('resumo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Botões --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('sessoes.index', ['campanha' => $campanha->id]) }}" class="btn btn-outline-light">
                        ⬅ Voltar
                    </a>
                    <button type="submit" class="btn btn-warning fw-bold">
                        ➕ Criar Sessão
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
