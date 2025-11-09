@extends('layouts.app')

@section('title', 'Amigos')

@section('content')
<div class="container mt-4">

    @include('amizades.partials._alertas')

    {{-- REDIRECIONAMENTOS / RESUMO --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-highlight">Central de Amizades</h4>
            <small class="text-muted">Gerencie suas conexões e convites</small>
        </div>

        <div class="card-body text-center">
            <p class="text-muted mb-4">
                Acompanhe suas solicitações, encontre novos amigos ou visualize suas conexões atuais.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('amizades.amigos') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    👥 Meus Amigos
                </a>
                <a href="{{ route('amizades.pendentes') }}" class="btn btn-outline-warning btn-lg rounded-pill px-4">
                    ⚡ Solicitações Pendentes
                </a>
                <a href="{{ route('amizades.procurar') }}" class="btn btn-outline-info btn-lg rounded-pill px-4">
                    🔍 Procurar Usuários
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
