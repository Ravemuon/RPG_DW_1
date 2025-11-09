@csrf

<div class="mb-3">
    <label for="titulo" class="form-label fw-bold">Título da Missão</label>
    <input type="text" name="titulo" id="titulo" class="form-control"
           value="{{ old('titulo', $missao->titulo ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="descricao" class="form-label fw-bold">Descrição</label>
    <textarea name="descricao" id="descricao" class="form-control">{{ old('descricao', $missao->descricao ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="recompensa" class="form-label fw-bold">Recompensa</label>
    <input type="text" name="recompensa" id="recompensa" class="form-control"
           value="{{ old('recompensa', $missao->recompensa ?? '') }}">
</div>

<div class="mb-3">
    <label for="status" class="form-label fw-bold">Status</label>
    <select name="status" id="status" class="form-select">
        <option value="pendente" {{ old('status', $missao->status ?? 'pendente') == 'pendente' ? 'selected' : '' }}>Pendente</option>
        <option value="em_andamento" {{ old('status', $missao->status ?? 'pendente') == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
        <option value="concluida" {{ old('status', $missao->status ?? 'pendente') == 'concluida' ? 'selected' : '' }}>Concluída</option>
        <option value="cancelada" {{ old('status', $missao->status ?? 'pendente') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
    </select>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <button type="submit" class="btn btn-success">
        <i class="bi bi-save"></i> Salvar
    </button>
</div>
