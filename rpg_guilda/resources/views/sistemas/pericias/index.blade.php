@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">Perícias do Sistema: {{ $sistema->nome }}</h1>
    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary mb-3">⬅ Voltar</a>

    @if($sistema->pericias->isNotEmpty())
        <div class="list-group">
            @foreach($sistema->pericias as $pericia)
                <div class="list-group-item">
                    <h5>{{ $pericia->nome }}</h5>
                    <p>{{ $pericia->descricao ?? 'Sem descrição' }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">Nenhuma perícia cadastrada para este sistema.</p>
    @endif
</div>
@endsection
