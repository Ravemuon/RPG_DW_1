<div class="amigo-card card text-light shadow-lg border-0 h-100 overflow-hidden position-relative">

    <div class="amigo-card-banner position-relative">
        <div class="amigo-card-banner-bg w-100" style="background-image: url('{{ $usuario->banner_url }}');"></div>
        <div class="position-absolute top-0 start-0 w-100 h-100"
             style="background: linear-gradient(to bottom, rgba(0,0,0,0.6), transparent 80%);"></div>
    </div>

    <div class="text-center amigo-card-avatar-container">
        <img src="{{ $usuario->avatar_url }}"
             alt="Avatar de {{ $usuario->nome }}"
             class="amigo-card-avatar rounded-circle border shadow-lg">
    </div>

    <div class="card-body text-center px-3 pb-4 pt-0">

        <h5 class="fw-bolder mt-3 mb-0 text-highlight">{{ $usuario->nome }}</h5>
        <p class="text-secondary small mb-3">@ {{ $usuario->username }}</p>

        <div class="mb-4">
            @if($tipo === 'recebida')
                <span class="badge bg-warning text-dark fw-bolder px-4 py-2 rounded-pill shadow-sm">
                    Solicitação Recebida
                </span>
            @else
                <span class="badge bg-secondary text-light fw-bold px-4 py-2 rounded-pill">
                    Solicitação Enviada
                </span>
            @endif
        </div>

        @if($tipo === 'recebida')
            <form action="{{ route('amizades.aceitar', $amizade->id) }}" method="POST" class="d-grid gap-2 mb-2">
                @csrf
                <button type="submit"
                        class="btn btn-success btn-lg w-100 fw-bold rounded-pill"
                        style="--bs-btn-bg: var(--btn-bg); border: none; --bs-btn-border-color: var(--btn-bg);">
                    ✅ Aceitar
                </button>
            </form>

            <form action="{{ route('amizades.remover', $amizade->id) }}" method="POST" class="d-grid gap-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-outline-danger btn-lg w-100 fw-bold rounded-pill">
                    ❌ Recusar
                </button>
            </form>
        @else
            <form action="{{ route('amizades.remover', $amizade->id) }}" method="POST" class="d-grid gap-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-outline-danger btn-lg w-100 fw-bold rounded-pill">
                    🛑 Cancelar Solicitação
                </button>
            </form>
        @endif
    </div>
</div>

<style>
.amigo-card {
    background: linear-gradient(145deg, #1a1a1a, #222);
    border-radius: 15px;
    transition: transform .3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow .3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.amigo-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.8);
}

.amigo-card-banner-bg {
    height: 100px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: brightness(0.75);
}

.amigo-card-avatar-container {
    margin-top: -65px !important;
}

.amigo-card-avatar {
    width: 105px;
    height: 105px;
    object-fit: cover;
    border-color: #2c2c2c !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.4);
}

.btn-success {
    background-color: var(--btn-bg, #198754) !important;
    border-color: var(--btn-bg, #198754) !important;
}
</style>
