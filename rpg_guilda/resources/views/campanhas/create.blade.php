@extends('layouts.app')

@section('title', 'Criar Campanha')

@section('content')
<div class="container py-5 text-light">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-highlight">Criar Nova Campanha</h1>
        <p class="text-muted">Preencha os dados da campanha.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark border-0 shadow-lg p-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('campanhas.store') }}" method="POST">
                    @csrf

                    {{-- Nome da Campanha --}}
                    <div class="mb-3">
                        <label class="form-label">Nome da Campanha</label>
                        <input type="text" name="nome" class="form-control" maxlength="100"
                               value="{{ old('nome') }}" required placeholder="Ex: Aventura Épica">
                    </div>

                    {{-- Sistema --}}
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

                    {{-- Descrição --}}
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4"
                                  placeholder="Opcional">{{ old('descricao') }}</textarea>
                    </div>

                    {{-- Hidden para fallback --}}
                    <input type="hidden" name="privada" value="0">

                    {{-- Switch: Campanha Privada --}}
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" name="privada" class="form-check-input" id="privada_checkbox"
                            value="1" {{ old('privada') ? 'checked' : '' }}>
                        <label class="form-check-label" for="privada_checkbox">Campanha Privada</label>
                        <div class="form-text text-muted">
                            Se ativado, jogadores precisarão de um código para entrar.
                        </div>
                    </div>

                    {{-- Código de Convite --}}
                    @php
                        $showCodeField = old('privada') ? 'block' : 'none';
                    @endphp
                    <div class="mb-3" id="codigoConviteGroup" style="display: {{ $showCodeField }};">
                        <label class="form-label">Código de Convite (Opcional)</label>
                        <input type="text" name="codigo_convite" class="form-control" maxlength="10"
                               value="{{ old('codigo_convite') }}"
                               placeholder="Deixe vazio para gerar automaticamente">
                        <div class="form-text text-light-50">Máximo de 10 caracteres.</div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="ativa" {{ old('status', 'ativa') == 'ativa' ? 'selected' : '' }}>Ativa</option>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const privadaCheckbox = document.getElementById('privada_checkbox');
        const codigoConviteGroup = document.getElementById('codigoConviteGroup');

        function toggleCodigoConvite() {
            codigoConviteGroup.style.display = privadaCheckbox.checked ? 'block' : 'none';
        }

        privadaCheckbox.addEventListener('change', toggleCodigoConvite);
        toggleCodigoConvite();
    });
</script>
@endsection
