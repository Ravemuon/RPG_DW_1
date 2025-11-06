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

    <!-- Ícones Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=IM+Fell+English+SC&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <!-- Estilos customizados -->
    <style>
        /* ===== VARIÁVEIS DE TEMA ===== */
        :root {
            --bg-color: #1b1b1b;
            --text-color: #f5e6c4;
            --nav-bg: #222;
            --nav-border: #b8945e;
            --btn-bg: #b8945e;
            --btn-hover: #d8aa64;
            --btn-text: #fff;
            --card-bg: rgba(30,30,30,0.9);
            --card-border: #b8945e;
            --font-heading: 'Cinzel', serif;
            --font-brand: 'IM Fell English SC', serif;
            --shadow-glow: 0 0 8px #b8945e;
        }

        [data-theme="sobrenatural"] {
            --bg-color: #0a0a23;
            --text-color: #9bd0ff;
            --nav-bg: #10104a;
            --nav-border: #5f7fff;
            --btn-bg: #5f7fff;
            --btn-hover: #7f9fff;
            --btn-text: #fff;
            --card-bg: rgba(10,10,35,0.85);
            --card-border: #5f7fff;
            --font-heading: 'Share Tech Mono', monospace;
            --font-brand: 'Share Tech Mono', monospace;
            --shadow-glow: 0 0 8px #5f7fff;
        }

        [data-theme="cyberpunk"] {
            --bg-color: #0c0c0c;
            --text-color: #ff00ff;
            --nav-bg: #1a001a;
            --nav-border: #ff00ff;
            --btn-bg: #ff00ff;
            --btn-hover: #ff66ff;
            --btn-text: #000;
            --card-bg: rgba(20,0,20,0.85);
            --card-border: #ff00ff;
            --font-heading: 'Share Tech Mono', monospace;
            --font-brand: 'Share Tech Mono', monospace;
            --shadow-glow: 0 0 10px #ff00ff;
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

        h1, h2, h3, h4, h5, h6 {
            text-shadow: var(--shadow-glow);
        }

        /* ===== NAVBAR ===== */
        nav {
            background-color: var(--nav-bg);
            border-bottom: 2px solid var(--nav-border);
            box-shadow: 0 2px 15px rgba(0,0,0,0.5);
        }

        .navbar-brand {
            font-family: var(--font-brand);
            font-size: 1.7rem;
            color: var(--text-color) !important;
            text-shadow: var(--shadow-glow);
        }

        .nav-link {
            color: var(--text-color) !important;
            font-weight: 600;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .nav-link:hover {
            color: var(--btn-hover) !important;
            transform: scale(1.05);
        }

        /* ===== BOTÕES ===== */
        .btn-custom {
            background-color: var(--btn-bg);
            color: var(--btn-text);
            border-radius: 0.7rem;
            font-weight: bold;
            text-shadow: var(--shadow-glow);
            box-shadow: var(--shadow-glow);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: var(--btn-hover);
            box-shadow: 0 0 12px var(--btn-hover);
        }

        /* ===== CARDS ===== */
        .card {
            background-color: var(--card-bg);
            border: 2px solid var(--card-border);
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.6);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.7);
        }

        .card-header {
            font-family: var(--font-brand);
            font-weight: bold;
            text-shadow: var(--shadow-glow);
        }

        /* ===== TABELAS ===== */
        table.table-dark th,
        table.table-dark td {
            vertical-align: middle;
        }

        table.table-dark th {
            color: var(--text-color);
            text-shadow: var(--shadow-glow);
        }

        table.table-dark td {
            color: #fff;
        }

        /* ===== RODAPÉ ===== */
        footer {
            background-color: #111;
            color: var(--nav-border);
            text-align: center;
            padding: 25px 0;
            border-top: 2px solid var(--nav-border);
            margin-top: auto;
            font-size: 0.9rem;
        }

        footer a {
            color: var(--btn-hover);
            transition: color 0.3s;
        }

        footer a:hover {
            color: var(--btn-bg);
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 768px) {
            h1.display-4 { font-size: 2rem; }
            .card { margin: 1rem 0; }
        }
    </style>

    <!-- Arquivos CSS customizados -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex flex-col min-h-screen" data-theme="{{ Auth::user()->tema ?? 'medieval' }}">

    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Conteúdo principal --}}
    <main class="flex-1 container py-5">
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
