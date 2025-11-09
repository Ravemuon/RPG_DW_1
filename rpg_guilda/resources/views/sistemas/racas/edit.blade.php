@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">✏️ Editar Raça – {{ $raca->nome }}</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('sistemas.racas.update', [$sistema->id, $raca->id]) }}" method="POST">
                @method('PUT')
                @include('sistemas.racas._form')
            </form>
        </div>
    </div>
</div>
@endsection
