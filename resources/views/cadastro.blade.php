@extends('layouts.guest')

@section('title', 'Cadastro de Usuário')

@section('content')

    <form id="registerForm" action="/api/register" method="POST">
        <label>
            Nome:
            <input type="text" name="nome" required>
        </label>
        
        <label>
            Email:
            <input type="email" name="email" required>
        </label>
        
        <label>
            Senha:
            <input type="password" name="senha" required>
        </label>
        
        <label>
            Grupo:
            <select name="grupo_id" id="grupo_id_select" required>
                <option value="">Carregando...</option>
            </select>
        </label>
        
        <label>
            Perfil:
            <select name="perfil" required>
                <option value="usuário">Usuário</option>
                <option value="gestor">Gestor</option>
                <option value="administrador">Administrador</option>
            </select>
        </label>
        
        <button type="submit">Cadastrar</button>
        <div id="formMessageArea" class="message-area"></div>
    </form>
@endsection

@push('scripts')
<script>
    const todosOsGrupos = @json($grupos)

    function sanitizeForHtml(unsafeText) {
        if (unsafeText === null || typeof unsafeText === 'undefined') return '';
        return String(unsafeText).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function populateGruposDropdown(gruposArray, selectElement) {
        if (!selectElement) return;
        
        let optionsHtml = '<option value="">Selecione um grupo</option>';
        if (Array.isArray(gruposArray)) {
            gruposArray.forEach(grupo => {
                optionsHtml += `<option value="${sanitizeForHtml(grupo.id)}">${sanitizeForHtml(grupo.nome)}</option>`;
            });
        }
        selectElement.innerHTML = optionsHtml;
    }

    document.getElementById('registerForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        const submitButton = this.querySelector('button[type="submit"]');
        const formMessageArea = document.getElementById('formMessageArea');
        
        submitButton.disabled = true;
        formMessageArea.textContent = 'Enviando...';
        formMessageArea.style.color = 'inherit';

        const formData = new FormData(this);
        const formActionUrl = this.action;

        try {
            const response = await fetch(formActionUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });
            
            const responseData = await response.json();

            if (response.ok) {
                formMessageArea.textContent = responseData.message || 'Usuário cadastrado com sucesso!';
                formMessageArea.style.color = 'green';
                this.reset();
            } else {
                let errorMessage = responseData.error || `Erro ao cadastrar: ${response.statusText}`;
                if (responseData.errors) {
                    errorMessage = Object.values(responseData.errors).flat().join(' ');
                }
                throw new Error(errorMessage);
            }

        } catch (error) {
            console.error('Erro ao cadastrar usuário:', error);
            formMessageArea.textContent = `Erro: ${sanitizeForHtml(error.message)}`;
            formMessageArea.style.color = 'red';
        } finally {
            submitButton.disabled = false;
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const userInfoElement = document.getElementById('userInfoDisplay');
        if (userInfoElement) {
            userInfoElement.innerHTML = '<a href="/login">Já tem uma conta? Faça login</a>';
        }

        const grupoSelectElement = document.getElementById('grupo_id_select');
        populateGruposDropdown(todosOsGrupos, grupoSelectElement);
    });
</script>
@endpush