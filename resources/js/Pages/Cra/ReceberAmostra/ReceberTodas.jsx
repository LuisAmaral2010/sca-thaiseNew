import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

export default function ReceberTodas({ solicitacao, ordens }) {
    const { data, setData, post, processing, errors } = useForm({
        data_recebimento: new Date().toISOString().slice(0, 10),
        observacao: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('cra.receber-amostra.receber-todas.store', solicitacao.solicitacao_servico_id));
    }

    return (
        <AppLayout>
            <Head title={`Receber Todas — Solicitação #${solicitacao.solicitacao_servico_id}`} />
            <Card className="max-w-2xl">
                <form onSubmit={submit}>
                    <CardHeader>
                        <CardTitle>
                            Receber Todas as Ordens — Solicitação #{solicitacao.solicitacao_servico_id}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div>
                            <p className="mb-2 text-sm text-muted-foreground">
                                {ordens.length} ordem(ns) serão marcadas como recebidas:
                            </p>
                            <ul className="list-inside list-disc text-sm">
                                {ordens.map((ordem) => (
                                    <li key={ordem.ordem_servico_id}>
                                        Ordem #{ordem.ordem_servico_id} — {ordem.unidade_operacional}
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <FieldGroup>
                            <Field data-invalid={!!errors.data_recebimento}>
                                <FieldLabel htmlFor="data_recebimento">Data de Recebimento</FieldLabel>
                                <Input
                                    id="data_recebimento"
                                    type="date"
                                    value={data.data_recebimento}
                                    aria-invalid={!!errors.data_recebimento}
                                    onChange={(e) => setData('data_recebimento', e.target.value)}
                                />
                                {errors.data_recebimento && <FieldError>{errors.data_recebimento}</FieldError>}
                            </Field>

                            <Field data-invalid={!!errors.observacao}>
                                <FieldLabel htmlFor="observacao">Observação</FieldLabel>
                                <Input
                                    id="observacao"
                                    value={data.observacao}
                                    aria-invalid={!!errors.observacao}
                                    onChange={(e) => setData('observacao', e.target.value)}
                                    placeholder="Observação (opcional, aplicada a todas as ordens)"
                                />
                                {errors.observacao && <FieldError>{errors.observacao}</FieldError>}
                            </Field>
                        </FieldGroup>
                    </CardContent>
                    <CardFooter className="justify-end gap-2">
                        <Button
                            variant="outline"
                            render={<Link href={route('cra.receber-amostra.ordens', solicitacao.solicitacao_servico_id)} />}
                        >
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Confirmar Recebimento de Todas
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </AppLayout>
    );
}
