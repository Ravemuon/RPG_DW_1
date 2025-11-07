@extends('layouts.app')

@section('title', 'Minhas Campanhas')

@section('content')
<div class="container py-5">
    <h2 class="text-warning text-center mb-5">Minhas Campanhas</h2>

    @if($campanhas->isEmpty())
        <p class="text-secondary text-center fst-italic">Você ainda não participa de nenhuma campanha.</p>
    @else
        <div class="row g-4">
            @foreach($campanhas as $campanha)
                <div class="col-md-6 col-lg-4">
                    <div class="card bg-dark text-light border-warning h-100">
                        <div class="card-body d-flex flex-column">
                            <h4 class="fw-bold text-warning text-center mb-3">{{ $campanha->nome }}</h4>

                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center border-warning">
                                    <span>Status do usuário:</span>
                                    <span class="badge bg-info text-dark">
                                        {{ is_string($campanha->statusUsuario) ? $campanha->statusUsuario : 'Desconhecido' }}
                                    </span>
                                </li>
                                <li class="list-group-item bg-dark border-warning">
                                    <strong>Privacidade:</strong> {{ $campanha->privada ? 'Privada' : 'Pública' }}
                                </li>
                                <li class="list-group-item bg-dark border-warning">
                                    <strong>Sistema:</strong> {{ $campanha->sistema->nome ?? 'Sistema Desconhecido' }}
                                </li>
                            </ul>

                            <div class="mt-auto d-flex justify-content-between">
                                <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-sm btn-warning">Ver detalhes</a>
                                @if(Auth::id() === $campanha->criador_id)
                                    <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-sm btn-primary">Editar</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
