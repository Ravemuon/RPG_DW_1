@extends('layouts.app')

@section('title', "Classes - {$sistema->nome}")

@section('content')
<div class="container py-4">

    {{-- Botão voltar --}}
    <a href="{{ route('sistemas.show', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar ao Sistema
    </a>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold text-primary">
            <i class="bi bi-award-fill me-2"></i> Classes de {{ $sistema->nome }}
        </h1>

        <div class="d-flex gap-2 flex-wrap">

            {{-- Search --}}
            <form action="{{ route('sistemas.classes.index', $sistema->id) }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Pesquisar classe..." value="{{ $search ?? '' }}">
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
            </form>

            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('sistemas.classes.create', $sistema->id) }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Nova Classe
                </a>
            @endif
        </div>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Lista de Classes --}}
    <div class="row g-4">
        @forelse($classes as $classe)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm hover-lift">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ $classe->nome }}</h5>
                        @if($classe->descricao)
                            <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($classe->descricao, 100) }}</p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('sistemas.classes.show', [$sistema->id, $classe->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>

                            @if(auth()->check() && auth()->user()->is_admin)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('sistemas.classes.edit', [$sistema->id, $classe->id]) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('sistemas.classes.destroy', [$sistema->id, $classe->id]) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta classe?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Nenhuma classe cadastrada.</h4>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $classes->links() }}
    </div>

</div>
@endsection
