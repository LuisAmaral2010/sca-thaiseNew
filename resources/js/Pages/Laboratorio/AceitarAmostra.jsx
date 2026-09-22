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

                        <Button type="submit">Filtrar</Button>
                        <Button variant="outline" render={<Link href={route('resptec')} />}>
                            Voltar
                        </Button>
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
                        <Table className="mt-6">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ordem de Serviço</TableHead>
                                    <TableHead>Material</TableHead>
                                    <TableHead>Serviço</TableHead>
                                    <TableHead>Responsável pela Atividade</TableHead>
                                    <TableHead>Unidade Operacional</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Data Aceite Laboratório</TableHead>
                                    <TableHead>Ação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {ordensComRowSpan.map((ordem) => (
                                    <TableRow key={ordem.ordem_servico_id}>
                                        <TableCell>#{ordem.ordem_servico_id}</TableCell>
                                        {ordem.materialRowSpan > 0 && (
                                            <TableCell rowSpan={ordem.materialRowSpan}>
                                                {ordem.material ?? '—'}
                                            </TableCell>
                                        )}
                                        <TableCell>{ordem.servicos ?? '—'}</TableCell>
                                        <TableCell>{ordem.responsavel_atividade ?? '—'}</TableCell>
                                        <TableCell>{ordem.unidade_operacional ?? '—'}</TableCell>
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
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
