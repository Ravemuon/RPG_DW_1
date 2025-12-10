<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha: {{ $personagem->nome }}</title>
    <style>
        @page {
            margin: 20px;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #2c3e50;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-card {
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        
        .info-card .label {
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .info-card .value {
            font-size: 14px;
            font-weight: bold;
        }
        
        .attributes-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .attribute-card {
            border: 1px solid #3498db;
            border-radius: 4px;
            text-align: center;
            padding: 8px 5px;
            background-color: #ecf0f1;
        }
        
        .attribute-name {
            font-size: 10px;
            text-transform: uppercase;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .attribute-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        
        .attribute-modifier {
            font-size: 14px;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .attribute-modifier.positive {
            color: #27ae60;
        }
        
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .skill-item {
            border-bottom: 1px solid #eee;
            padding: 4px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .skill-name {
            font-weight: bold;
        }
        
        .skill-bonus {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .proficient {
            color: #27ae60;
            font-weight: bold;
        }
        
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .inventory-table th {
            background-color: #34495e;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        
        .inventory-table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .text-content {
            font-size: 11px;
            line-height: 1.6;
            white-space: pre-line;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #7f8c8d;
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .badge-primary { background-color: #3498db; color: white; }
        .badge-success { background-color: #27ae60; color: white; }
        .badge-warning { background-color: #f39c12; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-danger { background-color: #e74c3c; color: white; }
        
        .xp-bar {
            height: 15px;
            background-color: #ecf0f1;
            border-radius: 7px;
            margin: 10px 0;
            overflow: hidden;
        }
        
        .xp-progress {
            height: 100%;
            background-color: #27ae60;
            text-align: center;
            color: white;
            font-size: 10px;
            line-height: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <h1>{{ $personagem->nome }}</h1>
            <div class="subtitle">
                {{ $personagem->classe->nome ?? 'Sem Classe' }} • Nível {{ $personagem->nivel }} • 
                {{ $personagem->campanha->nome }} • {{ $personagem->sistema->nome }}
            </div>
        </div>
        
        <!-- Informações Básicas -->
        <div class="section">
            <div class="section-title">Informações Básicas</div>
            <div class="grid">
                <div class="info-card">
                    <div class="label">Raça</div>
                    <div class="value">{{ $personagem->raca->nome ?? 'Não definida' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Classe</div>
                    <div class="value">{{ $personagem->classe->nome ?? 'Não definida' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Origem</div>
                    <div class="value">{{ $personagem->origem->nome ?? 'Não definida' }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Nível</div>
                    <div class="value">{{ $personagem->nivel }}</div>
                </div>
                <div class="info-card">
                    <div class="label">XP</div>
                    <div class="value">{{ number_format($personagem->xp) }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Bônus Proficiência</div>
                    <div class="value">+{{ $personagem->bonus_proficiencia }}</div>
                </div>
            </div>
            
            @if($personagem->sanidade || $personagem->sorte)
            <div class="grid" style="margin-top: 10px;">
                @if($personagem->sanidade)
                <div class="info-card">
                    <div class="label">Sanidade</div>
                    <div class="value">{{ $personagem->sanidade }}</div>
                </div>
                @endif
                @if($personagem->sorte)
                <div class="info-card">
                    <div class="label">Sorte</div>
                    <div class="value">{{ $personagem->sorte }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
        
        <!-- Atributos -->
        <div class="section">
            <div class="section-title">Atributos</div>
            <div class="attributes-grid">
                @foreach($atributosCompletos as $key => $atributo)
                    <div class="attribute-card">
                        <div class="attribute-name">
                            {{ $personagem->sistema->atributos[$key] ?? $key }}
                        </div>
                        <div class="attribute-value">{{ $atributo['valor'] }}</div>
                        <div class="attribute-modifier {{ $atributo['modificador'] >= 0 ? 'positive' : '' }}">
                            {{ $atributo['modificador'] >= 0 ? '+' : '' }}{{ $atributo['modificador'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pontos de Vida -->
        @if($pontosVida)
        <div class="section">
            <div class="section-title">Pontos de Vida</div>
            <div style="text-align: center; padding: 15px; background-color: #f8d7da; border-radius: 5px;">
                <div style="font-size: 32px; font-weight: bold; color: #dc3545;">{{ $pontosVida }}</div>
                <div style="font-size: 11px; color: #721c24;">
                    Dado de Vida: {{ $personagem->classe?->dado_vida ?? 'N/A' }}
                </div>
            </div>
        </div>
        @endif
        
        <!-- Progresso de Nível -->
        <div class="section">
            <div class="section-title">Progresso do Nível</div>
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 5px;">
                    <span>XP Atual: {{ number_format($personagem->xp) }}</span>
                    <span>Próximo Nível: {{ number_format($personagem->xpProximoNivel()) }}</span>
                </div>
                <div class="xp-bar">
                    <div class="xp-progress" style="width: {{ min($progressoNivel, 100) }}%;">
                        {{ number_format($progressoNivel, 1) }}%
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Perícias -->
        @if($personagem->pericias && $personagem->pericias->count() > 0)
        <div class="section page-break">
            <div class="section-title">Perícias</div>
            <div class="skills-grid">
                @foreach($personagem->pericias as $personagemPericia)
                    @if($personagemPericia->pericia)
                        <div class="skill-item">
                            <div class="skill-name">
                                {{ $personagemPericia->pericia->nome }}
                                @if($personagemPericia->proficiente)
                                    <span class="proficient">(P)</span>
                                @endif
                            </div>
                            <div class="skill-bonus">
                                {{ $personagemPericia->calcularBonus() >= 0 ? '+' : '' }}{{ $personagemPericia->calcularBonus() }}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Inventário -->
        @if($personagem->inventario && count($personagem->inventario) > 0)
        <div class="section">
            <div class="section-title">Inventário</div>
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantidade</th>
                        <th>Peso</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($personagem->inventario as $item)
                        <tr>
                            <td>{{ $item['nome'] ?? 'Item' }}</td>
                            <td>{{ $item['quantidade'] ?? 1 }}</td>
                            <td>{{ $item['peso'] ?? '-' }}</td>
                            <td>{{ $item['descricao'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Descrição, História e Personalidade -->
        <div class="section page-break">
            <div class="two-columns">
                <!-- Descrição -->
                <div>
                    <div class="section-title">Descrição Física</div>
                    <div class="text-content">
                        {{ $personagem->descricao ?: 'Nenhuma descrição fornecida.' }}
                    </div>
                </div>
                
                <!-- Personalidade -->
                <div>
                    <div class="section-title">Personalidade</div>
                    <div class="text-content">
                        {{ $personagem->personalidade ?: 'Nenhuma descrição de personalidade fornecida.' }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- História -->
        <div class="section">
            <div class="section-title">História de Fundo</div>
            <div class="text-content">
                {{ $personagem->historia ?: 'Nenhuma história fornecida.' }}
            </div>
        </div>
        
        <!-- Rodapé -->
        <div class="footer">
            Ficha gerada em {{ now()->format('d/m/Y H:i') }} • 
            Campanha: {{ $personagem->campanha->nome }} • 
            Sistema: {{ $personagem->sistema->nome }} • 
            Jogador: {{ $personagem->user->name }}
        </div>
    </div>
</body>
</html>