class PacientesManager {
    constructor() {
        this.pacientes = [];
        this.pacienteSelecionado = null;
        this.init();
    }

    async init() {
        await this.carregarPacientes();
        this.configurarEventos();
    }

    configurarEventos() {
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.filtrarPacientes(e.target.value);
        });
    }

    async carregarPacientes() {
        try {
            this.mostrarLoading();
            
            // Simular um delay para teste (remova depois)
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            const response = await fetch('php/buscar_pacientes.php');
            const resultado = await response.json();
            
            if (resultado.success) {
                this.pacientes = resultado.data;
                this.exibirPacientes(this.pacientes);
                this.atualizarContador(resultado.total);
            } else {
                this.mostrarErro('Erro ao carregar pacientes: ' + resultado.message);
                this.atualizarContador(0);
            }
        } catch (error) {
            this.mostrarErro('Erro de conexão: ' + error.message);
            this.atualizarContador(0);
        }
    }

    exibirPacientes(pacientes) {
        const container = document.getElementById('patientsList');
        
        if (pacientes.length === 0) {
            container.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-user-slash"></i>
                    Nenhum paciente encontrado
                </div>
            `;
            return;
        }

        let html = '';
        pacientes.forEach(paciente => {
            html += `
                <div class="patient-row" data-id="${paciente.id}" data-name="${paciente.nome}">
                    <div class="patient-info">
                        <div class="info-item name">${this.escapeHtml(paciente.nome)}</div>
                        <div class="info-item phone">${this.escapeHtml(paciente.telefone || '-')}</div>
                        <div class="info-item email">${this.escapeHtml(paciente.email || '-')}</div>
                        <div class="info-item age">${paciente.idade ? paciente.idade + ' anos' : '-'}</div>
                    </div>
                    <div class="patient-actions">
                        <button class="btn-view" onclick="pacientesManager.verDetalhes(${paciente.id})">
                            <i class="fas fa-eye"></i>
                            Ver Ficha
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    filtrarPacientes(termo) {
        if (!termo) {
            this.exibirPacientes(this.pacientes);
            return;
        }

        const termoLower = termo.toLowerCase();
        const pacientesFiltrados = this.pacientes.filter(paciente => 
            paciente.nome.toLowerCase().includes(termoLower) ||
            (paciente.email && paciente.email.toLowerCase().includes(termoLower)) ||
            (paciente.telefone && paciente.telefone.includes(termo))
        );

        this.exibirPacientes(pacientesFiltrados);
        this.atualizarContador(pacientesFiltrados.length, true);
    }

    async verDetalhes(pacienteId) {
        try {
            this.mostrarLoadingModal();
            
            const response = await fetch('php/buscar_paciente_detalhes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: pacienteId })
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                this.exibirDetalhesPaciente(resultado.paciente, resultado.procedimentos);
                this.abrirModal();
            } else {
                alert('Erro ao carregar detalhes: ' + resultado.message);
            }
        } catch (error) {
            alert('Erro de conexão: ' + error.message);
        }
    }

    exibirDetalhesPaciente(paciente, procedimentos) {
        document.getElementById('modalPatientName').textContent = paciente.nome;
        document.getElementById('detailName').textContent = paciente.nome;
        document.getElementById('detailPhone').textContent = paciente.telefone || '-';
        document.getElementById('detailEmail').textContent = paciente.email || '-';
        document.getElementById('detailAge').textContent = paciente.idade ? paciente.idade + ' anos' : '-';
        
        if (paciente.data_cadastro) {
            const data = new Date(paciente.data_cadastro);
            document.getElementById('detailDataCadastro').textContent = 
                data.toLocaleDateString('pt-BR');
        } else {
            document.getElementById('detailDataCadastro').textContent = '-';
        }
        
        document.getElementById('detailDescription').textContent = 
            paciente.descricao || 'Nenhuma descrição cadastrada.';
        
        this.exibirProcedimentos(procedimentos);
        this.pacienteSelecionado = paciente;
    }

    exibirProcedimentos(procedimentos) {
        const container = document.getElementById('proceduresList');
        
        if (!procedimentos || procedimentos.length === 0) {
            container.innerHTML = `
                <div class="no-procedures">
                    <i class="fas fa-info-circle"></i>
                    Nenhum procedimento registrado
                </div>
            `;
            return;
        }

        let html = '';
        procedimentos.forEach(proc => {
            html += `
                <div class="procedure-item">
                    <i class="fas fa-check-circle"></i>
                    <span>${this.escapeHtml(proc.nome_procedimento)}</span>
                    <small>${proc.data_procedimento ? new Date(proc.data_procedimento).toLocaleDateString('pt-BR') : ''}</small>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    abrirModal() {
        document.getElementById('patientModal').style.display = 'block';
    }

    fecharModal() {
        document.getElementById('patientModal').style.display = 'none';
        this.pacienteSelecionado = null;
    }

    mostrarLoading() {
        document.getElementById('patientsList').innerHTML = `
            <div class="loading-message">
                <i class="fas fa-spinner fa-spin"></i>
                Carregando pacientes...
            </div>
        `;
    }

    mostrarLoadingModal() {
        document.getElementById('modalPatientName').textContent = 'Carregando...';
        document.getElementById('detailName').textContent = '-';
        document.getElementById('detailPhone').textContent = '-';
        document.getElementById('detailEmail').textContent = '-';
        document.getElementById('detailAge').textContent = '-';
        document.getElementById('detailDataCadastro').textContent = '-';
        document.getElementById('detailDescription').textContent = '-';
        document.getElementById('proceduresList').innerHTML = '<div class="no-procedures">Carregando...</div>';
    }

    mostrarErro(mensagem) {
        document.getElementById('patientsList').innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                ${mensagem}
            </div>
        `;
    }

    atualizarContador(total, isFiltered = false) {
        const element = document.getElementById('totalPacientes');
        if (isFiltered) {
            element.textContent = `${total} paciente(s) encontrado(s)`;
        } else {
            element.textContent = `${total} paciente(s) cadastrado(s)`;
        }
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    recarregar() {
        this.carregarPacientes();
    }
}

// Instância global
const pacientesManager = new PacientesManager();

// Funções globais para o HTML
function voltar() {
    // Tenta voltar para a página anterior
    if (document.referrer && document.referrer.includes(window.location.hostname)) {
        window.history.back();
    } else {
        // Se não houver página anterior, vai para a dashboard
        window.location.href = 'entrar_aline.html';
    }
}

function closeModal() {
    pacientesManager.fecharModal();
}

function filtrarPacientes() {
    const termo = document.getElementById('searchInput').value;
    pacientesManager.filtrarPacientes(termo);
}

function carregarPacientes() {
    pacientesManager.recarregar();
}

function editarPaciente() {
    if (pacientesManager.pacienteSelecionado) {
        alert('Funcionalidade de edição em desenvolvimento para o paciente: ' + pacientesManager.pacienteSelecionado.nome);
    }
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('patientModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Fechar modal com ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});