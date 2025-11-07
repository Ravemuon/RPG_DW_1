<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório da Sessão</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Relatório da Sessão</h1>
    <h2>{{ $sessao->titulo }}</h2>

    <p><strong>Campanha:</strong> {{ $sessao->campanha->nome }}</p>
    <p><strong>Data/Hora:</strong> {{ $sessao->data_hora->format('d/m/Y H:i') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($sessao->status) }}</p>

    <h3>Resumo</h3>
    <p>{{ $sessao->resumo ?? 'Nenhum resumo registrado.' }}</p>

    <h3>Personagens Presentes</h3>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Presente</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personagens as $p)
            <tr>
                <td>{{ $p->nome }}</td>
                <td>{{ $p->pivot->presente ? 'Sim' : 'Não' }}</td>
                <td>{{ json_encode($p->pivot->resultado) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
