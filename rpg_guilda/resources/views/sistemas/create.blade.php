@extends('layouts.app')

@section('title', 'Criar Sistema de RPG')

@section('content')
<div class="container py-4">

    <div class="card shadow-lg border-0 rounded">

        {{-- Cabeçalho --}}
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4 class="m-0"><i class="bi bi-gear-fill me-2"></i>Criar Novo Sistema</h4>
            <a href="{{ route('sistemas.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card-body">

            {{-- ERROS --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <strong><i class="bi bi-exclamation-triangle-fill"></i> Corrija os erros abaixo:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sistemas.store') }}" method="POST">
                @csrf

                {{-- Nome --}}
                <div class="mb-3">
                    <label class="fw-semibold">Nome do Sistema <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
                </div>

                {{-- Descrição --}}
                <div class="mb-3">
                    <label class="fw-semibold">Descrição</label>
                    <textarea name="descricao" rows="3" class="form-control">{{ old('descricao') }}</textarea>
                </div>

                {{-- Campos básicos --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Foco</label>
                        <input type="text" name="foco" value="{{ old('foco') }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Mecânica Principal</label>
                        <input type="text" name="mecanica_principal" value="{{ old('mecanica_principal') }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Complexidade</label>

                        <select name="complexidade" class="form-select">
                            <option value="" disabled {{ old('complexidade') ? '' : 'selected' }}>Selecione...</option>
                            <option value="Básico"   {{ old('complexidade') == 'Básico'   ? 'selected' : '' }}>Básico (Simples)</option>
                            <option value="Médio"    {{ old('complexidade') == 'Médio'    ? 'selected' : '' }}>Médio (Equilibrado)</option>
                            <option value="Avançado" {{ old('complexidade') == 'Avançado' ? 'selected' : '' }}>Avançado (Profundo)</option>
                        </select>
                    </div>
                </div>


                {{-- 🔥 ATRIBUTOS DINÂMICOS --}}
                <hr>
                <label class="fw-bold mb-2 d-block">Atributos do Sistema</label>

                <div id="areaAtributos">
                    @php $old_attr = old('atributos', []); @endphp

                    @if(count($old_attr))
                        @foreach($old_attr as $chave => $nome)
                        <div class="row g-2 mb-2 atributo-box">
                            <div class="col-md-5">
                                <input type="text" name="atributos_chave[]" class="form-control" placeholder="Chave (FOR, DES)" value="{{ $chave }}">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="atributos_nome[]" class="form-control" placeholder="Nome (Força, Destreza)" value="{{ $nome }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-outline-danger w-100 removeAtributo">X</button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <button type="button" id="addAtributo" class="btn btn-sm btn-outline-primary mb-3">
                    + Adicionar atributo
                </button>


                {{-- JSON REGRAS OPCIONAIS --}}
                <hr>
                <div class="mb-3">
                    <label class="fw-semibold">Regras Opcionais (JSON)</label>
                    <textarea name="regras_opcionais" id="jsonInput" rows="3" class="form-control"
                              placeholder='{"critico_duplo": true}'>{{ old('regras_opcionais') }}</textarea>

                    <small class="text-muted">Se vazio → sistema converte para `{}` automaticamente.</small>
                    <div id="jsonError" class="text-danger fw-bold" style="display:none">JSON inválido ❌</div>
                </div>

                {{-- Fórmula PV --}}
                <div class="mb-3">
                    <label class="fw-semibold">Fórmula de Pontos de Vida</label>
                    <input type="text" name="formula_pontos_vida" class="form-control"
                           value="{{ old('formula_pontos_vida') }}" placeholder="Ex: 10 + modificador">
                </div>

                {{-- Sanidade --}}
                <div class="form-check mb-4">
                    <input type="checkbox" name="usa_sanidade" value="1" class="form-check-input" id="san">
                    <label for="san" class="fw-semibold form-check-label">Sistema utiliza sanidade?</label>
                </div>


                {{-- BOTÕES --}}
                <div class="text-end mt-3 d-flex gap-2 justify-content-end">
                    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-success">Salvar Sistema</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ========== JAVASCRIPT ========== --}}
<script>
    // Adicionar atributo
    document.getElementById('addAtributo').addEventListener('click', () => {
        document.getElementById('areaAtributos').insertAdjacentHTML('beforeend', `
            <div class="row g-2 mb-2 atributo-box">
                <div class="col-md-5">
                    <input type="text" name="atributos_chave[]" class="form-control" placeholder="FOR">
                </div>
                <div class="col-md-5">
                    <input type="text" name="atributos_nome[]" class="form-control" placeholder="Força">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-danger w-100 removeAtributo">X</button>
                </div>
            </div>
        `);
    });

    // Remover atributo
    document.addEventListener('click', e => {
        if(e.target.closest('.removeAtributo')){
            e.target.closest('.atributo-box').remove();
        }
    });

    // JSON validation live
    document.getElementById('jsonInput').addEventListener('input', e => {
        const txt = e.target.value;
        try { if(txt.trim() !== "") JSON.parse(txt); document.getElementById('jsonError').style.display="none"; }
        catch { document.getElementById('jsonError').style.display="block"; }
    });
</script>

@endsection
