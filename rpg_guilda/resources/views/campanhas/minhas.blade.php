@extends('layouts.app')

@section('title', 'Minhas Campanhas')

@section('content')
<div class="container py-5">
    {{-- Título --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="fw-bold text-warning m-0">🎲 Minhas Campanhas</h2>
        <a href="{{ route('campanhas.create') }}" class="btn btn-warning fw-bold text-dark shadow-sm">
            ➕ Nova Campanha
        </a>
    </div>

    {{-- Lista de campanhas --}}
    @if($minhasCampanhas->count())
        <div class="row g-4">
            @foreach($minhasCampanhas as $campanha)
                <div class="col-md-6">
                    <div class="card bg-dark border-warning shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            {{-- Cabeçalho --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold text-warning mb-0">
                                    {{ $campanha->nome }}
                                </h5>
                                <span class="badge bg-{{ $campanha->status === 'ativa' ? 'success' : ($campanha->status === 'pausada' ? 'warning text-dark' : 'secondary') }}">
                                    {{ ucfirst($campanha->status) }}
                                </span>
                            </div>

                            {{-- Informações --}}
                            <p class="text-light small mb-1">
                                👤 Mestre: <strong>{{ $campanha->criador->nome ?? 'Desconhecido' }}</strong>
                            </p>
                            <p class="text-light small mb-1">
                                ⚙️ Sistema: <strong>{{ $campanha->sistema->nome ?? 'N/A' }}</strong>
                            </p>
                            <p class="text-secondary small mb-3">
                                {{ Str::limit($campanha->descricao, 100) ?: 'Sem descrição.' }}
                            </p>

                            {{-- Ações --}}
                            <div class="mt-auto d-flex flex-wrap gap-2">
                                <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning btn-sm grow">
                                    🔍 Ver Detalhes
                                </a>

                                @auth
                                    @php
                                        $jaParticipa = $campanha->jogadores->pluck('id')->contains(auth()->id());
                                    @endphp
                                    @if(auth()->id() !== $campanha->criador_id && !$jaParticipa)
                                        <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST" class="grow
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm w-100"
                                                onclick="return confirm('Deseja solicitar entrada nesta campanha?')">
                                                🚪 Pedir para Entrar
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Nenhuma campanha --}}
        <div class="text-center text-light mt-5">
            <i class="bi bi-emoji-frown display-5 d-block mb-3"></i>
            <p class="mb-2">Você ainda não participa de nenhuma campanha.</p>
            <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-warning">
                🔍 Explorar Campanhas Públicas
            </a>
        </div>
    @endif
</div>
@endsection
