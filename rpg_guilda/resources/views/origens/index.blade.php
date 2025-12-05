@extends('layouts.app')

@section('title', "Origens - {$sistema->nome}")

@section('content')
<div class="container py-4">

    <a href="{{ route('sistemas.show', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar ao Sistema
    </a>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold text-primary">
            <i class="bi bi-bookmarks-fill me-2"></i> Origens de {{ $sistema->nome }}
        </h1>

        <div class="d-flex gap-2 flex-wrap">
            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('sistemas.origens.create', $sistema->id) }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Nova Origem
                </a>
            @endif
        </div>
    </div>

    {{-- Search --}}
    <form action="{{ route('sistemas.origens.index', $sistema->id) }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Pesquisar origem..." value="{{ $search ?? '' }}">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    {{-- Flash success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Lista de Origens --}}
    <div class="row g-4">
        @forelse($origens as $origem)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm hover-lift">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ $origem->nome }}</h5>
                        @if($origem->descricao)
                            <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($origem->descricao, 100) }}</p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('sistemas.origens.show', [$sistema->id, $origem->id]) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i> Ver
                            </a>

                            @if(auth()->check() && auth()->user()->is_admin)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('sistemas.origens.edit', [$sistema->id, $origem->id]) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form action="{{ route('sistemas.origens.destroy', [$sistema->id, $origem->id]) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta origem?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Excluir
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
                <h4 class="mt-3 text-muted">Nenhuma origem cadastrada.</h4>
            </div>
        @endforelse
    </div>

</div>
@endsection
