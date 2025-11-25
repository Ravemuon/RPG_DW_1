@extends('layouts.app')

@section('title', 'Editar Campanha')

@section('content')
<div class="container py-5 text-light">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-highlight">✏️ Editar Campanha: {{ $campanha->nome }}</h1>
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

                    {{-- Este input hidden garante que 'privada' será '0' se o checkbox não for marcado --}}
                    <input type="hidden" name="privada" value="0">

                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" name="privada" class="form-check-input" id="privada_checkbox"
                            value="1"
                            {{ old('privada', $campanha->privada) ? 'checked' : '' }}>
                        <label class="form-check-label" for="privada_checkbox">Campanha Privada</label>
                        <div class="form-text text-muted">
                            Se ativado, jogadores precisarão de um código para entrar ou solicitar entrada.
                        </div>
                    </div>

                    {{-- NOVO CAMPO: Código de Convite (Visível apenas se for privada) --}}
                    @php
                        // Prioriza o valor do 'old', senão usa o valor atual da campanha
                        $currentCode = old('codigo_convite', $campanha->codigo_convite);
                        // Determina se o campo deve estar visível por padrão (se o checkbox estiver marcado)
                        $showCodeField = old('privada', $campanha->privada) ? 'block' : 'none';
                    @endphp
                    <div class="mb-3" id="codigoConviteGroup" style="display: {{ $showCodeField }};">
                        <label class="form-label">Código de Convite (Opcional)</label>
                        <input type="text" name="codigo_convite" class="form-control" maxlength="10"
                            value="{{ $currentCode }}" placeholder="Deixe em branco para gerar automaticamente">
                        <div class="form-text text-light-50">O código deve ter até 10 caracteres. Se for removido e a campanha for privada, um novo código será gerado ao salvar.</div>
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

{{-- Script para toggle do campo Código de Convite --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const privadaCheckbox = document.getElementById('privada_checkbox');
        const codigoConviteGroup = document.getElementById('codigoConviteGroup');

        function toggleCodigoConvite() {
            if (privadaCheckbox.checked) {
                // Se a campanha for marcada como privada, mostra o campo de código
                codigoConviteGroup.style.display = 'block';
            } else {
                // Se for marcada como pública, esconde o campo de código
                codigoConviteGroup.style.display = 'none';
            }
        }

        // Adiciona o listener para mudança no checkbox
        privadaCheckbox.addEventListener('change', toggleCodigoConvite);

        // Garante o estado inicial (importante para o carregamento da página)
        toggleCodigoConvite();
    });
</script>
@endsection
