<form action="{{ route('sistemas.pericias.store', $sistema) }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="nome" class="form-label fw-bold">Nome da Perícia</label>
        <input type="text" name="nome" id="nome" class="form-control"
               value="{{ old('nome', $pericia->nome ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="sistemaRPG" class="form-label fw-bold">Sistema</label>
        <input type="text" name="sistemaRPG" id="sistemaRPG" class="form-control"
               value="{{ old('sistemaRPG', $pericia->sistemaRPG ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="automatica" class="form-label fw-bold">Automática?</label>
        <select name="automatica" id="automatica" class="form-select">
            <option value="0" {{ old('automatica', $pericia->automatica ?? 0) == 0 ? 'selected' : '' }}>Não</option>
            <option value="1" {{ old('automatica', $pericia->automatica ?? 0) == 1 ? 'selected' : '' }}>Sim</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="formula" class="form-label fw-bold">Fórmula (ex: Destreza + 2)</label>
        <input type="text" name="formula" id="formula" class="form-control"
               value="{{ old('formula', $pericia->formula ?? '') }}">
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> Salvar
        </button>
    </div>
</form>
