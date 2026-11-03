import { Head, Link, router, usePage } from '@inertiajs/react';
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
import { Alert, AlertDescription } from '@/components/ui/alert';
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

    const { flash, errors } = usePage().props;
    const [gravandoLaudoId, setGravandoLaudoId] = useState(null);
    const [erroSelecao, setErroSelecao] = useState(null);

    // Abre a caixa de diálogo "Abrir" iniciando na pasta Documentos e
    // exibindo apenas arquivos PDF. A File System Access API (Chrome/Edge)
    // permite definir a pasta inicial; nos demais navegadores usa-se um
    // <input type="file">, que abre na última pasta usada pelo sistema.
    // Os navegadores só filtram a caixa de diálogo por tipo/extensão, não por
    // nome; por isso o filtro indica o nome esperado (número do laudo) e o
    // arquivo escolhido é conferido antes do envio.
    async function selecionarPdf(laudo) {
        let arquivo;
        const nomeEsperado = `${laudo.numero_laudo}.pdf`;

        setErroSelecao(null);

        if (window.showOpenFilePicker) {
            try {
                const [handle] = await window.showOpenFilePicker({
                    startIn: 'documents',
                    multiple: false,
                    excludeAcceptAllOption: true,
                    types: [
                        {
                            description: `Laudo ${laudo.numero_laudo} (${nomeEsperado})`,
                            accept: { 'application/pdf': ['.pdf'] },
                        },
                    ],
                });
                arquivo = await handle.getFile();
            } catch (error) {
                if (error.name === 'AbortError') return;
                throw error;
            }
        } else {
            arquivo = await new Promise((resolve) => {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'application/pdf,.pdf';
                input.title = `Selecione o arquivo ${nomeEsperado}`;
                input.onchange = () => resolve(input.files[0]);
                input.click();
            });
        }

        if (!arquivo) return;

        if (arquivo.name.toLowerCase() !== nomeEsperado.toLowerCase()) {
            setErroSelecao(
                `O arquivo deve se chamar ${nomeEsperado} (arquivo selecionado: ${arquivo.name}). Nenhum arquivo foi gravado.`
            );
            return;
        }

        router.post(
            route('laboratorio.gravar-laudo.gravar', laudo.laudo_id),
            { arquivo },
            {
                forceFormData: true,
                preserveScroll: true,
                onStart: () => setGravandoLaudoId(laudo.laudo_id),
                onFinish: () => setGravandoLaudoId(null),
            }
        );
    }

    function StatusLaudo({ laudo }) {
        if (!laudo.status_atual) {
            return '—';
        }

        return (
            <Badge variant="secondary" className="h-auto whitespace-normal text-left">
                {laudo.status_atual}
            </Badge>
        );
    }

    // Botão "Gravar Laudo PDF" e o nome do PDF já gravado, se houver
    function AcaoGravar({ laudo, className = '', larguraTotal = false }) {
        return (
            <div className={className}>
                <Button
                    type="button"
                    size="sm"
                    className={larguraTotal ? 'w-full' : ''}
                    disabled={gravandoLaudoId !== null}
                    onClick={() => selecionarPdf(laudo)}
                >
                    {gravandoLaudoId === laudo.laudo_id ? 'Gravando...' : 'Gravar Laudo PDF'}
                </Button>
                {laudo.arquivo_nome && (
                    <div className="mt-1 text-xs break-words text-muted-foreground">{laudo.arquivo_nome}</div>
                )}
            </div>
        );
    }

    return (
        <AppLayout>
            <Head title="Gravar Laudo" />
            <Card>
                <CardHeader>
                    <CardTitle>Gravar Laudo</CardTitle>
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

                                render={<Link href={route('laboratorio')} />}

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
                    {erroSelecao && (
                        <Alert variant="destructive" className="mt-6">
                            <AlertDescription>{erroSelecao}</AlertDescription>
                        </Alert>
                    )}
                    {!erroSelecao && errors.arquivo && (
                        <Alert variant="destructive" className="mt-6">
                            <AlertDescription>{errors.arquivo}</AlertDescription>
                        </Alert>
                    )}

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
                        <>
                            {/* Telas grandes (a partir de 1024px): tabela */}
                            <div className="mt-6 hidden lg:block">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead className="whitespace-normal">Número do Laudo</TableHead>
                                            <TableHead className="whitespace-normal">Ordem de Serviço</TableHead>
                                            <TableHead className="whitespace-normal">Unidade Operacional</TableHead>
                                            <TableHead className="whitespace-normal">Status</TableHead>
                                            <TableHead className="whitespace-normal">Data de Emissão</TableHead>
                                            <TableHead className="text-center">Ações</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {laudos.map((laudo) => (
                                            <TableRow key={laudo.laudo_id}>
                                                <TableCell>{laudo.numero_laudo}</TableCell>
                                                <TableCell>#{laudo.ordem_servico_id}</TableCell>
                                                <TableCell className="whitespace-normal">
                                                    {laudo.unidade_operacional ?? '—'}
                                                </TableCell>
                                                <TableCell>
                                                    <StatusLaudo laudo={laudo} />
                                                </TableCell>
                                                <TableCell>{laudo.data_emissao ?? '—'}</TableCell>
                                                <TableCell className="text-center whitespace-normal">
                                                    <AcaoGravar laudo={laudo} />
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Até 1024px: um cartão por laudo */}
                            <div className="mt-6 grid gap-3 sm:grid-cols-2 lg:hidden">
                                {laudos.map((laudo) => (
                                    <div key={laudo.laudo_id} className="flex flex-col gap-3 rounded-lg border p-3">
                                        <div className="flex items-start justify-between gap-2">
                                            <div className="min-w-0">
                                                <div className="text-xs text-muted-foreground">Número do Laudo</div>
                                                <div className="font-medium break-words">{laudo.numero_laudo}</div>
                                            </div>
                                            <StatusLaudo laudo={laudo} />
                                        </div>

                                        <dl className="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-sm">
                                            <dt className="text-muted-foreground">Ordem de Serviço</dt>
                                            <dd>#{laudo.ordem_servico_id}</dd>
                                            <dt className="text-muted-foreground">Unidade</dt>
                                            <dd className="break-words">{laudo.unidade_operacional ?? '—'}</dd>
                                            <dt className="text-muted-foreground">Emissão</dt>
                                            <dd>{laudo.data_emissao ?? '—'}</dd>
                                        </dl>

                                        <AcaoGravar laudo={laudo} className="mt-auto" larguraTotal />
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
