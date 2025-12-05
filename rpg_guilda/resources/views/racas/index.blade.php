@extends('layouts.app')

@section('title', "Raças - {$sistema->nome}")

@section('content')
<div class="container py-4">

    {{-- Voltar --}}
    <a href="{{ route('sistemas.show', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar ao Sistema
    </a>

    {{-- Cabeçalho + Ações --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold text-primary">
            <i class="bi bi-people-fill me-2"></i> Raças de {{ $sistema->nome }}
        </h1>

        <div class="d-flex gap-2 flex-wrap">
            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('sistemas.racas.create', $sistema->id) }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Nova Raça
                </a>
            @endif
        </div>
    </div>

    {{-- Barra de pesquisa --}}
    <form action="{{ route('sistemas.racas.index', $sistema->id) }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Pesquisar raças..." value="{{ request('search') }}">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> Pesquisar</button>
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

    {{-- Lista de Raças --}}
    <div class="row g-4">
        @forelse($racas as $raca)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm hover-lift h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold">{{ $raca->nome }}</h5>

                        @if($raca->descricao)
                            <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($raca->descricao, 100) }}</p>
                        @endif

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <a href="{{ route('sistemas.racas.show', [$sistema->id, $raca->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>

                            @if(auth()->check() && auth()->user()->is_admin)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('sistemas.racas.edit', [$sistema->id, $raca->id]) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('sistemas.racas.destroy', [$sistema->id, $raca->id]) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta raça?');">
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
                <h4 class="mt-3 text-muted">Nenhuma raça cadastrada.</h4>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    @if(method_exists($racas, 'links'))
        <div class="mt-4">
            {{ $racas->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    .hover-lift:hover {
        transform: translateY(-3px);
        transition: transform 0.2s;
    }
</style>
@endpush
