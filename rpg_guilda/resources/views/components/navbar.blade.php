<nav class="navbar navbar-expand-lg p-3" style="background-color: var(--nav-bg); border-bottom: 2px solid var(--nav-border);">
    <div class="container-fluid">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="navbar-brand fw-bold fs-3" style="color: var(--text-color); font-family: var(--font-brand);">
            <i class="bi bi-dice-6-fill me-2"></i> RPG Universe
        </a>

        {{-- Mobile Toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                style="border-color: var(--nav-border);">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Links --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill me-2"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('campanhas.todas') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('campanhas.todas') ? 'active' : '' }}">
                        <i class="bi bi-flag-fill me-2"></i> Campanhas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sistemas.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('sistemas.index') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill me-2"></i> Sistemas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('amizades.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('amizades.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> Amigos
                    </a>
                </li>
            </ul>

            {{-- Direita --}}
            <ul class="navbar-nav">
                @auth
                    @php
                        $usuario = Auth::user();
                        $notificacoesNaoLidas = $usuario->notificacoes()->where('lida', false)->count();
                    @endphp

                    {{-- Notificações --}}
                    <li class="nav-item me-2 position-relative">
                        <a href="{{ route('notificacoes.index') }}" class="nav-link d-flex align-items-center">
                            <i class="bi bi-bell-fill me-2"></i> Notificações
                            @if($notificacoesNaoLidas > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $notificacoesNaoLidas }}
                                    <span class="visually-hidden">novas notificações</span>
                                </span>
                            @endif
                        </a>
                    </li>

                    {{-- Perfil --}}
                    <li class="nav-item me-2">
                        <a href="{{ route('usuarios.perfil') }}" class="nav-link d-flex align-items-center">
                            <i class="bi bi-person-circle me-2"></i> {{ $usuario->nome }}
                        </a>
                    </li>

                    {{-- Logout --}}
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-custom btn-sm d-flex align-items-center">
                                <i class="bi bi-box-arrow-right me-1"></i> Sair
                            </button>
                        </form>
                    </li>
                @else
                    {{-- Visitante --}}
                    <li class="nav-item me-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-custom btn-sm d-flex align-items-center">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-custom btn-sm d-flex align-items-center">
                            <i class="bi bi-pencil-fill me-1"></i> Registrar
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
