import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardAction, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { PencilIcon } from 'lucide-react';

function InfoRow({ label, value }) {
    return (
        <div className="grid grid-cols-3 gap-4 py-2 text-sm">
            <span className="text-muted-foreground">{label}</span>
            <span className="col-span-2">{value ?? '—'}</span>
        </div>
    );
}

export default function Show({ amostra }) {
    return (
        <AppLayout>
            <Head title={`Amostra #${amostra.amostra_id}`} />
            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle>Amostra #{amostra.amostra_id}</CardTitle>
                    {amostra.can_update && (
                        <CardAction>
                            <Button
                                variant="outline"
                                size="sm"
                                render={<Link href={route('amostras.edit', { amostra: amostra.amostra_id })} />}
                            >
                                <PencilIcon data-icon="inline-start" />
                                Editar
                            </Button>
                        </CardAction>
                    )}
                </CardHeader>
                <CardContent>
                    <InfoRow label="Descrição" value={amostra.descricao} />
                    <InfoRow label="Solicitação" value={amostra.solicitacao_id} />
                    <InfoRow label="Validade (dias)" value={amostra.validade_dias} />
                    <InfoRow label="Condição de Armazenamento" value={amostra.condicao_armazenamento} />
                    <InfoRow label="Número CRA" value={amostra.numero_cra} />
                    <Separator className="my-2" />
                    <InfoRow label="Cadastrado em" value={amostra.created_at_formatted} />
                    <InfoRow label="Editado em" value={amostra.updated_at_formatted} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
