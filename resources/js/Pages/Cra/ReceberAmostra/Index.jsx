import { Head, Link } from '@inertiajs/react';
import { ClipboardListIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

export default function Index({ ordens }) {
    return (
        <AppLayout>
            <Head title="CRA — Receber Amostra" />
            <Card>
                <CardHeader>
                    <CardTitle>Receber Amostra</CardTitle>
                </CardHeader>
                <CardContent>
                    {ordens.length === 0 ? (
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
                                    <TableHead>Ordem de Serviço</TableHead>
                                    <TableHead>Solicitação</TableHead>
                                    <TableHead>Descrição</TableHead>
                                    <TableHead>Unidade Operacional</TableHead>
                                    <TableHead>Data Envio</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {ordens.map((ordem) => (
                                    <TableRow key={ordem.ordem_servico_id}>
                                        <TableCell>#{ordem.ordem_servico_id}</TableCell>
                                        <TableCell>#{ordem.solicitacao_servico?.solicitacao_servico_id}</TableCell>
                                        <TableCell>{ordem.solicitacao_servico?.descricao}</TableCell>
                                        <TableCell>{ordem.unidade_operacional?.nome}</TableCell>
                                        <TableCell>{ordem.data_status_atual}</TableCell>
                                        <TableCell>
                                            <Button
                                                size="sm"
                                                render={
                                                    <Link
                                                        href={route('cra.receber-amostra.show', ordem.ordem_servico_id)}
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
