@extends('layouts.app')

@section('title', 'Edição de Personagem - 1. Coração do Personagem')

@section('content')

@php
    // Modo Edição: Carrega dados do objeto $personagem
    $nome = old('nome', $personagem->nome);
    $nivel = old('nivel', $personagem->nivel);
    $xp = old('xp', $personagem->xp);
    $descricao = old('descricao', $personagem->descricao);
    $historia = old('historia', $personagem->historia);
    $personalidade = old('personalidade', $personagem->personalidade);
    $imagem_path = old('imagem', $personagem->imagem); // Path da imagem no DB
    $pagina = old('pagina', $personagem->pagina);
    $ativo = old('ativo', $personagem->ativo);
@endphp

<div class="container my-5">
    <div class="card shadow-xl border-0">
        <div class="card-header bg-primary text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1"><i class="fas fa-heart me-2"></i> Editando Etapa 1: Coração do Personagem</h1>
                    <p class="mb-0 fs-6">
                        Personagem: <strong>{{ $personagem->nome }}</strong>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-primary fs-5 p-2">Nível Atual: {{ $nivel }}</span>
                </div>
            </div>

            <div class="progress mt-3" style="height: 10px;">
                <div class="progress-bar bg-light" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="d-block mt-1 text-light">Editando dados conceituais</small>
        </div>

        <div class="card-body p-5">
            {{-- Edição: Envia para rota de update do step 1 --}}
            <form action="{{ route('personagens.update.step1', $personagem->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Campo oculto para garantir que ativo sempre tenha valor --}}
                <input type="hidden" name="ativo" value="0">
                {{-- Campo oculto para persistir o caminho da imagem atual do DB --}}
                <input type="hidden" name="imagem" value="{{ $imagem_path }}">

                <div class="row g-5">
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                            <h3 class="mb-4 text-primary"><i class="fas fa-pencil-alt me-2"></i> Informações Essenciais</h3>

                            <div class="mb-4">
                                <label for="nome" class="form-label fs-5">
                                    <i class="fas fa-user-tag me-1"></i> Nome do Personagem <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                        class="form-control form-control-lg @error('nome') is-invalid @enderror"
                                        id="nome"
                                        name="nome"
                                        value="{{ $nome }}"
                                        required
                                        maxlength="100"
                                        placeholder="Ex: Elara, a Ladra Silenciosa">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="nivel" class="form-label fs-5">
                                        <i class="fas fa-chart-line me-1"></i> Nível
                                        <span class="badge bg-secondary ms-2">Opcional</span>
                                    </label>
                                    <input type="number"
                                            class="form-control form-control-lg @error('nivel') is-invalid @enderror"
                                            id="nivel"
                                            name="nivel"
                                            value="{{ $nivel }}"
                                            min="1"
                                            max="20"
                                            placeholder="Deixe em branco para nível 1">
                                    @error('nivel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Padrão: 1.</small>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="xp" class="form-label fs-5">
                                        <i class="fas fa-star me-1"></i> Experiência - XP
                                        <span class="badge bg-secondary ms-2">Opcional</span>
                                    </label>
                                    <input type="number"
                                            class="form-control form-control-lg @error('xp') is-invalid @enderror"
                                            id="xp"
                                            name="xp"
                                            value="{{ $xp }}"
                                            min="0"
                                            placeholder="Deixe em branco para 0 XP">
                                    @error('xp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Padrão: 0.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="imagem_upload" class="form-label fs-5">
                                        <i class="fas fa-image me-1"></i> Arte do Personagem
                                        <span class="badge bg-secondary ms-2">Opcional</span>
                                    </label>
                                    <input type="file"
                                            class="form-control @error('imagem_upload') is-invalid @enderror"
                                            id="imagem_upload"
                                            name="imagem_upload"
                                            accept="image/jpeg,image/png,image/jpg,image/webp">
                                    @error('imagem_upload')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Formatos aceitos: JPG, PNG, WEBP (máx. 2MB). Um novo upload substitui o atual.</small>

                                    @if ($imagem_path)
                                        <div class="mt-3 p-3 border rounded bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-image me-2 text-primary"></i>
                                                Imagem atual: <code>{{ basename($imagem_path) }}</code>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="remove_imagem" name="remove_imagem" value="1">
                                                <label class="form-check-label text-danger" for="remove_imagem">
                                                    <i class="fas fa-trash-alt me-1"></i> Remover
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label for="pagina" class="form-label fs-5">
                                        <i class="fas fa-book-open me-1"></i> Página de Referência
                                        <span class="badge bg-secondary ms-2">Opcional</span>
                                    </label>
                                    <input type="text"
                                            class="form-control @error('pagina') is-invalid @enderror"
                                            id="pagina"
                                            name="pagina"
                                            value="{{ $pagina }}"
                                            maxlength="50"
                                            placeholder="Ex: Pág. 45 do Livro do Jogador">
                                    @error('pagina')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Onde a ficha original pode ser encontrada.</small>
                                </div>
                            </div>

                            <div class="mb-4 pt-3 border-top">
                                <div class="form-check form-switch d-flex align-items-center">
                                    <input class="form-check-input me-3"
                                            type="checkbox"
                                            id="ativo_checkbox"
                                            name="ativo_checkbox_only"
                                            value="1"
                                            @checked($ativo == 1)>
                                    <label class="form-check-label fs-5 fw-bold" for="ativo_checkbox">
                                        <i class="fas fa-toggle-on me-1"></i> Personagem Ativo
                                    </label>
                                </div>
                                <small class="text-muted ms-5">Marque se o personagem está atualmente em jogo (Padrão: Ativo).</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm p-4">
                            <h3 class="mb-4 text-primary"><i class="fas fa-scroll me-2"></i> Descrições Detalhadas</h3>

                            <div class="mb-4">
                                <label for="descricao" class="form-label fs-5">
                                    <i class="fas fa-eye me-1"></i> Descrição e Aparência
                                    <span class="badge bg-secondary ms-2">Opcional</span>
                                </label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror"
                                            id="descricao"
                                            name="descricao"
                                            rows="4"
                                            maxlength="1000"
                                            placeholder="Foco na aparência física, vestimentas, traços marcantes, idade, altura, peso...">{{ $descricao }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">O que outros personagens veem ao olhar para você.</small>
                            </div>

                            <div class="mb-4">
                                <label for="historia" class="form-label fs-5">
                                    <i class="fas fa-history me-1"></i> História (Background)
                                    <span class="badge bg-secondary ms-2">Opcional</span>
                                </label>
                                <textarea class="form-control @error('historia') is-invalid @enderror"
                                            id="historia"
                                            name="historia"
                                            rows="5"
                                            placeholder="Descreva a origem, eventos importantes, motivações, objetivos de longo prazo...">{{ $historia }}</textarea>
                                @error('historia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">O que te trouxe até aqui e o que te move para frente.</small>
                            </div>

                            <div class="mb-4">
                                <label for="personalidade" class="form-label fs-5">
                                    <i class="fas fa-mask me-1"></i> Personalidade
                                    <span class="badge bg-secondary ms-2">Opcional</span>
                                </label>
                                <textarea class="form-control @error('personalidade') is-invalid @enderror"
                                            id="personalidade"
                                            name="personalidade"
                                            rows="3"
                                            maxlength="1000"
                                            placeholder="Traços de personalidade, maneirismos, ideais, valores, defeitos e ambições...">{{ $personalidade }}</textarea>
                                @error('personalidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Como você reage ao mundo e como o mundo te percebe.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i> Cancelar e Voltar
                    </a>

                    <button type="submit" class="btn btn-primary btn-lg shadow-lg">
                        Atualizar Coração do Personagem <i class="fas fa-check ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@endpush

@endsection
