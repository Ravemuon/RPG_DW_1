document.addEventListener('DOMContentLoaded', function() {
    const racaSelect = document.getElementById('raca_id');
    const classeSelect = document.getElementById('classe_id');
    const origemSelect = document.getElementById('origem_id');
    const form = document.getElementById('step2-form');

    // Elementos de detalhes
    const racaDetalhes = document.getElementById('raca-detalhes');
    const classeDetalhes = document.getElementById('classe-detalhes');
    const origemDetalhes = document.getElementById('origem-detalhes');

    // Elementos de resumo
    const resumoRaca = document.getElementById('resumo-raca');
    const resumoClasse = document.getElementById('resumo-classe');
    const resumoOrigem = document.getElementById('resumo-origem');

    // Função para analisar dados JSON
    function parseJsonData(str) {
        if (!str || str === 'null' || str === 'undefined') return {};
        try {
            return typeof str === 'object' ? str : JSON.parse(str);
        } catch (e) {
            console.error('Erro ao parsear JSON:', e, str);
            return {};
        }
    }

    // Atualizar detalhes da Raça
    function updateRacaDetalhes() {
        const selected = racaSelect.options[racaSelect.selectedIndex];
        if (!selected || !selected.value) {
            racaDetalhes.innerHTML = `
                <div class="alert alert-info">
                    <p class="mb-0">Selecione uma raça para ver seus detalhes</p>
                </div>
            `;
            resumoRaca.innerHTML = '<span class="text-muted">Não selecionada</span>';
            return;
        }

        const nome = selected.text;
        const descricao = selected.getAttribute('data-descricao') || 'Sem descrição disponível.';
        const modificadores = parseJsonData(selected.getAttribute('data-modificadores'));
        const bonusLivre = parseInt(selected.getAttribute('data-bonus-livre')) || 0;
        const pagina = selected.getAttribute('data-pagina');

        let modificadoresHtml = '';
        if (Object.keys(modificadores).length > 0) {
            modificadoresHtml = '<div class="mt-2"><strong>Bônus de Atributos:</strong><br>';
            for (const [atributo, valor] of Object.entries(modificadores)) {
                const sinal = valor >= 0 ? '+' : '';
                modificadoresHtml += `<span class="badge bg-success me-1 mb-1">${atributo}: ${sinal}${valor}</span>`;
            }
            modificadoresHtml += '</div>';
        }

        if (bonusLivre > 0) {
            modificadoresHtml += `<div class="mt-2"><strong>Bônus Livre:</strong> +${bonusLivre} em atributos à sua escolha</div>`;
        }

        racaDetalhes.innerHTML = `
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">${nome}</h5>
                    <p class="card-text">${descricao}</p>
                    ${modificadoresHtml}
                    ${pagina ? `<div class="mt-2"><small class="text-muted">Referência: ${pagina}</small></div>` : ''}
                </div>
            </div>
        `;

        resumoRaca.innerHTML = `<strong class="text-success">${nome}</strong>`;
    }

    // Atualizar detalhes da Classe
    function updateClasseDetalhes() {
        const selected = classeSelect.options[classeSelect.selectedIndex];
        if (!selected || !selected.value) {
            classeDetalhes.innerHTML = `
                <div class="alert alert-info">
                    <p class="mb-0">Selecione uma classe para ver seus detalhes</p>
                </div>
            `;
            resumoClasse.innerHTML = '<span class="text-muted">Não selecionada</span>';
            return;
        }

        const nome = selected.text;
        const descricao = selected.getAttribute('data-descricao') || 'Sem descrição disponível.';
        const dadoVida = selected.getAttribute('data-dado-vida') || 'd6';
        const usaMagia = selected.getAttribute('data-usa-magia') === 'Sim';
        const pericias = parseJsonData(selected.getAttribute('data-pericias'));
        const equipamento = parseJsonData(selected.getAttribute('data-equipamento'));
        const atributosBonus = parseJsonData(selected.getAttribute('data-atributos-bonus'));
        const poderes = parseJsonData(selected.getAttribute('data-poderes'));
        const pagina = selected.getAttribute('data-pagina');

        let detalhesHtml = `
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">${nome}</h5>
                    <p class="card-text">${descricao}</p>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Dado de Vida:</strong> ${dadoVida.toUpperCase()}</p>
                            <p><strong>Usa Magia:</strong> ${usaMagia ? 'Sim' : 'Não'}</p>
                        </div>
        `;

        // Perícias
        if (pericias && Object.keys(pericias).length > 0) {
            detalhesHtml += '<div class="col-md-6"><strong>Perícias:</strong><br>';
            if (pericias.fixas && pericias.fixas.length > 0) {
                detalhesHtml += `<small>Fixas: ${pericias.fixas.join(', ')}</small><br>`;
            }
            if (pericias.lista && pericias.lista.length > 0) {
                detalhesHtml += `<small>Escolher ${pericias.escolha || 0} de: ${pericias.lista.join(', ')}</small>`;
            }
            detalhesHtml += '</div>';
        }

        detalhesHtml += '</div>';

        // Atributos Bônus
        if (atributosBonus && Object.keys(atributosBonus).length > 0) {
            detalhesHtml += '<div class="mt-2"><strong>Bônus de Atributos:</strong><br>';
            for (const [atributo, valor] of Object.entries(atributosBonus)) {
                const sinal = valor >= 0 ? '+' : '';
                detalhesHtml += `<span class="badge bg-primary me-1 mb-1">${atributo}: ${sinal}${valor}</span>`;
            }
            detalhesHtml += '</div>';
        }

        // Poderes
        if (poderes && Object.keys(poderes).length > 0) {
            detalhesHtml += '<div class="mt-2"><strong>Poderes:</strong><br>';
            for (const [poder, desc] of Object.entries(poderes)) {
                detalhesHtml += `<p class="mb-1"><strong>${poder}:</strong> ${desc}</p>`;
            }
            detalhesHtml += '</div>';
        }

        // Equipamento
        if (equipamento && Object.keys(equipamento).length > 0) {
            detalhesHtml += '<div class="mt-2"><strong>Equipamento Inicial:</strong><br>';
            if (equipamento.fixas && equipamento.fixas.length > 0) {
                detalhesHtml += `<small>Fixos: ${equipamento.fixas.join(', ')}</small><br>`;
            }
            if (equipamento.opcoes && equipamento.opcoes.length > 0) {
                detalhesHtml += '<small>Opções disponíveis</small>';
            }
            detalhesHtml += '</div>';
        }

        if (pagina) {
            detalhesHtml += `<div class="mt-2"><small class="text-muted">Referência: ${pagina}</small></div>`;
        }

        detalhesHtml += '</div></div>';

        classeDetalhes.innerHTML = detalhesHtml;
        resumoClasse.innerHTML = `<strong class="text-primary">${nome}</strong>`;
    }

    // Atualizar detalhes da Origem
    function updateOrigemDetalhes() {
        const selected = origemSelect.options[origemSelect.selectedIndex];
        if (!selected || !selected.value) {
            origemDetalhes.innerHTML = `
                <div class="alert alert-info">
                    <p class="mb-0">Selecione uma origem para ver seus detalhes</p>
                </div>
            `;
            resumoOrigem.innerHTML = '<span class="text-muted">Não selecionada</span>';
            return;
        }

        const nome = selected.text;
        const descricao = selected.getAttribute('data-descricao') || 'Sem descrição disponível.';
        const bonusPericias = parseJsonData(selected.getAttribute('data-bonus-pericias'));
        const recursosAdicionais = parseJsonData(selected.getAttribute('data-recursos-adicionais'));
        const pagina = selected.getAttribute('data-pagina');

        let detalhesHtml = `
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">${nome}</h5>
                    <p class="card-text">${descricao}</p>
        `;

        // Bônus de Perícias
        if (bonusPericias && Object.keys(bonusPericias).length > 0) {
            detalhesHtml += '<div class="mt-2"><strong>Perícias Bônus:</strong><br>';
            for (const [pericia, bonus] of Object.entries(bonusPericias)) {
                detalhesHtml += `<span class="badge bg-info me-1 mb-1">${pericia}</span>`;
            }
            detalhesHtml += '</div>';
        }

        // Recursos Adicionais
        if (recursosAdicionais && Object.keys(recursosAdicionais).length > 0) {
            detalhesHtml += '<div class="mt-2"><strong>Recursos Adicionais:</strong><br>';
            for (const [recurso, desc] of Object.entries(recursosAdicionais)) {
                detalhesHtml += `<p class="mb-1"><strong>${recurso}:</strong> ${desc}</p>`;
            }
            detalhesHtml += '</div>';
        }

        if (pagina) {
            detalhesHtml += `<div class="mt-2"><small class="text-muted">Referência: ${pagina}</small></div>`;
        }

        detalhesHtml += '</div></div>';

        origemDetalhes.innerHTML = detalhesHtml;
        resumoOrigem.innerHTML = `<strong class="text-success">${nome}</strong>`;
    }

    // Event Listeners
    racaSelect.addEventListener('change', updateRacaDetalhes);
    classeSelect.addEventListener('change', updateClasseDetalhes);
    origemSelect.addEventListener('change', updateOrigemDetalhes);

    // Inicializar
    updateRacaDetalhes();
    updateClasseDetalhes();
    updateOrigemDetalhes();
});
