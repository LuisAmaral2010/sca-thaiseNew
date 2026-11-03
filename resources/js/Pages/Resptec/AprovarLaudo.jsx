import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { CheckIcon, DownloadIcon, XIcon } from 'lucide-react';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Alert, AlertDescription } from '@/components/ui/alert';
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
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
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

function StatusBadge({ status }) {
    if (!status) {
        return '—';
    }

    return (
        <Badge variant="secondary" className="h-auto whitespace-normal text-left">
            {status}
        </Badge>
    );
}

// Download do PDF gravado para o laudo (link comum, não navegação Inertia);
// desabilitado quando o laudo ainda não tem arquivo
function BotaoBaixar({ ordem, className = '' }) {
    if (!ordem.laudo_id || !ordem.arquivo_nome) {
        return (
            <Button size="sm" variant="outline" className={className} disabled>
                <DownloadIcon />
                Baixar Arquivo
            </Button>
        );
    }

    return (
        <Button
            size="sm"
            variant="outline"
            className={className}
            render={<a href={route('resptec.aprovar-laudo.arquivo', ordem.laudo_id)} download />}
        >
            <DownloadIcon />
            Baixar Arquivo
        </Button>
    );
}

// Botão "Aprovar" com confirmação antes de gravar
function BotaoAprovar({ ordem, aprovandoId, onAprovar, className = '' }) {
    const aprovando = aprovandoId === ordem.laudo_id;

    if (!ordem.laudo_id) {
        return (
            <Button size="sm" className={className} disabled>
                <CheckIcon />
                Aprovar
            </Button>
        );
    }

    return (
        <AlertDialog>
            <AlertDialogTrigger
                render={<Button size="sm" className={className} disabled={aprovandoId !== null} />}
            >
                <CheckIcon />
                {aprovando ? 'Aprovando...' : 'Aprovar'}
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Aprovar o laudo {ordem.numero_laudo}?</AlertDialogTitle>
                    <AlertDialogDescription>
                        O laudo da Ordem de Serviço #{ordem.ordem_servico_id} será marcado como aprovado pelo
                        RespTec e sairá desta lista.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction onClick={() => onAprovar(ordem)}>Aprovar</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}

// Botão "Rejeitar": abre uma caixa que exige o motivo antes de gravar
function BotaoRejeitar({ ordem, desabilitado = false, className = '' }) {
    const [aberto, setAberto] = useState(false);
    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({ motivo: '' });

    if (!ordem.laudo_id) {
        return (
            <Button size="sm" variant="destructive" className={className} disabled>
                <XIcon />
                Rejeitar
            </Button>
        );
    }

    function alterarAberto(valor) {
        setAberto(valor);
        if (!valor) {
            reset();
            clearErrors();
        }
    }

    function submit(e) {
        e.preventDefault();
        post(route('resptec.aprovar-laudo.rejeitar', ordem.laudo_id), {
            preserveScroll: true,
            onSuccess: () => alterarAberto(false),
        });
    }

    return (
        <Dialog open={aberto} onOpenChange={alterarAberto}>
            <DialogTrigger
                render={
                    <Button size="sm" variant="destructive" className={className} disabled={desabilitado || processing} />
                }
            >
                <XIcon />
                {processing ? 'Rejeitando...' : 'Rejeitar'}
            </DialogTrigger>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>Rejeitar o laudo {ordem.numero_laudo}?</DialogTitle>
                        <DialogDescription>
                            O laudo da Ordem de Serviço #{ordem.ordem_servico_id} será marcado como rejeitado pelo
                            RespTec e sairá desta lista. Informe o motivo da rejeição.
                        </DialogDescription>
                    </DialogHeader>
                    <Field className="mt-4" data-invalid={!!errors.motivo}>
                        <FieldLabel htmlFor={`motivo-${ordem.laudo_id}`}>Motivo da Rejeição</FieldLabel>
                        <Input
                            id={`motivo-${ordem.laudo_id}`}
                            value={data.motivo}
                            maxLength={255}
                            required
                            aria-invalid={!!errors.motivo}
                            onChange={(e) => setData('motivo', e.target.value)}
                            placeholder="Descreva o motivo da rejeição"
                        />
                        {errors.motivo && <FieldError>{errors.motivo}</FieldError>}
                    </Field>
                    <DialogFooter className="mt-4">
                        <DialogClose render={<Button type="button" variant="outline" />}>Cancelar</DialogClose>
                        <Button type="submit" variant="destructive" disabled={processing}>
                            Confirmar Rejeição
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}

function Laudo({ ordem }) {
    return (
        <>
            {ordem.numero_laudo ?? '—'}
            {ordem.arquivo_nome && (
                <div className="text-xs break-words text-muted-foreground">{ordem.arquivo_nome}</div>
            )}
        </>
    );
}

export default function AprovarLaudo({ unidadesOperacionais, filtros, ordens }) {
    const { flash } = usePage().props;
    const [aprovandoId, setAprovandoId] = useState(null);

    function aprovar(ordem) {
        router.post(
            route('resptec.aprovar-laudo.aprovar', ordem.laudo_id),
            {},
            {
                preserveScroll: true,
                onStart: () => setAprovandoId(ordem.laudo_id),
                onFinish: () => setAprovandoId(null),
            }
        );
    }

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

    // Mensagem da tabela vazia: antes de filtrar, orienta a escolher a unidade
    const mensagemVazia = filtros.unidade_operacional_id
        ? 'Nenhum laudo aguardando aprovação para os filtros informados.'
        : 'Selecione uma unidade operacional e clique em Filtrar para listar os laudos aguardando aprovação.';

    function filtrar(e) {
        e.preventDefault();
        router.get(route('resptec.aprovar-laudo'), {
            data_inicial: dataInicial || undefined,
            data_final: dataFinal || undefined,
            unidade_operacional_id: unidadeOperacionalId || undefined,
        });
    }

    return (
        <AppLayout>
            <Head title="Aprovar Laudo" />
            <Card>
                <CardHeader>
                    <CardTitle>Aprovar Laudo</CardTitle>
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

                    {flash?.success && (
                        <Alert className="mt-6">
                            <AlertDescription>{flash.success}</AlertDescription>
                        </Alert>
                    )}
                    {flash?.error && (
                        <Alert variant="destructive" className="mt-6">
                            <AlertDescription>{flash.error}</AlertDescription>
                        </Alert>
                    )}

                    {/* A tabela aparece sempre; vazia até que uma unidade seja filtrada */}
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
                                            <TableHead className="whitespace-normal">Laudo</TableHead>
                                            <TableHead className="whitespace-normal">Status</TableHead>
                                            <TableHead className="whitespace-normal">Data de Envio</TableHead>
                                            <TableHead className="text-center">Ação</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {ordens.length === 0 && (
                                            <TableRow>
                                                <TableCell
                                                    colSpan={9}
                                                    className="py-8 text-center whitespace-normal text-muted-foreground"
                                                >
                                                    {mensagemVazia}
                                                </TableCell>
                                            </TableRow>
                                        )}
                                        {ordensComRowSpan.map((ordem) => (
                                            <TableRow key={ordem.ordem_servico_id}>
                                                <TableCell>#{ordem.ordem_servico_id}</TableCell>
                                                {ordem.materialRowSpan > 0 && (
                                                    <TableCell
                                                        rowSpan={ordem.materialRowSpan}
                                                        className="align-top whitespace-normal"
                                                    >
                                                        {ordem.material ?? '—'}
                                                    </TableCell>
                                                )}
                                                <TableCell className="whitespace-normal">{ordem.servicos ?? '—'}</TableCell>
                                                <TableCell className="whitespace-normal">
                                                    {ordem.responsavel_atividade ?? '—'}
                                                </TableCell>
                                                <TableCell className="whitespace-normal">
                                                    {ordem.unidade_operacional ?? '—'}
                                                </TableCell>
                                                <TableCell className="whitespace-normal">
                                                    <Laudo ordem={ordem} />
                                                </TableCell>
                                                <TableCell>
                                                    <StatusBadge status={ordem.status} />
                                                </TableCell>
                                                <TableCell>{ordem.data_envio ?? '—'}</TableCell>
                                                <TableCell className="text-center">
                                                    <div className="flex flex-col gap-1.5">
                                                        <BotaoBaixar ordem={ordem} className="w-full" />
                                                        <BotaoAprovar ordem={ordem} aprovandoId={aprovandoId} onAprovar={aprovar} className="w-full" />
                                                        <BotaoRejeitar ordem={ordem} desabilitado={aprovandoId !== null} className="w-full" />
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Até 1280px: um cartão por material, com suas ordens de serviço */}
                            <div className="mt-6 flex flex-col gap-3 xl:hidden">
                                {ordens.length === 0 && (
                                    <div className="rounded-lg border px-3 py-8 text-center text-sm text-muted-foreground">
                                        {mensagemVazia}
                                    </div>
                                )}
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
                                                        <StatusBadge status={ordem.status} />
                                                    </div>

                                                    <dl className="grid grid-cols-1 gap-x-3 gap-y-1 text-sm sm:grid-cols-[auto_1fr]">
                                                        <dt className="text-muted-foreground">Laudo</dt>
                                                        <dd className="mb-1 break-words sm:mb-0">
                                                            <Laudo ordem={ordem} />
                                                        </dd>
                                                        <dt className="text-muted-foreground">Serviço</dt>
                                                        <dd className="mb-1 break-words sm:mb-0">{ordem.servicos ?? '—'}</dd>
                                                        <dt className="text-muted-foreground">Responsável pela Atividade</dt>
                                                        <dd className="mb-1 break-words sm:mb-0">
                                                            {ordem.responsavel_atividade ?? '—'}
                                                        </dd>
                                                        <dt className="text-muted-foreground">Unidade Operacional</dt>
                                                        <dd className="mb-1 break-words sm:mb-0">
                                                            {ordem.unidade_operacional ?? '—'}
                                                        </dd>
                                                        <dt className="text-muted-foreground">Data de Envio</dt>
                                                        <dd>{ordem.data_envio ?? '—'}</dd>
                                                    </dl>

                                                    <div className="grid grid-cols-2 gap-2">

                                                        <BotaoBaixar ordem={ordem} className="col-span-2 w-full" />

                                                        <BotaoAprovar ordem={ordem} aprovandoId={aprovandoId} onAprovar={aprovar} className="w-full" />

                                                        <BotaoRejeitar ordem={ordem} desabilitado={aprovandoId !== null} className="w-full" />

                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                    </>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
