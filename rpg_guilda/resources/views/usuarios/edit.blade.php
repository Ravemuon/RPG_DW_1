@extends('layouts.app')

@section('title', 'Editar Campanha')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-warning mb-4">🛡️ Editar Campanha</h2>

    <form action="{{ route('campanhas.update', $campanha->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nome da campanha -->
        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Campanha</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $campanha->nome) }}" required>
        </div>

        <!-- Sistema (somente leitura) -->
        <div class="mb-3">
            <label for="sistemaRPG" class="form-label">Sistema RPG</label>
            <input type="text" class="form-control" id="sistemaRPG" value="{{ $campanha->sistemaRPG }}" readonly>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="4">{{ old('descricao', $campanha->descricao) }}</textarea>
        </div>

        <!-- Campanha privada -->
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="privada" name="privada" value="1" {{ $campanha->privada ? 'checked' : '' }}>
            <label class="form-check-label" for="privada">Campanha Privada</label>
        </div>

        <!-- Código de convite -->
        <div class="mb-3" id="codigoConviteDiv" style="display: {{ $campanha->privada ? 'block' : 'none' }};">
            <label for="codigo_convite" class="form-label">Código de Convite</label>
            <input type="text" class="form-control" id="codigo_convite" name="codigo_convite" value="{{ $campanha->codigo_convite }}" readonly>
        </div>

        <button type="submit" class="btn btn-warning mb-4">Atualizar Campanha</button>
    </form>

    <!-- Participantes -->
    <h4 class="fw-bold text-warning mt-5 mb-3">🎲 Participantes</h4>
    <div class="list-group mb-4">
        @foreach($campanha->jogadores as $jogador)
            <div class="list-group-item d-flex justify-content-between align-items-center bg-dark border-warning text-warning">
                {{ $jogador->nome }} <span class="badge bg-secondary">{{ $jogador->pivot->status }}</span>
                @if($jogador->id !== $campanha->criador_id)
                    <form action="{{ route('campanhas.remover-jogador', $campanha->id) }}" method="POST" class="mb-0">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                        <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Adicionar jogador -->
    <h5 class="text-warning mb-2">➕ Adicionar jogador</h5>
    <form action="{{ route('campanhas.adicionar-jogador', $campanha->id) }}" method="POST" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="text" name="user_id" class="form-control" placeholder="ID do usuário ou e-mail" required>
            <button type="submit" class="btn btn-success">Adicionar</button>
        </div>
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
