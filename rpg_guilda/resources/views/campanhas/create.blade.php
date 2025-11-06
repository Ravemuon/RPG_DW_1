@extends('layouts.app')

@section('title', 'Criar Nova Campanha')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-warning mb-4">✨ Criar Nova Campanha</h2>

    <form action="{{ route('campanhas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Campanha</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" required>
        </div>

        <div class="mb-3">
            <label for="sistemaRPG" class="form-label">Sistema RPG</label>
            <select class="form-select" name="sistemaRPG" id="sistemaRPG" required>
                <option value="">-- Selecione --</option>
                <option value="D&D">D&D</option>
                <option value="Ordem Paranormal">Ordem Paranormal</option>
                <option value="Call of Cthulhu">Call of Cthulhu</option>
                <option value="Fate Core">Fate Core</option>
                <option value="Cypher System">Cypher System</option>
                <option value="Apocalypse World">Apocalypse World</option>
                <option value="Cyberpunk 2093 - Arkana-RPG">Cyberpunk 2093 - Arkana-RPG</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="4">{{ old('descricao') }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="privada" name="privada" value="1" {{ old('privada') ? 'checked' : '' }}>
            <label class="form-check-label" for="privada">Campanha Privada</label>
        </div>

        <div class="mb-3" id="codigoConviteDiv" style="display: none;">
            <label for="codigo_convite" class="form-label">Código de Convite (gerado automaticamente)</label>
            <input type="text" class="form-control" id="codigo_convite" name="codigo_convite" readonly>
        </div>

        <button type="submit" class="btn btn-warning">Criar Campanha</button>
    </form>
</div>

<script>
    const privadaCheckbox = document.getElementById('privada');
    const codigoDiv = document.getElementById('codigoConviteDiv');
    const codigoInput = document.getElementById('codigo_convite');

    privadaCheckbox.addEventListener('change', function() {
        if(this.checked) {
            codigoDiv.style.display = 'block';
            codigoInput.value = Math.random().toString(36).substring(2, 8).toUpperCase();
        } else {
            codigoDiv.style.display = 'none';
            codigoInput.value = '';
        }
    });
</script>
@endsection
