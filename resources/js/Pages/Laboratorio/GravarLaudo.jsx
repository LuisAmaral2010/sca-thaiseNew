import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { SaveIcon } from 'lucide-react';
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

export default function GravarLaudo({ unidadesOperacionais, filtros, laudos }) {
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
        router.get(route('laboratorio.gravar-laudo'), {
            data_inicial: dataInicial || undefined,
            data_final: dataFinal || undefined,
            unidade_operacional_id: unidadeOperacionalId || undefined,
        });
    }

    return (
        <AppLayout>
            <Head title="Gravar Laudo" />
            <Card>
                <CardHeader>
                    <CardTitle>Gravar Laudo</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={filtrar} className="flex flex-wrap items-end gap-4">
                        <Field className="w-40">
                            <FieldLabel htmlFor="data_inicial">Data Inicial</FieldLabel>
                            <Input
                                id="data_inicial"
                                type="date"
                                value={dataInicial}
                                onChange={(e) => setDataInicial(e.target.value)}
                            />
                        </Field>

                        <Field className="w-40">
                            <FieldLabel htmlFor="data_final">Data Final</FieldLabel>
                            <Input
                                id="data_final"
                                type="date"
                                value={dataFinal}
                                onChange={(e) => setDataFinal(e.target.value)}
                            />
                        </Field>

                        <Field className="w-64">
                            <FieldLabel htmlFor="unidade_operacional_id">Unidade Operacional</FieldLabel>
                            <Select
                                items={unidadeItems}
                                value={unidadeOperacionalId}
                                onValueChange={setUnidadeOperacionalId}
                            >
                                <SelectTrigger id="unidade_operacional_id">
                                    <SelectValue placeholder="Selecione a unidade" />
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

                        <Button type="submit">Filtrar</Button>
                        <Button variant="outline" render={<Link href={route('laboratorio')} />}>
                            Voltar
                        </Button>
                    </form>

                    {laudos.length === 0 ? (
                        <Empty className="mt-6">
                            <EmptyHeader>
                                <EmptyMedia>
                                    <SaveIcon />
                                </EmptyMedia>
                                <EmptyTitle>
                                    {unidadeOperacionalId
                                        ? 'Nenhum laudo encontrado'
                                        : 'Selecione uma unidade operacional para visualizar os laudos'}
                                </EmptyTitle>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table className="mt-6">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Número do Laudo</TableHead>
                                    <TableHead>Ordem de Serviço</TableHead>
                                    <TableHead>Unidade Operacional</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Data de Emissão</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {laudos.map((laudo) => (
                                    <TableRow key={laudo.laudo_id}>
                                        <TableCell>{laudo.numero_laudo}</TableCell>
                                        <TableCell>#{laudo.ordem_servico_id}</TableCell>
                                        <TableCell>{laudo.unidade_operacional ?? '—'}</TableCell>
                                        <TableCell>
                                            {laudo.status_atual ? (
                                                <Badge variant="secondary">{laudo.status_atual}</Badge>
                                            ) : (
                                                '—'
                                            )}
                                        </TableCell>
                                        <TableCell>{laudo.data_emissao ?? '—'}</TableCell>
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
