@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">{{ $pericia->nome }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <p><strong>Sistema:</strong> {{ $pericia->sistemaRPG }}</p>
            <p><strong>Automática:</strong> {{ $pericia->automatica ? 'Sim' : 'Não' }}</p>
            <p><strong>Fórmula:</strong> {{ $pericia->formula ?? '—' }}</p>
        </div>
    </div>
</div>
@endsection
    