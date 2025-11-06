@extends('layouts.app')

@section('title', 'Minhas Campanhas')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Minhas Campanhas</h2>

    <a href="{{ route('campanhas.create') }}" class="btn btn-success mb-3">✨ Criar Nova Campanha</a>

    @if($campanhas->count())
        <div class="list-group">
            @foreach($campanhas as $campanha)
                <a href="{{ route('campanhas.show', $campanha->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    {{ $campanha->nome }}
                    @if($campanha->privada)
                        <span class="badge bg-warning text-dark">Privada</span>
                    @endif
                </a>
            @endforeach
        </div>
    @else
        <p>Nenhuma campanha encontrada.</p>
    @endif
</div>
@endsection
