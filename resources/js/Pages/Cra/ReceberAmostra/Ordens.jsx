import { Head, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';

function StatusBadge({ status }) {
    if (!status) {
        return <Badge variant="secondary">Pendente</Badge>;
    }

    return <Badge variant={status === 'REJEITADO_CRA' ? 'destructive' : 'secondary'}>{status}</Badge>;
}

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
    const { auth } = usePage().props;

    const podeEnviarLaboratorio =
        linhas.every((linha) => linha.fracao_status_atual !== 'ENVIADO_CRA') &&
        linhas.some((linha) => linha.fracao_status_atual === 'ACEITO_CRA');

    const podeRejeitarRequisicao = linhas.every((linha) => linha.fracao_status_atual === 'REJEITADO_CRA');

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
                                <StatusBadge status={linha.fracao_status_atual} />
                            </TableCell>
                        )}
                        {idxServico === 0 && (
                            <TableCell rowSpan={itensUnidade.length}>
                                <div className="flex w-full justify-center gap-2">
                                    <Button
                                        size="sm"
                                        render={
                                            <Link
                                                href={route('cra.receber-amostra.show', {
                                                    ordem_servico: linha.ordem_servico_id,
                                                    fracao_amostra: linha.fracao_amostra_id,
                                                })}
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
                                                href={route('cra.receber-amostra.rejeitar', {
                                                    ordem_servico: linha.ordem_servico_id,
                                                    fracao_amostra: linha.fracao_amostra_id,
                                                })}
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
            <Head title={`CRA – Recepção de Amostra — Solicitação #${solicitacao.solicitacao_servico_id}`} />
            <Card>
                <CardHeader>
                    <CardTitle>
                        CRA – Recepção de Amostra — Solicitação #{solicitacao.solicitacao_servico_id}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-sm text-muted-foreground">Atividade: {solicitacao.atividade_id}</p>
                    <p className="text-sm text-muted-foreground">Material: {solicitacao.descricao}</p>
                    <p className="text-sm text-muted-foreground">Operador: {auth.user?.name}</p>
                    <p className="mb-4 text-sm text-muted-foreground">Status: {solicitacao.status}</p>

                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Amostra</TableHead>
                                <TableHead>Unidade Operacional</TableHead>
                                <TableHead>Serviço</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>
                                    <div className="flex gap-2">
                                        <Button
                                            size="sm"
                                            render={
                                                <Link
                                                    href={route(
                                                        'cra.receber-amostra.receber-todas',
                                                        solicitacao.solicitacao_servico_id
                                                    )}
                                                />
                                            }
                                        >
                                            Receber todas
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="destructive"
                                            render={
                                                <Link
                                                    href={route(
                                                        'cra.receber-amostra.rejeitar-todas',
                                                        solicitacao.solicitacao_servico_id
                                                    )}
                                                />
                                            }
                                        >
                                            Rejeitar todas
                                        </Button>
                                    </div>
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>{rows}</TableBody>
                    </Table>
                </CardContent>
                <CardFooter className="justify-between">
                    <Button variant="outline" render={<Link href={route('cra.receber-amostra.index')} />}>
                        Voltar
                    </Button>
                    <div className="flex gap-2">
                        {podeRejeitarRequisicao && (
                            <Button
                                variant="destructive"
                                render={
                                    <Link
                                        href={route(
                                            'cra.receber-amostra.rejeitar-requisicao',
                                            solicitacao.solicitacao_servico_id
                                        )}
                                        method="post"
                                    />
                                }
                            >
                                Rejeitar esta Requisição
                            </Button>
                        )}
                        {podeEnviarLaboratorio ? (
                            <Button
                                render={
                                    <Link
                                        href={route(
                                            'cra.receber-amostra.enviar-laboratorio',
                                            solicitacao.solicitacao_servico_id
                                        )}
                                        method="post"
                                    />
                                }
                            >
                                Enviar para o(s) Laboratório(s)
                            </Button>
                        ) : (
                            <Tooltip>
                                <TooltipTrigger render={<span className="inline-flex" />}>
                                    <Button disabled>Enviar para o(s) Laboratório(s)</Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    Todas as frações precisam ser aceitas ou rejeitadas
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </div>
                </CardFooter>
            </Card>
        </AppLayout>
    );
}
