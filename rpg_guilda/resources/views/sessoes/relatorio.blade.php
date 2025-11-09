<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório da Sessão - {{ $sessao->titulo }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
            color: #222;
            line-height: 1.6;
            background-color: #fff;
        }
        h1, h2 {
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .section { margin-bottom: 25px; }
        .info {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 15px;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.85em;
            color: #777;
        }
        .highlight { color: #444; font-weight: bold; }
    </style>
</head>
<body>

    <h1>🎲 {{ $sessao->titulo }}</h1>

    <p class="info">
        <strong>Campanha:</strong> {{ $sessao->campanha->nome ?? 'Desconhecida' }}<br>
        <strong>Data:</strong> {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}<br>
        <strong>Mestre:</strong> {{ $sessao->campanha->mestre->name ?? 'Desconhecido' }}
    </p>

    <div class="section">
        <h2>📜 Resumo da Aventura</h2>
        <p>{{ $sessao->resumo ?? 'Nenhum resumo foi registrado.' }}</p>
    </div>

    <div class="section">
        <h2>🧙 Personagens Envolvidos</h2>
        @if($personagens->isNotEmpty())
            <ul>
                @foreach($personagens as $personagem)
                    <li>
                        {{ $personagem->nome }}
                        <span class="highlight">—</span>
                        jogador: {{ $personagem->usuario->name ?? 'Desconhecido' }}
                    </li>
                @endforeach
            </ul>
        @else
            <p><em>Nenhum personagem vinculado a esta sessão.</em></p>
        @endif
    </div>

    <div class="section">
        <h2>💰 Recompensas</h2>
        <p>
            XP: <strong>{{ $sessao->xp_ganho ?? '0' }}</strong><br>
            Ouro: <strong>{{ $sessao->ouro_ganho ?? '0' }}</strong>
        </p>
    </div>

    @if(!empty($sessao->notas))
        <div class="section">
            <h2>🗒️ Observações do Mestre</h2>
            <p>{{ $sessao->notas }}</p>
        </div>
    @endif

    <div class="footer">
        Relatório gerado automaticamente pelo Portal do Aventureiro em {{ now()->format('d/m/Y H:i') }}.
    </div>

</body>
</html>
