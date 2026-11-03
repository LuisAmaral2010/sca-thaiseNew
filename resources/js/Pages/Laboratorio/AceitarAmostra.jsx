import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { ClipboardListIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

function comRowSpanMaterial(ordens) {
    return ordens.map((ordem, index) => {
        const anterior = ordens[index - 1];
        const mesmoMaterial = anterior && anterior.material === ordem.material;

        if (mesmoMaterial) {
            return { ...ordem, materialRowSpan: 0 };
        }

        let rowSpan = 1;
        while (ordens[index + rowSpan] && ordens[index + rowSpan].material === ordem.material) {
            rowSpan++;
        }

        return { ...ordem, materialRowSpan: rowSpan };
    });
}

// Agrupa ordens consecutivas do mesmo material (mesmo critério do rowSpan da tabela)
function agruparPorMaterial(ordens) {
    return ordens.reduce((grupos, ordem) => {
        const ultimo = grupos[grupos.length - 1];
        if (ultimo && ultimo.material === ordem.material) {
            ultimo.ordens.push(ordem);
        } else {
            grupos.push({ material: ordem.material, ordens: [ordem] });
        }
        return grupos;
    }, []);
}

export default function AceitarAmostra({ unidadesOperacionais, filtros, ordens }) {
    const [dataInicial, setDataInicial] = useState(filtros.data_inicial ?? '');
    const [dataFinal, setDataFinal] = useState(filtros.data_final ?? '');
    const [unidadeOperacionalId, setUnidadeOperacionalId] = useState(
        filtros.unidade_operacional_id ? String(filtros.unidade_operacional_id) : null
    );

    const unidadeItems = unidadesOperacionais.map((unidade) => ({
        label: unidade.nome,
        value: String(unidade.unidade_operacional_id),
    }));

    const ordensComRowSpan = comRowSpanMaterial(ordens);

    function filtrar(e) {
        e.preventDefault();
        router.get(route('laboratorio.aceitar-amostra'), {
            data_inicial: dataInicial || undefined,
            data_final: dataFinal || undefined,
            unidade_operacional_id: unidadeOperacionalId || undefined,
        });
    }

    return (
        <AppLayout>
            <Head title="Gerenciar Cadastro de Análises" />
            <Card>
                <CardHeader>
                    <CardTitle>Gerenciar Cadastro de Análises</CardTitle>
                </CardHeader>
                <CardContent>
                    <form
                        onSubmit={filtrar}
                        className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-end"
                    >
                        <Field className="w-full lg:w-40">
                            <FieldLabel htmlFor="data_inicial">Data Inicial</FieldLabel>
                            <Input
                                id="data_inicial"
                                type="date"
                                value={dataInicial}
                                onChange={(e) => setDataInicial(e.target.value)}
                            />
                        </Field>

                        <Field className="w-full lg:w-40">
                            <FieldLabel htmlFor="data_final">Data Final</FieldLabel>
                            <Input
                                id="data_final"
                                type="date"
                                value={dataFinal}
                                onChange={(e) => setDataFinal(e.target.value)}
                            />
                        </Field>

                        <Field className="w-full lg:w-64">
                            <FieldLabel htmlFor="unidade_operacional_id">Unidade Operacional</FieldLabel>
                            <Select
                                items={unidadeItems}
                                value={unidadeOperacionalId}
                                onValueChange={setUnidadeOperacionalId}
                            >
                                <SelectTrigger id="unidade_operacional_id">
                                    <SelectValue placeholder="Todas as unidades" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        {unidadeItems.map((item) => (
                                            <SelectItem key={item.value} value={item.value}>
                                                {item.label}
                                            </SelectItem>
                                        ))}
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </Field>

                        <div className="flex gap-2 sm:col-span-2 lg:col-auto">

                            <Button type="submit" className="flex-1 lg:flex-none">

                                Filtrar

                            </Button>

                            <Button

                                variant="outline"

                                className="flex-1 lg:flex-none"

                                render={<Link href={route('resptec')} />}

                            >

                                Voltar

                            </Button>

                        </div>
                    </form>

                    {ordens.length === 0 ? (
                        <Empty className="mt-6">
                            <EmptyHeader>
                                <EmptyMedia>
                                    <ClipboardListIcon />
                                </EmptyMedia>
                                <EmptyTitle>
                                    {unidadeOperacionalId
                                        ? 'Nenhuma ordem de serviço encontrada'
                                        : 'Selecione uma unidade operacional para visualizar as ordens de serviço'}
                                </EmptyTitle>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <>
                        {/* Telas grandes (a partir de 1280px): tabela */}
                        <div className="mt-6 hidden xl:block">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="whitespace-normal">Ordem de Serviço</TableHead>
                                    <TableHead className="whitespace-normal">Material</TableHead>
                                    <TableHead className="whitespace-normal">Serviço</TableHead>
                                    <TableHead className="whitespace-normal">Responsável pela Atividade</TableHead>
                                    <TableHead className="whitespace-normal">Unidade Operacional</TableHead>
                                    <TableHead className="whitespace-normal">Status</TableHead>
                                    <TableHead className="whitespace-normal">Data Aceite Laboratório</TableHead>
                                    <TableHead className="whitespace-normal">Ação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {ordensComRowSpan.map((ordem) => (
                                    <TableRow key={ordem.ordem_servico_id}>
                                        <TableCell>#{ordem.ordem_servico_id}</TableCell>
                                        {ordem.materialRowSpan > 0 && (
                                            <TableCell rowSpan={ordem.materialRowSpan} className="align-top whitespace-normal">
                                                {ordem.material ?? '—'}
                                            </TableCell>
                                        )}
                                        <TableCell className="whitespace-normal">{ordem.servicos ?? '—'}</TableCell>
                                        <TableCell className="whitespace-normal">{ordem.responsavel_atividade ?? '—'}</TableCell>
                                        <TableCell className="whitespace-normal">{ordem.unidade_operacional ?? '—'}</TableCell>
                                        <TableCell>
                                            {ordem.status ? (
                                                <Badge variant="secondary">{ordem.status}</Badge>
                                            ) : (
                                                '—'
                                            )}
                                        </TableCell>
                                        <TableCell>{ordem.data_aceite_laboratorio ?? '—'}</TableCell>
                                        <TableCell>
                                            <Button
                                                size="sm"
                                                render={
                                                    <Link
                                                        href={route(
                                                            'laboratorio.aceitar-amostra.show',
                                                            ordem.ordem_servico_id
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

                        {/* Até 1280px: um cartão por material, com suas ordens de serviço */}
                        <div className="mt-6 flex flex-col gap-3 xl:hidden">
                            {agruparPorMaterial(ordens).map((grupo, idx) => (
                                <div key={idx} className="rounded-lg border">
                                    <div className="border-b bg-muted/50 px-3 py-2">
                                        <div className="text-xs text-muted-foreground">Material</div>
                                        <div className="font-medium break-words">{grupo.material ?? '—'}</div>
                                    </div>

                                    <div className="divide-y">
                                        {grupo.ordens.map((ordem) => (
                                            <div key={ordem.ordem_servico_id} className="flex flex-col gap-3 px-3 py-3">
                                                <div className="flex items-start justify-between gap-2">
                                                    <span className="text-sm font-medium">
                                                        Ordem de Serviço #{ordem.ordem_servico_id}
                                                    </span>
                                                    {ordem.status && (
                                                        <Badge variant="secondary" className="h-auto whitespace-normal text-left">
                                                            {ordem.status}
                                                        </Badge>
                                                    )}
                                                </div>

                                                <dl className="grid grid-cols-1 gap-x-3 gap-y-1 text-sm sm:grid-cols-[auto_1fr]">
                                                    <dt className="text-muted-foreground">Serviço</dt>
                                                    <dd className="mb-1 break-words sm:mb-0">{ordem.servicos ?? '—'}</dd>
                                                    <dt className="text-muted-foreground">Responsável pela Atividade</dt>
                                                    <dd className="mb-1 break-words sm:mb-0">{ordem.responsavel_atividade ?? '—'}</dd>
                                                    <dt className="text-muted-foreground">Unidade Operacional</dt>
                                                    <dd className="mb-1 break-words sm:mb-0">{ordem.unidade_operacional ?? '—'}</dd>
                                                    <dt className="text-muted-foreground">Data Aceite Laboratório</dt>
                                                    <dd>{ordem.data_aceite_laboratorio ?? '—'}</dd>
                                                </dl>

                                                <Button
                                                    size="sm"
                                                    className="w-full"
                                                    render={
                                                        <Link
                                                            href={route(
                                                                'laboratorio.aceitar-amostra.show',
                                                                ordem.ordem_servico_id
                                                            )}
                                                        />
                                                    }
                                                >
                                                    Detalhes
                                                </Button>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>
                        </>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
