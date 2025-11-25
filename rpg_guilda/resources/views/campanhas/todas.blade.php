@extends('layouts.app')

@section('title', 'Todas as Campanhas')

@section('content')
<div class="container py-5 text-light">

    {{-- Cabeçalho --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold text-highlight">🗺️ Todas as Campanhas</h1>
        <p class="text-muted">Explore aventuras públicas ou entre em campanhas privadas com convite.</p>
    </div>

    {{-- Barra de busca e criar campanha --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
        <form action="{{ route('campanhas.todas') }}" method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="🔍 Buscar campanhas..."
                   class="form-control rounded-pill">
            <button type="submit" class="btn btn-outline-warning rounded-pill px-4 fw-bold">
                Buscar
            </button>
        </form>

        @auth
            <a href="{{ route('campanhas.create') }}" class="btn btn-success fw-bold rounded-pill px-4">
                ➕ Criar Campanha
            </a>
        @endauth
    </div>

    {{-- Lista de campanhas --}}
    <div class="row g-4">
        @forelse($todasCampanhas as $campanha)
            @php
                $user = auth()->user();

                // Verifica se o usuário participa
                $participa = $user && (
                    $user->id === $campanha->criador_id ||
                    $campanha->jogadores->pluck('id')->contains($user->id)
                );

                // Recupera pivot com segurança (sem erro caso não exista)
                $jogador = $campanha->jogadores->where('id', auth()->id())->first();
                $pivot = $jogador?->pivot;
            @endphp

            <div class="col-md-6 col-lg-4">
                <div class="card campanha-card border-0 shadow-lg text-light h-100">
                    <div class="card-body d-flex flex-column text-center">

                        {{-- Avatar do mestre --}}
                        <div class="mb-3">
                            <img src="{{ $campanha->criador->avatar_url ?? '/images/default-avatar.png' }}"
                                 alt="Avatar do mestre"
                                 class="rounded-circle border shadow"
                                 style="width: 85px; height: 85px; object-fit: cover; border-color: var(--btn-bg) !important;">
                        </div>

                        {{-- Nome da campanha --}}
                        <h5 class="fw-bold text-highlight mb-1">{{ $campanha->nome }}</h5>
                        <p class="text-muted small mb-2">👑 Mestre: {{ $campanha->criador->nome ?? 'Desconhecido' }}</p>

                        {{-- Descrição curta --}}
                        <p class="text-muted small mb-3">{{ Str::limit($campanha->descricao ?? 'Sem descrição disponível.', 90, '...') }}</p>

                        {{-- Info básicas --}}
                        <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                            <span class="badge rounded-pill bg-info text-dark">
                                🎲 {{ $campanha->sistema->nome ?? 'Sistema Desconhecido' }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $campanha->status === 'ativa' ? 'success' : 'secondary' }}">
                                {{ ucfirst($campanha->status) }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $campanha->privada ? 'warning text-dark' : 'primary' }}">
                                {{ $campanha->privada ? 'Privada 🔒' : 'Pública 🌍' }}
                            </span>
                        </div>

                        {{-- Ações --}}
                        <div class="mt-auto d-flex flex-column gap-2">

                            {{-- Ver Detalhes --}}
                            <a href="{{ route('campanhas.show', $campanha->id) }}"
                               class="btn btn-outline-warning btn-sm rounded-pill fw-bold w-100">
                                👁️ Ver Detalhes
                            </a>

                            @auth
                                {{-- Solicitar entrada --}}
                                @if(!$participa && auth()->id() !== $campanha->criador_id)
                                    <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill fw-bold w-100">
                                            🤝 Solicitar Participação
                                        </button>
                                    </form>

                                {{-- Solicitação pendente --}}
                                @elseif($pivot && $pivot->status === 'pendente')
                                    <button class="btn btn-warning btn-sm rounded-pill fw-bold w-100" disabled>
                                        ⏳ Solicitação Pendente
                                    </button>

                                {{-- Solicitação aprovada --}}
                                @elseif($pivot && $pivot->status === 'aprovado')
                                    <button class="btn btn-success btn-sm rounded-pill fw-bold w-100" disabled>
                                        ✔ Você Participa
                                    </button>

                                {{-- Recusada --}}
                                @elseif($pivot && $pivot->status === 'recusado')
                                    <button class="btn btn-danger btn-sm rounded-pill fw-bold w-100" disabled>
                                        ❌ Solicitação Recusada
                                    </button>
                                @endif

                                {{-- Editar / Excluir --}}
                                @if(auth()->id() === $campanha->criador_id || auth()->user()->tipo === 'administrador')
                                    <div class="d-flex justify-content-center gap-2 flex-wrap mt-2">
                                        <a href="{{ route('campanhas.edit', $campanha->id) }}"
                                           class="btn btn-outline-info btn-sm rounded-pill fw-bold flex-fill">
                                            ✏️ Editar
                                        </a>
                                        <form action="{{ route('campanhas.destroy', $campanha->id) }}" method="POST" class="d-inline flex-fill">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-bold w-100"
                                                    onclick="return confirm('Deseja realmente excluir esta campanha?')">
                                                🗑️ Excluir
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <p class="text-muted fst-italic">Nenhuma campanha disponível no momento.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-5 d-flex justify-content-center">
        {{ $todasCampanhas->withQueryString()->links() }}
    </div>
</div>

<style>
.campanha-card {
    transition: all .25s ease-in-out;
}
.campanha-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.6);
}
.text-highlight {
    color: var(--btn-bg, #ffc107);
}
</style>
@endsection
