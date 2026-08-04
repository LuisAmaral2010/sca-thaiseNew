import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import DataPagination from '@/components/DataPagination';
import { ClipboardListIcon } from 'lucide-react';

export default function Index({ ordensServicos }) {
    return (
        <AppLayout>
            <Head title="Ordens de Serviço" />
            <Card>
                <CardHeader>
                    <CardTitle>Ordens de Serviço</CardTitle>
                </CardHeader>
                <CardContent>
                    {ordensServicos.data.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <ClipboardListIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhuma ordem de serviço encontrada</EmptyTitle>
                                <EmptyDescription>
                                    As ordens de serviço cadastradas aparecerão aqui.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Data do Status</TableHead>
                                    <TableHead>Observação</TableHead>
                                    <TableHead>Recebedor</TableHead>
                                    <TableHead>Solicitação</TableHead>
                                    <TableHead>Unidade Operacional</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {ordensServicos.data.map((ordemServico) => (
                                    <TableRow key={ordemServico.ordem_servico_id}>
                                        <TableCell>{ordemServico.ordem_servico_id}</TableCell>
                                        <TableCell>
                                            {ordemServico.status_atual ? (
                                                <Badge variant="secondary">
                                                    {ordemServico.status_atual}
                                                </Badge>
                                            ) : (
                                                '—'
                                            )}
                                        </TableCell>
                                        <TableCell>{ordemServico.data_status_atual ?? '—'}</TableCell>
                                        <TableCell className="max-w-64 truncate">
                                            {ordemServico.observacao ?? '—'}
                                        </TableCell>
                                        <TableCell>{ordemServico.recebedor_matricula ?? '—'}</TableCell>
                                        <TableCell>{ordemServico.solicitacao_servico_id ?? '—'}</TableCell>
                                        <TableCell>{ordemServico.unidade_operacional_id ?? '—'}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}

                    <DataPagination links={ordensServicos.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
