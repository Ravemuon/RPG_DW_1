@extends('layouts.app')

@section('title', 'Chat - ' . $campanha->nome)

@section('content')
<div class="container py-5 bg-dark text-light min-vh-100">
    <h1 class="fw-bold text-warning mb-4">💬 Chat da Campanha: {{ $campanha->nome }}</h1>

    <div class="card bg-secondary text-light shadow-lg">
        <div class="card-body d-flex flex-column" style="height: 70vh;">
            {{-- Área de Mensagens --}}
            <div id="chat-messages" style="background-color: #343a40; border-radius: .25rem;">
                {{-- Exemplo de Mensagens (simulado) --}}
                <div class="d-flex justify-content-start mb-2">
                    <div class="p-2 bg-info rounded text-dark" style="max-width: 70%;">
                        <small class="fw-bold">Mestre:</small><br>
                        A sessão de hoje foi épica! Próxima semana veremos o que acontece em Dragon's Lair.
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-2">
                    <div class="p-2 bg-warning rounded text-dark" style="max-width: 70%;">
                        <small class="fw-bold">Jogador A:</small><br>
                        Mal posso esperar! Meu personagem quase morreu, haha!
                    </div>
                </div>
                {{-- Fim do Exemplo --}}

                {{-- Assumindo que você listaria as mensagens aqui com um @foreach --}}
                @forelse($mensagens as $mensagem)
                    @php
                        $isUser = auth()->check() && $mensagem->user_id === auth()->id();
                        $bgClass = $isUser ? 'bg-success' : 'bg-info';
                        $justifyClass = $isUser ? 'justify-content-end' : 'justify-content-start';
                        $textColor = $isUser ? 'text-light' : 'text-dark';
                    @endphp
                    <div class="d-flex {{ $justifyClass }} mb-2">
                        <div class="p-2 {{ $bgClass }} rounded {{ $textColor }}" style="max-width: 70%;">
                            <small class="fw-bold">{{ $mensagem->usuario->nome }}:</small><br>
                            {{ $mensagem->conteudo }}
                        </div>
                    </div>
                @empty
                    <p class="text-center text-secondary fst-italic">Nenhuma mensagem ainda. Comece a conversar!</p>
                @endforelse
            </div>

            {{-- Formulário de Envio --}}
            <form action="{{ route('campanhas.chat.store', $campanha->id) }}" method="POST" class="mt-auto">
                @csrf
                <div class="input-group">
                    <input type="text" class="form-control bg-dark text-light border-warning" placeholder="Escreva sua mensagem..." name="conteudo" required>
                    <button class="btn btn-warning" type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-light">⬅️ Voltar à Campanha</a>
    </div>
</div>
@endsection
