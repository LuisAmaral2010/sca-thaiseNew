import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { EyeIcon, PencilIcon, PlusIcon, UsersIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
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
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import DataPagination from '@/components/DataPagination';
import ExcluirClienteButton from './ExcluirClienteButton';
import { mascaraTelefone } from './formatos';

const tipoItems = [
    { label: 'Todos', value: 'todos' },
    { label: 'Pessoa Física', value: 'F' },
    { label: 'Pessoa Jurídica', value: 'J' },
];

export default function Index({ clientes, filtros }) {
    const { flash } = usePage().props;
    const [busca, setBusca] = useState(filtros.busca ?? '');
    const [tipoPessoa, setTipoPessoa] = useState(filtros.tipo_pessoa ?? 'todos');

    function filtrar(e) {
        e.preventDefault();
        router.get(
            route('clientes_externos.index'),
            {
                busca: busca || undefined,
                tipo_pessoa: tipoPessoa === 'todos' ? undefined : tipoPessoa,
            },
            { preserveState: true }
        );
    }

    return (
        <AppLayout>
            <Head title="Clientes Externos" />
            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <CardTitle>Clientes Externos</CardTitle>
                    <Button size="sm" render={<Link href={route('clientes_externos.create')} />}>
                        <PlusIcon data-icon="inline-start" />
                        Novo Cliente
                    </Button>
                </CardHeader>
                <CardContent>
                    {flash?.success && (
                        <Alert className="mb-4">
                            <AlertDescription>{flash.success}</AlertDescription>
                        </Alert>
                    )}
                    {flash?.error && (
                        <Alert variant="destructive" className="mb-4">
                            <AlertDescription>{flash.error}</AlertDescription>
                        </Alert>
                    )}

                    <form onSubmit={filtrar} className="mb-6 flex flex-wrap items-end gap-4">
                        <Field className="w-full sm:w-80">
                            <FieldLabel htmlFor="busca">Buscar</FieldLabel>
                            <Input
                                id="busca"
                                value={busca}
                                placeholder="Nome, nome fantasia, CPF ou CNPJ"
                                onChange={(e) => setBusca(e.target.value)}
                            />
                        </Field>
                        <Field className="w-full sm:w-48">
                            <FieldLabel htmlFor="tipo_pessoa">Tipo de pessoa</FieldLabel>
                            <Select items={tipoItems} value={tipoPessoa} onValueChange={setTipoPessoa}>
                                <SelectTrigger id="tipo_pessoa" className="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        {tipoItems.map((item) => (
                                            <SelectItem key={item.value} value={item.value}>
                                                {item.label}
                                            </SelectItem>
                                        ))}
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </Field>
                        <Button type="submit" variant="secondary">
                            Filtrar
                        </Button>
                    </form>

                    {clientes.data.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <UsersIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhum cliente externo encontrado</EmptyTitle>
                                <EmptyDescription>Cadastre um novo cliente externo para começar.</EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome / Razão social</TableHead>
                                    <TableHead>Tipo</TableHead>
                                    <TableHead>CPF / CNPJ</TableHead>
                                    <TableHead>Contato</TableHead>
                                    <TableHead>Cidade/UF</TableHead>
                                    <TableHead>Situação</TableHead>
                                    <TableHead className="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {clientes.data.map((cliente) => (
                                    <TableRow key={cliente.cliente_externo_id}>
                                        <TableCell className="whitespace-normal">
                                            {cliente.nome}
                                            {cliente.nome_fantasia && (
                                                <div className="text-muted-foreground">{cliente.nome_fantasia}</div>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="outline">{cliente.tipo_pessoa === 'J' ? 'PJ' : 'PF'}</Badge>
                                        </TableCell>
                                        <TableCell className="tabular-nums">{cliente.cpf_cnpj_formatado}</TableCell>
                                        <TableCell className="whitespace-normal">
                                            {cliente.email || cliente.telefone ? (
                                                <>
                                                    {cliente.email && <div>{cliente.email}</div>}
                                                    {cliente.telefone && (
                                                        <div className="text-muted-foreground">{mascaraTelefone(cliente.telefone)}</div>
                                                    )}
                                                </>
                                            ) : (
                                                '—'
                                            )}
                                        </TableCell>
                                        <TableCell>{cliente.cidade_uf ?? '—'}</TableCell>
                                        <TableCell>
                                            <Badge variant={cliente.is_ativo ? 'secondary' : 'outline'}>
                                                {cliente.is_ativo ? 'Ativo' : 'Inativo'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex justify-end gap-1.5">
                                                <Button
                                                    variant="outline"
                                                    size="icon-sm"
                                                    aria-label={`Ver ${cliente.nome}`}
                                                    render={<Link href={route('clientes_externos.show', cliente.cliente_externo_id)} />}
                                                >
                                                    <EyeIcon />
                                                </Button>
                                                <Button
                                                    variant="outline"
                                                    size="icon-sm"
                                                    aria-label={`Editar ${cliente.nome}`}
                                                    render={<Link href={route('clientes_externos.edit', cliente.cliente_externo_id)} />}
                                                >
                                                    <PencilIcon />
                                                </Button>
                                                <ExcluirClienteButton cliente={cliente} />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}

                    <DataPagination links={clientes.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
