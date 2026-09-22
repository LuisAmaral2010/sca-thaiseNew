import { Head, Link, router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';

function AceitarButton({ ordemServicoId }) {
    function handleAceitar() {
        router.post(route('laboratorio.aceitar-amostra.aceitar', ordemServicoId));
    }

    return (
        <AlertDialog>
            <AlertDialogTrigger render={<Button />}>Aceitar</AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Aceitar amostra da Ordem #{ordemServicoId}?</AlertDialogTitle>
                    <AlertDialogDescription>
                        A ordem será marcada como aceita pelo laboratório.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction onClick={handleAceitar}>Confirmar</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}

function RejeitarButton({ ordemServicoId }) {
    const { data, setData, post, processing, errors, reset } = useForm({ motivo: '' });

    function submit(e) {
        e.preventDefault();
        post(route('laboratorio.aceitar-amostra.rejeitar', ordemServicoId), {
            onSuccess: () => reset(),
        });
    }

    return (
        <Dialog>
            <DialogTrigger render={<Button variant="destructive" />}>Rejeitar</DialogTrigger>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>Rejeitar amostra da Ordem #{ordemServicoId}</DialogTitle>
                        <DialogDescription>
                            Informe o motivo da rejeição pelo laboratório.
                        </DialogDescription>
                    </DialogHeader>
                    <Field className="mt-4" data-invalid={!!errors.motivo}>
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
                    <DialogFooter className="mt-4">
                        <DialogClose render={<Button type="button" variant="outline" />}>Cancelar</DialogClose>
                        <Button type="submit" variant="destructive" disabled={processing}>
                            Confirmar Rejeição
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}

function comRowSpanAmostra(fracoes) {
    return fracoes.map((fracao, index) => {
        const anterior = fracoes[index - 1];
        const mesmaAmostra = anterior && anterior.amostra_descricao === fracao.amostra_descricao;

        if (mesmaAmostra) {
            return { ...fracao, amostraRowSpan: 0 };
        }

        let rowSpan = 1;
        while (
            fracoes[index + rowSpan] &&
            fracoes[index + rowSpan].amostra_descricao === fracao.amostra_descricao
        ) {
            rowSpan++;
        }

        return { ...fracao, amostraRowSpan: rowSpan };
    });
}

export default function AceitarAmostraShow({ ordem, fracoes }) {
    const fracoesComRowSpan = comRowSpanAmostra(fracoes);

    return (
        <AppLayout>
            <Head title={`Ordem de Serviço #${ordem.ordem_servico_id}`} />
            <Card>
                <CardHeader>
                    <CardTitle>Ordem de Serviço #{ordem.ordem_servico_id}</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <dl className="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt className="text-muted-foreground">Material</dt>
                            <dd>{ordem.material ?? '—'}</dd>
                        </div>
                        <div>
                            <dt className="text-muted-foreground">Responsável pela Atividade</dt>
                            <dd>{ordem.responsavel_atividade ?? '—'}</dd>
                        </div>
                        <div>
                            <dt className="text-muted-foreground">Unidade Operacional</dt>
                            <dd>{ordem.unidade_operacional ?? '—'}</dd>
                        </div>
                        <div>
                            <dt className="text-muted-foreground">Status</dt>
                            <dd>
                                {ordem.status ? (
                                    <Badge variant="secondary">{ordem.status}</Badge>
                                ) : (
                                    '—'
                                )}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-muted-foreground">Data Aceite Laboratório</dt>
                            <dd>{ordem.data_aceite_laboratorio ?? '—'}</dd>
                        </div>
                        <div>
                            <dt className="text-muted-foreground">Observação</dt>
                            <dd>{ordem.observacao ?? '—'}</dd>
                        </div>
                    </dl>

                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Amostra</TableHead>
                                <TableHead>Serviço</TableHead>
                                <TableHead>Status da Fração</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {fracoesComRowSpan.map((fracao, index) => (
                                <TableRow key={index}>
                                    {fracao.amostraRowSpan > 0 && (
                                        <TableCell rowSpan={fracao.amostraRowSpan}>
                                            {fracao.amostra_descricao ?? '—'}
                                        </TableCell>
                                    )}
                                    <TableCell>{fracao.servico_descricao ?? '—'}</TableCell>
                                    <TableCell>
                                        {fracao.fracao_status_atual ? (
                                            <Badge variant="secondary">{fracao.fracao_status_atual}</Badge>
                                        ) : (
                                            '—'
                                        )}
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </CardContent>
                <CardFooter className="justify-end gap-2">
                    <Button variant="outline" render={<Link href={route('laboratorio.aceitar-amostra')} />}>
                        Voltar
                    </Button>
                    {ordem.status === 'ENVIADO_LABORATORIO' && (
                        <>
                            <RejeitarButton ordemServicoId={ordem.ordem_servico_id} />
                            <AceitarButton ordemServicoId={ordem.ordem_servico_id} />
                        </>
                    )}
                </CardFooter>
            </Card>
        </AppLayout>
    );
}
