import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

export default function Ordens({ solicitacao, ordens }) {
    return (
        <AppLayout>
            <Head title={`Ordens Pendentes — Solicitação #${solicitacao.solicitacao_servico_id}`} />
            <Card>
                <CardHeader>
                    <CardTitle>
                        Ordens Pendentes — Solicitação #{solicitacao.solicitacao_servico_id}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="mb-4 text-sm text-muted-foreground">{solicitacao.descricao}</p>

                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Amostras</TableHead>
                                <TableHead>Ordem de Serviço</TableHead>
                                <TableHead>Unidade Operacional</TableHead>
                                <TableHead>Análises</TableHead>
                                <TableHead>Data Envio</TableHead>
                                <TableHead />
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {ordens.map((ordem) => (
                                <TableRow key={ordem.ordem_servico_id}>
                                    <TableCell>{ordem.amostras_descricao}</TableCell>
                                    <TableCell>#{ordem.ordem_servico_id}</TableCell>
                                    <TableCell>{ordem.unidade_operacional?.nome}</TableCell>
                                    <TableCell>{ordem.analises_descricao}</TableCell>
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
                </CardContent>
                <CardFooter>
                    <Button variant="outline" render={<Link href={route('cra.receber-amostra.index')} />}>
                        Voltar
                    </Button>
                </CardFooter>
            </Card>
        </AppLayout>
    );
}
