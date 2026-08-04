import { Head } from '@inertiajs/react';
import { HistoryIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import DataPagination from '@/components/DataPagination';

export default function Index({ historicos }) {
    return (
        <AppLayout>
            <Head title="Históricos" />
            <Card>
                <CardHeader>
                    <CardTitle>Históricos</CardTitle>
                </CardHeader>
                <CardContent>
                    {historicos.data.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <HistoryIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhum histórico encontrado</EmptyTitle>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Escopo</TableHead>
                                    <TableHead>ID do Escopo</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Data</TableHead>
                                    <TableHead>Usuário</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {historicos.data.map((historico) => (
                                    <TableRow key={historico.historico_id}>
                                        <TableCell>{historico.escopo}</TableCell>
                                        <TableCell>{historico.escopo_id}</TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">{historico.status}</Badge>
                                        </TableCell>
                                        <TableCell>{historico.data}</TableCell>
                                        <TableCell>{historico.usuario_matricula}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                    <DataPagination links={historicos.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
