@extends('layouts.app')

@section('content')
<style>
    /* Estilo customizado para o efeito de card, se o Tailwind não estiver disponível */
    .transform-on-hover {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .transform-on-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; /* Sombra mais destacada no hover */
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

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bolder m-0 text-primary">
            <i class="bi bi-book me-2"></i> Sistemas de RPG
        </h1>

        <div class="d-flex gap-2 flex-wrap">
            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('sistemas.create') }}" class="btn btn-success btn-action shadow-sm">
                    <i class="bi bi-plus-lg"></i> Novo Sistema
                </a>
            @endif

            <a href="{{ route('sistemas.exportar-pdf') }}" target="_blank" class="btn btn-secondary btn-action shadow-sm">
                <i class="bi bi-filetype-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- VISUALIZAÇÃO ADMIN: Tabela de Gerenciamento --}}
    @if(auth()->check() && auth()->user()->is_admin)
        <hr>
        <h2 class="fs-5 fw-bold mb-3 mt-4 pb-2 text-dark">
            <i class="bi bi-clipboard-data me-1 text-info"></i> Gerenciamento Rápido
        </h2>

        <div class="card shadow-sm mb-5">
            <div class="card-body p-0">
                {{-- Incluir partial para tabela de sistemas --}}
                @include('sistemas.partials._tabela_admin', ['sistemas' => $sistemas])
            </div>
        </div>
        <hr>
    @endif

    <h2 class="fs-5 fw-bold {{ auth()->check() && auth()->user()->is_admin ? 'mt-4' : 'mt-2' }} mb-3 pb-2 text-dark">
        <i class="bi bi-dice-5-fill me-1 text-primary"></i>
        @if(auth()->check() && auth()->user()->is_admin)
            Visão de Cards (Detalhada)
        @else
            Explorar Sistemas
        @endif
    </h2>

    <div class="row g-4 mb-4">
        @forelse ($sistemas as $sistema)
            {{-- O partial _sistema deve ser ajustado para exibir as novas colunas JSON (atributos, recursos, etc.) --}}
            @include('sistemas.partials._sistema', ['sistema' => $sistema])
        @empty
            <div class="col-12">
                <div class="text-center p-5 bg-light rounded-3 border">
                    <i class="bi bi-emoji-frown-fill fs-3 text-muted"></i>
                    <h4 class="mt-3 text-muted">Nenhum sistema de RPG encontrado.</h4>
                    @if(auth()->check() && auth()->user()->is_admin)
                        <p class="text-muted mb-0">Clique em **Novo Sistema** para começar.</p>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
