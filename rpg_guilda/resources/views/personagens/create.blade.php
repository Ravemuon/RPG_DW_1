@extends('layouts.app')

@section('title', 'Criar Novo Personagem')

@section('content')
<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h1 class="h3 mb-0">Criação de Personagem</h1>
            @if($campanha)
                <p class="mb-0">Campanha: {{ $campanha->nome }} | Sistema: {{ $campanha->sistema->nome }}</p>
            @else
                <p class="mb-0">Crie um novo personagem</p>
            @endif
        </div>

        <div class="card-body">
            @if($campanha && $campanha->sistema)
            <form action="{{ route('personagens.store.step1') }}" method="POST">
                @csrf

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Dados Básicos do Personagem</h5>
                            </div>
                            <div class="card-body p-4">
                                <input type="hidden" name="campanha_id" value="{{ $campanha->id }}">
                                <input type="hidden" name="sistema_id" value="{{ $campanha->sistema_id }}">

                                <div class="mb-4">
                                    <label for="nome" class="form-label fw-bold">Nome do Personagem</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
                                           class="form-control form-control-lg" required
                                           placeholder="Digite o nome do seu personagem">
                                </div>

                                <div class="mb-4">
                                    <label for="descricao" class="form-label fw-bold">Descrição</label>
                                    <textarea name="descricao" id="descricao" class="form-control"
                                              rows="3" placeholder="Descreva fisicamente seu personagem">{{ old('descricao') }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="historia" class="form-label fw-bold">História</label>
                                    <textarea name="historia" id="historia" class="form-control"
                                              rows="3" placeholder="Conte a história do seu personagem">{{ old('historia') }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="personalidade" class="form-label fw-bold">Personalidade</label>
                                    <textarea name="personalidade" id="personalidade" class="form-control"
                                              rows="3" placeholder="Descreva a personalidade do seu personagem">{{ old('personalidade') }}</textarea>
                                </div>

                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-3 fa-2x"></i>
                                        <div>
                                            <strong>Informação:</strong> Você poderá editar essas informações posteriormente.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                Próximo Passo <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @else
            <div class="alert alert-danger">
                <h4 class="alert-heading">Erro!</h4>
                <p>Não foi possível criar o personagem porque a campanha não possui um sistema definido.</p>
                <a href="{{ route('campanhas.index') }}" class="btn btn-danger">Voltar para Campanhas</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
