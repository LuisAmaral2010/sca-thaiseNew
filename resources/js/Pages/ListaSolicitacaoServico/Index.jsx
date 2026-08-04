import { Head } from '@inertiajs/react';
import { ClipboardListIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

export default function Index({ items }) {
    return (
        <AppLayout>
            <Head title="Lista de Solicitações de Serviço" />
            <Card>
                <CardHeader>
                    <CardTitle>Lista de Solicitações de Serviço</CardTitle>
                </CardHeader>
                <CardContent>
                    {items.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <ClipboardListIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhum registro encontrado</EmptyTitle>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Número CRA</TableHead>
                                    <TableHead>Data Solicitação</TableHead>
                                    <TableHead>Amostra</TableHead>
                                    <TableHead>Solicitante (Matrícula)</TableHead>
                                    <TableHead>Código</TableHead>
                                    <TableHead>Descrição Solicitação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {items.map((item) => (
                                    <TableRow key={item.solicitacao_servico_id}>
                                        <TableCell>{item.solicitacao_servico_id}</TableCell>
                                        <TableCell>{item.numero_cra}</TableCell>
                                        <TableCell>{item.created_at}</TableCell>
                                        <TableCell>{item.amostraDescricao}</TableCell>
                                        <TableCell>{item.solicitante_matricula}</TableCell>
                                        <TableCell>{item.codigo}</TableCell>
                                        <TableCell>{item.solicitacaoDescricaolistasolicitacaoservico}</TableCell>
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
