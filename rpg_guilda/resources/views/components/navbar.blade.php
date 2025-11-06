<nav class="p-4 rounded-md shadow-md" style="background-color: var(--btn-bg); color: var(--btn-text)">
    <div class="container mx-auto flex justify-between items-center">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center font-bold text-2xl text-white">
            <i class="bi bi-dice-6-fill me-2"></i> RPG Universe
        </a>

        {{-- Links --}}
        <div class="flex items-center space-x-4">

            <a href="{{ route('home') }}" class="hover:underline flex items-center">
                <i class="bi bi-house-door-fill me-1"></i> Início
            </a>

            {{-- Campanhas --}}
            <a href="{{ route('campanhas.todas') }}" class="hover:underline flex items-center">
                <i class="bi bi-flag-fill me-1"></i> Campanhas
            </a>

            <a href="{{ route('sistemas.index') }}" class="hover:underline flex items-center">
                <i class="bi bi-gear-fill me-1"></i> Sistemas
            </a>

            @auth
                @php
                    $usuario = Auth::user();
                    $notificacoesNaoLidas = $usuario->notificacoes()->where('lida', false)->count();
                @endphp

                {{-- Notificações --}}
                <a href="{{ route('notificacoes.index') }}" class="hover:underline relative flex items-center">
                    <i class="bi bi-bell-fill me-1"></i> Notificações
                    @if($notificacoesNaoLidas > 0)
                        <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs px-1 rounded-full">
                            {{ $notificacoesNaoLidas }}
                        </span>
                    @endif
                </a>

                {{-- Perfil --}}
                <a href="{{ route('perfil') }}" class="hover:underline flex items-center">
                    <i class="bi bi-person-circle me-1"></i> {{ $usuario->nome }}
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="ml-3 px-3 py-1 rounded-md border border-white hover:bg-white hover:text-black transition flex items-center">
                        <i class="bi bi-box-arrow-right me-1"></i> Sair
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-3 py-1 rounded-md border border-white hover:bg-white hover:text-black transition flex items-center">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </a>
                <a href="{{ route('register') }}" class="px-3 py-1 rounded-md border border-yellow-400 hover:bg-yellow-400 hover:text-black transition flex items-center">
                    <i class="bi bi-pencil-fill me-1"></i> Registrar
                </a>
            @endauth
        </div>
    </div>
</nav>
