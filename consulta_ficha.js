const pacientes = [
    { 
        id: 1, 
        nome: "Ana Silva", 
        idade: 34, 
        telefone: "(11) 99999-1234", 
        ultimaConsulta: "15/03/2024",
        dataNascimento: "10/05/1989",
        cpf: "123.456.789-00",
        endereco: "Rua das Flores, 123 - Jardim das Acácias, São Paulo/SP",
        proximaConsulta: "20/04/2024",
        alergias: "Penicilina, Poeira, Frutos do mar",
        medicacoes: "Antialérgico (uso contínuo), Complexo vitamínico",
        tipoSanguineo: "A+",
        planoSaude: "Unimed",
        observacoes: "Paciente em acompanhamento regular. Apresenta melhora significativa nos últimos 3 meses.\nHistórico de rinite alérgica controlada.\nRecomendado manter acompanhamento trimestral."
    },
    { 
        id: 2, 
        nome: "Bruno Oliveira", 
        idade: 45, 
        telefone: "(11) 98888-5678", 
        ultimaConsulta: "22/03/2024",
        dataNascimento: "15/08/1978",
        cpf: "234.567.890-11",
        endereco: "Av. Paulista, 1000 - Bela Vista, São Paulo/SP",
        proximaConsulta: "30/04/2024",
        alergias: "Nenhuma registrada",
        medicacoes: "Anti-hipertensivo, Estatinas",
        tipoSanguineo: "O+",
        planoSaude: "Amil",
        observacoes: "Necessita monitoramento da pressão arterial semanalmente.\nColesterol elevado controlado com medicação.\nRecomendado reduzir consumo de sal e praticar exercícios regularmente.\nPeso atual: 85kg - Meta: 78kg"
    },
    { 
        id: 3, 
        nome: "Carla Santos", 
        idade: 28, 
        telefone: "(11) 97777-9012", 
        ultimaConsulta: "10/03/2024",
        dataNascimento: "22/11/1995",
        cpf: "345.678.901-22",
        endereco: "Rua Augusta, 500 - Consolação, São Paulo/SP",
        proximaConsulta: "25/04/2024",
        alergias: "Frutos do mar, Amendoim",
        medicacoes: "Nenhuma",
        tipoSanguineo: "B-",
        planoSaude: "Bradesco Saúde",
        observacoes: "Paciente saudável. Realizar exames de rotina anualmente.\nPratica atividades físicas 3x por semana.\nÚltimos exames dentro da normalidade.\nControle ginecológico em dia."
    },
    { 
        id: 4, 
        nome: "Daniel Costa", 
        idade: 52, 
        telefone: "(11) 96666-3456", 
        ultimaConsulta: "18/03/2024",
        dataNascimento: "03/03/1971",
        cpf: "456.789.012-33",
        endereco: "Rua Consolação, 800 - Cerqueira César, São Paulo/SP",
        proximaConsulta: "15/05/2024",
        alergias: "Iodo, Analgésicos opioides",
        medicacoes: "Insulina, Metformina, Enalapril",
        tipoSanguineo: "A+",
        planoSaude: "SulAmérica",
        observacoes: "Paciente diabético tipo 2. Controlar dieta e realizar exercícios regularmente.\nGlicemia de jejum: 110mg/dL (última medição)\nEncaminhado para nutricionista.\nRetorno em 60 dias para reavaliação medicamentosa."
    },
    { 
        id: 5, 
        nome: "Eduarda Lima", 
        idade: 39, 
        telefone: "(11) 95555-7890", 
        ultimaConsulta: "25/03/2024",
        dataNascimento: "18/07/1984",
        cpf: "567.890.123-44",
        endereco: "Alameda Santos, 200 - Jardim Paulista, São Paulo/SP",
        proximaConsulta: "05/05/2024",
        alergias: "Dipirona, Látex",
        medicacoes: "Anticoncepcional, Suplemento de ferro",
        tipoSanguineo: "AB+",
        planoSaude: "NotreDame Intermédica",
        observacoes: "Controle hormonal regular.\nApresenta quadro leve de anemia - em tratamento.\nSessões de fisioterapia 2x por semana para dor lombar.\nEvolução satisfatória."
    },
    { 
        id: 6, 
        nome: "Fernando Alves", 
        idade: 61, 
        telefone: "(11) 94444-2345", 
        ultimaConsulta: "12/03/2024",
        dataNascimento: "30/09/1962",
        cpf: "678.901.234-55",
        endereco: "Rua Haddock Lobo, 400 - Cerqueira César, São Paulo/SP",
        proximaConsulta: "28/04/2024",
        alergias: "Picada de insetos, Sulfas",
        medicacoes: "AAS, Losartana, Sinvastatina",
        tipoSanguineo: "O-",
        planoSaude: "Golden Cross",
        observacoes: "Paciente cardiopata em acompanhamento.\nRealizou cateterismo em 2022.\nPressão arterial controlada.\nPratica caminhada diária de 30 minutos.\nEncaminhado para avaliação cardiológica semestral."
    }
];

const listScreen = document.getElementById('listScreen');
const detailScreen = document.getElementById('detailScreen');
const backButton = document.getElementById('backButton');

function visualizarFicha(id) {
    const paciente = pacientes.find(p => p.id === id);
    
    if (paciente) {
        document.getElementById('patientName').textContent = paciente.nome;
        document.getElementById('detailNome').textContent = paciente.nome;
        document.getElementById('detailIdade').textContent = `${paciente.idade} anos`;
        document.getElementById('detailTelefone').textContent = paciente.telefone;
        document.getElementById('detailNascimento').textContent = paciente.dataNascimento;
        document.getElementById('detailCPF').textContent = paciente.cpf;
        document.getElementById('detailEndereco').textContent = paciente.endereco;
        document.getElementById('detailUltimaConsulta').textContent = paciente.ultimaConsulta;
        document.getElementById('detailProximaConsulta').textContent = paciente.proximaConsulta;
        document.getElementById('detailAlergias').textContent = paciente.alergias;
        document.getElementById('detailMedicacoes').textContent = paciente.medicacoes;
        document.getElementById('detailTipoSanguineo').textContent = paciente.tipoSanguineo;
        document.getElementById('detailPlanoSaude').textContent = paciente.planoSaude;
        document.getElementById('detailObservacoes').textContent = paciente.observacoes;
        
        listScreen.style.display = 'none';
        detailScreen.style.display = 'block';
    }
}

function voltarParaLista() {
    detailScreen.style.display = 'none';
    listScreen.style.display = 'block';
}

function filtrarPacientes() {
    const termo = document.getElementById('searchInput').value.toLowerCase();
    const pacientesFiltrados = pacientes.filter(paciente => 
        paciente.nome.toLowerCase().includes(termo)
    );
    
    const container = document.getElementById('patientsList');
    
    if (pacientesFiltrados.length === 0) {
        container.innerHTML = '<div class="no-results">Nenhum paciente encontrado</div>';
        return;
    }
    
    let html = '';
    pacientesFiltrados.forEach(paciente => {
        html += `
            <div class="patient-item">
                <div class="patient-info">
                    <h3>${paciente.nome}</h3>
                    <p>Idade: ${paciente.idade} anos | Telefone: ${paciente.telefone}</p>
                    <p>Última consulta: ${paciente.ultimaConsulta}</p>
                </div>
                <div class="patient-actions">
                    <button onclick="visualizarFicha(${paciente.id})">Visualizar Ficha</button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchButton').addEventListener('click', filtrarPacientes);
    document.getElementById('searchInput').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            filtrarPacientes();
        }
    });
    
    backButton.addEventListener('click', voltarParaLista);
});