<!DOCTYPE html>
<html lang="pt-BR" data-theme="{{ Auth::user()->tema ?? 'medieval' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RPG Manager')</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones (opcional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos customizados -->
    <style>
        /* ===== VARIÁVEIS DE TEMA ===== */
        :root {
            /* Medieval (default) */
            --bg-color: #151515;
            --text-color: #e2c89c;
            --nav-bg: #1a1a1a;
            --nav-border: #b8945e;
            --btn-bg: #b8945e;
            --btn-hover: #d8aa64;
            --btn-text: #fff;
            --card-bg: rgba(20, 20, 20, 0.85);
            --font-heading: 'Cinzel', serif;
            --font-brand: 'IM Fell English SC', serif;
        }

        [data-theme="sobrenatural"] {
            --bg-color: #0a0a23;
            --text-color: #9bd0ff;
            --nav-bg: #10104a;
            --nav-border: #5f7fff;
            --btn-bg: #5f7fff;
            --btn-hover: #7f9fff;
            --btn-text: #fff;
            --card-bg: rgba(10, 10, 35, 0.85);
            --font-heading: 'Share Tech Mono', monospace;
            --font-brand: 'Share Tech Mono', monospace;
        }

        [data-theme="cyberpunk"] {
            --bg-color: #0c0c0c;
            --text-color: #ff007f;
            --nav-bg: #1a001a;
            --nav-border: #ff00ff;
            --btn-bg: #ff00ff;
            --btn-hover: #ff66ff;
            --btn-text: #000;
            --card-bg: rgba(20, 0, 20, 0.85);
            --font-heading: 'Share Tech Mono', monospace;
            --font-brand: 'Share Tech Mono', monospace;
        }

        /* ===== ESTILO GERAL ===== */
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: var(--font-heading);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { text-decoration: none; }

        /* ===== NAVBAR ===== */
        nav {
            background-color: var(--nav-bg);
            border-bottom: 2px solid var(--nav-border);
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        }

        .navbar-brand {
            font-family: var(--font-brand);
            font-size: 1.5rem;
            color: var(--text-color) !important;
        }

        .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--btn-hover) !important;
        }

        /* ===== BOTÕES ===== */
        .btn-custom {
            background-color: var(--btn-bg);
            color: var(--btn-text);
            border-radius: 0.5rem;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: var(--btn-hover);
        }

        /* ===== CARDS ===== */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--nav-border);
        }

        /* ===== RODAPÉ ===== */
        footer {
            background-color: #111;
            color: var(--nav-border);
            text-align: center;
            padding: 20px 0;
            border-top: 2px solid var(--nav-border);
        }

        footer a { color: var(--btn-hover); }
        footer a:hover { color: var(--btn-bg); }
    </style>

    <!-- Arquivos Tailwind/Bootstrap customizados -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen" data-theme="{{ Auth::user()->tema ?? 'medieval' }}">

    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Conteúdo principal --}}
    <main class="container py-5 flex-1">
        @include('components.alert')
        @yield('content')
    </main>

    {{-- Rodapé --}}
    <footer>
        <p>🜂 Desenvolvido por <strong>Emilly Marteninghe Fortes</strong> • RPG Manager © {{ date('Y') }}</p>
        <p><a href="{{ route('home') }}">Voltar ao topo ↑</a></p>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts customizados -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
