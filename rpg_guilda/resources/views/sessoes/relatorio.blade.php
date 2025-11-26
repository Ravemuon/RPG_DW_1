<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório da Sessão {{ $sessao->titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }

        h1, h2, h3 {
            color: #222;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .resumo {
            margin-top: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #333;
        }
    </style>
</head>
<body>
    <h1>Relatório da Sessão</h1>

    <h2>Informações da Sessão</h2>
    <p><strong>Título:</strong> {{ $sessao->titulo }}</p>
    <p><strong>Campanha:</strong> {{ $sessao->campanha->nome }}</p>
    <p><strong>Data/Hora:</strong> {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}</p>
    <p><strong>Status:</strong> <span class="status">{{ $sessao->status }}</span></p>

    @if($sessao->resumo)
        <div class="resumo">
            <h3>Resumo da Sessão</h3>
            <p>{{ $sessao->resumo }}</p>
        </div>
    @endif

    <h2>Personagens</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Jogador</th>
                <th>Presença</th>
                <th>Resultado / Observações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessao->personagens as $personagem)
                <tr>
                    <td>{{ $personagem->nome }}</td>
                    <td>{{ $personagem->user->name ?? 'N/A' }}</td>
                    <td>{{ $personagem->pivot->presente ? '✔ Presente' : '✖ Ausente' }}</td>
                    <td>
                        @if($personagem->pivot->resultado)
                            @foreach($personagem->pivot->resultado as $key => $value)
                                <strong>{{ ucfirst($key) }}:</strong> {{ $value }}<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        Relatório gerado automaticamente pelo sistema RPG.
    </p>
</body>
</html>
