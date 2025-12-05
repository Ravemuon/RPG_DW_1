@csrf

<div class="mb-3">
    <label for="nome" class="form-label fw-bold">Nome da Origem</label>
    <input type="text" name="nome" id="nome" class="form-control"
           value="{{ old('nome', $origem->nome ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="descricao" class="form-label fw-bold">Descrição</label>
    <textarea name="descricao" id="descricao" class="form-control" rows="4">{{ old('descricao', $origem->descricao ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="bonus" class="form-label fw-bold">Bônus (ex: +2 Força, +1 Destreza)</label>
    <input type="text" name="bonus" id="bonus" class="form-control"
           value="{{ old('bonus', $origem->bonus ?? '') }}">
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <button type="submit" class="btn btn-success">
        <i class="bi bi-save"></i> Salvar
    </button>
</div>
