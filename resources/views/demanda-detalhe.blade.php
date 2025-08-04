@extends('layouts.app')

@section('title', 'Detalhes da Demanda')

@push('styles')
<style>
    .back-button {display: inline-block; padding: 8px 15px; margin-bottom: 25px; background: #6c757d; color: #fff; border-radius: 4px; text-decoration: none; font-size: 0.9em; font-weight: 500;}
    .back-button:hover {background-color: #5a6268;}
    .demand-details-card, .updates-section {background-color: #ffffff; padding: 25px 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); margin-bottom: 25px;max-width: 800px;}
    .demand-header-actions {display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
    .demand-header-actions h2 {margin: 0; font-size: 1.5em; color: #007bff; border-bottom: 1px solid #eee; padding-bottom: 10px; flex-grow: 0;}
    .detail-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px 25px; margin-bottom: 15px;}
    .detail-item {margin-bottom: 5px;}
    .detail-item strong {display: block; color: #555; font-weight: 600; margin-bottom: 3px; font-size: 0.9em; text-transform: capitalize;}
    .detail-item span, .detail-item div {font-size: 1em; color: #333; word-wrap: break-word;}
    .description-box {background-color: #f9f9f9; border: 1px solid #eee; padding: 10px 15px; border-radius: 4px; min-height: 60px; white-space: pre-wrap; margin-top: 5px;}
    .full-width {grid-column: 1 / -1;}
    .update-list {list-style: none; padding: 0;}
    .update-item {background-color: #fdfdfd; border: 1px solid #f0f0f0; padding: 15px; margin-bottom: 15px; border-radius: 6px; border-left: 4px solid #007bff;}
    .update-item:last-child {margin-bottom: 0;}
    .update-meta {font-size: 0.85em; color: #777; margin-bottom: 8px;}
    .update-meta .user {font-weight: 600; color: #555;}
    .update-meta .date {margin-left: 10px;}
    .update-description {font-size: 0.95em; margin-bottom: 8px; white-space: pre-wrap;}
    .update-status-change {font-size: 0.9em; color: #333;}
    .update-status-change strong {font-weight: 600;}
    .message-area {text-align: center; color: #777; padding: 20px 0;}

    .page-container {
            width: 100%;
            max-width: 800px;
            justify-content: center;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
    }
    
    /* Estilos para o checklist */
    .checklist-container {
        background-color: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 4px;
        padding: 15px;
        margin-top: 5px;
    }
    
    .checklist-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        padding: 8px;
        background-color: white;
        border-radius: 4px;
        border: 1px solid #e0e0e0;
    }
    
    .checklist-item:last-child {
        margin-bottom: 0;
    }
    
    .checklist-checkbox {
        margin-right: 10px;
        transform: scale(1.2);
    }
    
    .checklist-text {
        flex: 1;
        font-size: 0.95em;
        color: #333;
    }
    
    .checklist-item.concluido .checklist-text {
        text-decoration: line-through;
        color: #666;
    }
    
    .checklist-item.concluido {
        background-color: #f8f9fa;
        border-color: #d4edda;
    }
    
    .checklist-meta {
        font-size: 0.8em;
        color: #777;
        margin-left: 10px;
    }
    
    .checklist-empty {
        color: #777;
        font-style: italic;
        text-align: center;
        padding: 20px;
    }

</style>
@endpush

@section('content')
    <a href="/dashboard" class="back-button">&larr; Voltar para o Painel</a>

    <div class="demand-details-card">
        <div class="demand-header-actions">
            <h2>Informações da Demanda</h2>
            <a href="#" id="editDemandButton" class="primary-action-button" style="display: none;">EDITAR DEMANDA</a>
        </div>
        <div id="demandDetailsGrid" class="detail-grid">
            <p class="message-area">Carregando informações...</p>
        </div>
    </div>

    <div class="updates-section">
        <h2>Histórico de Atualizações</h2>
        <ul id="updatesList" class="update-list">
            <li class="message-area">Carregando histórico...</li>
        </ul>
    </div>
@endsection

@push('scripts')
<script>
    function sanitizeForHtml(text) {
        if (text === null || typeof text === 'undefined') return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function formatDateToBrazilian(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Data inválida';
        return date.toLocaleDateString('pt-BR', { timeZone: 'UTC' });
    }

    function formatTimestampToBrazilian(timestampString) {
        if (!timestampString) return 'N/A';
        const date = new Date(timestampString);
        return isNaN(date.getTime()) ? 'Data inválida' : date.toLocaleString('pt-BR', { timeZone: 'America/Sao_Paulo' });
    }

    function renderChecklist(items) {
        if (!items || items.length === 0) {
            return '<div class="checklist-container"><div class="checklist-empty">Nenhum item no checklist.</div></div>';
        }

        let checklistHtml = '<div class="checklist-container">';
        
        items.forEach(item => {
            const isConcluido = item.concluido;
            const concluidoClass = isConcluido ? 'concluido' : '';
            const concluidoMeta = isConcluido && item.concluido_por ? 
                `<div class="checklist-meta">Concluído por ${sanitizeForHtml(item.concluido_por.nome)} em ${formatTimestampToBrazilian(item.concluido_em)}</div>` : '';
            
            checklistHtml += `
                <div class="checklist-item ${concluidoClass}">
                    <input type="checkbox" class="checklist-checkbox" ${isConcluido ? 'checked' : ''} disabled>
                    <div class="checklist-text">${sanitizeForHtml(item.descricao)}</div>
                    ${concluidoMeta}
                </div>
            `;
        });
        
        checklistHtml += '</div>';
        return checklistHtml;
    }

    function getCssClassForStatus(statusText) {
        if (!statusText) return 'desconhecido';
        return String(statusText).toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, '-').replace(/[^\w-]+/g, '');
    }

    function renderDemandDetails(demanda) {
        const gridElement = document.getElementById('demandDetailsGrid');
        if (!gridElement) return;

        const formattedDemand = {
            'número da demanda': sanitizeForHtml(demanda.numero_demanda || 'N/D'),
            'tipo': sanitizeForHtml(demanda.tipo || 'N/A'),
            'empresa': sanitizeForHtml(demanda.empresa || 'N/A'),
            'natureza': sanitizeForHtml(demanda.natureza || 'N/A'),
            'cliente': sanitizeForHtml(demanda.cliente || 'N/A'),
            'pedido/nf': sanitizeForHtml(demanda.numero_pedido_ou_nf || 'N/A'),
            'status da execução': `<span class="status-tag status-${getCssClassForStatus(demanda.status_execucao)}">${sanitizeForHtml(demanda.status_execucao || 'N/A')}</span>`,
            'status do planejamento': `<span class="status-tag status-${getCssClassForStatus(demanda.status_planejamento)}">${sanitizeForHtml(demanda.status_planejamento || 'N/A')}</span>`,
            'solicitante': sanitizeForHtml(demanda.solicitante ? demanda.solicitante.nome : 'N/A'),
            'executor': sanitizeForHtml(demanda.executor ? demanda.executor.nome : 'N/A'),
            'data de recebimento': formatDateToBrazilian(demanda.data_recebimento),
            'prazo de execução': formatDateToBrazilian(demanda.prazo_execucao),
            'previsão de início': formatDateToBrazilian(demanda.previsao_inicio),
            'início da execução': formatDateToBrazilian(demanda.inicio_execucao),
            'finalização da execução': formatDateToBrazilian(demanda.finalizacao_execucao),
            'tempo de execução': sanitizeForHtml(demanda.tempo_execucao || 'N/A'),
            'descrição dos itens': `<div class="description-box">${sanitizeForHtml(demanda.descricao_itens || 'Nenhuma descrição.')}</div>`,
            'checklist': renderChecklist(demanda.checklist_items || []),
            'descrição da pendência': `<div class="description-box">${sanitizeForHtml(demanda.descricao_pendencia || 'Nenhuma pendência.')}</div>`,
            'observações': `<div class="description-box">${sanitizeForHtml(demanda.observacoes || 'Nenhuma observação.')}</div>`,
            'criado em': formatTimestampToBrazilian(demanda.criado_em),
            'atualizado em': formatTimestampToBrazilian(demanda.atualizado_em)
        };
        
        let detailsHtmlContent = '';
        for (const key in formattedDemand) {
            const itemClass = (key.includes('descrição') || key.includes('observações')) ? 'detail-item full-width' : 'detail-item';
            detailsHtmlContent += `<div class="${itemClass}"><strong>${key}:</strong><span>${formattedDemand[key]}</span></div>`;
        }
        gridElement.innerHTML = detailsHtmlContent;
    }

    function renderUpdatesHistory(updatesArray) {
        const listElement = document.getElementById('updatesList');
        if (!listElement) return;

        if (!updatesArray || updatesArray.length === 0) {
            listElement.innerHTML = '<li class="message-area">Nenhum histórico de atualização.</li>';
            return;
        }

        let updatesHtmlContent = updatesArray.map(update => `
            <li class="update-item">
                <div class="update-meta">
                    <span class="user">${sanitizeForHtml(update.usuario ? update.usuario.nome : 'Desconhecido')}</span>
                    <span class="date">em ${formatTimestampToBrazilian(update.data)}</span>
                </div>
                <div class="update-description">${sanitizeForHtml(update.descricao || '')}</div>
                ${update.status ? `<div class="update-status-change"><strong>Status alterado para:</strong> 
                <span class="status-tag status-${getCssClassForStatus(update.status)}">${sanitizeForHtml(update.status)}</span></div>` : ''}
            </li>
        `).join('');
        listElement.innerHTML = updatesHtmlContent;
    }

    async function initializeDetailPage() {
        const userToken = localStorage.getItem('jwtToken');
        if (!userToken) {
            window.location.href = '/login';
            return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const demandId = urlParams.get('id');
        const pageTitleElement = document.getElementById('pageTitle');

        if (!demandId) {
            if(pageTitleElement) pageTitleElement.textContent = 'ID da Demanda não fornecido';
            return;
        }

        const apiEndpoint = `/api/demandas/${demandId}`;

        try {
            const response = await fetch(apiEndpoint, {
                headers: { 'Authorization': `Bearer ${userToken}`, 'Accept': 'application/json' }
            });

            if (response.status === 401 || response.status === 403) {
                localStorage.removeItem('jwtToken');
                alert('Sessão expirada ou inválida.');
                window.location.href = '/login';
                return;
            }
            if (response.status === 404) {
                 if(pageTitleElement) pageTitleElement.textContent = 'Demanda Não Encontrada';
                 document.getElementById('demandDetailsGrid').innerHTML = '<p class="message-area">A demanda solicitada não foi encontrada.</p>';
                 return;
            }
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            const responseData = await response.json();

            if (responseData && responseData.demanda) {
                if(pageTitleElement) pageTitleElement.textContent = `Detalhes da Demanda #${responseData.demanda.numero_demanda}`;
                document.title = `Demanda #${responseData.demanda.numero_demanda}`;
                
                renderDemandDetails(responseData.demanda);
                renderUpdatesHistory(responseData.historico);

                const editButton = document.getElementById('editDemandButton');
                if(editButton) {
                    editButton.href = `/demanda-update?id=${responseData.demanda.numero_demanda}`;
                    editButton.style.display = 'inline-block';
                }
            } else {
                throw new Error('Formato de dados da API inválido.');
            }

        } catch (error) {
            console.error('Falha ao carregar detalhes da demanda:', error);
            if(pageTitleElement) pageTitleElement.textContent = 'Erro ao Carregar';
        }
    }

    document.addEventListener('DOMContentLoaded', initializeDetailPage);
</script>
@endpush