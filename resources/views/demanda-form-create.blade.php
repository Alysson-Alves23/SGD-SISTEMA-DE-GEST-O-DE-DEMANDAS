@extends('layouts.app')

@section('title', 'Cadastrar Nova Demanda')

@push('styles')
<style>
    .form-page-container {
        max-width: 800px;
        margin: auto;
    }
    .back-button {
        display: inline-block;
        padding: 8px 15px;
        margin-bottom: 25px;
        background: #6c757d;
        color: #fff;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9em;
        font-weight: 500;
    }
    .back-button:hover {
        background: #5a6268;
    }
    .demand-form-card {
        background: #fff;
        padding: 25px 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .demand-form-card h2 {
        margin: 0 0 25px;
        font-size: 1.5em;
        color: #007bff;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    .form-group label {
        display: block;
        color: #555;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.2);
    }
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    .form-actions {
        margin-top: 30px;
        text-align: right;
    }
    .create-button { /* Usando classe específica para não conflitar com a do layout */
        padding: 12px 25px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }
    .create-button:hover {
        background: #0056b3;
    }
    .full-width-group {
        grid-column: 1 / -1;
    }
    .message-area {
        text-align: center;
        color: #777;
        padding: 10px 0;
        min-height: 20px;
    }
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
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        
        .checklist-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            gap: 8px;
        }
        
        .checklist-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        .remove-checklist-item {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
        }
        
        .remove-checklist-item:hover {
            background: #c82333;
        }
        
        .add-checklist-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            margin-top: 8px;
        }
        
        .add-checklist-btn:hover {
            background: #218838;
        }

</style>
@endpush

@section('content')
    <div class="form-page-container">
        <a href="/dashboard" class="back-button">&larr; Voltar para o Painel</a>

        <div class="demand-form-card">
            <h2>Informações da Nova Demanda</h2>
            <form id="createDemandForm" action="api/demandas" method="POST">
                <input type="hidden" name="solicitante_id" id="solicitante_id_hidden">
                <input type="hidden" name="status_execucao" value="Pendente">

                <div class="form-grid">
                    <div class="form-group"><label for="tipo">Tipo da Demanda</label><select id="tipo" name="tipo" required><option value="">Selecione o Tipo</option><option value="Demanda Padrão">Demanda Padrão</option><option value="Suporte Técnico">Suporte Técnico</option><option value="Demanda Comercial">Demanda Comercial</option><option value="Demanda de Estoque">Demanda de Estoque</option><option value="Demanda de Manutenção">Demanda de Manutenção</option><option value="Demanda Produção">Demanda Produção</option><option value="Demanda de Serviços Gerais">Demanda de Serviços Gerais</option></select></div>
                    <div class="form-group"><label for="empresa">Empresa</label><input type="text" id="empresa" name="empresa"></div>
                    <div class="form-group"><label for="natureza">Natureza</label><select id="natureza" name="natureza"><option value="">Selecione a Natureza</option><option value="SERVIÇOS GERAIS">SERVIÇOS GERAIS</option><option value="PRODUÇÃO">PRODUÇÃO</option><option value="SUPORTE E MANUTENÇÃO">SUPORTE E MANUTENÇÃO</option><option value="PRODUÇÃO P/ ESTOQUE">PRODUÇÃO P/ ESTOQUE</option><option value="INTERNA">INTERNA</option></select></div>
                    <div class="form-group"><label for="cliente">Cliente</label><input type="text" id="cliente" name="cliente"></div>
                    <div class="form-group"><label for="numero_pedido_ou_nf">Nº Pedido/NF</label><input type="text" id="numero_pedido_ou_nf" name="numero_pedido_ou_nf"></div>
                    <div class="form-group"><label for="executor_id">Executor Designado</label><select id="executor_id" name="executor_id"><option value="">Carregando...</option></select></div>
                    <div class="form-group"><label for="prazo_execucao">Prazo de Execução</label><input type="date" id="prazo_execucao" name="prazo_execucao"></div>
                    <div class="form-group full-width-group" style="margin-top:20px;">
                        <label for="descricao_itens">Descrição Detalhada</label>
                        <textarea id="descricao_itens" name="descricao_itens" required></textarea>
                    </div>
                    
                    <div class="form-group full-width-group">
                        <label>Checklist de Itens</label>
                        <div id="checklist-container">
                            <div class="checklist-item">
                                <input type="text" name="checklist_items[]" placeholder="Digite um item do checklist" class="checklist-input">
                                <button type="button" class="remove-checklist-item" style="display: none;">×</button>
                            </div>
                        </div>
                        <button type="button" id="add-checklist-item" class="add-checklist-btn">+ Adicionar Item</button>
                    </div>
                    
                    <div class="form-group full-width-group"><label for="observacoes">Observações</label><textarea id="observacoes" name="observacoes"></textarea></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="create-button" id="submitButton">Cadastrar Demanda</button>
                </div>
                <div id="formMessageArea" class="message-area"></div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
