@extends('layouts.app')

@section('content')
<style>
    /* Estilo customizado para o efeito de card, se o Tailwind não estiver disponível */
    .transform-on-hover {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .transform-on-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    /* Estilos de botão aprimorados */
    .btn-action {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-action i {
        font-size: 1.2rem;
    }
</style>

<div class="container py-4">

    <!-- Cabeçalho com título e botões -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold m-0">📚 Sistemas de RPG</h1>

        <div class="d-flex gap-2 flex-wrap">
            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('sistemas.create') }}" class="btn btn-primary btn-action">
                    <i class="bi bi-plus-circle"></i> Novo Sistema
                </a>
            @endif

            <a href="{{ route('sistemas.exportar-pdf') }}" target="_blank" class="btn btn-danger btn-action">
                <i class="bi bi-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    <!-- Mensagem de sucesso -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VISUALIZAÇÃO ADMIN (TABELA E CARDS) --}}
    @if(auth()->check() && auth()->user()->is_admin)
        <!-- Tabela para gerenciamento (Admin) -->
        <h2 class="fs-5 fw-bold mb-3 border-bottom pb-2 text-dark">
            <i class="bi bi-table me-1"></i> Gerenciamento (Tabela)
        </h2>

        {{-- Incluir partial para tabela de sistemas --}}
        @include('sistemas.partials._tabela_admin', ['sistemas' => $sistemas])

        <!-- Cards detalhados para administração -->
        <h2 class="fs-5 fw-bold mt-5 mb-3 border-bottom pb-2 text-primary">
            <i class="bi bi-grid-fill me-1"></i> Visão de Cards (Detalhada)
        </h2>
        <div class="row g-4 mb-4">
            @forelse ($sistemas as $sistema)
                @include('sistemas.partials._sistema', ['sistema' => $sistema])
            @empty
                <div class="col-12">
                    <div class="text-center p-5 bg-light rounded shadow-sm">
                        <i class="bi bi-emoji-frown fs-3 text-muted"></i>
                        <h4 class="mt-3 text-muted">Nenhum sistema de RPG encontrado.</h4>
                    </div>
                </div>
            @endforelse
        </div>
    @else
        <!-- Visão para usuários comuns -->
        <h2 class="fs-5 fw-bold mb-3 border-bottom pb-2 text-dark">
            <i class="bi bi-dice-3-fill me-1"></i> Explorar Sistemas
        </h2>

        <div class="row g-4 mb-4">
            @forelse ($sistemas as $sistema)
                @include('sistemas.partials._sistema', ['sistema' => $sistema])
            @empty
                <div class="col-12">
                    <div class="text-center p-5 bg-light rounded shadow-sm">
                        <i class="bi bi-emoji-frown fs-3 text-muted"></i>
                        <h4 class="mt-3 text-muted">Nenhum sistema de RPG disponível para visualização.</h4>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

</div>
@endsection
