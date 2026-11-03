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

    return (
        <Badge variant={status === 'REJEITADO_CRA' ? 'destructive' : 'secondary'} className="h-auto whitespace-normal text-left">
            {status}
        </Badge>
    );
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

// Agrupa as linhas em amostra -> frações (unidade operacional) -> serviços,
// estrutura usada tanto pela tabela (telas maiores) quanto pelos cartões (celular)
function agruparAmostras(linhas) {
    const amostraGroups = groupBy(
        linhas,
        (linha) => linha.amostra_descricao ?? `__sem_amostra_${linha.ordem_servico_id}`
    );

    return [...amostraGroups.entries()].map(([amostraKey, itensAmostra]) => {
        const unidadeGroups = groupBy(itensAmostra, (linha) => linha.unidade_operacional);

        return {
            key: amostraKey,
            amostra: itensAmostra[0],
            totalLinhas: itensAmostra.length,
            fracoes: [...unidadeGroups.entries()].map(([unidadeNome, itensUnidade], idx) => ({
                key: `${amostraKey}-${unidadeNome}`,
                numero: idx + 1,
                unidade: unidadeNome,
                primeira: itensUnidade[0],
                servicos: itensUnidade,
            })),
        };
    });
}

function AmostraInfo({ amostra }) {
    return (
        <>
            <strong>{amostra.amostra_descricao ?? 'Sem amostra associada'}</strong>
            {amostra.amostra_descricao && (
                <div className="text-xs text-muted-foreground">
                    Validade: {amostra.amostra_validade_dias ?? '—'} dias
                    <br />
                    Condição: {amostra.amostra_condicao_armazenamento ?? '—'}
                </div>
            )}
        </>
    );
}

function ServicoInfo({ linha }) {
    return (
        <>
            {linha.servico_descricao ?? 'Nenhum serviço associado'}
            {linha.servico_tipo_servico && (
                <span className="ml-1 text-xs text-muted-foreground">({linha.servico_tipo_servico})</span>
            )}
        </>
    );
}

function AcoesFracao({ linha, className = '' }) {
    const params = { ordem_servico: linha.ordem_servico_id, fracao_amostra: linha.fracao_amostra_id };

    return (
        <div className={`flex gap-2 ${className}`}>
            <Button size="sm" className="flex-1 md:flex-none" render={<Link href={route('cra.receber-amostra.show', params)} />}>
                Receber
            </Button>
            <Button
                size="sm"
                variant="destructive"
                className="flex-1 md:flex-none"
                render={<Link href={route('cra.receber-amostra.rejeitar', params)} />}
            >
                Rejeitar
            </Button>
        </div>
    );
}

function AcoesTodas({ solicitacaoId, className = '' }) {
    return (
        <div className={`flex gap-2 ${className}`}>
            <Button
                size="sm"
                className="flex-1 md:flex-none"
                render={<Link href={route('cra.receber-amostra.receber-todas', solicitacaoId)} />}
            >
                Receber todas
            </Button>
            <Button
                size="sm"
                variant="destructive"
                className="flex-1 md:flex-none"
                render={<Link href={route('cra.receber-amostra.rejeitar-todas', solicitacaoId)} />}
            >
                Rejeitar todas
            </Button>
        </div>
    );
}

