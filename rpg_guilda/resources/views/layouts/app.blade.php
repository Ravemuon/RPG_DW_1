<!DOCTYPE html>
<html lang="pt-BR" data-theme="{{ auth()->check() ? auth()->user()->tema : session('theme', 'sobrenatural') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Portal do Aventureiro</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- CSS do Projeto -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=MedievalSharp&family=Orbitron&family=Pirata+One&display=swap" rel="stylesheet">

    <!-- CSRF Token para AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Conteúdo principal --}}
    <main class="container py-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    <!-- Bootstrap JS bundle -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- JS do Projeto -->
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Script para alterar tema ao vivo e salvar via AJAX --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeSelect = document.getElementById('tema');
            if(themeSelect){
                themeSelect.addEventListener('change', function(){

                    const newTheme = this.value;
                    // Atualiza visualmente
                    document.documentElement.setAttribute('data-theme', newTheme);

                    // Salva via AJAX
                    fetch("{{ route('usuarios.tema.update', auth()->id()) }}", {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ tema: newTheme })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success){
                            console.log('Tema atualizado com sucesso!');
                        } else {
                            console.error('Erro ao atualizar tema');
                        }
                    })
                    .catch(err => console.error('Erro AJAX:', err));
                });
            }
        });
    </script>
</body>
</html>
