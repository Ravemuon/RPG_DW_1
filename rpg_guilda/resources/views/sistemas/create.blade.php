@extends('layouts.app')

@section('title', 'Criar Novo Sistema')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">⚙️ Criar Novo Sistema</h4>
            <a href="{{ route('sistemas.index') }}" class="btn btn-outline-light btn-sm">
                ⬅️ Voltar
            </a>
        </div>

        <div class="card-body">
            {{-- Mensagens de erro --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6><strong>⚠️ Ocorreram alguns erros:</strong></h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulário de criação --}}
            <form action="{{ route('sistemas.store') }}" method="POST">
                @csrf

                {{-- Nome --}}
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome do Sistema <span class="text-danger">*</span></label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" class="form-control" required>
                </div>

                {{-- Descrição --}}
                <div class="mb-3">
                    <label for="descricao" class="form-label fw-semibold">Descrição</label>
                    <textarea name="descricao" id="descricao" rows="4" class="form-control">{{ old('descricao') }}</textarea>
                </div>

                {{-- Foco / Mecânica / Complexidade --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="foco" class="form-label fw-semibold">Foco</label>
                        <input type="text" name="foco" id="foco" value="{{ old('foco') }}" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="mecanica_principal" class="form-label fw-semibold">Mecânica Principal</label>
                        <input type="text" name="mecanica_principal" id="mecanica_principal" value="{{ old('mecanica_principal') }}" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="complexidade" class="form-label fw-semibold">Complexidade</label>
                        <input type="text" name="complexidade" id="complexidade" value="{{ old('complexidade') }}" class="form-control">
                    </div>
                </div>

                {{-- Máximo de atributos --}}
                <div class="mb-3">
                    <label for="max_atributos" class="form-label fw-semibold">Quantidade Máxima de Atributos <span class="text-danger">*</span></label>
                    <input type="number" name="max_atributos" id="max_atributos" min="1" max="6" value="{{ old('max_atributos', 6) }}" class="form-control" required>
                    <div class="form-text">Escolha entre 1 e 6 atributos principais.</div>
                </div>

                {{-- Nomes dos atributos --}}
                <div class="row">
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="col-md-6 mb-3">
                            <label for="atributo{{ $i }}_nome" class="form-label fw-semibold">Atributo {{ $i }}</label>
                            <input type="text" name="atributo{{ $i }}_nome" id="atributo{{ $i }}_nome" value="{{ old('atributo' . $i . '_nome') }}" class="form-control">
                        </div>
                    @endfor
                </div>

                {{-- Regras opcionais --}}
                <div class="mb-3">
                    <label for="regras_opcionais" class="form-label fw-semibold">Regras Opcionais (JSON)</label>
                    <textarea name="regras_opcionais" id="regras_opcionais" rows="3" class="form-control" placeholder='Exemplo: {"critico_duplo": true, "iniciativa_variavel": false}'>{{ old('regras_opcionais') }}</textarea>
                </div>

                {{-- Página / Fonte --}}
                <div class="mb-3">
                    <label for="pagina" class="form-label fw-semibold">Página / Fonte</label>
                    <input type="text" name="pagina" id="pagina" value="{{ old('pagina') }}" class="form-control">
                </div>

                {{-- Botões --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary">
                        ❌ Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        💾 Salvar Sistema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
