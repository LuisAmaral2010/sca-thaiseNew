import { Field, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

export default function AmostraFields({ data, setData, errors, solicitacoes }) {
    const solicitacaoItems = solicitacoes.map((solicitacao) => ({
        label: `#${solicitacao.solicitacao_servico_id} — ${solicitacao.descricao}`,
        value: String(solicitacao.solicitacao_servico_id),
    }));

    return (
        <FieldGroup>
            <Field data-invalid={!!errors.descricao}>
                <FieldLabel htmlFor="descricao">Descrição</FieldLabel>
                <Input
                    id="descricao"
                    value={data.descricao}
                    aria-invalid={!!errors.descricao}
                    onChange={(e) => setData('descricao', e.target.value)}
                    placeholder="Descrição da amostra"
                />
                {errors.descricao && <FieldError>{errors.descricao}</FieldError>}
            </Field>

            <Field data-invalid={!!errors.solicitacao_id}>
                <FieldLabel htmlFor="solicitacao_id">Solicitação de Serviço</FieldLabel>
                <Select
                    items={solicitacaoItems}
                    value={data.solicitacao_id ? String(data.solicitacao_id) : null}
                    onValueChange={(value) => setData('solicitacao_id', value)}
                >
                    <SelectTrigger id="solicitacao_id" aria-invalid={!!errors.solicitacao_id}>
                        <SelectValue placeholder="Selecione a solicitação" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            {solicitacaoItems.map((item) => (
                                <SelectItem key={item.value} value={item.value}>
                                    {item.label}
                                </SelectItem>
                            ))}
                        </SelectGroup>
                    </SelectContent>
                </Select>
                {errors.solicitacao_id && <FieldError>{errors.solicitacao_id}</FieldError>}
            </Field>

            <Field data-invalid={!!errors.validade_dias}>
                <FieldLabel htmlFor="validade_dias">Validade (dias)</FieldLabel>
                <Input
                    id="validade_dias"
                    type="number"
                    min="0"
                    value={data.validade_dias}
                    aria-invalid={!!errors.validade_dias}
                    onChange={(e) => setData('validade_dias', e.target.value)}
                    placeholder="Validade em dias"
                />
                {errors.validade_dias && <FieldError>{errors.validade_dias}</FieldError>}
            </Field>

            <Field data-invalid={!!errors.condicao_armazenamento}>
                <FieldLabel htmlFor="condicao_armazenamento">Condição de Armazenamento</FieldLabel>
                <Input
                    id="condicao_armazenamento"
                    value={data.condicao_armazenamento}
                    aria-invalid={!!errors.condicao_armazenamento}
                    onChange={(e) => setData('condicao_armazenamento', e.target.value)}
                    placeholder="Condicionamento e armazenamento da amostra"
                />
                {errors.condicao_armazenamento && (
                    <FieldError>{errors.condicao_armazenamento}</FieldError>
                )}
            </Field>

            <Field data-invalid={!!errors.numero_cra}>
                <FieldLabel htmlFor="numero_cra">Número CRA</FieldLabel>
                <Input
                    id="numero_cra"
                    type="number"
                    value={data.numero_cra}
                    aria-invalid={!!errors.numero_cra}
                    onChange={(e) => setData('numero_cra', e.target.value)}
                    placeholder="Número do CRA"
                />
                {errors.numero_cra && <FieldError>{errors.numero_cra}</FieldError>}
            </Field>
        </FieldGroup>
    );
}
