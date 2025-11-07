@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">Raças do Sistema: {{ $sistema->nome }}</h1>
    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary mb-3">⬅ Voltar</a>

    @if($sistema->racas->isNotEmpty())
        <div class="list-group">
            @foreach($sistema->racas as $raca)
                <div class="list-group-item">
                    <h5>{{ $raca->nome }}</h5>
                    <p>{{ $raca->descricao ?? 'Sem descrição' }}</p>
                    <ul class="mb-0">
                        <li>Força: {{ $raca->forca_bonus }}</li>
                        <li>Destreza: {{ $raca->destreza_bonus }}</li>
                        <li>Constituição: {{ $raca->constituicao_bonus }}</li>
                        <li>Inteligência: {{ $raca->inteligencia_bonus }}</li>
                        <li>Sabedoria: {{ $raca->sabedoria_bonus }}</li>
                        <li>Carisma: {{ $raca->carisma_bonus }}</li>
                    </ul>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">Nenhuma raça cadastrada para este sistema.</p>
    @endif
</div>
@endsection
