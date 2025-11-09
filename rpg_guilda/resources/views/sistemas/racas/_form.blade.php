@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Nome</label>
        <input type="text" name="nome" value="{{ old('nome', $raca->nome ?? '') }}" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Página</label>
        <input type="text" name="pagina" value="{{ old('pagina', $raca->pagina ?? '') }}" class="form-control">
    </div>

    <div class="col-12">
        <label class="form-label fw-bold">Descrição</label>
        <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $raca->descricao ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <h6 class="fw-bold mt-3">Atributos Bônus</h6>
        <div class="row g-2">
            @foreach(['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'] as $atributo)
                <div class="col-md-2">
                    <label class="form-label text-capitalize">{{ ucfirst($atributo) }}</label>
                    <input type="number" name="{{ $atributo }}_bonus" value="{{ old($atributo . '_bonus', $raca->{$atributo . '_bonus'} ?? 0) }}" class="form-control">
                </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('sistemas.racas.index', $sistema->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <button type="submit" class="btn btn-success ms-2">
            <i class="bi bi-save"></i> Salvar
        </button>
    </div>
</div>
