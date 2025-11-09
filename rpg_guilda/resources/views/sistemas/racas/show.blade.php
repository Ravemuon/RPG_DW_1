@extends('layouts.app')

@section('content')
<style>
    /* Estilo customizado para o efeito de card, se o Tailwind não estiver disponível */
    .transform-on-hover {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    .transform-on-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container py-4">

    <!-- Cabeçalho com título e botões -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold m-0">🛡️ Detalhes da Classe - {{ $classe->nome }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('sistemas.classes.index', $sistema->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para a Lista
            </a>

            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('sistemas.classes.edit', [$sistema->id, $classe->id]) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar Classe
                </a>
                <form action="{{ route('sistemas.classes.destroy', [$sistema->id, $classe->id]) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Tem certeza que deseja excluir esta classe?')" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir Classe
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title fw-bold">Nome: {{ $classe->nome }}</h5>
            <p><strong>Descrição:</strong> {{ $classe->descricao ?? 'Sem descrição.' }}</p>
            <p><strong>Página:</strong> {{ $classe->pagina ?? 'Não informada' }}</p>

            <h6 class="mt-4">Atributos:</h6>
            <ul>
                <li><strong>Força:</strong> {{ $classe->forca ?? '-' }}</li>
                <li><strong>Destreza:</strong> {{ $classe->destreza ?? '-' }}</li>
                <li><strong>Constituição:</strong> {{ $classe->constituicao ?? '-' }}</li>
                <li><strong>Inteligência:</strong> {{ $classe->inteligencia ?? '-' }}</li>
                <li><strong>Sabedoria:</strong> {{ $classe->sabedoria ?? '-' }}</li>
                <li><strong>Carisma:</strong> {{ $classe->carisma ?? '-' }}</li>
                <li><strong>Agilidade:</strong> {{ $classe->agilidade ?? '-' }}</li>
                <li><strong>Intelecto:</strong> {{ $classe->intelecto ?? '-' }}</li>
                <li><strong>Presença:</strong> {{ $classe->presenca ?? '-' }}</li>
                <li><strong>Vigor:</strong> {{ $classe->vigor ?? '-' }}</li>
                <li><strong>Nex:</strong> {{ $classe->nex ?? '-' }}</li>
                <li><strong>Sanidade:</strong> {{ $classe->sanidade ?? '-' }}</li>
            </ul>

            <h6 class="mt-4">Aspectos e Poderes:</h6>
            <ul>
                <li><strong>Aspectos:</strong> {{ $classe->aspects ? json_encode($classe->aspects) : 'Não informados' }}</li>
                <li><strong>Stunts:</strong> {{ $classe->stunts ? json_encode($classe->stunts) : 'Não informados' }}</li>
                <li><strong>Poderes:</strong> {{ $classe->poderes ? json_encode($classe->poderes) : 'Não informados' }}</li>
            </ul>

            <h6 class="mt-4">Atributos Customizados:</h6>
            <ul>
                @foreach($classe->atributos_custom as $atributo => $valor)
                    <li><strong>{{ ucfirst($atributo) }}:</strong> {{ $valor }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@endsection
