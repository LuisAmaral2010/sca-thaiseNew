import { Head, Link } from '@inertiajs/react';
import { ClipboardListIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

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
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Solicitação</TableHead>
                                    <TableHead>Descrição</TableHead>
                                    <TableHead>Atividade</TableHead>
                                    <TableHead>Solicitante</TableHead>
                                    <TableHead>Data Solicitação</TableHead>
                                    <TableHead>Ordens Pendentes</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {solicitacoes.map((solicitacao) => (
                                    <TableRow key={solicitacao.solicitacao_servico_id}>
                                        <TableCell>#{solicitacao.solicitacao_servico_id}</TableCell>
                                        <TableCell>{solicitacao.descricao}</TableCell>
                                        <TableCell>{solicitacao.atividade?.titulo}</TableCell>
                                        <TableCell>
                                            {solicitacao.empregado?.nome}{' '}
                                            {solicitacao.solicitante_matricula &&
                                                `(${solicitacao.solicitante_matricula})`}
                                        </TableCell>
                                        <TableCell>{solicitacao.data_solicitacao}</TableCell>
                                        <TableCell>{solicitacao.ordens_pendentes_count}</TableCell>
                                        <TableCell>
                                            <Button
                                                size="sm"
                                                render={
                                                    <Link
                                                        href={route(
                                                            'cra.receber-amostra.ordens',
                                                            solicitacao.solicitacao_servico_id
                                                        )}
                                                    />
                                                }
                                            >
                                                CRA
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
