<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Missão - {{ $missao->titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; color: #212529; }
        h1, h2 { color: #343a40; }
        .card { border: 1px solid #343a40; padding: 20px; margin-bottom: 20px; border-radius: 10px; background-color: #ffffff; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 5px; margin-right: 5px; }
        .badge-prioridade { background-color: #0d6efd; color: #fff; }
        .badge-status { background-color: #6c757d; color: #fff; }
    </style>
</head>
<body>
    <h1>Missão: {{ $missao->titulo }}</h1>
    <h2>Campanha: {{ $campanha->nome }}</h2>

    <div class="card">
        <div>
            <span class="badge badge-prioridade">Prioridade: {{ ucfirst($missao->prioridade) }}</span>
            <span class="badge badge-status">Status: {{ ucfirst($missao->status) }}</span>
        </div>
        <hr>
        <p><strong>Descrição:</strong> {{ $missao->descricao ?? 'Nenhuma descrição.' }}</p>
        <p><strong>Recompensa:</strong> {{ $missao->recompensa ?? 'Nenhuma recompensa definida.' }}</p>
        <p><strong>Criada em:</strong> {{ $missao->created_at->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
