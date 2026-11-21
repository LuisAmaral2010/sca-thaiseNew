import { Field, FieldDescription, FieldError, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { FORMATOS, nomeResponsavel } from './campos';

const UFS = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA',
    'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
];
const ufItems = UFS.map((uf) => ({ label: uf, value: uf }));

const classeTextarea =
    'w-full min-w-0 rounded-lg border border-input bg-transparent px-2.5 py-2 text-base outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 aria-invalid:border-destructive md:text-sm dark:bg-input/30';

const digitos = (max) => (valor) => valor.replace(/\D/g, '').slice(0, max);

// Campo de texto com label e mensagem de erro; `mascara`/`limpar` tratam campos só numéricos
function Campo({ id, label, data, setData, errors, className, mascara, limpar, ...props }) {
    return (
        <Field data-invalid={!!errors[id]} className={className}>
            <FieldLabel htmlFor={id}>{label}</FieldLabel>
            <Input
                id={id}
                value={mascara ? mascara(data[id] ?? '') : (data[id] ?? '')}
                aria-invalid={!!errors[id]}
                onChange={(e) => setData(id, limpar ? limpar(e.target.value) : e.target.value)}
                {...props}
            />
            {errors[id] && <FieldError>{errors[id]}</FieldError>}
        </Field>
    );
}

// Nome lido da unidade_operacional do CRA (somente leitura)
function NomeResponsavel({ id, label, empregado }) {
    return (
        <Field>
            <FieldLabel htmlFor={id}>{label}</FieldLabel>
            <Input id={id} value={nomeResponsavel(empregado)} readOnly disabled />
            <FieldDescription>Obtido da unidade operacional do CRA.</FieldDescription>
        </Field>
    );
}

export default function CraPerfilFields({ data, setData, errors, responsaveis = {} }) {
    const props = { data, setData, errors };

    return (
        <FieldGroup>
            <FieldSet>
                <FieldLegend>Identificação</FieldLegend>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-6">
                    <Campo id="nome" label="Nome" required maxLength={255} className="sm:col-span-4" {...props} />
                    <Campo id="sigla" label="Sigla" maxLength={20} className="sm:col-span-2" {...props} />
                    <Campo
                        id="cnpj"
                        label="CNPJ"
                        inputMode="numeric"
                        placeholder="00.000.000/0000-00"
                        mascara={FORMATOS.cnpj}
                        limpar={digitos(14)}
                        className="sm:col-span-3"
                        {...props}
                    />
                </div>
            </FieldSet>

            <FieldSet>
                <FieldLegend>Responsável</FieldLegend>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <NomeResponsavel id="responsavel_nome" label="Nome do responsável" empregado={responsaveis.responsavel} />
                    <Campo id="responsavel_cargo" label="Cargo" maxLength={100} {...props} />
                </div>
            </FieldSet>

            <FieldSet>
                <FieldLegend>Responsável Substituto</FieldLegend>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <NomeResponsavel
                        id="responsavel_substituto_nome"
                        label="Nome do responsável substituto"
                        empregado={responsaveis.substituto}
                    />
                    <Campo id="responsavel_substituto_cargo" label="Cargo" maxLength={100} {...props} />
                </div>
            </FieldSet>

            <FieldSet>
                <FieldLegend>Contato</FieldLegend>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <Campo id="email" label="E-mail" type="email" maxLength={255} {...props} />
                    <Campo
                        id="telefone"
                        label="Telefone"
                        type="tel"
                        placeholder="(00) 00000-0000"
                        mascara={FORMATOS.telefone}
                        limpar={digitos(11)}
                        {...props}
                    />
                    <Campo
                        id="site"
                        label="Site"
                        type="url"
                        placeholder="https://"
                        maxLength={255}
                        className="sm:col-span-2"
                        {...props}
                    />
                </div>
            </FieldSet>

            <FieldSet>
                <FieldLegend>Endereço</FieldLegend>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-6">
                    <Campo
                        id="cep"
                        label="CEP"
                        inputMode="numeric"
                        placeholder="00000-000"
                        mascara={FORMATOS.cep}
                        limpar={digitos(8)}
                        className="sm:col-span-2"
                        {...props}
                    />
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
                <FieldLegend>Atendimento</FieldLegend>
                <FieldGroup>
                    <Campo
                        id="horario_atendimento"
                        label="Horário de atendimento"
                        placeholder="Ex.: Segunda a sexta, 8h às 17h"
                        maxLength={255}
                        {...props}
                    />
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
                </FieldGroup>
            </FieldSet>
        </FieldGroup>
    );
}
