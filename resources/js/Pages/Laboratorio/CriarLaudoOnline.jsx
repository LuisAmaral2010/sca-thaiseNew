import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { FilePenLineIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldLabel } from '@/components/ui/field';
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

function BotaoPreencher({ ordem, className = '' }) {
    return (
        <Button
            size="sm"
            className={className}
            render={<Link href={route('laboratorio.criar-laudo-online.show', ordem.ordem_servico_id)} />}
        >
            Preencher laudo
        </Button>
    );
}

function Progresso({ ordem }) {
    const completo = ordem.total_analises > 0 && ordem.analises_preenchidas === ordem.total_analises;

    return (
        <Badge variant={completo ? 'default' : 'outline'}>
            {ordem.analises_preenchidas}/{ordem.total_analises}
        </Badge>
    );
}

export default function CriarLaudoOnline({ unidadesOperacionais, filtros, ordens }) {
    const [dataInicial, setDataInicial] = useState(filtros.data_inicial ?? '');
    const [dataFinal, setDataFinal] = useState(filtros.data_final ?? '');
    const [unidadeOperacionalId, setUnidadeOperacionalId] = useState(
        filtros.unidade_operacional_id ? String(filtros.unidade_operacional_id) : null
    );

    const unidadeItems = unidadesOperacionais.map((unidade) => ({
        label: unidade.nome,
        value: String(unidade.unidade_operacional_id),
    }));

    function filtrar(e) {
        e.preventDefault();
        router.get(route('laboratorio.criar-laudo-online'), {
            data_inicial: dataInicial || undefined,
            data_final: dataFinal || undefined,
            unidade_operacional_id: unidadeOperacionalId || undefined,
        });
    }

    return (
        <AppLayout>
            <Head title="Criar Laudo Online" />
            <Card>
                <CardHeader>
                    <CardTitle>Criar Laudo Online</CardTitle>
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
                                render={<Link href={route('laboratorio')} />}
                            >
                                Voltar
                            </Button>
                        </div>
                    </form>

                    {ordens.length === 0 ? (
                        <Empty className="mt-6">
                            <EmptyHeader>
                                <EmptyMedia>
                                    <FilePenLineIcon />
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
                                            <TableHead className="whitespace-normal">Status</TableHead>
                                            <TableHead className="whitespace-normal">Resultados</TableHead>
                                            <TableHead className="whitespace-normal">Ação</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {ordens.map((ordem) => (
                                            <TableRow key={ordem.ordem_servico_id}>
                                                <TableCell>#{ordem.ordem_servico_id}</TableCell>
                                                <TableCell className="whitespace-normal">{ordem.material ?? '—'}</TableCell>
                                                <TableCell className="whitespace-normal">{ordem.servicos ?? '—'}</TableCell>
                                                <TableCell className="whitespace-normal">
                                                    {ordem.responsavel_atividade ?? '—'}
                                                </TableCell>
                                                <TableCell>
                                                    <Badge variant="secondary" className="h-auto whitespace-normal text-left">
                                                        {ordem.status}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell>
                                                    <Progresso ordem={ordem} />
                                                </TableCell>
                                                <TableCell>
                                                    <BotaoPreencher ordem={ordem} />
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Até 1280px: um cartão por ordem de serviço */}
                            <div className="mt-6 flex flex-col gap-3 xl:hidden">
                                {ordens.map((ordem) => (
                                    <div key={ordem.ordem_servico_id} className="flex flex-col gap-3 rounded-lg border px-3 py-3">
                                        <div className="flex items-start justify-between gap-2">
                                            <span className="text-sm font-medium">
                                                Ordem de Serviço #{ordem.ordem_servico_id}
                                            </span>
                                            <Badge variant="secondary" className="h-auto whitespace-normal text-left">
                                                {ordem.status}
                                            </Badge>
                                        </div>

                                        <dl className="grid grid-cols-1 gap-x-3 gap-y-1 text-sm sm:grid-cols-[auto_1fr]">
                                            <dt className="text-muted-foreground">Material</dt>
                                            <dd className="mb-1 break-words sm:mb-0">{ordem.material ?? '—'}</dd>
                                            <dt className="text-muted-foreground">Serviço</dt>
                                            <dd className="mb-1 break-words sm:mb-0">{ordem.servicos ?? '—'}</dd>
                                            <dt className="text-muted-foreground">Responsável pela Atividade</dt>
                                            <dd className="mb-1 break-words sm:mb-0">
                                                {ordem.responsavel_atividade ?? '—'}
                                            </dd>
                                            <dt className="text-muted-foreground">Resultados</dt>
                                            <dd>
                                                <Progresso ordem={ordem} />
                                            </dd>
                                        </dl>

                                        <BotaoPreencher ordem={ordem} className="w-full" />
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
