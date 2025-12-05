@extends('layouts.app')

@section('title', "Editar Sessão - {$campanha->nome}")

@section('content')
<div class="container py-5 text-light">

    {{-- Título centralizado --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5 gradient-title">Editar Sessão</h1>
        <p class="text-muted fs-5">Campanha: <span class="text-success fw-semibold">{{ $campanha->nome }}</span></p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Validações --}}
            @if($errors->any())
                <div class="alert alert-danger bg-opacity-75 border-danger border-2 rounded-3 shadow-lg fade show" role="alert">
                    <ul class="mb-0 ps-1">
                        @foreach($errors->all() as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Card de edição --}}
            <div class="card session-card border-0 shadow-2xl p-4">
                <div class="card-body p-4">

                    <form action="{{ route('sessoes.update', [$campanha->id, $sessao->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Título --}}
                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Título da Sessão</label>
                            <input
                                type="text"
                                name="titulo"
                                class="form-control form-control-lg custom-input"
                                placeholder="Ex: O Ataque dos Orcs"
                                value="{{ old('titulo', $sessao->titulo) }}"
                                required
                            >
                        </div>

                        {{-- Data e Hora --}}
                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Data e Hora</label>
                            <input
                                type="datetime-local"
                                name="data_hora"
                                class="form-control form-control-lg custom-input"
                                value="{{ old('data_hora', $sessao->data_hora->format('Y-m-d\TH:i')) }}"
                                required
                            >
                        </div>

                        {{-- NOVO CAMPO: Status da Sessão --}}
                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Status da Sessão</label>
                            <select
                                name="status"
                                class="form-select form-select-lg custom-input"
                                required
                            >
                                @php
                                    $currentStatus = old('status', $sessao->status);
                                @endphp
                                <option value="planejada" {{ $currentStatus == 'planejada' ? 'selected' : '' }}>Planejada (Agendada)</option>
                                <option value="em andamento" {{ $currentStatus == 'em andamento' ? 'selected' : '' }}>Em Andamento</option>
                                <option value="concluida" {{ $currentStatus == 'concluida' ? 'selected' : '' }}>Concluída</option>
                                <option value="cancelada" {{ $currentStatus == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        {{-- FIM NOVO CAMPO --}}

                        {{-- Resumo --}}
                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Resumo / Notas do Mestre</label>
                            <textarea
                                name="resumo"
                                rows="6"
                                class="form-control form-control-lg custom-input"
                                placeholder="Escreva aqui o planejamento da sessão, ganchos narrativos, NPCs importantes..."
                            >{{ old('resumo', $sessao->resumo) }}</textarea>
                        </div>

                        {{-- Botões --}}
                        <div class="d-flex flex-wrap gap-3 mt-4 justify-content-end">
                            <a href="{{ route('campanhas.mestre', $campanha->id) }}"
                               class="btn btn-outline-light btn-lg rounded-pill px-4 shadow-sm hover-lift">
                                Voltar ao Painel
                            </a>

                            <button type="submit"
                                    class="btn btn-success btn-lg rounded-pill px-5 shadow success-btn hover-lift">
                                Atualizar Sessão
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- Estilos Modernos --}}
<style>
.gradient-title {
    background: linear-gradient(90deg, #198754, #47d381);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.session-card {
    background: #161a1d;
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.25);
    box-shadow: 0 0 20px rgba(25, 135, 84, 0.15);
}

.custom-input {
    background-color: #202326 !important;
    color: #e5e5e5 !important;
    border: 1px solid #2b3a33 !important;
    border-radius: 0.6rem;
    font-size: 1.05rem;
    padding: 14px;
    transition: 0.25s;
}

.custom-input:focus {
    border-color: #198754 !important;
    box-shadow: 0 0 10px rgba(25, 135, 84, 0.4);
}

.success-btn {
    font-weight: 700;
    letter-spacing: 0.5px;
    background: linear-gradient(90deg, #198754, #1f9d68);
    border: none;
}

.hover-lift {
    transition: 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(255, 255, 255, 0.15);
}
</style>
@endsection