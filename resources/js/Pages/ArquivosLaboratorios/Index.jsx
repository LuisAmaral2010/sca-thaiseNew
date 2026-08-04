import { Head } from '@inertiajs/react';
import { FileIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import DataPagination from '@/components/DataPagination';

export default function Index({ arquivos_laboratorios }) {
    return (
        <AppLayout>
            <Head title="Arquivos dos Laboratórios" />
            <Card>
                <CardHeader>
                    <CardTitle>Arquivos dos Laboratórios</CardTitle>
                </CardHeader>
                <CardContent>
                    {arquivos_laboratorios.data.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <FileIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhum arquivo encontrado</EmptyTitle>
                                <EmptyDescription>Os arquivos de laboratório aparecerão aqui.</EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Tipo</TableHead>
                                    <TableHead>Tamanho</TableHead>
                                    <TableHead>Aprovado Resp. Téc.</TableHead>
                                    <TableHead>Data Apreciação</TableHead>
                                    <TableHead>Observação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {arquivos_laboratorios.data.map((arquivo) => (
                                    <TableRow key={arquivo.arquivo_laboratorio_id}>
                                        <TableCell>{arquivo.nome}</TableCell>
                                        <TableCell>{arquivo.content_type}</TableCell>
                                        <TableCell>{arquivo.tamanho}</TableCell>
                                        <TableCell>{arquivo.aprovado_resp_tec ? 'Sim' : 'Não'}</TableCell>
                                        <TableCell>{arquivo.data_apreciacao}</TableCell>
                                        <TableCell>{arquivo.observacao}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                    <DataPagination links={arquivos_laboratorios.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
