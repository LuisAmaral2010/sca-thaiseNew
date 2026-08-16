import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

function groupBy(items, keyFn) {
    const groups = new Map();
    items.forEach((item) => {
        const key = keyFn(item);
        if (!groups.has(key)) {
            groups.set(key, []);
        }
        groups.get(key).push(item);
    });
    return groups;
}

export default function Ordens({ solicitacao, linhas }) {
    const amostraGroups = groupBy(
        linhas,
        (linha) => linha.amostra_descricao ?? `__sem_amostra_${linha.ordem_servico_id}`
    );

    const rows = [...amostraGroups.entries()].flatMap(([amostraKey, itensAmostra]) => {
        const unidadeGroups = groupBy(itensAmostra, (linha) => linha.unidade_operacional);
        const totalLinhasAmostra = itensAmostra.length;

        let linhaGlobal = 0;
        let n = 0;

        return [...unidadeGroups.entries()].flatMap(([unidadeNome, itensUnidade]) => {
            n += 1;

            return itensUnidade.map((linha, idxServico) => {
                linhaGlobal += 1;

                return (
                    <TableRow key={`${amostraKey}-${unidadeNome}-${idxServico}`}>
                        {linhaGlobal === 1 && (
                            <TableCell rowSpan={totalLinhasAmostra}>
                                <strong>{linha.amostra_descricao ?? 'Sem amostra associada'}</strong>
                                {linha.amostra_descricao && (
                                    <div className="text-xs text-muted-foreground">
                                        Validade: {linha.amostra_validade_dias ?? '—'} dias
                                        <br />
                                        Condição: {linha.amostra_condicao_armazenamento ?? '—'}
                                    </div>
                                )}
                            </TableCell>
                        )}
                        {idxServico === 0 && (
                            <TableCell rowSpan={itensUnidade.length}>
                                {`Fração ${n}: ${unidadeNome}`}
                            </TableCell>
                        )}
                        <TableCell>
                            {linha.servico_descricao ?? 'Nenhum serviço associado'}
                            {linha.servico_tipo_servico && (
                                <span className="ml-1 text-xs text-muted-foreground">
                                    ({linha.servico_tipo_servico})
                                </span>
                            )}
                        </TableCell>
                        {idxServico === 0 && (
                            <TableCell rowSpan={itensUnidade.length}>
                                <div className="flex gap-2">
                                    <Button
                                        size="sm"
                                        render={
                                            <Link
                                                href={route('cra.receber-amostra.show', linha.ordem_servico_id)}
                                            />
                                        }
                                    >
                                        Receber
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="destructive"
                                        render={
                                            <Link
                                                href={route('cra.receber-amostra.rejeitar', linha.ordem_servico_id)}
                                            />
                                        }
                                    >
                                        Rejeitar
                                    </Button>
                                </div>
                            </TableCell>
                        )}
                    </TableRow>
                );
            });
        });
    });

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
                                <TableHead>Amostra</TableHead>
                                <TableHead>Unidade Operacional</TableHead>
                                <TableHead>Serviço</TableHead>
                                <TableHead />
                            </TableRow>
                        </TableHeader>
                        <TableBody>{rows}</TableBody>
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
