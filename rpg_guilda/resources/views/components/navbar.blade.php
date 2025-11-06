<nav class="p-4 rounded-md shadow-md" style="background-color: var(--btn-bg); color: var(--btn-text)">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ route('home') }}" class="font-bold text-xl">RPG Manager</a>

        <div class="flex items-center space-x-4">
            <a href="{{ route('home') }}" class="hover:underline">Início</a>
            <a href="{{ route('campanhas.index') }}" class="hover:underline">Campanhas</a>

            @auth
                <span class="ml-4">Olá, {{ Auth::user()->nome }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="ml-3 px-3 py-1 rounded-md border border-white hover:bg-white hover:text-black transition">
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
