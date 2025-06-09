@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="form-container">
        <h2>Login</h2>
        <form id="loginForm" action="/api/login" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            
            <button type="submit">Entrar</button>
            <div id="formMessageArea" class="message-area"></div>
        </form>
        <div class="form-links">
            <a href="/cadastro">Não tem uma conta? Cadastre-se</a>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const submitButton = this.querySelector('button[type="submit"]');
        const formMessageArea = document.getElementById('formMessageArea');
        const formData = new FormData(this);
        const formActionUrl = this.action;

        submitButton.disabled = true;
        formMessageArea.textContent = 'Autenticando...';
        formMessageArea.style.color = 'inherit';

        try {
            const response = await fetch(formActionUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const responseData = await response.json();

           if (response.ok && responseData.access_token) {
                localStorage.setItem('jwtToken', responseData.access_token);
                   window.location.href = 'dashboard';

            } else {
                let errorMessage = responseData.error || `Falha no login: ${response.statusText}`;
                if (responseData.message) {
                    errorMessage = responseData.message;
                }
                throw new Error(errorMessage);
            }
        } catch (error) {
            console.error('Erro no login:', error);
            formMessageArea.textContent = `Erro: ${error.message}`;
            formMessageArea.style.color = 'red';
        } finally {
            submitButton.disabled = false;
        }
    });
</script>
@endpush