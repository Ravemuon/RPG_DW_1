@extends('layouts.app')

@section('title', 'Notificações')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4 fw-bold text-warning">🔔 Minhas Notificações</h2>

    @if($notificacoes->isEmpty())
        <p class="text-center text-light">Você não tem notificações.</p>
    @else
        <div class="list-group">
            @foreach($notificacoes as $notif)
                <div class="list-group-item list-group-item-action mb-2 {{ $notif->lida ? 'bg-dark text-light' : 'bg-warning text-dark' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>[{{ strtoupper($notif->data['tipo'] ?? 'Info') }}]</strong> {{ $notif->data['message'] ?? $notif->message }}
                            <br>
                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!$notif->lida)
                        <form method="POST" action="{{ route('notificacoes.marcar', $notif->id) }}">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-sm btn-success">Marcar como lida</button>
                        </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
