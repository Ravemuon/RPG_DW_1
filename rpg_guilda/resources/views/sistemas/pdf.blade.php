<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistemas de RPG</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #ddd; }
    </style>
</head>
<body>
    <h1>Sistemas de RPG</h1>
    @foreach($sistemas as $sistema)
        <h2>{{ $sistema->nome }}</h2>
        <p><strong>Foco:</strong> {{ $sistema->foco ?? 'N/A' }} |
           <strong>Complexidade:</strong> {{ $sistema->complexidade ?? 'N/A' }}</p>
        <p><strong>Descrição:</strong> {{ $sistema->descricao ?? 'N/A' }}</p>

        @if($sistema->classes->count())
        <h3>Classes</h3>
        <ul>
            @foreach($sistema->classes as $classe)
                <li>{{ $classe->nome }}</li>
            @endforeach
        </ul>
        @endif

        @if($sistema->racas->count())
        <h3>Raças</h3>
        <ul>
            @foreach($sistema->racas as $raca)
                <li>{{ $raca->nome }}</li>
            @endforeach
        </ul>
        @endif

        @if($sistema->origens->count())
        <h3>Origens</h3>
        <ul>
            @foreach($sistema->origens as $origem)
                <li>{{ $origem->nome }}</li>
            @endforeach
        </ul>
        @endif

        @if($sistema->pericias->count())
        <h3>Perícias</h3>
        <ul>
            @foreach($sistema->pericias as $pericia)
                <li>{{ $pericia->nome }}</li>
            @endforeach
        </ul>
        @endif

        <hr>
    @endforeach
</body>
</html>
