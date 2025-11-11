<div class="card text-light shadow-lg border-0 h-100 overflow-hidden position-relative amigo-card"
     style="background: linear-gradient(145deg, #1a1a1a, #222); border-radius: 15px; transition: transform .2s ease, box-shadow .2s ease;">

    {{-- Banner no topo --}}
    <div class="position-relative">
        <div class="w-100" style="height: 90px; background: url('{{ $usuario->banner_url }}') center/cover no-repeat; filter: brightness(0.8);"></div>
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.4), transparent 60%);"></div>
    </div>

    {{-- Avatar centralizado sobre o banner --}}
    <div class="text-center mt-n5">
        <img src="{{ $usuario->avatar_url }}"
             alt="Avatar de {{ $usuario->nome }}"
             class="rounded-circle border shadow-lg"
             style="width: 95px; height: 95px; object-fit: cover; border-color: var(--btn-bg) !important;">
    </div>

    <div class="card-body text-center px-3 pb-4">
        {{-- Nome e username --}}
        <h6 class="fw-bold mt-2 mb-0 text-highlight">{{ $usuario->nome }}</h6>
        <p class="text-muted small">@ {{ $usuario->username }}</p>

        {{-- Estatísticas resumidas --}}
        <div class="d-flex justify-content-center gap-4 mb-3">
            <div>
                <small class="text-muted d-block">🎭 Personagens</small>
                <span class="fw-bold text-light">{{ $usuario->personagens->count() }}</span>
            </div>
            <div>
                <small class="text-muted d-block">🗺️ Campanhas</small>
                <span class="fw-bold text-light">{{ $usuario->campanhas->count() }}</span>
            </div>
        </div>

        {{-- Botões --}}
        <a href="{{ route('amizades.perfilpublico', $usuario->id) }}"
           class="btn btn-outline-light btn-sm w-100 fw-bold mb-2 rounded-pill"
           style="border-color: var(--btn-bg); color: var(--btn-bg);">
           👁️ Ver Perfil
        </a>

        @if(Auth::check() && Auth::id() !== $usuario->id)
            <form action="{{ route('amizades.adicionar', $usuario->id) }}" method="POST">
                @csrf
                <button class="btn btn-success btn-sm w-100 fw-bold rounded-pill" style="background-color: var(--btn-bg); border: none;">
                    🤝 Adicionar
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Hover effect --}}
<style>
.amigo-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.6);
}
</style>