export default function Ordens({ solicitacao, linhas }) {
    const { auth } = usePage().props;

    const podeEnviarLaboratorio =
        linhas.every((linha) => linha.fracao_status_atual !== 'ENVIADO_CRA') &&
        linhas.some((linha) => linha.fracao_status_atual === 'ACEITO_CRA');

    const podeRejeitarRequisicao = linhas.every((linha) => linha.fracao_status_atual === 'REJEITADO_CRA');

    const amostras = agruparAmostras(linhas);

    // Tabela (telas médias e maiores): amostra e fração mescladas por rowSpan
    const rows = amostras.flatMap((grupo) =>
        grupo.fracoes.flatMap((fracao, idxFracao) =>
            fracao.servicos.map((linha, idxServico) => (
                <TableRow key={`${fracao.key}-${idxServico}`}>
                    {idxFracao === 0 && idxServico === 0 && (
                        <TableCell rowSpan={grupo.totalLinhas} className="align-top whitespace-normal">
                            <AmostraInfo amostra={grupo.amostra} />
                        </TableCell>
                    )}
                    {idxServico === 0 && (
                        <TableCell rowSpan={fracao.servicos.length} className="align-top whitespace-normal">
                            {`Fração ${fracao.numero}: ${fracao.unidade}`}
                        </TableCell>
                    )}
                    <TableCell className="whitespace-normal">
                        <ServicoInfo linha={linha} />
                    </TableCell>
                    {idxServico === 0 && (
                        <TableCell rowSpan={fracao.servicos.length} className="align-top">
                            <StatusBadge status={fracao.primeira.fracao_status_atual} />
                        </TableCell>
                    )}
                    {idxServico === 0 && (
                        <TableCell rowSpan={fracao.servicos.length} className="align-top">
                            <AcoesFracao linha={fracao.primeira} className="justify-center" />
                        </TableCell>
                    )}
                </TableRow>
            ))
        )
    );

    return (
        <AppLayout>
            <Head title={`CRA – Recepção de Amostra — Solicitação #${solicitacao.solicitacao_servico_id}`} />
            <Card>
                <CardHeader>
                    <CardTitle className="leading-snug">
                        CRA – Recepção de Amostra — Solicitação #{solicitacao.solicitacao_servico_id}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <dl className="mb-4 grid gap-x-6 gap-y-1 text-sm text-muted-foreground sm:grid-cols-2">
                        <div className="break-words">Atividade: {solicitacao.atividade_id}</div>
                        <div className="break-words">Material: {solicitacao.descricao}</div>
                        <div className="break-words">Operador: {auth.user?.name}</div>
                        <div className="break-words">Status: {solicitacao.status}</div>
                    </dl>

                    {/* Telas médias e maiores: tabela */}
                    <div className="hidden md:block">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Amostra</TableHead>
                                    <TableHead>Unidade Operacional</TableHead>
                                    <TableHead>Serviço</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>
                                        <AcoesTodas solicitacaoId={solicitacao.solicitacao_servico_id} />
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>{rows}</TableBody>
                        </Table>
                    </div>

                    {/* Celular: um cartão por amostra, com as frações empilhadas */}
                    <div className="flex flex-col gap-3 md:hidden">
                        <AcoesTodas solicitacaoId={solicitacao.solicitacao_servico_id} />

                        {amostras.map((grupo) => (
                            <div key={grupo.key} className="rounded-lg border">
                                <div className="border-b bg-muted/50 px-3 py-2">
                                    <AmostraInfo amostra={grupo.amostra} />
                                </div>

                                <div className="divide-y">
                                    {grupo.fracoes.map((fracao) => (
                                        <div key={fracao.key} className="flex flex-col gap-2 px-3 py-3">
                                            <div className="flex items-start justify-between gap-2">
                                                <span className="text-sm font-medium">
                                                    {`Fração ${fracao.numero}: ${fracao.unidade}`}
                                                </span>
                                                <StatusBadge status={fracao.primeira.fracao_status_atual} />
                                            </div>

                                            <ul className="list-disc space-y-1 pl-5 text-sm">
                                                {fracao.servicos.map((linha, idx) => (
                                                    <li key={idx}>
                                                        <ServicoInfo linha={linha} />
                                                    </li>
                                                ))}
                                            </ul>

                                            <AcoesFracao linha={fracao.primeira} />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </CardContent>
                <CardFooter className="flex-col-reverse gap-2 sm:flex-row sm:justify-between">
                    <Button
                        variant="outline"
                        className="w-full sm:w-auto"
                        render={<Link href={route('cra.receber-amostra.index')} />}
                    >
                        Voltar
                    </Button>
                    <div className="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                        {podeRejeitarRequisicao && (
                            <Button
                                variant="destructive"
                                className="w-full sm:w-auto"
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
                                className="w-full sm:w-auto"
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
                                <TooltipTrigger render={<span className="inline-flex w-full sm:w-auto" />}>
                                    <Button disabled className="w-full sm:w-auto">
                                        Enviar para o(s) Laboratório(s)
                                    </Button>
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
