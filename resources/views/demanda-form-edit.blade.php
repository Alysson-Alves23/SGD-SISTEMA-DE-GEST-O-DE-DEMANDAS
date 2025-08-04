@extends('layouts.app')

@section('title', 'Editar Demanda')

@push('styles')
<style>
    .back-button {display: inline-block; padding: 8px 15px; margin-bottom: 25px; background: #6c757d; color: #fff; border-radius: 4px; text-decoration: none; font-size: 0.9em; font-weight: 500;}
    .back-button:hover {background: #5a6268;}
    .demand-form-card {background: #fff; padding: 25px 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);}
    .demand-form-card h2 {margin: 0 0 25px; font-size: 1.5em; color: #007bff; border-bottom: 1px solid #eee; padding-bottom: 10px;}
    .form-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px 25px;}
    .form-group {margin-bottom: 5px;}
    .form-group label {display: block; color: #555; font-weight: 600; margin-bottom: 6px; font-size: 0.9em;}
    .form-group input, .form-group select, .form-group textarea, .form-group .readonly-field {width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 1rem; color: #333; background-color: #fff;}
    .form-group .readonly-field {background-color: #e9ecef; color: #495057; cursor: not-allowed; line-height: 1.5;}
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {border-color: #007bff; outline: none; box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);}
    .form-group textarea {min-height: 100px; resize: vertical;}
    .form-actions {margin-top: 30px; text-align: right;}
    .submit-button {padding: 12px 25px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s ease;}
    .submit-button:hover {background-color: #218838;}
    .full-width-group {grid-column: 1 / -1;}
    .message-area {text-align: center; color: #777; padding: 10px 0; min-height: 20px;}
    .modal {display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);}
    .modal-content {background-color: #fefefe; margin: 10% auto; padding: 25px; border: 1px solid #ccc; border-radius: 8px; width: 90%; max-width: 550px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);}
    .modal-header {padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 20px;}
    .modal-header h2 {margin: 0; font-size: 1.6em; color: #333;}
    .modal-body textarea {width: 100%; min-height: 100px; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; resize: vertical; font-size: 1rem;}
    .modal-footer {text-align: right; padding-top: 10px;}
    .modal-footer button {padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer; font-weight: 500; margin-left: 10px; font-size: 0.95rem;}
    .modal-confirm-button {background-color: #28a745; color: white;}
    .modal-confirm-button:hover {background-color: #218838;}
    .modal-cancel-button {background-color: #6c757d; color: white;}
    .modal-cancel-button:hover {background-color: #5a6268;}

           .page-container {
            max-width: 800px;
        }
</style>
@endpush

@section('content')
    <div id="demandEditContainer" style="display: none;">
        <a href="/dashboard" id="backButtonForm" class="back-button">&larr; Voltar para o Painel</a>

        <div class="demand-form-card">
            <h2 id="formSubTitle">Editar Informações da Demanda</h2>
            <form id="editDemandForm" method="POST">
                <div class="form-grid">
                    <div class="form-group"><label>Número da Demanda:</label><div id="numero_demanda_display" class="readonly-field"></div></div>
                    <div class="form-group"><label>Solicitante:</label><div id="solicitante_nome_display" class="readonly-field"></div></div>
                    <div class="form-group"><label>Data de Recebimento:</label><div id="data_recebimento_display" class="readonly-field"></div></div>
                    <div class="form-group"><label>Executor Designado:</label><div id="executor_nome_display" class="readonly-field"></div></div>
                    <div class="form-group"><label for="tipo">Tipo da Demanda</label><select id="tipo" name="tipo" required></select></div>
                    <div class="form-group"><label for="empresa">Empresa</label><input type="text" id="empresa" name="empresa"></div>
                    <div class="form-group"><label for="natureza">Natureza</label><select id="natureza" name="natureza"></select></div>
                    <div class="form-group"><label for="cliente">Cliente</label><input type="text" id="cliente" name="cliente"></div>
                    <div class="form-group"><label for="numero_pedido_ou_nf">Nº Pedido/NF</label><input type="text" id="numero_pedido_ou_nf" name="numero_pedido_ou_nf"></div>
                    <div class="form-group"><label for="prazo_execucao">Prazo de Execução</label><input type="date" id="prazo_execucao" name="prazo_execucao"></div>
                    <div class="form-group"><label for="data_planejamento">Data de Planejamento</label><input type="date" id="data_planejamento" name="data_planejamento"></div>
                    <div class="form-group"><label for="previsao_inicio">Previsão de Início</label><input type="date" id="previsao_inicio" name="previsao_inicio"></div>
                    <div class="form-group"><label for="inicio_execucao">Início da Execução</label><input type="date" id="inicio_execucao" name="inicio_execucao"></div>
                    <div class="form-group"><label for="finalizacao_execucao">Finalização da Execução</label><input type="date" id="finalizacao_execucao" name="finalizacao_execucao"></div>
                    <div class="form-group"><label for="status_planejamento">Status do Planejamento</label><select id="status_planejamento" name="status_planejamento"></select></div>
                    <div class="form-group"><label for="status_execucao">Status da Execução</label><select id="status_execucao" name="status_execucao" required></select></div>
                    <div class="form-group"><label for="tempo_execucao">Tempo de Execução</label><input type="text" id="tempo_execucao" name="tempo_execucao"></div>
                    <div class="form-group full-width-group"><label for="descricao_itens">Descrição Detalhada</label><textarea id="descricao_itens" name="descricao_itens" rows="4" required></textarea></div>
                    <div class="form-group full-width-group"><label for="descricao_pendencia">Descrição da Pendência</label><textarea id="descricao_pendencia" name="descricao_pendencia" rows="3"></textarea></div>
                    <div class="form-group full-width-group"><label for="observacoes">Observações Gerais</label><textarea id="observacoes" name="observacoes" rows="3"></textarea></div>
                </div>
                <div class="form-actions"><button type="button" id="openChangeDescriptionModalButton" class="submit-button">Salvar Alterações</button></div>
                <div id="formMessageArea" class="message-area"></div>
            </form>
        </div>
    </div>
    <div id="loadingOrErrorArea"></div>

    <div id="changeDescriptionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h2>Descreva a Alteração</h2></div>
            <div class="modal-body"><p>Forneça um breve resumo ou motivo para as alterações realizadas.</p><textarea id="mudanca_descricao_modal_textarea" placeholder="Ex: Status alterado para Em Andamento."></textarea></div>
            <div class="modal-footer"><button type="button" id="cancelModalButton" class="modal-cancel-button">Cancelar</button><button type="button" id="confirmSaveButton" class="modal-confirm-button">Confirmar e Salvar</button></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function sanitizeForHtml(text) {
        return text ? String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;") : '';
    }

    function formatDateToInputValue(dateString) {
        if (!dateString) return '';
        try {
            return new Date(dateString).toISOString().split('T')[0];
        } catch (e) { return ''; }
    }
    
    function populateSelectWithOptions(selectEl, options, selectedVal, placeholder) {
        if (!selectEl) return;
        let html = `<option value="">${sanitizeForHtml(placeholder)}</option>`;
        options.forEach(opt => {
            const val = typeof opt === 'object' ? opt.value : opt;
            const label = typeof opt === 'object' ? opt.label : opt;
            const isSelected = String(val) === String(selectedVal);
            html += `<option value="${sanitizeForHtml(val)}" ${isSelected ? 'selected' : ''}>${sanitizeForHtml(label)}</option>`;
        });
        selectEl.innerHTML = html;
    }

    function fillForm(demanda) {
        document.getElementById('numero_demanda_display').textContent = demanda.numero_demanda;
        document.getElementById('solicitante_nome_display').textContent = demanda.solicitante.nome;
        document.getElementById('executor_nome_display').textContent = demanda.executor ? demanda.executor.nome : 'N/A';
        document.getElementById('data_recebimento_display').textContent = new Date(demanda.criado_em).toLocaleDateString('pt-BR');
        
        document.getElementById('empresa').value = demanda.empresa || '';
        document.getElementById('cliente').value = demanda.cliente || '';
        document.getElementById('numero_pedido_ou_nf').value = demanda.numero_pedido_ou_nf || '';
        document.getElementById('prazo_execucao').value = formatDateToInputValue(demanda.prazo_execucao);
        document.getElementById('data_planejamento').value = formatDateToInputValue(demanda.data_planejamento);
        document.getElementById('previsao_inicio').value = formatDateToInputValue(demanda.previsao_inicio);
        document.getElementById('inicio_execucao').value = formatDateToInputValue(demanda.inicio_execucao);
        document.getElementById('finalizacao_execucao').value = formatDateToInputValue(demanda.finalizacao_execucao);
        document.getElementById('tempo_execucao').value = demanda.tempo_execucao || '';
        document.getElementById('descricao_itens').value = demanda.descricao_itens || '';
        document.getElementById('descricao_pendencia').value = demanda.descricao_pendencia || '';
        document.getElementById('observacoes').value = demanda.observacoes || '';

        populateSelectWithOptions(document.getElementById('tipo'), ["Demanda Padrão", "Suporte Técnico", "Demanda Comercial", "Demanda de Estoque", "Demanda de Manutenção", "Demanda Produção", "Demanda de Serviços Gerais"], demanda.tipo, "Selecione");
        populateSelectWithOptions(document.getElementById('natureza'), ["SERVIÇOS GERAIS", "PRODUÇÃO", "SUPORTE E MANUTENÇÃO", "PRODUÇÃO P/ ESTOQUE", "INTERNA"], demanda.natureza, "Selecione");
        populateSelectWithOptions(document.getElementById('status_planejamento'),  ['Finalizada','Aberta','Fechada','Em Andamento','Pendente'], demanda.status_planejamento, "Não Definido");
        populateSelectWithOptions(document.getElementById('status_execucao'),  ['Finalizada','Aberta','Fechada','Em Andamento','Pendente'], demanda.status_execucao, "Selecione");
    }



    async function initializeEditForm() {
        const userToken = localStorage.getItem('jwtToken');
        if (!userToken) { window.location.href = '/login'; return; }

        const urlParams = new URLSearchParams(window.location.search);
        const demandId = urlParams.get('id');
        const loadingOrErrorArea = document.getElementById('loadingOrErrorArea');
        const pageTitleElement = document.getElementById('pageTitle');
        if (!demandId) {
            loadingOrErrorArea.innerHTML = '<p class="message-area">ID da Demanda não fornecido.</p>';
            return;
        }

        loadingOrErrorArea.innerHTML = '<p class="message-area">Carregando dados...</p>';

        try {
            const [demandResponse, usersResponse] = await Promise.all([
                fetch(`/api/demandas/${demandId}`, { headers: { 'Authorization': `Bearer ${userToken}`, 'Accept': 'application/json' } }),
                fetch('/api/users', { headers: { 'Authorization': `Bearer ${userToken}`, 'Accept': 'application/json' } })
            ]);

            if (demandResponse.status === 401 || usersResponse.status === 401) {
                localStorage.removeItem('jwtToken');
                alert('Sessão expirada.');
                window.location.href = '/login';
                return;
            }
            if (demandResponse.status === 403) {
                const errorData = await demandResponse.json();
                throw new Error(errorData.error || 'Não autorizado para visualizar esta demanda.');
            }
            if (usersResponse.status === 403) {
                const errorData = await usersResponse.json();
                throw new Error(errorData.error || 'Não autorizado para acessar usuários.');
            }
            if (!demandResponse.ok) throw new Error(`Erro ao buscar demanda: ${demandResponse.statusText}`);
            if (!usersResponse.ok) throw new Error(`Erro ao buscar usuários: ${usersResponse.statusText}`);

            const demandData = await demandResponse.json();
            const allExecutors = await usersResponse.json();

            if (demandData && demandData.demanda) {
                loadingOrErrorArea.style.display = 'none';
                document.getElementById('demandEditContainer').style.display = 'block';
                if(pageTitleElement) pageTitleElement.textContent = `Editar Demanda #${demandData.demanda.numero_demanda}`;
                fillForm(demandData.demanda);
            } else {
                throw new Error('Dados da demanda não encontrados na resposta da API.');
            }
        } catch (error) {
            loadingOrErrorArea.innerHTML = `<p class="message-area">Falha ao carregar dados: ${sanitizeForHtml(error.message)}</p>`;
        }
    }

    document.addEventListener('DOMContentLoaded', initializeEditForm);

    const openModalButton = document.getElementById('openChangeDescriptionModalButton');
    const modal = document.getElementById('changeDescriptionModal');
    const cancelModalButton = document.getElementById('cancelModalButton');
    const confirmSaveButton = document.getElementById('confirmSaveButton');
    
    openModalButton.addEventListener('click', () => modal.style.display = 'block');
    cancelModalButton.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (event) => {
        if (event.target == modal) modal.style.display = 'none';
    });

    confirmSaveButton.addEventListener('click', async () => {
        const form = document.getElementById('editDemandForm');
        const formData = new FormData(form);
        formData.append('mudanca_descricao', document.getElementById('mudanca_descricao_modal_textarea').value);
        formData.append('_method', 'PUT');

        const demandId = new URLSearchParams(window.location.search).get('id');
        const formActionUrl = `/api/demandas/${demandId}`;
        const userToken = localStorage.getItem('jwtToken');
        const formMessageArea = document.getElementById('formMessageArea');

        confirmSaveButton.disabled = true;
        formMessageArea.textContent = 'Salvando...';
        formMessageArea.style.color = 'inherit';

        try {
            const response = await fetch(formActionUrl, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${userToken}`, 'Accept': 'application/json' },
                body: formData
            });
            const responseData = await response.json();
            if (response.ok) {
                formMessageArea.textContent = 'Demanda atualizada com sucesso!';
                formMessageArea.style.color = 'green';
                modal.style.display = 'none';
            } else if (response.status === 403) {
                throw new Error(responseData.error || 'Não autorizado para editar esta demanda.');
            } else {
                let errorMsg = responseData.error || (responseData.errors ? Object.values(responseData.errors).flat().join(' ') : `Erro: ${response.statusText}`);
                throw new Error(errorMsg);
            }
        } catch (error) {
            formMessageArea.textContent = `Erro: ${sanitizeForHtml(error.message)}`;
            formMessageArea.style.color = 'red';
        } finally {
            confirmSaveButton.disabled = false;
        }
    });
</script>
@endpush
