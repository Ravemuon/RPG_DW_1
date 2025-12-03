@extends('layouts.app')

@section('title', "Editar Sessão - {$sessao->titulo}")

@section('content')
<div class="container py-5 text-light">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-highlight">✏️ Editar Sessão</h1>
        <p class="text-muted">{{ $sessao->titulo }} | Campanha: {{ $sessao->campanha->nome }}</p>
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
                    {{-- Formulário de Edição --}}
                    <form action="{{ route('sessoes.update', ['campanha' => $sessao->campanha->id, 'sessao' => $sessao->id]) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        {{-- Título --}}
                        <div class="mb-3">
                            <label class="form-label text-success fw-semibold">Título da Sessão</label>
                            <input type="text" name="titulo" class="form-control form-control-lg bg-secondary text-light border-success"
                                   value="{{ old('titulo', $sessao->titulo) }}" required>
                        </div>

                        {{-- Data e Hora --}}
                        <div class="mb-3">
                            <label class="form-label text-success fw-semibold">Data e Hora</label>
                            {{-- Formata o datetime para ser aceito pelo campo 'datetime-local' --}}
                            <input type="datetime-local" name="data_hora" class="form-control form-control-lg bg-secondary text-light border-success"
                                   value="{{ old('data_hora', optional($sessao->data_hora)->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        {{-- Resumo --}}
                        <div class="mb-3">
                            <label class="form-label text-success fw-semibold">Resumo (Notas rápidas ou para o Mestre)</label>
                            <textarea name="resumo" class="form-control form-control-lg bg-secondary text-light border-success"
                                     rows="5">{{ old('resumo', $sessao->resumo) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Descrição Detalhada (Relato do Jogo ou Notas da Sessão)</label>
                            <textarea name="descricao_detalhada" class="form-control form-control-lg bg-secondary text-light border-success"
                                     rows="8">{{ old('descricao_detalhada', $sessao->descricao_detalhada) }}</textarea>
                            <div class="form-text text-muted">Use este campo para o relato completo, especialmente quando a sessão for concluída.</div>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Status</label>
                            <select name="status" class="form-select form-select-lg bg-secondary text-light border-success" required>
                                {{-- Opções de Status --}}
                                @php
                                    $statuses = [
                                        'agendada' => 'Agendada',
                                        'em_andamento' => 'Em andamento',
                                        'concluida' => 'Concluída',
                                        'cancelada' => 'Cancelada',
                                    ];
                                @endphp

                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $sessao->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="d-flex flex-wrap gap-3 mt-5 justify-content-between">
                            <button type="button" class="btn btn-danger btn-lg rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                🗑️ Excluir Sessão
                            </button>

                            <div class="d-flex gap-3">
                                <a href="{{ route('sessoes.index', $sessao->campanha->id) }}" class="btn btn-outline-secondary btn-lg rounded-pill fw-bold">
                                    ❌ Cancelar
                                </a>
                                <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm fw-bold">
                                    💾 Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Confirmação de Exclusão --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-danger">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-danger fw-bold" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza de que deseja excluir a sessão **{{ $sessao->titulo }}**? Esta ação é irreversível e removerá todos os dados relacionados (presenças, personagens, etc.).
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('sessoes.destroy', ['campanha' => $sessao->campanha->id, 'sessao' => $sessao->id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold">Sim, Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>

.text-highlight {
    color: #198754;
}

.bg-dark {
    background-color: #212529 !important;
}

.bg-secondary {
    background-color: #343a40 !important;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    border-color: #198754;
}

.form-select-lg {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    height: 3rem;
}
</style>
@endsection
