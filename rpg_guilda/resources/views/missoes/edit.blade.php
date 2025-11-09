@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">✏️ Editar Missão</h1>

    <form action="{{ route('missoes.update', [$campanha->id, $missao->id]) }}" method="POST">
        @method('PUT')
        @include('missoes._form')
    </form>
</div>
@endsection
