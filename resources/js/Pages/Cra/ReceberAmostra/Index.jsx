import { Head, Link } from '@inertiajs/react';
import { ClipboardListIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

function Solicitante({ solicitacao }) {
    return (
        <>
            {solicitacao.empregado?.nome}{' '}
            {solicitacao.solicitante_matricula && `(${solicitacao.solicitante_matricula})`}
        </>
    );
}

function StatusRejeitadas({ solicitacao }) {
    if (!(solicitacao.ordens_rejeitadas_count > 0)) {
        return null;
    }

    return <Badge variant="destructive">Rejeitado ({solicitacao.ordens_rejeitadas_count})</Badge>;
}

function BotaoCra({ solicitacao, className = '' }) {
    return (
        <Button
            size="sm"
            className={className}
            render={<Link href={route('cra.receber-amostra.ordens', solicitacao.solicitacao_servico_id)} />}
        >
            CRA
        </Button>
    );
}

export default function Index({ solicitacoes }) {
    return (
        <AppLayout>
            <Head title="CRA — Receber Amostra" />
            <Card>
                <CardHeader>
                    <CardTitle>Receber Amostra</CardTitle>
                </CardHeader>
                <CardContent>
                    {solicitacoes.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <ClipboardListIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhuma requisição aguardando recebimento</EmptyTitle>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <>
                            {/* Telas grandes: tabela */}
                            <div className="hidden lg:block">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Solicitação</TableHead>
                                            <TableHead>Descrição</TableHead>
                                            <TableHead>Atividade</TableHead>
                                            <TableHead>Solicitante</TableHead>
                                            <TableHead>Data Solicitação</TableHead>
                                            <TableHead>Ordens Pendentes</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead />
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {solicitacoes.map((solicitacao) => (
                                            <TableRow key={solicitacao.solicitacao_servico_id}>
                                                <TableCell>#{solicitacao.solicitacao_servico_id}</TableCell>
                                                <TableCell className="whitespace-normal">{solicitacao.descricao}</TableCell>
                                                <TableCell className="whitespace-normal">{solicitacao.atividade?.titulo}</TableCell>
                                                <TableCell className="whitespace-normal">
                                                    <Solicitante solicitacao={solicitacao} />
                                                </TableCell>
                                                <TableCell>{solicitacao.data_solicitacao}</TableCell>
                                                <TableCell>{solicitacao.ordens_pendentes_count}</TableCell>
                                                <TableCell>
                                                    <StatusRejeitadas solicitacao={solicitacao} />
                                                </TableCell>
                                                <TableCell>
                                                    <BotaoCra solicitacao={solicitacao} />
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Celular e tablet: um cartão por solicitação */}
                            <div className="grid gap-3 sm:grid-cols-2 lg:hidden">
                                {solicitacoes.map((solicitacao) => (
                                    <div
                                        key={solicitacao.solicitacao_servico_id}
                                        className="flex flex-col gap-3 rounded-lg border p-3"
                                    >
                                        <div className="flex items-start justify-between gap-2">
                                            <div className="min-w-0">
                                                <div className="text-xs text-muted-foreground">
                                                    Solicitação #{solicitacao.solicitacao_servico_id}
                                                </div>
                                                <div className="font-medium break-words">{solicitacao.descricao}</div>
                                            </div>
                                            <StatusRejeitadas solicitacao={solicitacao} />
                                        </div>

                                        <dl className="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-sm">
                                            <dt className="text-muted-foreground">Atividade</dt>
                                            <dd className="break-words">{solicitacao.atividade?.titulo ?? '—'}</dd>
                                            <dt className="text-muted-foreground">Solicitante</dt>
                                            <dd className="break-words">
                                                <Solicitante solicitacao={solicitacao} />
                                            </dd>
                                            <dt className="text-muted-foreground">Data</dt>
                                            <dd>{solicitacao.data_solicitacao}</dd>
                                            <dt className="text-muted-foreground">Ordens pendentes</dt>
                                            <dd>{solicitacao.ordens_pendentes_count}</dd>
                                        </dl>

                                        <BotaoCra solicitacao={solicitacao} className="mt-auto w-full" />
                                    </div>
                                ))}
                            </div>
                        </>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
