<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Sistemas de RPG</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --border-color: #dee2e6;
            --text-color: #333;
            --light-text: #666;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #fff;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--primary-color);
        }

        h1 {
            font-size: 32px;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 700;
        }

        .subtitle {
            font-size: 16px;
            color: var(--dark-gray);
            font-style: italic;
            margin-bottom: 20px;
        }

        .sistema-card {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 5px solid var(--accent-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sistema-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2 {
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--secondary-color);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--medium-gray);
        }

        .sistema-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .meta-item {
            flex: 1;
            min-width: 200px;
        }

        .meta-item strong {
            display: block;
            color: var(--primary-color);
            font-size: 13px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-item span {
            font-size: 14px;
            line-height: 1.5;
        }

        .descricao {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            border-left: 3px solid var(--medium-gray);
            font-size: 14px;
            line-height: 1.7;
        }

        h3 {
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 12px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h3:before {
            content: "▶";
            font-size: 12px;
            color: var(--accent-color);
        }

        .section-container {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-top: 5px;
            margin-bottom: 15px;
        }

        th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            text-align: left;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        td {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
            background: white;
            transition: background-color 0.2s ease;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8f9fa;
        }

        .no-data {
            font-style: italic;
            color: var(--dark-gray);
            text-align: center;
            padding: 20px;
            background: var(--light-gray);
            border-radius: 6px;
            border: 1px dashed var(--border-color);
            margin: 10px 0;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .item-tag {
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .item-tag:hover {
            background: var(--medium-gray);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            color: var(--dark-gray);
            font-size: 13px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
            background: var(--medium-gray);
            color: var(--text-color);
        }

        .complexidade-baixa { background: #d4edda; color: #155724; }
        .complexidade-media { background: #fff3cd; color: #856404; }
        .complexidade-alta { background: #f8d7da; color: #721c24; }

        @media print {
            .sistema-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .sistema-card:hover {
                transform: none;
                box-shadow: none;
            }
            
            .no-print {
                display: none;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
                font-size: 13px;
            }
            
            .sistema-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .meta-item {
                min-width: 100%;
            }
            
            .items-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            h1 {
                font-size: 26px;
            }
            
            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>📚 Catálogo de Sistemas de RPG</h1>
        <div class="subtitle">Análise comparativa de sistemas e suas mecânicas</div>
        <div class="no-print" style="font-size: 13px; color: var(--dark-gray);">
            {{ count($sistemas) }} sistemas cadastrados | Gerado em {{ date('d/m/Y H:i') }}
        </div>
    </div>

    @foreach($sistemas as $sistema)
        <div class="sistema-card">
            <h2>
                {{ $sistema->nome }}
                @if($sistema->complexidade)
                    <span class="badge complexidade-{{ strtolower($sistema->complexidade) }}">
                        {{ $sistema->complexidade }}
                    </span>
                @endif
            </h2>

            <div class="sistema-meta">
                @if($sistema->foco)
                    <div class="meta-item">
                        <strong>🎯 Foco Principal</strong>
                        <span>{{ $sistema->foco }}</span>
                    </div>
                @endif
                
                @if($sistema->complexidade)
                    <div class="meta-item">
                        <strong>⚙️ Nível de Complexidade</strong>
                        <span>{{ $sistema->complexidade }}</span>
                    </div>
                @endif
            </div>

            @if($sistema->descricao)
                <div class="descricao">
                    <strong>📖 Descrição:</strong><br>
                    {{ $sistema->descricao }}
                </div>
            @endif

            <div class="sections-container">
                {{-- CLASSES --}}
                <div class="section-container">
                    <h3>Classes Disponíveis 
                        @if($sistema->classes->count())
                            <span class="badge">{{ $sistema->classes->count() }}</span>
                        @endif
                    </h3>
                    @if($sistema->classes->count())
                        <div class="items-grid">
                            @foreach($sistema->classes as $classe)
                                <div class="item-tag">{{ $classe->nome }}</div>
                            @endforeach
                        </div>
                    @else
                        <p class="no-data">Nenhuma classe cadastrada para este sistema</p>
                    @endif
                </div>

                {{-- RAÇAS --}}
                <div class="section-container">
                    <h3>Raças 
                        @if($sistema->racas->count())
                            <span class="badge">{{ $sistema->racas->count() }}</span>
                        @endif
                    </h3>
                    @if($sistema->racas->count())
                        <div class="items-grid">
                            @foreach($sistema->racas as $raca)
                                <div class="item-tag">{{ $raca->nome }}</div>
                            @endforeach
                        </div>
                    @else
                        <p class="no-data">Nenhuma raça cadastrada para este sistema</p>
                    @endif
                </div>

                {{-- ORIGENS --}}
                <div class="section-container">
                    <h3>Origens 
                        @if($sistema->origens->count())
                            <span class="badge">{{ $sistema->origens->count() }}</span>
                        @endif
                    </h3>
                    @if($sistema->origens->count())
                        <div class="items-grid">
                            @foreach($sistema->origens as $origem)
                                <div class="item-tag">{{ $origem->nome }}</div>
                            @endforeach
                        </div>
                    @else
                        <p class="no-data">Nenhuma origem cadastrada para este sistema</p>
                    @endif
                </div>

                {{-- PERÍCIAS --}}
                <div class="section-container">
                    <h3>Perícias 
                        @if($sistema->pericias->count())
                            <span class="badge">{{ $sistema->pericias->count() }}</span>
                        @endif
                    </h3>
                    @if($sistema->pericias->count())
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome da Perícia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sistema->pericias as $pericia)
                                    <tr>
                                        <td>{{ $pericia->nome }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="no-data">Nenhuma perícia cadastrada para este sistema</p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <div class="footer">
        <p>Documento gerado automaticamente • Sistema de Catálogo RPG • 
           Total de {{ count($sistemas) }} sistemas analisados</p>
    </div>
</body>
</html>