// Máscaras de exibição; os valores são guardados e enviados só com números

export function mascaraCpfCnpj(valor, tipoPessoa) {
    const d = String(valor ?? '').replace(/\D/g, '');

    if (tipoPessoa === 'J' || d.length > 11) {
        return d
            .slice(0, 14)
            .replace(/^(\d{2})(\d)/, '$1.$2')
            .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
            .replace(/\.(\d{3})(\d)/, '.$1/$2')
            .replace(/(\d{4})(\d)/, '$1-$2');
    }

    return d
        .slice(0, 11)
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

export function mascaraCep(valor) {
    return String(valor ?? '')
        .replace(/\D/g, '')
        .slice(0, 8)
        .replace(/(\d{5})(\d)/, '$1-$2');
}

export function mascaraTelefone(valor) {
    const d = String(valor ?? '').replace(/\D/g, '').slice(0, 11);

    if (d.length <= 2) {
        return d.length ? `(${d}` : '';
    }

    const ddd = d.slice(0, 2);
    const numero = d.slice(2);
    const corte = numero.length > 8 ? 5 : 4;

    return numero.length > corte
        ? `(${ddd}) ${numero.slice(0, corte)}-${numero.slice(corte)}`
        : `(${ddd}) ${numero}`;
}
