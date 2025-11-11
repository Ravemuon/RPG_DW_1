@extends('layouts.app')

@section('title', 'Criar Campanha')

@section('content')
<div class="card bg-dark text-light border-secondary shadow-lg">
    <div class="card-header d-flex align-items-center gap-2">
        {{-- Ícone SVG --}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" width="24" height="24" class="text-warning me-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.5 17.5L7 10l-2 2-2-2 2-2L5.5 6.5 10 2l2 2-2 2 7.5 7.5 2-2 2 2-2 2 1.5 1.5a2.121 2.121 0 010 3L19 23l-2-2 1.5-1.5a.5.5 0 000-.7L17.5 18l-3 3-2-2 2-2z" />
        </svg>
        <h1 class="h4 mb-0 fw-bold">Criar Nova Campanha</h1>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('campanhas.store') }}">
            @csrf

            {{-- Nome da campanha --}}
            <div class="mb-3">
                <label for="nome" class="form-label fw-semibold">Nome da Campanha</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
                       class="container py-2"
                       placeholder="Ex: Crônicas de Ravemuon" required>
                @error('nome') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Sistema --}}
            <div class="mb-3">
                <label for="sistema_id" class="form-label fw-semibold">Sistema</label>
                <select name="sistema_id" id="sistema_id"
                        class="container py-2" required>
                    <option value="">Selecione um sistema...</option>
                    @foreach ($sistemas as $sistema)
                        <option value="{{ $sistema->id }}" {{ old('sistema_id') == $sistema->id ? 'selected' : '' }}>
                            {{ $sistema->nome }}
                        </option>
                    @endforeach
                </select>
                @error('sistema_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Descrição --}}
            <div class="mb-3">
                <label for="descricao" class="form-label fw-semibold">Descrição</label>
                <textarea name="descricao" id="descricao" rows="4"
                          class="container py-2"
                          placeholder="Fale brevemente sobre a campanha...">{{ old('descricao') }}</textarea>
                @error('descricao') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select name="status" id="status"
                        class="container py-3" required>
                    <option value="ativa" {{ old('status') === 'ativa' ? 'selected' : '' }}>Ativa</option>
                    <option value="inativa" {{ old('status') === 'inativa' ? 'selected' : '' }}>Inativa</option>
                </select>
                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Privada --}}
            <div class="form-check form-switch bg-dark border rounded p-3 mb-3">
                <input class="container py-2" type="checkbox" name="privada" id="privada"
                       value="1" {{ old('privada') ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="privada">Campanha Privada?</label>
                <div class="small text-secondary">Apenas convidados poderão participar.</div>
            </div>

            {{-- Código de convite (opcional) --}}
            <div id="codigo-container" class="{{ old('privada') ? '' : 'd-none' }}">
                <label for="codigo_convite" class="form-label fw-semibold">Código de Convite (opcional)</label>
                <input type="text" name="codigo_convite" id="codigo_convite"
                       value="{{ old('codigo_convite') }}"
                       class="form-control bg-dark text-light border-secondary"
                       maxlength="10" placeholder="Ex: AB12CD">
                <div class="small text-secondary mt-1">Deixe em branco para gerar automaticamente.</div>
                @error('codigo_convite') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Botões --}}
            <div class="text-end mt-4">
                <a href="{{ route('campanhas.minhas') }}" class="btn btn-secondary me-2">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-warning fw-semibold text-dark">
                    Criar Campanha
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script para mostrar/esconder o campo de convite --}}
<script>
document.getElementById('privada').addEventListener('change', function() {
    document.getElementById('codigo-container').classList.toggle('d-none', !this.checked);
});
</script>
@endsection
