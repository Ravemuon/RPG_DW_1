@extends('layouts.app')

@section('title', "Raça - {$raca->nome}")

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-primary mb-4">
        <i class="bi bi-people-fill me-2"></i> {{ $raca->nome }}
    </h1>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            @if($raca->descricao)
                <p class="mb-3">{{ $raca->descricao }}</p>
            @endif

            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Tipo de Bônus:</strong> {{ ucfirst($raca->tipo_bonus) }}</li>
                <li class="list-group-item"><strong>Bônus Livre:</strong> {{ $raca->bonus_livre }}</li>
                @if($raca->modificadores_atributos)
                    <li class="list-group-item">
                        <strong>Modificadores de Atributos:</strong>
                        <pre class="mb-0">{{ json_encode($raca->modificadores_atributos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </li>
                @endif
                @if($raca->pagina)
                    <li class="list-group-item"><strong>Página:</strong> {{ $raca->pagina }}</li>
                @endif
            </ul>

            <a href="{{ route('sistemas.racas.index', $sistema->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('sistemas.racas.edit', [$sistema->id, $raca->id]) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
            @endif
        </div>
    </div>

</div>
@endsection
