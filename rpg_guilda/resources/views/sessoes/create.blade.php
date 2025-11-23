@extends('layouts.app')

@section('title', "Nova Sessão - {$campanha->nome}")

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">📝 Criar Nova Sessão - {{ $campanha->nome }}</h2>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <form action="{{ route('sessoes.store', $campanha->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Título da Sessão</label>
                    <input type="text" name="titulo" class="form-control form-control-lg" placeholder="Digite o título..." value="{{ old('titulo') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Data e Hora</label>
                    <input type="datetime-local" name="data_hora" class="form-control form-control-lg" value="{{ old('data_hora') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Resumo</label>
                    <textarea name="resumo" class="form-control form-control-lg" rows="5" placeholder="Escreva um resumo da sessão...">{{ old('resumo') }}</textarea>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm">✅ Criar Sessão</button>
                    <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-secondary btn-lg rounded-pill shadow-sm">❌ Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
