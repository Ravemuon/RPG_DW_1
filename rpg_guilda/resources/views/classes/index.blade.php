@extends('layouts.app')

@section('title', 'Classes de RPG')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Classes de RPG</h2>

    <a href="{{ route('classes.create') }}" class="btn btn-success mb-3">✨ Criar Nova Classe</a>

    @if($classes->count())
        <div class="list-group">
            @foreach($classes as $classe)
                <a href="{{ route('classes.show', $classe->id) }}" class="list-group-item list-group-item-action">
                    {{ $classe->nome }} <span class="text-muted">({{ $classe->sistemaRPG }})</span>
                </a>
            @endforeach
        </div>
    @else
        <p>Nenhuma classe cadastrada.</p>
    @endif
</div>
@endsection
