@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">✏️ Criar Classe para o Sistema: {{ $sistema->nome }}</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('sistemas.classes.store', $sistema->id) }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Nome da Classe -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nome da Classe</label>
                        <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
                    </div>

                    <!-- Descrição da Classe -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3">{{ old('descricao') }}</textarea>
                    </div>

                    <!-- Atributos da Classe -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Força</label>
                        <input type="number" name="forca" class="form-control" value="{{ old('forca') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Destreza</label>
                        <input type="number" name="destreza" class="form-control" value="{{ old('destreza') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Constituição</label>
                        <input type="number" name="constituicao" class="form-control" value="{{ old('constituicao') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Inteligência</label>
                        <input type="number" name="inteligencia" class="form-control" value="{{ old('inteligencia') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Sabedoria</label>
                        <input type="number" name="sabedoria" class="form-control" value="{{ old('sabedoria') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Carisma</label>
                        <input type="number" name="carisma" class="form-control" value="{{ old('carisma') }}">
                    </div>

                    <!-- Atributos adicionais -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Agilidade</label>
                        <input type="number" name="agilidade" class="form-control" value="{{ old('agilidade') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Intelecto</label>
                        <input type="number" name="intelecto" class="form-control" value="{{ old('intelecto') }}">
                    </div>

                    <!-- Aspectos e poderes especiais -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Aspectos</label>
                        <textarea name="aspects" class="form-control" rows="3">{{ old('aspects') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Poderes</label>
                        <textarea name="poderes" class="form-control" rows="3">{{ old('poderes') }}</textarea>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('sistemas.classes.index', $sistema->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-success ms-2">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
