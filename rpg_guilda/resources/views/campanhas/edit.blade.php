@extends('layouts.app')

@section('title', 'Editar Campanha')

@section('content')
<div class="container py-5 text-light">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-highlight">✏️ Editar Campanha</h1>
        <p class="text-muted">Altere os dados da campanha conforme necessário.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark border-0 shadow-lg p-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('campanhas.update', $campanha->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nome da Campanha</label>
                        <input type="text" name="nome" class="form-control" maxlength="100"
                               value="{{ old('nome', $campanha->nome) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sistema RPG</label>
                        <select name="sistema_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($sistemas as $sistema)
                                <option value="{{ $sistema->id }}" {{ old('sistema_id', $campanha->sistema_id) == $sistema->id ? 'selected' : '' }}>
                                    {{ $sistema->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4">{{ old('descricao', $campanha->descricao) }}</textarea>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" name="privada" class="form-check-input" id="privada"
                               {{ old('privada', $campanha->privada) ? 'checked' : '' }}>
                        <label class="form-check-label" for="privada">Campanha Privada</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="ativa" {{ old('status', $campanha->status) == 'ativa' ? 'selected' : '' }}>Ativa</option>
                            <option value="inativa" {{ old('status', $campanha->status) == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-info fw-bold rounded-pill px-4">
                        Atualizar Campanha
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<style>
.text-highlight {
    color: var(--btn-bg, #ffc107);
}
</style>
@endsection
