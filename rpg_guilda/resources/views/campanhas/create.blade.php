@extends('layouts.app')

@section('title', 'Criar Campanha')

@section('content')
<div class="container py-5 text-light">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-highlight">➕ Criar Nova Campanha</h1>
        <p class="text-muted">Preencha os dados da campanha para começar a aventura.</p>
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

                <form action="{{ route('campanhas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nome da Campanha</label>
                        <input type="text" name="nome" class="form-control" maxlength="100"
                               value="{{ old('nome') }}" required placeholder="Ex: Aventura Épica">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sistema RPG</label>
                        <select name="sistema_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($sistemas as $sistema)
                                <option value="{{ $sistema->id }}" {{ old('sistema_id') == $sistema->id ? 'selected' : '' }}>
                                    {{ $sistema->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4"
                                  placeholder="Opcional">{{ old('descricao') }}</textarea>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" name="privada" class="form-check-input" id="privada"
                               {{ old('privada') ? 'checked' : '' }}>
                        <label class="form-check-label" for="privada">Campanha Privada</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="ativa" {{ old('status') == 'ativa' ? 'selected' : '' }}>Ativa</option>
                            <option value="inativa" {{ old('status') == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-4">
                        Criar Campanha
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
