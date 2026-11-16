import { Field, FieldError, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { mascaraCep, mascaraCpfCnpj, mascaraTelefone } from './formatos';

const UFS = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA',
    'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
];

const classeTextarea =
    'w-full min-w-0 rounded-lg border border-input bg-transparent px-2.5 py-2 text-base outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 aria-invalid:border-destructive md:text-sm dark:bg-input/30';

// Campo de texto com label e mensagem de erro
function Campo({ id, label, data, setData, errors, className, ...props }) {
    return (
        <Field data-invalid={!!errors[id]} className={className}>
            <FieldLabel htmlFor={id}>{label}</FieldLabel>
            <Input
                id={id}
                value={data[id] ?? ''}
                aria-invalid={!!errors[id]}
                onChange={(e) => setData(id, e.target.value)}
                {...props}
            />
            {errors[id] && <FieldError>{errors[id]}</FieldError>}
        </Field>
    );
}

export default function ClienteExternoFields({ data, setData, errors }) {
    const pj = data.tipo_pessoa === 'J';
    const props = { data, setData, errors };

    const tipoItems = [
        { label: 'Pessoa Física', value: 'F' },
        { label: 'Pessoa Jurídica', value: 'J' },
    ];
    const ufItems = UFS.map((uf) => ({ label: uf, value: uf }));

    return (
        <FieldGroup>
            <FieldSet>
                <FieldLegend>Identificação</FieldLegend>
                <FieldGroup>
                    <Field data-invalid={!!errors.tipo_pessoa}>
                        <FieldLabel htmlFor="tipo_pessoa">Tipo de pessoa</FieldLabel>
                        <Select
                            items={tipoItems}
                            value={data.tipo_pessoa}
                            onValueChange={(valor) => {
                                setData((atual) => ({ ...atual, tipo_pessoa: valor, cpf_cnpj: '' }));
                            }}
                        >
                            <SelectTrigger id="tipo_pessoa" className="w-full sm:w-64" aria-invalid={!!errors.tipo_pessoa}>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    {tipoItems.map((item) => (
                                        <SelectItem key={item.value} value={item.value}>
                                            {item.label}
                                        </SelectItem>
                                    ))}
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        {errors.tipo_pessoa && <FieldError>{errors.tipo_pessoa}</FieldError>}
                    </Field>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Campo id="nome" label={pj ? 'Razão social' : 'Nome completo'} required maxLength={255} {...props} />
                        <Field data-invalid={!!errors.cpf_cnpj}>
                            <FieldLabel htmlFor="cpf_cnpj">{pj ? 'CNPJ' : 'CPF'}</FieldLabel>
                            <Input
                                id="cpf_cnpj"
                                inputMode="numeric"
                                required
                                placeholder={pj ? '00.000.000/0000-00' : '000.000.000-00'}
                                value={mascaraCpfCnpj(data.cpf_cnpj ?? '', data.tipo_pessoa)}
                                aria-invalid={!!errors.cpf_cnpj}
                                onChange={(e) => setData('cpf_cnpj', e.target.value.replace(/\D/g, '').slice(0, pj ? 14 : 11))}
                            />
                            {errors.cpf_cnpj && <FieldError>{errors.cpf_cnpj}</FieldError>}
                        </Field>
                    </div>

                    {pj ? (
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Campo id="nome_fantasia" label="Nome fantasia" maxLength={255} {...props} />
                            <Campo id="inscricao_estadual" label="Inscrição estadual" maxLength={20} {...props} />
                            <Campo
                                id="responsavel_nome"
                                label="Nome do responsável"
                                maxLength={255}
                                className="sm:col-span-2"
                                {...props}
                            />
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Campo id="rg" label="RG" maxLength={20} {...props} />
                            <Campo id="data_nascimento" label="Data de nascimento" type="date" {...props} />
                        </div>
                    )}
                </FieldGroup>
            </FieldSet>

            <FieldSet>
                <FieldLegend>Contato</FieldLegend>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <Campo id="email" label="E-mail" type="email" maxLength={255} {...props} />
                    <Field data-invalid={!!errors.telefone}>
                        <FieldLabel htmlFor="telefone">Telefone</FieldLabel>
                        <Input
                            id="telefone"
                            type="tel"
                            placeholder="(00) 00000-0000"
                            value={mascaraTelefone(data.telefone ?? '')}
                            aria-invalid={!!errors.telefone}
                            onChange={(e) => setData('telefone', e.target.value.replace(/\D/g, '').slice(0, 11))}
                        />
                        {errors.telefone && <FieldError>{errors.telefone}</FieldError>}
                    </Field>
                </div>
            </FieldSet>

            <FieldSet>
                <FieldLegend>Endereço</FieldLegend>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-6">
                    <Field data-invalid={!!errors.cep} className="sm:col-span-2">
                        <FieldLabel htmlFor="cep">CEP</FieldLabel>
                        <Input
                            id="cep"
                            inputMode="numeric"
                            placeholder="00000-000"
                            value={mascaraCep(data.cep ?? '')}
                            aria-invalid={!!errors.cep}
                            onChange={(e) => setData('cep', e.target.value.replace(/\D/g, '').slice(0, 8))}
                        />
                        {errors.cep && <FieldError>{errors.cep}</FieldError>}
                    </Field>
                    <Campo id="logradouro" label="Logradouro" maxLength={255} className="sm:col-span-4" {...props} />
                    <Campo id="numero" label="Número" maxLength={20} className="sm:col-span-2" {...props} />
                    <Campo id="complemento" label="Complemento" maxLength={100} className="sm:col-span-4" {...props} />
                    <Campo id="bairro" label="Bairro" maxLength={100} className="sm:col-span-2" {...props} />
                    <Campo id="cidade" label="Cidade" maxLength={100} className="sm:col-span-3" {...props} />
                    <Field data-invalid={!!errors.uf} className="sm:col-span-1">
                        <FieldLabel htmlFor="uf">UF</FieldLabel>
                        <Select items={ufItems} value={data.uf || null} onValueChange={(valor) => setData('uf', valor ?? '')}>
                            <SelectTrigger id="uf" className="w-full" aria-invalid={!!errors.uf}>
                                <SelectValue placeholder="—" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    {ufItems.map((item) => (
                                        <SelectItem key={item.value} value={item.value}>
                                            {item.label}
                                        </SelectItem>
                                    ))}
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        {errors.uf && <FieldError>{errors.uf}</FieldError>}
                    </Field>
                </div>
            </FieldSet>

            <FieldSet>
                <FieldLegend>Outros</FieldLegend>
                <FieldGroup>
                    <Field data-invalid={!!errors.observacao}>
                        <FieldLabel htmlFor="observacao">Observação</FieldLabel>
                        <textarea
                            id="observacao"
                            rows={3}
                            maxLength={2000}
                            className={classeTextarea}
                            value={data.observacao ?? ''}
                            aria-invalid={!!errors.observacao}
                            onChange={(e) => setData('observacao', e.target.value)}
                        />
                        {errors.observacao && <FieldError>{errors.observacao}</FieldError>}
                    </Field>
                    <label className="flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            className="size-4 accent-primary"
                            checked={!!data.is_ativo}
                            onChange={(e) => setData('is_ativo', e.target.checked)}
                        />
                        Cliente ativo
                    </label>
                </FieldGroup>
            </FieldSet>
        </FieldGroup>
    );
}
