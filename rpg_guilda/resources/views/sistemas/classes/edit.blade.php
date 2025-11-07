@extends('layouts.app')

@section('content')

<div class="container py-4">
<h1 class="fw-bold mb-4">Editar Classe: {{ $classe->nome }} (Sistema: {{ $sistema->nome }})</h1>
<a href="{{ route('sistemas.classes.index', $sistema) }}" class="btn btn-secondary mb-4">⬅ Voltar para Classes</a>

<div class="card shadow-sm">
    <div class="card-body">
        <!-- O formulário será enviado para a rota 'update' (atualização) -->
        <form action="{{ route('sistemas.classes.update', [$sistema, $classe]) }}" method="POST">
            @csrf
            <!-- Laravel usa este campo escondido para simular o método PUT/PATCH -->
            @method('PUT')

            <!-- Inclui o partial do formulário. A variável $classe estará disponível para preenchimento. -->
            @include('classes._form')

            <button type="submit" class="btn btn-success mt-3">Salvar Alterações</button>
        </form>
    </div>
</div>


</div>
@endsection
