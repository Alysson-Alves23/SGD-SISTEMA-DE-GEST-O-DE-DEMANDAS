@extends('layouts.app')

@section('title', 'Painel de Demandas')
@section('page-title', 'Painel de Demandas')

@section('content')
    <nav class="filter-nav"></nav>
    <div class="demands-list">
        <div class="loading-message">Carregando demandas...</div>
    </div>
@endsection

@push('scripts')
<script>
    function escapeHtml(unsafe) {
        if (unsafe === null || typeof unsafe === 'undefined') return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function generateViewLinkClient(viewType, currentStatus, baseUrl = 'dashboard') {
        const currentUrlParams = new URLSearchParams(window.location.search);
        const params = new URLSearchParams();
        
        params.set('view', viewType);
        if (currentStatus) {
            params.set('status', currentStatus);
        } else if (currentUrlParams.has('status') && viewType === currentUrlParams.get('view')) {
            params.set('status', currentUrlParams.get('status'));
        }
        return `/${baseUrl}?${params.toString()}`;
    }
    
    function applyStatusFilter() {
        const statusSelect = document.getElementById('status_filter');
        const selectedStatus = statusSelect.value;
        const currentUrl = new URL(window.location.href);
        
        if (selectedStatus) {
            currentUrl.searchParams.set('status', selectedStatus);
        } else {
            currentUrl.searchParams.delete('status');
        }

        if (!currentUrl.searchParams.has('view')) {
            const urlParams = new URLSearchParams(window.location.search);
            const currentView = urlParams.get('view') || 'all';
            currentUrl.searchParams.set('view', currentView);
        }
        window.location.href = currentUrl.toString();
    }

    function renderUserInfo(userInfo) {
        const userInfoDiv = document.getElementById('userInfoDisplay');
        if (userInfoDiv && userInfo) {
            userInfoDiv.innerHTML = `Bem-vindo, ${escapeHtml(userInfo.nome)}! <a href="/login" id="logout-link">Sair</a>`;
            
            const logoutLink = document.getElementById('logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    localStorage.removeItem('jwtToken');
                    window.location.href = '/login';
                });
            }
        }
    }

    function renderFilterNav(viewFilter, statusFilter, allStatuses) {
        const filterNav = document.querySelector('.filter-nav');
        if (!filterNav) return;

        const urlParams = new URLSearchParams(window.location.search);
        const currentView = urlParams.get('view') || 'all'; 
        const currentStatus = urlParams.get('status') || '';   

        let filtersHtml = `
            <a href="${generateViewLinkClient('user', currentStatus)}" class="filter-button ${currentView === 'user' ? 'selected' : ''}">Minhas Demandas</a>
            <a href="${generateViewLinkClient('group', currentStatus)}" class="filter-button ${currentView === 'group' ? 'selected' : ''}">Demandas do Grupo</a>
            <a href="${generateViewLinkClient('all', currentStatus)}" class="filter-button ${currentView === 'all' ? 'selected' : ''}">Todas as Demandas</a>
        `;
        
        let statusOptionsHtml = `<option value="" ${currentStatus === '' ? 'selected' : ''}>Todos os Status</option>`;
        if (Array.isArray(allStatuses)) {
            allStatuses.forEach(s => {
                statusOptionsHtml += `<option value="${escapeHtml(s)}" ${currentStatus === s ? 'selected' : ''}>${escapeHtml(s)}</option>`;
            });
        }
        filtersHtml += `<select name="status_filter" id="status_filter" class="status-filter-select" onchange="applyStatusFilter()">${statusOptionsHtml}</select>`;

        const newDemandButtonHtml = `<a href="/demanda-form" class="primary-action-button">+ Nova Demanda</a>`;

        filterNav.innerHTML = filtersHtml + newDemandButtonHtml;
    }

    function renderDemandCards(demands) {
        const demandsListDiv = document.querySelector('.demands-list');
        if (!demandsListDiv) return;

        demandsListDiv.innerHTML = '';

        if (!demands || demands.length === 0) {
            demandsListDiv.innerHTML = `<div class="no-demands"><p>Nenhuma demanda encontrada para os filtros selecionados.</p></div>`;
            return;
        }

        demands.forEach(demanda => {
            if (demanda && typeof demanda.numero_demanda !== 'undefined' && demanda.numero_demanda !== null) {
                let statusOriginal = demanda.status_execucao || 'desconhecido';
                let statusClass = statusOriginal.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                    .replace(/\s+/g, '-').replace(/[^\w-]+/g, '');

                const cardDiv = document.createElement('div');
                cardDiv.className = 'demand-item';
                cardDiv.innerHTML = `
                    <div class="demand-header">
                        <span class="demand-id">#${escapeHtml(demanda.numero_demanda)}</span>
                        <span class="demand-status status-${escapeHtml(statusClass)}">${escapeHtml(statusOriginal)}</span>
                    </div>
                    <h3 class="demand-title">${escapeHtml(demanda.tipo) || 'Sem título'}</h3>
                    <div class="demand-details">
                        <p><strong>Solicitante:</strong> ${escapeHtml(demanda.solicitante.nome) || 'N/A'}</p>
                        <p><strong>Responsável:</strong> ${escapeHtml(demanda.executor ? demanda.executor.nome : 'N/A')}</p>
                        <p><strong>Prazo:</strong> ${escapeHtml(demanda.prazo_execucao ? new Date(demanda.prazo_execucao + 'T03:00:00').toLocaleDateString('pt-BR') : 'N/A')}</p>
                    </div>
                `;
                
                cardDiv.onclick = () => window.location.href = `/demanda-detalhe?id=${escapeHtml(demanda.numero_demanda)}`;
                demandsListDiv.appendChild(cardDiv);
            }
        });
    }

    async function loadDashboardData() {
        const token = localStorage.getItem('jwtToken');
        if (!token) {
            window.location.href = '/login'; 
            return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const view = urlParams.get('view') || 'all';
        const status = urlParams.get('status') || '';
        
        const apiUrl = new URL('/api/demandas', window.location.origin); 
        apiUrl.searchParams.set('view', view);
        if (status) {
            apiUrl.searchParams.set('status', status);
        }
        
        try {
            const response = await fetch(apiUrl.toString(), {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.status === 401 || response.status === 403) {
                localStorage.removeItem('jwtToken');
                alert('Sessão expirada ou inválida. Por favor, faça login novamente.');
                window.location.href = '/login';
                return;
            }
            if (!response.ok) {
                throw new Error(`Erro HTTP ao buscar dados: ${response.status}`);
            }

            const data = await response.json();
            
            // Note que agora a API /api/demandas retorna a chave 'demands' e as outras.
            // Ajustamos a chamada abaixo para usar 'data.demands'
            renderUserInfo(data.userInfo);
            renderFilterNav(data.viewFilter, data.statusFilter, data.allStatuses);
            renderDemandCards(data.demands);

        } catch (error) {
            console.error('Falha ao carregar dados do dashboard:', error);
            const demandsListDiv = document.querySelector('.demands-list');
            if (demandsListDiv) {
                demandsListDiv.innerHTML = `<div class="no-demands"><p>Erro ao carregar dados. Tente novamente mais tarde.</p></div>`;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>
@endpush