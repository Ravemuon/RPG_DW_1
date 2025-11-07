
<form action="{{ $action }}" method="POST">
    @csrf
    @method($method)

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops! Houve alguns erros de validação:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- Campo Nome --}}
        <div class="col-md-6 mb-3">
            <label for="nome" class="form-label">Nome do Sistema <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $sistema->nome) }}" required maxlength="100">
            @error('nome')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Max Atributos --}}
        <div class="col-md-6 mb-3">
            <label for="max_atributos" class="form-label">Máx. de Atributos <span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('max_atributos') is-invalid @enderror" id="max_atributos" name="max_atributos" value="{{ old('max_atributos', $sistema->max_atributos) }}" required min="1" max="6">
            @error('max_atributos')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Campo Descrição --}}
    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="3">{{ old('descricao', $sistema->descricao) }}</textarea>
        @error('descricao')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        {{-- Campo Foco --}}
        <div class="col-md-4 mb-3">
            <label for="foco" class="form-label">Foco Principal</label>
            <input type="text" class="form-control @error('foco') is-invalid @enderror" id="foco" name="foco" value="{{ old('foco', $sistema->foco) }}" maxlength="100">
            @error('foco')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Mecânica Principal --}}
        <div class="col-md-4 mb-3">
            <label for="mecanica_principal" class="form-label">Mecânica Principal</label>
            <input type="text" class="form-control @error('mecanica_principal') is-invalid @enderror" id="mecanica_principal" name="mecanica_principal" value="{{ old('mecanica_principal', $sistema->mecanica_principal) }}" maxlength="50">
            @error('mecanica_principal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Complexidade --}}
        <div class="col-md-4 mb-3">
            <label for="complexidade" class="form-label">Complexidade</label>
            <input type="text" class="form-control @error('complexidade') is-invalid @enderror" id="complexidade" name="complexidade" value="{{ old('complexidade', $sistema->complexidade) }}" maxlength="50">
            @error('complexidade')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Campos de Atributo (Iteração para simplificar) --}}
    <h4 class="mt-4">Nomes dos Atributos</h4>
    <div class="row">
        @for ($i = 1; $i <= 6; $i++)
            @php $attr_name = "atributo{$i}_nome"; @endphp
            <div class="col-md-4 mb-3">
                <label for="{{ $attr_name }}" class="form-label">Atributo {{ $i }}</label>
                <input type="text" class="form-control @error($attr_name) is-invalid @enderror" id="{{ $attr_name }}" name="{{ $attr_name }}" value="{{ old($attr_name, $sistema->$attr_name) }}" maxlength="50">
                @error($attr_name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endfor
    </div>

    {{-- Campo Regras Opcionais (JSON) --}}
    <div class="mb-3">
        <label for="regras_opcionais" class="form-label">Regras Opcionais (JSON)</label>
        {{-- O valor deve ser uma string JSON válida no input --}}
        <textarea class="form-control @error('regras_opcionais') is-invalid @enderror" id="regras_opcionais" name="regras_opcionais" rows="3">{{ old('regras_opcionais', json_encode($sistema->regras_opcionais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
        <small class="form-text text-muted">Deve ser um JSON válido (ex: `{"regra1": true, "regra2": "opcional"}`).</small>
        @error('regras_opcionais')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Campo Página --}}
    <div class="mb-3">
        <label for="pagina" class="form-label">Página de Referência</label>
        <input type="text" class="form-control @error('pagina') is-invalid @enderror" id="pagina" name="pagina" value="{{ old('pagina', $sistema->pagina) }}" maxlength="50">
        @error('pagina')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    <button type="submit" class="btn btn-success mt-3">Salvar Sistema</button>
    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
</form>
