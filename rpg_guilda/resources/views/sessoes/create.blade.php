@extends('layouts.app')

@section('title', "Nova Sessão - {$campanha->nome}")

@section('content')
<div class="container py-5 text-light">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-highlight">🗓️ Criar Nova Sessão</h1>
        <p class="text-muted">Campanha: {{ $campanha->nome }}</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show bg-danger text-light border-0 shadow-sm" role="alert">
                    <ul class="mb-0 list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            <div class="card bg-dark border-success shadow-lg p-4">
                <div class="card-body">
                    <form action="{{ route('sessoes.store', $campanha->id) }}" method="POST">
                        @csrf

                        {{-- Título --}}
                        <div class="mb-3">
                            <label class="form-label text-success fw-semibold">Título da Sessão</label>
                            <input type="text" name="titulo" class="form-control form-control-lg bg-secondary text-light border-success"
                                   placeholder="Ex: O Ataque dos Orcs" value="{{ old('titulo') }}" required>
                        </div>

                        {{-- Data e Hora --}}
                        <div class="mb-3">
                            <label class="form-label text-success fw-semibold">Data e Hora</label>
                            <input type="datetime-local" name="data_hora" class="form-control form-control-lg bg-secondary text-light border-success"
                                   value="{{ old('data_hora') }}" required>
                        </div>

                        {{-- Resumo --}}
                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Resumo (Notas para o Mestre)</label>
                            <textarea name="resumo" class="form-control form-control-lg bg-secondary text-light border-success"
                                      rows="5" placeholder="Escreva os pontos principais e o que aconteceu...">{{ old('resumo') }}</textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-4 justify-content-end">
                            <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-outline-secondary btn-lg rounded-pill fw-bold">
                                ⬅️ Voltar ao Painel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm fw-bold">
                                ✅ Criar Sessão
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-highlight {
    color: var(--btn-bg, #198754); /* Cor do botão success (verde) */
}

.bg-dark {
    background-color: #212529 !important;
}

.bg-secondary {
    background-color: #343a40 !important;
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); /* Sombra verde */
    border-color: #198754;
}
</style>
@endsection
