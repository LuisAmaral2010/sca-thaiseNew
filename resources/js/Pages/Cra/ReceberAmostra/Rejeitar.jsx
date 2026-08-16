import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

export default function Rejeitar({ ordem }) {
    const { data, setData, post, processing, errors } = useForm({
        data_rejeicao: new Date().toISOString().slice(0, 10),
        motivo: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('cra.receber-amostra.rejeitar.store', ordem.ordem_servico_id));
    }

    return (
        <AppLayout>
            <Head title={`Rejeitar Amostra — Ordem #${ordem.ordem_servico_id}`} />
            <Card className="max-w-2xl">
                <form onSubmit={submit}>
                    <CardHeader>
                        <CardTitle>Rejeitar Amostra — Ordem #{ordem.ordem_servico_id}</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <dl className="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt className="text-muted-foreground">Solicitação</dt>
                                <dd>
                                    #{ordem.solicitacao_servico?.solicitacao_servico_id} —{' '}
                                    {ordem.solicitacao_servico?.descricao}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-muted-foreground">Unidade Operacional</dt>
                                <dd>{ordem.unidade_operacional?.nome}</dd>
                            </div>
                        </dl>

                        <FieldGroup>
                            <Field data-invalid={!!errors.data_rejeicao}>
                                <FieldLabel htmlFor="data_rejeicao">Data de Rejeição</FieldLabel>
                                <Input
                                    id="data_rejeicao"
                                    type="date"
                                    value={data.data_rejeicao}
                                    aria-invalid={!!errors.data_rejeicao}
                                    onChange={(e) => setData('data_rejeicao', e.target.value)}
                                />
                                {errors.data_rejeicao && <FieldError>{errors.data_rejeicao}</FieldError>}
                            </Field>

                            <Field data-invalid={!!errors.motivo}>
                                <FieldLabel htmlFor="motivo">Motivo da Rejeição</FieldLabel>
                                <Input
                                    id="motivo"
                                    value={data.motivo}
                                    aria-invalid={!!errors.motivo}
                                    onChange={(e) => setData('motivo', e.target.value)}
                                    placeholder="Descreva o motivo da rejeição"
                                />
                                {errors.motivo && <FieldError>{errors.motivo}</FieldError>}
                            </Field>
                        </FieldGroup>
                    </CardContent>
                    <CardFooter className="justify-end gap-2">
                        <Button variant="outline" render={<Link href={route('cra.receber-amostra.index')} />}>
                            Cancelar
                        </Button>
                        <Button type="submit" variant="destructive" disabled={processing}>
                            Confirmar Rejeição
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </AppLayout>
    );
}
