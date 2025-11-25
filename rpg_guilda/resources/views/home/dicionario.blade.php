@extends('layouts.app')

@section('title', '📖 Dicionário de RPG')

@section('content')
<div class="container py-4">

    {{-- Cabeçalho --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5 mb-3" style="color: var(--btn-bg); text-shadow: 0 0 8px rgba(0,0,0,0.8);">
            📖 Dicionário de RPG
        </h1>
        <p class="lead" style="color: var(--bs-body-color); text-shadow: 0 0 4px rgba(0,0,0,0.6);">
            Explore os principais termos, expressões e conceitos do universo de RPG!
        </p>
    </div>

    {{-- Campo de busca --}}
    <div class="mb-4">
        <input type="text" id="buscaTermo" class="form-control form-control-lg shadow-sm"
               placeholder="🔎 Buscar termo..." style="border-color: var(--btn-bg);">
    </div>

    {{-- Lista de termos --}}
    <div class="row g-3" id="listaDicionario">
        @php
            $termos = [
                ['titulo' => 'Mestre', 'descricao' => 'Também conhecido como Dungeon Master (DM) ou Game Master (GM), é o jogador responsável por narrar a história, controlar NPCs e o mundo do jogo.'],
                ['titulo' => 'Ficha de Personagem', 'descricao' => 'Documento onde estão registradas as informações do personagem: atributos, habilidades, equipamentos e histórico.'],
                ['titulo' => 'NPC', 'descricao' => 'Non-Player Character — personagens controlados pelo mestre, que interagem com os jogadores.'],
                ['titulo' => 'D20', 'descricao' => 'Dado de 20 lados. Muito usado em sistemas como D&D e Tormenta20 para testes de habilidade e combate.'],
                ['titulo' => 'Roleplay', 'descricao' => 'Interpretação de personagens dentro do jogo. Focar nas ações e falas coerentes com a personalidade e história do personagem.'],
                ['titulo' => 'Campanha', 'descricao' => 'Série de aventuras conectadas, com enredo contínuo e progressão dos personagens.'],
                ['titulo' => 'Perícia', 'descricao' => 'Habilidade específica de um personagem que define sua aptidão em determinada tarefa, como “Furtividade” ou “Investigação”.'],
                ['titulo' => 'Teste de Habilidade', 'descricao' => 'Rolagem de dados feita para determinar o sucesso ou fracasso de uma ação.'],
                ['titulo' => 'Pontos de Vida (PV ou HP)', 'descricao' => 'Representam a vitalidade do personagem. Quando chegam a 0, o personagem é derrotado.'],
                ['titulo' => 'Crítico', 'descricao' => 'Resultado máximo em um dado (geralmente 20 no D20), que causa efeitos especiais ou dano dobrado.'],
            ];
        @endphp

        @foreach($termos as $termo)
            <div class="col-12 col-md-6 col-lg-4 termo-card">
                <div class="card h-100 shadow-sm border-0" style="background-color: var(--card-bg);">
                    <div class="card-body">
                        <h5 class="fw-bold mb-2" style="color: var(--btn-bg);">
                            {{ $termo['titulo'] }}
                        </h5>
                        <p class="small mb-0 text-light">{{ $termo['descricao'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Botão de voltar --}}
    <div class="text-center mt-5">
        <a href="{{ url()->previous() }}" class="btn px-4 py-2 fw-bold shadow"
           style="border: 2px solid var(--btn-bg); color: var(--btn-bg); background-color: transparent;">
            ⬅️ Voltar
        </a>
    </div>

</div>

{{-- Script de busca simples --}}
<script>
document.getElementById('buscaTermo').addEventListener('input', function() {
    const termoBusca = this.value.toLowerCase();
    const cards = document.querySelectorAll('#listaDicionario .termo-card');
    cards.forEach(card => {
        const titulo = card.querySelector('h5').innerText.toLowerCase();
        const desc = card.querySelector('p').innerText.toLowerCase();
        card.style.display = (titulo.includes(termoBusca) || desc.includes(termoBusca)) ? '' : 'none';
    });
});
</script>
@endsection
