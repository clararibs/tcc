const response = await fetch('inserirPessoa.php', {

class FichaPaciente {
    

    constructor() {
        this.form = document.getElementById('fichaFormulario');
        this.btnConfirmar = document.getElementById('btnConfirmar');
        this.btnText = document.getElementById('btnText');
        this.loadingSpinner = document.getElementById('loadingSpinner');
        this.mensagemStatus = document.getElementById('mensagemStatus');
        
        this.init();
    }

    init() {
        this.configurarEventos();
        this.configurarMascaras();
    }

    configurarEventos() {
        // Evento de submit do formulário
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.salvarFicha();
        });

        // Validação em tempo real
        this.configurarValidacaoTempoReal();
    }

    configurarMascaras() {
        // Máscara para telefone
        const telefoneInput = document.getElementById('telefone');
        telefoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else {
                value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            }
            
            e.target.value = value;
        });

        // Validação de idade
        const idadeInput = document.getElementById('idade');
        idadeInput.addEventListener('input', (e) => {
            let value = e.target.value;
            if (value > 120) {
                e.target.value = 120;
            }
        });
    }

    configurarValidacaoTempoReal() {
        const campos = this.form.querySelectorAll('input[required], textarea[required]');
        
        campos.forEach(campo => {
            campo.addEventListener('blur', () => {
                this.validarCampo(campo);
            });
        });
    }

    validarCampo(campo) {
        const valor = campo.value.trim();
        
        if (campo.hasAttribute('required') && !valor) {
            this.mostrarErroCampo(campo, 'Este campo é obrigatório');
            return false;
        }

        if (campo.type === 'email' && valor) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(valor)) {
                this.mostrarErroCampo(campo, 'E-mail inválido');
                return false;
            }
        }

        if (campo.id === 'telefone' && valor) {
            const telefoneLimpo = valor.replace(/\D/g, '');
            if (telefoneLimpo.length < 10) {
                this.mostrarErroCampo(campo, 'Telefone incompleto');
                return false;
            }
        }

        this.limparErroCampo(campo);
        return true;
    }

    mostrarErroCampo(campo, mensagem) {
        this.limparErroCampo(campo);
        
        campo.style.borderColor = 'var(--cor-erro)';
        
        const erroElement = document.createElement('div');
        erroElement.className = 'erro-campo';
        erroElement.style.color = 'var(--cor-erro)';
        erroElement.style.fontSize = '0.8rem';
        erroElement.style.marginTop = '5px';
        erroElement.textContent = mensagem;
        
        campo.parentNode.appendChild(erroElement);
    }

    limparErroCampo(campo) {
        campo.style.borderColor = '';
        
        const erroExistente = campo.parentNode.querySelector('.erro-campo');
        if (erroExistente) {
            erroExistente.remove();
        }
    }

    validarFormulario() {
        const campos = this.form.querySelectorAll('input[required], textarea[required]');
        let valido = true;

        campos.forEach(campo => {
            if (!this.validarCampo(campo)) {
                valido = false;
            }
        });

        return valido;
    }

    async salvarFicha() {
        // Validar formulário
        if (!this.validarFormulario()) {
            this.mostrarMensagem('Por favor, corrija os erros no formulário.', 'erro');
            return;
        }

        // Coletar dados do formulário
        const dados = this.coletarDadosFormulario();

        try {
            // Mostrar loading
            this.mostrarLoading(true);
            this.mostrarMensagem('Salvando ficha do paciente...', 'aviso');

            // Enviar para o servidor
            const resultado = await this.enviarParaServidor(dados);

            if (resultado.success) {
                this.mostrarMensagem('Ficha do paciente salva com sucesso!', 'sucesso');
                this.limparFormulario();
                
                // Redirecionar após sucesso (opcional)
                setTimeout(() => {
                    window.location.href = 'historico.html';
                }, 2000);
                
            } else {
                throw new Error(resultado.message || 'Erro ao salvar ficha');
            }

        } catch (error) {
            console.error('Erro ao salvar ficha:', error);
            this.mostrarMensagem(`Erro: ${error.message}`, 'erro');
        } finally {
            this.mostrarLoading(false);
        }
    }

    coletarDadosFormulario() {
        return {
            nome: document.getElementById('nome').value.trim(),
            email: document.getElementById('email').value.trim(),
            telefone: document.getElementById('telefone').value.trim(),
            idade: document.getElementById('idade').value ? parseInt(document.getElementById('idade').value) : null,
            descricao: document.getElementById('descricao').value.trim()
        };
    }

    async enviarParaServidor(dados) {
        const response = await fetch('php/salvar_ficha.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dados)
        });

        return await response.json();
    }

    mostrarLoading(mostrar) {
        if (mostrar) {
            this.btnConfirmar.disabled = true;
            this.btnText.textContent = 'SALVANDO...';
            this.loadingSpinner.style.display = 'block';
        } else {
            this.btnConfirmar.disabled = false;
            this.btnText.textContent = 'CONFIRMAR CADASTRO';
            this.loadingSpinner.style.display = 'none';
        }
    }

    mostrarMensagem(mensagem, tipo) {
        this.mensagemStatus.textContent = mensagem;
        this.mensagemStatus.className = `mensagem-status ${tipo}`;
        
        // Auto-esconder mensagens de sucesso após 5 segundos
        if (tipo === 'sucesso') {
            setTimeout(() => {
                this.mensagemStatus.style.display = 'none';
            }, 5000);
        }
    }

    limparFormulario() {
        this.form.reset();
        
        // Limpar estilos de validação
        const campos = this.form.querySelectorAll('input, textarea');
        campos.forEach(campo => {
            this.limparErroCampo(campo);
        });
        
        this.mensagemStatus.style.display = 'none';
    }

 
}

// Instância global
const fichaPaciente = new FichaPaciente();

// Funções globais para o HTML
function confirmarVoltar(event) {
    const form = document.getElementById('fichaFormulario');
    const formData = new FormData(form);
    let hasData = false;

    for (let value of formData.values()) {
        if (value.trim() !== '') {
            hasData = true;
            break;
        }
    }

    if (hasData) {
        event.preventDefault();
        if (confirm('Você tem alterações não salvas. Tem certeza que deseja voltar?')) {
            window.location.href = event.target.href;
        }
    }
    // Se não há dados, permite o redirecionamento normal
}

// Tecla F2 para preencher dados de exemplo (apenas desenvolvimento)
document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') {
        e.preventDefault();
        fichaPaciente.preencherDadosExemplo();
    }
});

window.addEventListener('beforeunload', (e) => {
    const form = document.getElementById('fichaFormulario');
    const formData = new FormData(form);
    
    for (let value of formData.values()) {
        if (value.trim() !== '') {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    }
});
