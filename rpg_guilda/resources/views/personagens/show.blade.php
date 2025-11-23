@extends('layouts.app')

@section('title', $personagem->nome)

@section('content')
<div class="container py-4">
    <h1 class="mb-4">🧙 Personagem: {{ $personagem->nome }}</h1>

    <div class="row">
        <div class="col-md-4">
            @if($personagem->imagem)
                <img src="{{ asset('storage/'.$personagem->imagem) }}" alt="{{ $personagem->nome }}" class="img-fluid rounded mb-3">
            @endif
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Raça:</strong> {{ $personagem->raca->nome ?? '—' }}</li>
                <li class="list-group-item"><strong>Classe:</strong> {{ $personagem->classe }}</li>
                <li class="list-group-item"><strong>Origem:</strong> {{ $personagem->origem ?? '—' }}</li>
                <li class="list-group-item"><strong>Sistema RPG:</strong> {{ $personagem->sistema->nome ?? $personagem->sistema_rpg }}</li>
            </ul>

            <a href="{{ route('personagens.edit', $personagem->id) }}" class="btn btn-warning mb-2 w-100">Editar</a>

            <form action="{{ route('personagens.destroy', $personagem->id) }}" method="POST"
                  onsubmit="return confirm('Tem certeza que deseja deletar este personagem?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">Deletar</button>
            </form>
        </div>

        <div class="col-md-8">
            {{-- Dashboard de Atributos --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">📊 Atributos</h5>
                </div>
                <div class="card-body">
                    <canvas id="atributosChart" height="250"></canvas>
                </div>
            </div>

            {{-- Perícias --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Perícias</h5>
                </div>
                <div class="card-body">
                    @if($personagem->pericias->count())
                        <ul>
                            @foreach($personagem->pericias as $p)
                                <li>{{ $p->nome }} (Nível: {{ $p->pivot->nivel }}, Proficiente: {{ $p->pivot->proficiente ? 'Sim' : 'Não' }})</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">Nenhuma perícia cadastrada.</p>
                    @endif
                </div>
            </div>

            {{-- Descrição, História, Personalidade, Inventário --}}
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Descrição</h5></div>
                <div class="card-body"><p>{{ $personagem->descricao ?? '—' }}</p></div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">História</h5></div>
                <div class="card-body"><p>{{ $personagem->historia ?? '—' }}</p></div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Personalidade</h5></div>
                <div class="card-body"><p>{{ $personagem->personalidade ?? '—' }}</p></div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Inventário</h5></div>
                <div class="card-body"><p>{{ $personagem->inventario ?? '—' }}</p></div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const atributos = @json($personagem->atributos ?? []);
    const labels = Object.keys(atributos);
    const data = Object.values(atributos);

    const ctx = document.getElementById('atributosChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: '{{ $personagem->nome }}',
                data: data,
                backgroundColor: 'rgba(255, 193, 7, 0.2)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(255, 193, 7, 1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    suggestedMin: 0,
                    suggestedMax: 20
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
