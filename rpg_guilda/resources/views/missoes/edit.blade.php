@extends('layouts.app')

{{-- Define se é 'Criar' ou 'Editar' --}}
@php
$isEdit = isset($personagem) && $personagem->id;
$title = $isEdit ? 'Editar Personagem: ' . $personagem->nome : 'Criar Novo Personagem';
// Se for edição, assume-se que o formulário está sendo usado como Step 1
// Na edição, ele salva os dados e redireciona para o overview.
// Na criação, ele salva e avança para o Step 2 (Raça, Classe, etc.).
$formAction = $isEdit ? route('personagens.update', $personagem->id) : route('personagens.store.step1');
$buttonText = $isEdit ? 'Salvar Alterações' : 'Próximo Passo';
$campanhaInfo = $campanha ?? ($isEdit ? $personagem->campanha : null); // Tenta obter a campanha
@endphp

@section('title', $title)

@section('content')

<div class="container my-5">
<div class="card shadow-lg border-0">
<div class="card-header bg-primary text-white">
<h1 class="h3 mb-0">{{ $title }}</h1>
@if($campanhaInfo)
<p class="mb-0">Campanha: {{ $campanhaInfo->nome }} | Sistema: {{ $campanhaInfo->sistema->nome ?? 'N/A' }}</p>
@else
<p class="mb-0">Preencha os dados básicos do seu personagem</p>
@endif
</div>

    <div class="card-body">
        {{-- Verifica se a campanha/sistema está disponível para criação --}}
        @if($campanhaInfo && $campanhaInfo->sistema)

        {{-- Configura a action e o método do formulário --}}
        <form action="{{ $formAction }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT') {{-- Simula o método PUT/PATCH para atualização no Laravel --}}
            @endif

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Dados Básicos do Personagem</h5>
                        </div>
                        <div class="card-body p-4">

                            {{-- Campos ocultos para campanha e sistema (necessários para criação ou referência) --}}
                            <input type="hidden" name="campanha_id" value="{{ $campanhaInfo->id }}">
                            <input type="hidden" name="sistema_id" value="{{ $campanhaInfo->sistema_id }}">

                            {{-- Nome do Personagem --}}
                            <div class="mb-4">
                                <label for="nome" class="form-label fw-bold">Nome do Personagem</label>
                                <input type="text" name="nome" id="nome"
                                       {{-- Tenta o old() primeiro, depois o valor do personagem --}}
                                       value="{{ old('nome', $personagem->nome ?? '') }}"
                                       class="form-control form-control-lg" required
                                       placeholder="Digite o nome do seu personagem">
                                @error('nome') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            {{-- Descrição --}}
                            <div class="mb-4">
                                <label for="descricao" class="form-label fw-bold">Descrição</label>
                                <textarea name="descricao" id="descricao" class="form-control" rows="3"
                                          placeholder="Descreva fisicamente seu personagem">{{ old('descricao', $personagem->descricao ?? '') }}</textarea>
                                @error('descricao') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            {{-- História --}}
                            <div class="mb-4">
                                <label for="historia" class="form-label fw-bold">História</label>
                                <textarea name="historia" id="historia" class="form-control" rows="3"
                                          placeholder="Conte a história do seu personagem">{{ old('historia', $personagem->historia ?? '') }}</textarea>
                                @error('historia') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            {{-- Personalidade --}}
                            <div class="mb-4">
                                <label for="personalidade" class="form-label fw-bold">Personalidade</label>
                                <textarea name="personalidade" id="personalidade" class="form-control" rows="3"
                                          placeholder="Descreva a personalidade do seu personagem">{{ old('personalidade', $personagem->personalidade ?? '') }}</textarea>
                                @error('personalidade') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                                    <div>
                                        <strong>Informação:</strong> {{ $isEdit ? 'As alterações serão salvas. Para editar a ficha completa, use a aba específica.' : 'Você passará para o próximo passo para definir a ficha técnica.' }}
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
                            {{ $buttonText }}
                            @if(!$isEdit) <i class="fas fa-arrow-right ms-2"></i> @else <i class="fas fa-save ms-2"></i> @endif
                        </button>
                    </div>
                </div>
            </div>
        </form>

        @else
        {{-- Mensagem de Erro/Campanha Indefinida --}}
        <div class="alert alert-danger">
            <h4 class="alert-heading">Erro!</h4>
            <p>Não foi possível acessar este formulário. Verifique se a campanha e o sistema estão definidos.</p>
            <a href="{{ route('campanhas.index') }}" class="btn btn-danger">Voltar para Campanhas</a>
        </div>
        @endif
    </div>
</div>


</div>
@endsection
