@extends('layouts.app')

@section('content')

<div class="container py-4">
<h1 class="fw-bold mb-4">Criar Nova Classe para: {{ $sistema->nome }}</h1>
<a href="{{ route('sistemas.classes.index', $sistema) }}" class="btn btn-secondary mb-4">⬅ Voltar para Classes</a>

<div class="card shadow-sm">
    <div class="card-body">
        <!-- O formulário será enviado para a rota 'store' (criação) -->
        <form action="{{ route('sistemas.classes.store', $sistema) }}" method="POST">
            @csrf

            <!-- Inclui o partial do formulário. Passa a variável $sistema para contexto, se necessário. -->
            @include('classes._form')

            <button type="submit" class="btn btn-primary mt-3">Cadastrar Classe</button>
        </form>
    </div>
</div>


</div>
@endsection
