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
                    {{-- CORREÇÃO: Passando os dois parâmetros obrigatórios: 'campanha' e 'sessao' --}}
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
                            {{-- Formato o datetime para ser aceito pelo campo 'datetime-local' --}}
                            <input type="datetime-local" name="data_hora" class="form-control form-control-lg bg-secondary text-light border-success"
                                   value="{{ old('data_hora', $sessao->data_hora->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        {{-- Resumo --}}
                        <div class="mb-3">
                            <label class="form-label text-success fw-semibold">Resumo (Notas para o Mestre)</label>
                            <textarea name="resumo" class="form-control form-control-lg bg-secondary text-light border-success"
                                      rows="5">{{ old('resumo', $sessao->resumo) }}</textarea>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label class="form-label text-success fw-semibold">Status</label>
                            <select name="status" class="form-select form-select-lg bg-secondary text-light border-success" required>
                                <option value="agendada" {{ old('status', $sessao->status) == 'agendada' ? 'selected' : '' }}>Agendada</option>
                                <option value="em_andamento" {{ old('status', $sessao->status) == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                                <option value="concluida" {{ old('status', $sessao->status) == 'concluida' ? 'selected' : '' }}>Concluída</option>
                                <option value="cancelada" {{ old('status', $sessao->status) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="d-flex flex-wrap gap-3 mt-5 justify-content-between">
                            <button type="button" class="btn btn-danger btn-lg rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                🗑️ Excluir Sessão
                            </button>

                            <div class="d-flex gap-3">
                                <a href="{{ route('campanhas.mestre', $sessao->campanha->id) }}" class="btn btn-outline-secondary btn-lg rounded-pill fw-bold">
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

{{-- Modal de Confirmação de Exclusão (Requer Bootstrap JS) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-danger">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-danger fw-bold" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza de que deseja excluir a sessão **{{ $sessao->titulo }}**? Esta ação é irreversível.
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
    color: var(--btn-bg, #198754); /* Cor do botão success (verde) */
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
