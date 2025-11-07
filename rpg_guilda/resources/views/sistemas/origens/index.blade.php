@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">Origens do Sistema: {{ $sistema->nome }}</h1>
    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary mb-3">⬅ Voltar</a>

    @if($sistema->origens->isNotEmpty())
        <div class="list-group">
            @foreach($sistema->origens as $origem)
                <div class="list-group-item">
                    <h5>{{ $origem->nome }}</h5>
                    <p>{{ $origem->descricao ?? 'Sem descrição' }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">Nenhuma origem cadastrada para este sistema.</p>
    @endif
</div>
@endsection
