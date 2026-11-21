import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { ArrowLeftIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { BotaoAprovar, BotaoRejeitar } from '@/components/resptec/botoes-laudo';

// Visualização do laudo pelo RespTec: o PDF do laudo e os botões "Aceitar
// Laudo", "Rejeitar Laudo" e "Voltar". Depois de aceitar ou rejeitar, volta
// para a lista da unidade operacional (voltar_para_lista).
export default function VisualizarLaudo({ laudo }) {
    const { flash } = usePage().props;
    const [aprovandoId, setAprovandoId] = useState(null);

    const ordem = laudo;
    const urlLista = route('resptec.aprovar-laudo', { unidade_operacional_id: laudo.unidade_operacional_id });

    function aprovar() {
        router.post(
            route('resptec.aprovar-laudo.aprovar', laudo.laudo_id),
            { voltar_para_lista: 1 },
            {
                onStart: () => setAprovandoId(laudo.laudo_id),
                onFinish: () => setAprovandoId(null),
            }
        );
    }

    // Aceitar/rejeitar só enquanto o laudo espera aprovação do RespTec
    const bloqueado = !laudo.aguardando_aprovacao;

    return (
        <AppLayout>
            <Head title={`Laudo ${laudo.numero_laudo ?? ''}`} />
            <Card>
                <CardHeader>
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <CardTitle>Laudo {laudo.numero_laudo ?? '—'}</CardTitle>
                            <CardDescription className="mt-1">
                                Ordem de Serviço #{laudo.ordem_servico_id}
                                {laudo.arquivo_nome && <> · {laudo.arquivo_nome}</>}
                            </CardDescription>
                        </div>
                        {laudo.status && (
                            <Badge variant="secondary" className="h-auto self-start whitespace-normal text-left">
                                {laudo.status}
                            </Badge>
                        )}
                    </div>
                </CardHeader>
                <CardContent className="flex flex-col gap-4">
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
                    {bloqueado && (
                        <Alert>
                            <AlertDescription>
                                Este laudo não está esperando aprovação do RespTec; não pode ser aceito nem rejeitado.
                            </AlertDescription>
                        </Alert>
                    )}

                    {laudo.arquivo_nome ? (
                        <iframe
                            src={route('resptec.aprovar-laudo.pdf', laudo.laudo_id)}
                            title={`Laudo ${laudo.numero_laudo ?? ''} (PDF)`}
                            className="h-[75vh] min-h-[480px] w-full rounded-lg border"
                        />
                    ) : (
                        <div className="rounded-lg border px-3 py-8 text-center text-sm text-muted-foreground">
                            Este laudo ainda não tem arquivo PDF gravado.
                        </div>
                    )}

                    <div className="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <Button size="sm" variant="outline" render={<Link href={urlLista} />}>
                            <ArrowLeftIcon />
                            Voltar
                        </Button>
                        <BotaoRejeitar
                            ordem={ordem}
                            rotulo="Rejeitar Laudo"
                            complemento="e você voltará para a lista"
                            dadosExtras={{ voltar_para_lista: 1 }}
                            desabilitado={bloqueado || aprovandoId !== null}
                            className="sm:w-auto"
                        />
                        <BotaoAprovar
                            ordem={ordem}
                            aprovandoId={aprovandoId}
                            onAprovar={aprovar}
                            rotulo="Aceitar Laudo"
                            complemento="e você voltará para a lista"
                            desabilitado={bloqueado}
                            className="sm:w-auto"
                        />
                    </div>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
