import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { ArrowDownIcon, ArrowUpDownIcon, ArrowUpIcon, ClipboardListIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import DataPagination from '@/components/DataPagination';

// Mesma tabela "Solicitações de Serviço" de /solicitacoes_servicos

function listar(params) {
    router.get(route('cra.listar_solicitacoes_servicos.index'), params, { preserveState: true });
}

function CabecalhoOrdenavel({ coluna, filtros, children }) {
    const ativo = filtros.sort === coluna;
    const proximaDirecao = ativo && filtros.direction === 'asc' ? 'desc' : 'asc';
    const Icone = !ativo ? ArrowUpDownIcon : filtros.direction === 'asc' ? ArrowUpIcon : ArrowDownIcon;

    return (
        <TableHead aria-sort={ativo ? (filtros.direction === 'asc' ? 'ascending' : 'descending') : undefined}>
            <button
                type="button"
                className="inline-flex items-center gap-1 font-medium hover:underline"
                onClick={() =>
                    listar({
                        data_inicial: filtros.data_inicial || undefined,
                        data_final: filtros.data_final || undefined,
                        sort: coluna,
                        direction: proximaDirecao,
                    })
                }
            >
                {children}
                <Icone className={`size-3.5 ${ativo ? '' : 'text-muted-foreground'}`} />
            </button>
        </TableHead>
    );
}

export default function Index({ solicitacoes, filtros }) {
    const [dataInicial, setDataInicial] = useState(filtros.data_inicial ?? '');
    const [dataFinal, setDataFinal] = useState(filtros.data_final ?? '');

    function filtrar(e) {
        e.preventDefault();
        listar({
            data_inicial: dataInicial || undefined,
            data_final: dataFinal || undefined,
            sort: filtros.sort,
            direction: filtros.direction,
        });
    }

    function limparFiltro() {
        setDataInicial('');
        setDataFinal('');
        listar({ sort: filtros.sort, direction: filtros.direction });
    }

    return (
        <AppLayout>
            <Head title="Solicitações de Serviço" />
            <Card>
                <CardHeader>
                    <CardTitle>Solicitações de Serviço</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={filtrar} className="flex flex-wrap items-end gap-4">
                        <Field className="w-full sm:w-40">
                            <FieldLabel htmlFor="data_inicial">Data inicial</FieldLabel>
                            <Input
                                id="data_inicial"
                                type="date"
                                value={dataInicial}
                                onChange={(e) => setDataInicial(e.target.value)}
                            />
                        </Field>

                        <Field className="w-full sm:w-40">
                            <FieldLabel htmlFor="data_final">Data final</FieldLabel>
                            <Input
                                id="data_final"
                                type="date"
                                value={dataFinal}
                                onChange={(e) => setDataFinal(e.target.value)}
                            />
                        </Field>

                        <Button type="submit" variant="secondary">
                            Filtrar
                        </Button>

                        {(filtros.data_inicial || filtros.data_final) && (
                            <Button type="button" variant="link" onClick={limparFiltro}>
                                Limpar filtro
                            </Button>
                        )}
                    </form>

                    {solicitacoes.data.length === 0 ? (
                        <Empty className="mt-6">
                            <EmptyHeader>
                                <EmptyMedia>
                                    <ClipboardListIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhum registro encontrado!</EmptyTitle>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <div className="mt-6">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <CabecalhoOrdenavel coluna="solicitacao_servico_id" filtros={filtros}>
                                            #
                                        </CabecalhoOrdenavel>
                                        <TableHead>Número</TableHead>
                                        <CabecalhoOrdenavel coluna="data_solicitacao" filtros={filtros}>
                                            Data
                                        </CabecalhoOrdenavel>
                                        <CabecalhoOrdenavel coluna="descricao" filtros={filtros}>
                                            Descrição
                                        </CabecalhoOrdenavel>
                                        <TableHead>Atividade</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Solicitante</TableHead>
                                        <TableHead>Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {solicitacoes.data.map((solicitacao) => (
                                        <TableRow key={solicitacao.solicitacao_servico_id}>
                                            <TableCell>
                                                <Badge variant="outline">#{solicitacao.solicitacao_servico_id}</Badge>
                                            </TableCell>
                                            <TableCell>{solicitacao.numero_solicitacao_servico ?? '—'}</TableCell>
                                            <TableCell>{solicitacao.data_solicitacao ?? '—'}</TableCell>
                                            <TableCell className="whitespace-normal">{solicitacao.descricao ?? '—'}</TableCell>
                                            <TableCell className="whitespace-normal">{solicitacao.atividade ?? '—'}</TableCell>
                                            <TableCell>
                                                {solicitacao.status ? (
                                                    <Badge variant="secondary">{solicitacao.status}</Badge>
                                                ) : (
                                                    '—'
                                                )}
                                            </TableCell>
                                            <TableCell className="whitespace-normal">
                                                {solicitacao.solicitante_externo_nome ? (
                                                    // Requisição externa: nome do solicitante externo e quem cadastrou
                                                    <>
                                                        {solicitacao.solicitante_externo_nome}{' '}
                                                        <Badge variant="outline">Externo</Badge>
                                                        <div className="text-muted-foreground">
                                                            Cadastrado por{' '}
                                                            {solicitacao.solicitante ?? solicitacao.solicitante_matricula}
                                                        </div>
                                                    </>
                                                ) : (
                                                    <>
                                                        {solicitacao.solicitante ?? '—'}{' '}
                                                        <span className="text-muted-foreground">
                                                            ({solicitacao.solicitante_matricula})
                                                        </span>
                                                    </>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    render={
                                                        <a
                                                            href={route(
                                                                'solicitacoes_servicos.show',
                                                                solicitacao.solicitacao_servico_id
                                                            )}
                                                        />
                                                    }
                                                >
                                                    Detalhes
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    )}

                    <DataPagination links={solicitacoes.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
