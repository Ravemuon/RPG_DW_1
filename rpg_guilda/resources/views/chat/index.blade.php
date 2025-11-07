@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Chat da Campanha</h1>

    <div id="chat-box" class="border rounded p-4 h-96 overflow-y-auto bg-gray-50">
        <!-- Mensagens serão carregadas aqui -->
    </div>

    <form id="chat-form" class="mt-4 flex gap-2">
        <input type="text" id="mensagem" name="mensagem" class="flex-1 border rounded p-2" placeholder="Digite sua mensagem..." required>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Enviar</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
const chatId = {{ $chats->first()->id ?? 0 }};
const chatBox = document.getElementById('chat-box');
const chatForm = document.getElementById('chat-form');
const mensagemInput = document.getElementById('mensagem');

function carregarMensagens() {
    fetch(`/chats/${chatId}/mensagens`)
        .then(res => res.json())
        .then(data => {
            chatBox.innerHTML = '';
            data.forEach(msg => {
                const div = document.createElement('div');
                div.className = 'mb-2 p-2 rounded';
                div.innerHTML = `
                    <strong>${msg.user.nome}:</strong> ${msg.mensagem}
                    <small class="text-gray-400 block">${new Date(msg.created_at).toLocaleTimeString()}</small>
                `;
                chatBox.appendChild(div);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Atualiza mensagens a cada 3 segundos
setInterval(carregarMensagens, 3000);
carregarMensagens();

// Enviar mensagem via AJAX
chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const mensagem = mensagemInput.value.trim();
    if (!mensagem) return;

    fetch(`/chats/${chatId}/enviar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ mensagem })
    })
    .then(res => res.json())
    .then(data => {
        mensagemInput.value = '';
        carregarMensagens();
    });
});
</script>
@endsection
