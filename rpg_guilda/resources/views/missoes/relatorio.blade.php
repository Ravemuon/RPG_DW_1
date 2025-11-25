<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Missão - {{ $missao->titulo }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f2f3f7;
            color: #2c2c2c;
            margin: 40px;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 5px;
            color: #1d1d1d;
        }

        h2 {
            font-size: 22px;
            margin-top: 0;
            color: #444;
        }

        .card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #d0d0d0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-top: 25px;
        }

        .badge {
            display: inline-block;
            padding: 6px 14px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            margin-right: 8px;
        }

        .badge-prioridade {
            background: #0d6efd;
            color: #fff;
        }

        .badge-status {
            background: #6c757d;
            color: #fff;
        }

        .label {
            font-weight: bold;
            margin-top: 20px;
            font-size: 16px;
            display: block;
            color: #222;
        }

        .divider {
            height: 1px;
            background: #ccc;
            margin: 20px 0;
        }

    </style>
</head>
<body>

    <h1>🎯 Missão: {{ $missao->titulo }}</h1>
    <h2>📘 Campanha: {{ $campanha->nome }}</h2>

    <div class="card">

        <div>
            <span class="badge badge-prioridade">Prioridade: {{ ucfirst($missao->prioridade) }}</span>
            <span class="badge badge-status">Status: {{ ucfirst($missao->status) }}</span>
        </div>

        <div class="divider"></div>

        <span class="label">Descrição</span>
        <p>{{ $missao->descricao ?? 'Nenhuma descrição.' }}</p>

        <span class="label">Recompensa</span>
        <p>{{ $missao->recompensa ?? 'Nenhuma recompensa definida.' }}</p>

        <span class="label">Data de Criação</span>
        <p>{{ $missao->created_at->format('d/m/Y H:i') }}</p>

    </div>

</body>
</html>
