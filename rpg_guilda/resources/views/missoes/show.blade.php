@extends('layouts.app')

@section('title', 'Missões da Campanha')

@section('content')
<div class="container py-5">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🎮 Missões da Campanha: {{ $campanha->nome }}</h2>

        {{-- Botões de ação --}}
        <div>
            <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i> Criar Nova Missão
            </a>
            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Voltar à Campanha
            </a>
        </div>
    </div>

    {{-- Mensagens de sucesso --}}
    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Lista de Missões --}}
    <div class="row g-3">
        @foreach ($missoes as $missao)
            <div class="col-md-4">
                <div class="card bg-dark text-light shadow-sm">
                    <div class="card-header bg-info text-dark fw-bold">
                        <h5 class="mb-0">{{ $missao->titulo }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">{{ Str::limit($missao->descricao, 100, '...') }}</p>
                        <p class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge
                                @if($missao->status == 'pendente') bg-warning
                                @elseif($missao->status == 'em_andamento') bg-primary
                                @elseif($missao->status == 'concluida') bg-success
                                @else bg-secondary @endif">
                                {{ ucfirst($missao->status) }}
                            </span>
                        </p>

                        {{-- Botão de ver detalhes da missão --}}
                        <a href="{{ route('missoes.show', [$campanha->id, $missao->id]) }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-eye me-2"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
