import { Head, Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import DataPagination from '@/components/DataPagination';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { EyeIcon, PencilIcon, PlusIcon, Trash2Icon, FlaskConicalIcon } from 'lucide-react';

function DeleteAmostraButton({ amostra }) {
    function handleDelete() {
        router.delete(route('amostras.destroy', { amostra: amostra.amostra_id }));
    }

    return (
        <AlertDialog>
            <AlertDialogTrigger render={<Button variant="destructive" size="icon-sm" />}>
                <Trash2Icon />
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Apagar amostra #{amostra.amostra_id}?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Esta ação não pode ser desfeita. O registro será removido permanentemente.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction variant="destructive" onClick={handleDelete}>
                        Apagar
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}

export default function Index({ amostras }) {
    const { auth } = usePage().props;
    const can = auth?.can?.amostras ?? {};

    return (
        <AppLayout>
            <Head title="Amostras" />
            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <CardTitle>Amostras</CardTitle>
                    {can.create && (
                        <Button size="sm" render={<Link href={route('amostras.create')} />}>
                            <PlusIcon data-icon="inline-start" />
                            Nova Amostra
                        </Button>
                    )}
                </CardHeader>
                <CardContent>
                    {amostras.data.length === 0 ? (
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia>
                                    <FlaskConicalIcon />
                                </EmptyMedia>
                                <EmptyTitle>Nenhuma amostra encontrada</EmptyTitle>
                                <EmptyDescription>
                                    Cadastre uma nova amostra para começar.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Descrição</TableHead>
                                    <TableHead>Solicitação</TableHead>
                                    <TableHead>Validade (dias)</TableHead>
                                    <TableHead>Número CRA</TableHead>
                                    <TableHead className="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {amostras.data.map((amostra) => (
                                    <TableRow key={amostra.amostra_id}>
                                        <TableCell>{amostra.amostra_id}</TableCell>
                                        <TableCell>{amostra.descricao}</TableCell>
                                        <TableCell>{amostra.solicitacao_id}</TableCell>
                                        <TableCell>{amostra.validade_dias}</TableCell>
                                        <TableCell>{amostra.numero_cra}</TableCell>
                                        <TableCell className="flex justify-end gap-1.5">
                                            <Button
                                                variant="outline"
                                                size="icon-sm"
                                                render={
                                                    <Link
                                                        href={route('amostras.show', {
                                                            amostra: amostra.amostra_id,
                                                        })}
                                                    />
                                                }
                                            >
                                                <EyeIcon />
                                            </Button>
                                            {amostra.can_update && (
                                                <Button
                                                    variant="outline"
                                                    size="icon-sm"
                                                    render={
                                                        <Link
                                                            href={route('amostras.edit', {
                                                                amostra: amostra.amostra_id,
                                                            })}
                                                        />
                                                    }
                                                >
                                                    <PencilIcon />
                                                </Button>
                                            )}
                                            {amostra.can_delete && <DeleteAmostraButton amostra={amostra} />}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}

                    <DataPagination links={amostras.links} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
