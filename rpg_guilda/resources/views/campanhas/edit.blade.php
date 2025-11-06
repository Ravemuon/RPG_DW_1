@extends('layouts.app')

@section('title', 'Editar Campanha')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-warning mb-4">🛡️ Editar Campanha</h2>

    <form action="{{ route('campanhas.update', $campanha->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Campanha</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $campanha->nome) }}" required>
        </div>

        <div class="mb-3">
            <label for="sistemaRPG" class="form-label">Sistema RPG</label>
            <select class="form-select" name="sistemaRPG" id="sistemaRPG" required>
                @php
                    $sistemas = ['D&D','Ordem Paranormal','Call of Cthulhu','Fate Core','Cypher System','Apocalypse World','Cyberpunk 2093 - Arkana-RPG'];
                @endphp
                @foreach($sistemas as $s)
                    <option value="{{ $s }}" {{ $campanha->sistemaRPG == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="4">{{ old('descricao', $campanha->descricao) }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="privada" name="privada" value="1" {{ $campanha->privada ? 'checked' : '' }}>
            <label class="form-check-label" for="privada">Campanha Privada</label>
        </div>

        <div class="mb-3" id="codigoConviteDiv" style="display: {{ $campanha->privada ? 'block' : 'none' }};">
            <label for="codigo_convite" class="form-label">Código de Convite</label>
            <input type="text" class="form-control" id="codigo_convite" name="codigo_convite" value="{{ $campanha->codigo_convite }}" readonly>
        </div>

        <button type="submit" class="btn btn-warning">Atualizar Campanha</button>
    </form>
</div>

<script>
    const privadaCheckbox = document.getElementById('privada');
    const codigoDiv = document.getElementById('codigoConviteDiv');
    const codigoInput = document.getElementById('codigo_convite');

    privadaCheckbox.addEventListener('change', function() {
        if(this.checked) {
            codigoDiv.style.display = 'block';
            if(!codigoInput.value) {
                codigoInput.value = Math.random().toString(36).substring(2, 8).toUpperCase();
            }
        } else {
            codigoDiv.style.display = 'none';
            codigoInput.value = '';
        }
    });
</script>
@endsection
