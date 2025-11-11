<footer class="mt-5 pt-4 border-top">
    <div class="container text-center">

        {{-- Créditos --}}
        <p class="mb-2">
            ⚠️ Desenvolvido por <strong>Emilly M Fortes</strong> • RPG Guilda © {{ date('Y') }}
        </p>

        <p class="mb-2">
            O que significa <strong>R.P.G?</strong>? É uma sigla para <em>Role-playing game</em>.
        </p>

        {{-- Botão para o Dicionário --}}
        <div class="my-3">
            <a href="{{ route('home.dicionario') }}"
               class="btn px-4 py-2 fw-bold shadow-sm"
               style="background-color: var(--btn-bg); color: var(--btn-text); border-radius: 8px;">
                📖 Acessar Dicionário de RPG
            </a>
        </div>

        {{-- Voltar ao topo --}}
        <p class="mb-0">
            <a href="#top" class="text-decoration-none" style="color: var(--btn-bg);">
                ↑ Voltar ao topo
            </a>
        </p>

    </div>
</footer>
