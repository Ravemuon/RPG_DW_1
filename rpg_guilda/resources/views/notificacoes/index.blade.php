@extends('layouts.app')

@section('title', 'Notificações')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold text-warning mb-0">🔔 Minhas Notificações</h2>

        @if(!$notificacoes->isEmpty())
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('notificacoes.marcarTodas') }}">
                    @csrf
                    <button class="btn btn-outline-success btn-sm">
                        ✅ Marcar todas como lidas
                    </button>
                </form>

                <form method="POST" action="{{ route('notificacoes.limparTodas') }}"
                      onsubmit="return confirm('Tem certeza que deseja excluir todas as notificações?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">
                        🗑️ Excluir todas
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if($notificacoes->isEmpty())
        <div class="alert alert-info text-center">
            Você não tem notificações no momento.
        </div>
    @else
        <div class="list-group">
            @foreach($notificacoes as $notif)
                <div class="list-group-item list-group-item-action mb-2
                    {{ $notif->lida ? 'bg-dark text-light border-secondary' : 'bg-warning text-dark border-warning' }}">

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>[{{ strtoupper($notif->tipo ?? 'Info') }}]</strong> {{ $notif->mensagem }}
                            <br>
                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="d-flex gap-2">
                            @if(!$notif->lida)
                                <form method="POST" action="{{ route('notificacoes.marcar', $notif->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Marcar como lida</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('notificacoes.destroy', $notif->id) }}"
                                  onsubmit="return confirm('Deseja excluir esta notificação?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
