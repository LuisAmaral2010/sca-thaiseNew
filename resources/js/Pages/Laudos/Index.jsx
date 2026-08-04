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
import { FileTextIcon } from 'lucide-react';

export default function Index({ laudos }) {
    return (
        <AppLayout>
            <Head title="Laudos" />
            <Card>
                <CardHeader>
                    <CardTitle>Laudos</CardTitle>
                </CardHeader>
                <CardContent>
                    {laudos.data.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <FileTextIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhum laudo encontrado</EmptyTitle>
                                <EmptyDescription>
                                    Os laudos cadastrados aparecerão aqui.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Data de Emissão</TableHead>
                                    <TableHead>Data Laudo CRA</TableHead>
                                    <TableHead>Data Laudo Lab</TableHead>
                                    <TableHead>Ordem de Serviço</TableHead>
                                    <TableHead>Avaliador</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {laudos.data.map((laudo) => (
                                    <TableRow key={laudo.laudo_id}>
                                        <TableCell>{laudo.laudo_id}</TableCell>
                                        <TableCell>
                                            {laudo.status_atual ? (
                                                <Badge variant="secondary">{laudo.status_atual}</Badge>
                                            ) : (
                                                '—'
                                            )}
                                        </TableCell>
                                        <TableCell>{laudo.data_emissao ?? '—'}</TableCell>
                                        <TableCell>{laudo.data_laudo_cra ?? '—'}</TableCell>
                                        <TableCell>{laudo.data_laudo_lab ?? '—'}</TableCell>
                                        <TableCell>{laudo.ordem_servico_id ?? '—'}</TableCell>
                                        <TableCell>{laudo.avaliador_matricula ?? '—'}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}

                    <DataPagination links={laudos.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
