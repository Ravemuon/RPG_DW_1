@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">➕ Nova Raça – {{ $sistema->nome }}</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('sistemas.racas.store', $sistema->id) }}" method="POST">
                @include('sistemas.racas._form')
            </form>
        </div>
    </div>
</div>
@endsection
