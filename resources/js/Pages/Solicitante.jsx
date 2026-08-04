import { Head } from '@inertiajs/react';
import { FilePlusIcon, ClipboardListIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import DataPagination from '@/components/DataPagination';

export default function Solicitante({ solicitacoes_servicos }) {
    return (
        <AppLayout>
            <Head title="Solicitante" />
            <Card>
                <CardHeader>
                    <FilePlusIcon className="size-8 text-primary" />
                    <CardTitle>
                        <a href="/solicitacao/create">Criar solicitação</a>
                    </CardTitle>
                </CardHeader>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Solicitações</CardTitle>
                </CardHeader>
                <CardContent>
                    {solicitacoes_servicos.data.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <ClipboardListIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhuma solicitação encontrada</EmptyTitle>
                                <EmptyDescription>Suas solicitações aparecerão aqui.</EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Descrição</TableHead>
                                    <TableHead>Solicitado em</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {solicitacoes_servicos.data.map((solicitacao) => (
                                    <TableRow key={solicitacao.solicitacao_servico_id}>
                                        <TableCell>
                                            <a href={`/solicitacao/${solicitacao.solicitacao_servico_id}/fracoesamostra`}>
                                                {solicitacao.descricao}
                                            </a>
                                        </TableCell>
                                        <TableCell>{solicitacao.data_solicitacao}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                    <DataPagination links={solicitacoes_servicos.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
