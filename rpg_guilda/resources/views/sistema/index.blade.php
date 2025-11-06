@extends('layouts.app')

@section('title', 'Sistemas')

@section('content')
<div class="container py-5">
    <h2 class="text-center text-warning mb-4">🎲 Sistemas Disponíveis</h2>

    <div class="row justify-content-center">
        @foreach($sistemas as $sistema)
            <div class="col-md-4 mb-4">
                <div class="card p-4 bg-dark border-warning shadow">
                    <h5 class="fw-bold text-warning">{{ $sistema['nome'] }}</h5>
                    <p class="text-light">{{ $sistema['descricao'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
