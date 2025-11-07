@extends('layouts.app')

@section('title', $personagem->nome)

@section('content')
<div class="container py-5">
    <h2 class="text-warning">{{ $personagem->nome }}</h2>
    <p><strong>Raça:</strong> {{ $personagem->raca->nome ?? 'Sem raça' }}</p>
    <p><strong>Classe:</strong> {{ $personagem->classe ?? 'Sem classe' }}</p>
    <p><strong>Descrição:</strong> {{ $personagem->descricao ?? 'Sem descrição' }}</p>

    <a href="{{ route('campanhas.show', $personagem->campanha_id) }}" class="btn btn-outline-light mt-3">⬅️ Voltar para a campanha</a>
</div>
@endsection