<script>


    function sanitizeForHtml(text) {
        if (text === null || typeof text === 'undefined') return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function populateExecutorsDropdown(executoresArray, selectElement) {
        if (!selectElement) return;
        let optionsHtml = '<option value="">Selecione um Executor</option>';
        if (Array.isArray(executoresArray)) {
            executoresArray.forEach(executor => {
                optionsHtml += `<option value="${sanitizeForHtml(executor.id)}">${sanitizeForHtml(executor.nome)}</option>`;
            });
        }
        selectElement.innerHTML = optionsHtml;
    }

    function parseJwtPayload(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join(''));
            return JSON.parse(jsonPayload);
        } catch (e) {
            return null;
        }
    }
    
    document.addEventListener('DOMContentLoaded', async function() {
        const userToken = localStorage.getItem('jwtToken');
        if (!userToken) {
            window.location.href = '/login';
            return;
        }

        const userInfo = parseJwtPayload(userToken);
        if (!userInfo) {
            localStorage.removeItem('jwtToken');
            window.location.href = '/login';
            return;
        }
            console.log(userInfo);
        const userInfoElement = document.getElementById('userInfoDisplay');
        if (userInfoElement) {
            userInfoElement.innerHTML = `Bem-vindo, ${sanitizeForHtml(userInfo.nome)}! <a href="/login" id="logoutButton">Sair</a>`;
            document.getElementById('logoutButton').addEventListener('click', e => {
                e.preventDefault();
                localStorage.removeItem('jwtToken');
                window.location.href = '/login';
            });
        }
        try {
            const response = await fetch("api/users", {
                method: 'GET',
                headers: { 'Authorization': `Bearer ${userToken}`, 'Accept': 'application/json' },
            });
            const responseData = await response.json();
            if (response.ok) {
                 populateExecutorsDropdown(responseData, document.getElementById('executor_id'));
                this.reset();
            } else {
                let errorMsg = responseData.error || (responseData.errors ? Object.values(responseData.errors).flat().join(' ') : `Erro: ${response.statusText}`);
                throw new Error(errorMsg);
            }
        } catch (error) {
            formMessageArea.textContent = `Erro: ${sanitizeForHtml(error.message)}`;
            formMessageArea.style.color = 'red';
        }
       
    });

    document.getElementById('createDemandForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        const submitButton = document.getElementById('submitButton');
        const formMessageArea = document.getElementById('formMessageArea');
        const userToken = localStorage.getItem('jwtToken');
        
        if (!userToken) {
            window.location.href = '/login';
            return;
        }

        const solicitanteInfo = parseJwtPayload(userToken);
        if (!solicitanteInfo) {
            window.location.href = '/login';
            return;
        }

        submitButton.disabled = true;
        formMessageArea.textContent = 'Enviando...';
        formMessageArea.style.color = 'inherit';

        const formData = new FormData(this);
        formData.append('solicitante_id', solicitanteInfo.id);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${userToken}`, 'Accept': 'application/json' },
                body: formData
            });
            const responseData = await response.json();
            if (response.ok) {
                formMessageArea.textContent = responseData.message || 'Demanda cadastrada com sucesso!';
                formMessageArea.style.color = 'green';
                this.reset();
            } else {
                let errorMsg = responseData.error || (responseData.errors ? Object.values(responseData.errors).flat().join(' ') : `Erro: ${response.statusText}`);
                throw new Error(errorMsg);
            }
        } catch (error) {
            formMessageArea.textContent = `Erro: ${sanitizeForHtml(error.message)}`;
            formMessageArea.style.color = 'red';
        } finally {
            submitButton.disabled = false;
        }
    });

    // Funcionalidade do checklist
    document.addEventListener('DOMContentLoaded', function() {
        const checklistContainer = document.getElementById('checklist-container');
        const addChecklistBtn = document.getElementById('add-checklist-item');

        // Adicionar novo item
        addChecklistBtn.addEventListener('click', function() {
            const newItem = document.createElement('div');
            newItem.className = 'checklist-item';
            newItem.innerHTML = `
                <input type="text" name="checklist_items[]" placeholder="Digite um item do checklist" class="checklist-input">
                <button type="button" class="remove-checklist-item">×</button>
            `;
            checklistContainer.appendChild(newItem);
            
            // Mostrar botão de remover se houver mais de um item
            updateRemoveButtons();
        });

        // Remover item
        checklistContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-checklist-item')) {
                e.target.parentElement.remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const items = checklistContainer.querySelectorAll('.checklist-item');
            const removeButtons = checklistContainer.querySelectorAll('.remove-checklist-item');
            
            if (items.length === 1) {
                removeButtons[0].style.display = 'none';
            } else {
                removeButtons.forEach(btn => btn.style.display = 'block');
            }
        }

        // Inicializar
        updateRemoveButtons();
    });
</script>
@endpush