<nav class="p-4 rounded-md shadow-md" style="background-color: var(--btn-bg); color: var(--btn-text)">
    <div class="container mx-auto flex justify-between items-center">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="font-bold text-xl">RPG Manager</a>

        {{-- Links --}}
        <div class="flex items-center space-x-4">
            <a href="{{ route('home') }}" class="hover:underline">Início</a>
            <a href="{{ route('campanhas.index') }}" class="hover:underline">Campanhas</a>
            <a href="{{ route('sistemas.index') }}" class="hover:underline">Sistemas</a>

            @auth
                @php
                    $usuario = Auth::user();
                    $notificacoesNaoLidas = $usuario->notificacoes()->where('lida', false)->count();
                @endphp

                {{-- Notificações --}}
                <a href="{{ route('notificacoes.index') }}" class="hover:underline relative">
                    Notificações
                    @if($notificacoesNaoLidas > 0)
                        <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs px-1 rounded-full">
                            {{ $notificacoesNaoLidas }}
                        </span>
                    @endif
                </a>

                {{-- Perfil do usuário --}}
                <a href="{{ route('perfil') }}" class="hover:underline ml-4">Usuário</a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="ml-3 px-3 py-1 rounded-md border border-white hover:bg-white hover:text-black transition">
                        Sair
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-3 py-1 rounded-md border border-white hover:bg-white hover:text-black transition">Login</a>
                <a href="{{ route('register') }}" class="px-3 py-1 rounded-md border border-yellow-400 hover:bg-yellow-400 hover:text-black transition">Registrar</a>
            @endauth
        </div>
    </div>
</nav>
    