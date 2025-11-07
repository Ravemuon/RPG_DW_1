@extends('layouts.app')

@section('title', "Chat - {$campanha->nome}")

@section('content')
<div class="container py-5">
    <h2 class="text-warning mb-4">{{ $campanha->nome }} - Chat</h2>

    {{-- Mensagens --}}
    <div class="mb-3">
        @forelse($mensagens as $mensagem)
            <div class="mb-2 border-bottom pb-2">
                <p>
                    <strong>{{ $mensagem->user->nome }}:</strong>
                    {{ $mensagem->mensagem ?? 'Mensagem excluída' }}
                </p>

                @if(Auth::id() === $mensagem->user_id || Auth::id() === $campanha->criador_id)
                    <div class="d-flex gap-2 mb-2">
                        {{-- Editar --}}
                        <form action="{{ route('chat.mensagem.update', $mensagem->id) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="mensagem" class="form-control form-control-sm" value="{{ $mensagem->mensagem }}" required>
                            <button class="btn btn-sm btn-primary">Editar</button>
                        </form>

                        {{-- Excluir --}}
                        <form action="{{ route('chat.mensagem.destroy', $mensagem->id) }}" method="POST" class="d-inline-flex">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-secondary fst-italic">Nenhuma mensagem ainda.</p>
        @endforelse
    </div>

    {{-- Form para nova mensagem --}}
    <form action="{{ route('campanhas.chat.enviar', $campanha->id) }}" method="POST">
        @csrf
        <div class="input-group">
            <input type="text" name="mensagem" class="form-control" placeholder="Digite sua mensagem..." required>
            <button class="btn btn-warning">Enviar</button>
        </div>
    </form>
</div>
@endsection
