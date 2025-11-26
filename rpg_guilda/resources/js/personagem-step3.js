document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('step3-form');
    const metodoRolagem = document.getElementById('metodo_rolagem');
    const metodoPontos = document.getElementById('metodo_pontos');
    const metodoManual = document.getElementById('metodo_manual');
    const sortearAtributosBtn = document.getElementById('sortear-atributos');
    const sortearSorteBtn = document.getElementById('sortear-sorte');
    const totalPontosSpan = document.getElementById('total-pontos');

    // Constantes para compra de pontos
    const CUSTO_ATRIBUTOS = {
        8: 0, 9: 1, 10: 2, 11: 3, 12: 4, 13: 5, 14: 7, 15: 9
    };
    const PONTOS_TOTAIS = 27;

    let pontosRestantes = PONTOS_TOTAIS;

    // Inicializar
    function initialize() {
        updateModificadores();
        updateResumo();
        updateTotalPontos();
        setupEventListeners();
    }

    function setupEventListeners() {
        // Eventos para os inputs de atributo
        document.querySelectorAll('.atributo-input').forEach(input => {
            input.addEventListener('input', function() {
                updateModificador(this);
                updateResumoAtributo(this);
                updateTotalPontos();
            });
        });

        // Botões de incremento/decremento
        document.querySelectorAll('.incrementar').forEach(btn => {
            btn.addEventListener('click', function() {
                const atributo = this.getAttribute('data-atributo');
                const input = document.getElementById(`atributo-${atributo}`);
                input.value = parseInt(input.value) + 1;
                input.dispatchEvent(new Event('input'));
            });
        });

        document.querySelectorAll('.decrementar').forEach(btn => {
            btn.addEventListener('click', function() {
                const atributo = this.getAttribute('data-atributo');
                const input = document.getElementById(`atributo-${atributo}`);
                input.value = parseInt(input.value) - 1;
                input.dispatchEvent(new Event('input'));
            });
        });

        // Sortear atributos
        sortearAtributosBtn.addEventListener('click', sortearAtributos);

        // Sortear sorte
        if (sortearSorteBtn) {
            sortearSorteBtn.addEventListener('click', function() {
                document.getElementById('sorte').value = Math.floor(Math.random() * 100) + 1;
            });
        }

        // Mudança de método de distribuição
        metodoRolagem.addEventListener('change', handleMetodoChange);
        metodoPontos.addEventListener('change', handleMetodoChange);
        metodoManual.addEventListener('change', handleMetodoChange);
    }

    function updateModificador(input) {
        const valor = parseInt(input.value) || 0;
        const atributo = input.getAttribute('data-atributo');
        const modificador = Math.floor((valor - 10) / 2);

        document.getElementById(`mod-${atributo}`).textContent = modificador >= 0 ? `+${modificador}` : modificador;
    }

    function updateModificadores() {
        document.querySelectorAll('.atributo-input').forEach(input => {
            updateModificador(input);
        });
    }

    function updateResumoAtributo(input) {
        const valor = parseInt(input.value) || 0;
        const atributo = input.getAttribute('data-atributo');
        const modificador = Math.floor((valor - 10) / 2);

        document.getElementById(`resumo-${atributo}`).textContent = valor;
        document.getElementById(`resumo-mod-${atributo}`).textContent = `Mod: ${modificador >= 0 ? '+' : ''}${modificador}`;
    }

    function updateResumo() {
        document.querySelectorAll('.atributo-input').forEach(input => {
            updateResumoAtributo(input);
        });
    }

    function updateTotalPontos() {
        if (!metodoPontos.checked) {
            totalPontosSpan.textContent = 'N/A';
            return;
        }

        let total = 0;
        document.querySelectorAll('.atributo-input').forEach(input => {
            const valor = parseInt(input.value) || 0;
            total += CUSTO_ATRIBUTOS[valor] || 0;
        });

        pontosRestantes = PONTOS_TOTAIS - total;
        totalPontosSpan.textContent = `${total} (Restantes: ${pontosRestantes})`;

        // Colorir de vermelho se pontos forem negativos
        totalPontosSpan.className = pontosRestantes < 0 ? 'text-danger' : '';
    }

    function handleMetodoChange() {
        const isPontos = metodoPontos.checked;

        document.querySelectorAll('.atributo-input').forEach(input => {
            if (isPontos) {
                input.setAttribute('min', 8);
                input.setAttribute('max', 15);
            } else {
                input.setAttribute('min', 1);
                input.setAttribute('max', 20);
            }
        });

        updateTotalPontos();
    }

    function sortearAtributos() {
        // Fazer requisição para o endpoint de sortear atributos
        fetch(`/personagens/${PERSONAGEM_ID}/sortear-atributos`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Preencher os atributos principais
            for (const [atributo, valor] of Object.entries(data)) {
                const input = document.getElementById(`atributo-${atributo}`);
                if (input) {
                    input.value = valor;
                    input.dispatchEvent(new Event('input'));
                }
            }

            // Preencher sorte e sanidade se existirem
            if (USA_SORTE && data.sorte) {
                document.getElementById('sorte').value = data.sorte;
            }
            if (USA_SANIDADE && data.sanidade) {
                document.getElementById('sanidade').value = data.sanidade;
            }
        })
        .catch(error => {
            console.error('Erro ao sortear atributos:', error);
            alert('Erro ao sortear atributos. Tente novamente.');
        });
    }

    // Inicializar
    initialize();
});
