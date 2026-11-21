import { mascaraCep, mascaraCpfCnpj, mascaraTelefone } from '@/Pages/ClientesExternos/formatos';

// Campos editáveis de cra_perfil e seus rótulos
export const ROTULOS = {
    nome: 'Nome',
    sigla: 'Sigla',
    cnpj: 'CNPJ',
    responsavel_cargo: 'Cargo do responsável',
    responsavel_substituto_cargo: 'Cargo do responsável substituto',
    email: 'E-mail',
    telefone: 'Telefone',
    site: 'Site',
    cep: 'CEP',
    logradouro: 'Logradouro',
    numero: 'Número',
    complemento: 'Complemento',
    bairro: 'Bairro',
    cidade: 'Cidade',
    uf: 'UF',
    horario_atendimento: 'Horário de atendimento',
    observacao: 'Observação',
};

export const CAMPOS = Object.keys(ROTULOS);

// Valores iniciais do formulário (vazios no cadastro)
export function dadosIniciais(perfil = {}) {
    return Object.fromEntries(CAMPOS.map((campo) => [campo, perfil[campo] ?? '']));
}

// Máscaras dos campos gravados só com números
export const FORMATOS = {
    cnpj: (v) => mascaraCpfCnpj(v, 'J'),
    telefone: mascaraTelefone,
    cep: mascaraCep,
};

export function formatar(campo, valor) {
    if (valor === null || valor === undefined || valor === '') {
        return null;
    }
    return FORMATOS[campo] ? FORMATOS[campo](valor) : String(valor);
}

// Nome do responsável vindo da unidade_operacional
export function nomeResponsavel(empregado) {
    if (!empregado) {
        return 'Não definido na unidade operacional';
    }
    return empregado.nome ?? `Matrícula ${empregado.matricula} (nome não encontrado)`;
}
