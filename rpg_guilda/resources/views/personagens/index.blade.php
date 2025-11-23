@extends('layouts.app')

@section('title', 'Meus Personagens')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">🧙 Meus Personagens</h1>

    <a href="{{ route('personagens.create') }}" class="btn btn-primary mb-3">Novo Personagem</a>

    @if($personagens->count())
        <div class="list-group">
            @foreach($personagens as $p)
                <a href="{{ route('personagens.show', $p->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    {{ $p->nome }} - {{ $p->classe }} ({{ $p->raca->nome ?? '—' }})
                    <span class="badge bg-secondary rounded-pill">{{ $p->sistema->nome ?? $p->sistema_rpg }}</span>
                </a>
            @endforeach
        </div>
    @else
        <div class="alert alert-secondary">Você ainda não criou nenhum personagem.</div>
    @endif
</div>
@endsection
