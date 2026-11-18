import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { EyeIcon, FileCheck2Icon, FileTextIcon, SendIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
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
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import EditorResultado from '@/components/EditorResultado';

function Dado({ rotulo, children }) {
    return (
        <div>
            <dt className="text-muted-foreground">{rotulo}</dt>
            <dd className="break-words">{children ?? '—'}</dd>
        </div>
    );
}

export default function CriarLaudoOnlineShow({ ordem, amostras }) {
    const { flash } = usePage().props;
    const [alterado, setAlterado] = useState(false);
    const [enviando, setEnviando] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        resultados: amostras.flatMap((amostra) =>
            amostra.analises.map((analise) => ({
                execucao_analise_id: analise.execucao_analise_id,
                resultado: analise.resultado ?? '',
            }))
        ),
    });

    // Posição de cada análise no array "resultados" (para ligar o input ao estado e ao erro)
    const indicePorExecucao = Object.fromEntries(
        data.resultados.map((item, indice) => [item.execucao_analise_id, indice])
    );

    // Atualização funcional: o editor chama onChange a cada tecla e não pode
    // trabalhar sobre uma cópia antiga de "resultados"
    function alterarResultado(execucaoAnaliseId, valor) {
        setAlterado(true);
        setData((atual) => ({
            ...atual,
            resultados: atual.resultados.map((item) =>
                item.execucao_analise_id === execucaoAnaliseId ? { ...item, resultado: valor } : item
            ),
        }));
    }

    function submit(e) {
        e.preventDefault();
        post(route('laboratorio.criar-laudo-online.gravar', ordem.ordem_servico_id), {
            preserveScroll: true,
            onSuccess: () => setAlterado(false),
        });
    }

    function enviarRespTec() {
        router.post(route('laboratorio.criar-laudo-online.enviar-resptec', ordem.ordem_servico_id), {}, {
            preserveScroll: true,
            onStart: () => setEnviando(true),
            onFinish: () => setEnviando(false),
        });
    }

    const totalAnalises = data.resultados.length;
    // O PDF é gerado a partir dos resultados gravados no servidor
    const avisoAlteracoes = alterado ? 'Grave os resultados antes de gerar o PDF.' : null;

    return (
        <AppLayout>
            <Head title={`Laudo Online — Ordem #${ordem.ordem_servico_id}`} />
            <form onSubmit={submit}>
                <Card>
                    <CardHeader>
                        <CardTitle>Laudo Online — Ordem de Serviço #{ordem.ordem_servico_id}</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {flash?.success && (
                            <Alert>
                                <AlertDescription>{flash.success}</AlertDescription>
                            </Alert>
                        )}
                        {flash?.error && (
                            <Alert variant="destructive">
                                <AlertDescription>{flash.error}</AlertDescription>
                            </Alert>
                        )}
                        {!ordem.editavel && (
                            <Alert variant="destructive">
                                <AlertDescription>
                                    Os resultados desta ordem não podem mais ser alterados (status: {ordem.status}).
                                </AlertDescription>
                            </Alert>
                        )}

                        <dl className="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                            <Dado rotulo="Nº da Solicitação">{ordem.numero_solicitacao_servico}</Dado>
                            <Dado rotulo="Nº do Laudo">{ordem.numero_laudo}</Dado>
                            <Dado rotulo="Solicitante">{ordem.solicitante}</Dado>
                            <Dado rotulo="Atividade">{ordem.atividade}</Dado>
                            <Dado rotulo="Material">{ordem.material}</Dado>
                            <Dado rotulo="Unidade Operacional">{ordem.unidade_operacional}</Dado>
                            <Dado rotulo="Data Aceite Laboratório">{ordem.data_aceite_laboratorio}</Dado>
                            <Dado rotulo="Status">
                                {ordem.status && <Badge variant="secondary">{ordem.status}</Badge>}
                            </Dado>
                        </dl>

                        <div>
                            <h2 className="mb-3 font-heading text-base font-semibold">Resultados Obtidos</h2>

                            {totalAnalises === 0 ? (
                                <p className="text-sm text-muted-foreground">
                                    Nenhuma fração amostra selecionada para preenchimento nesta ordem de serviço.
                                </p>
                            ) : (
                                <div className="flex flex-col gap-4">
                                    {amostras.map((amostra) => (
                                        <div key={amostra.amostra_id ?? 'sem-amostra'} className="rounded-lg border">
                                            <div className="flex flex-wrap items-baseline justify-between gap-2 border-b bg-muted/50 px-3 py-2">
                                                <span className="font-medium break-words">
                                                    {amostra.descricao ?? 'Amostra sem descrição'}
                                                </span>
                                                <span className="text-xs text-muted-foreground">
                                                    Nº CRA: {amostra.numero_cra ?? '—'}
                                                </span>
                                            </div>

                                            <div className="divide-y">
                                                {amostra.analises.map((analise) => {
                                                    const indice = indicePorExecucao[analise.execucao_analise_id];
                                                    const erro =
                                                        errors[`resultados.${indice}.resultado`] ??
                                                        errors[`resultados.${indice}.execucao_analise_id`];
                                                    const id = `resultado_${analise.execucao_analise_id}`;

                                                    return (
                                                        <Field
                                                            key={analise.execucao_analise_id}
                                                            className="flex flex-col gap-2 px-3 py-3"
                                                            data-invalid={!!erro}
                                                        >
                                                            <FieldLabel htmlFor={id}>
                                                                {analise.servico ?? 'Análise'}
                                                            </FieldLabel>
                                                            <EditorResultado
                                                                id={id}
                                                                value={analise.resultado}
                                                                disabled={!ordem.editavel}
                                                                invalido={!!erro}
                                                                urlEnvioImagem={route(
                                                                    'laboratorio.criar-laudo-online.imagem.enviar',
                                                                    ordem.ordem_servico_id
                                                                )}
                                                                onChange={(html) =>
                                                                    alterarResultado(analise.execucao_analise_id, html)
                                                                }
                                                            />
                                                            {erro && <FieldError>{erro}</FieldError>}
                                                        </Field>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </CardContent>
                    <CardFooter className="flex-col gap-3 sm:items-end">
                        {ordem.editavel && avisoAlteracoes && (
                            <p className="text-sm text-muted-foreground">{avisoAlteracoes}</p>
                        )}
                        <div className="flex w-full flex-col-reverse gap-2 sm:w-auto sm:flex-row sm:flex-wrap sm:justify-end">
                            <Button
                                type="button"
                                variant="outline"
                                className="w-full sm:w-auto"
                                render={<Link href={route('laboratorio.criar-laudo-online')} />}
                            >
                                Voltar
                            </Button>

                            {ordem.tem_pdf && !ordem.editavel && (
                                <Button
                                    type="button"
                                    variant="outline"
                                    className="w-full sm:w-auto"
                                    render={
                                        <a
                                            href={route('laboratorio.criar-laudo-online.pdf', ordem.ordem_servico_id)}
                                            target="_blank"
                                            rel="noopener"
                                        />
                                    }
                                >
                                    <FileCheck2Icon data-icon="inline-start" />
                                    Ver PDF do laudo
                                </Button>
                            )}

                            {ordem.editavel && totalAnalises > 0 && (
                                <>
                                    {alterado ? (
                                        <Button type="button" variant="outline" className="w-full sm:w-auto" disabled>
                                            <EyeIcon data-icon="inline-start" />
                                            Pré-visualizar PDF
                                        </Button>
                                    ) : (
                                        <Button
                                            type="button"
                                            variant="outline"
                                            className="w-full sm:w-auto"
                                            render={
                                                <a
                                                    href={route(
                                                        'laboratorio.criar-laudo-online.pdf.previa',
                                                        ordem.ordem_servico_id
                                                    )}
                                                    target="_blank"
                                                    rel="noopener"
                                                />
                                            }
                                        >
                                            <EyeIcon data-icon="inline-start" />
                                            Pré-visualizar PDF
                                        </Button>
                                    )}

                                    <Button
                                        type="submit"
                                        variant={alterado ? 'default' : 'outline'}
                                        className="w-full sm:w-auto"
                                        disabled={processing || enviando}
                                    >
                                        <FileTextIcon data-icon="inline-start" />
                                        Gravar resultados
                                    </Button>

                                    <AlertDialog>
                                        <AlertDialogTrigger
                                            render={
                                                <Button
                                                    type="button"
                                                    className="w-full sm:w-auto"
                                                    disabled={alterado || processing || enviando}
                                                />
                                            }
                                        >
                                            <SendIcon data-icon="inline-start" />
                                            Gerar PDF e enviar ao RespTec
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>
                                                    Gerar o laudo da Ordem #{ordem.ordem_servico_id}?
                                                </AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    O PDF do Relatório de Análise será gerado com os resultados
                                                    gravados{ordem.numero_laudo ? '' : ', o laudo receberá um número'} e
                                                    será enviado para aprovação do Responsável Técnico. Depois disso os
                                                    resultados não poderão mais ser alterados.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                                <AlertDialogAction onClick={enviarRespTec}>
                                                    Gerar e enviar
                                                </AlertDialogAction>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </>
                            )}
                        </div>
                    </CardFooter>
                </Card>
            </form>
        </AppLayout>
    );
}
