import { Head, Link, usePage } from '@inertiajs/react';
import { Building2, PencilIcon, PlusIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardAction, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Separator } from '@/components/ui/separator';
import Historico from './Historico';
import { formatar, nomeResponsavel } from './campos';

function InfoRow({ label, value }) {
    return (
        <div className="grid grid-cols-3 gap-4 py-2 text-sm">
            <span className="text-muted-foreground">{label}</span>
            <span className="col-span-2 break-words whitespace-pre-line">{value || '—'}</span>
        </div>
    );
}

function comCargo(nome, cargo) {
    return cargo ? `${nome} — ${cargo}` : nome;
}

function Mensagens() {
    const { flash } = usePage().props;

    return (
        <>
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
        </>
    );
}

// Perfil ainda não cadastrado
function SemPerfil({ podeCadastrar }) {
    return (
        <Card className="max-w-3xl">
            <CardHeader>
                <CardTitle>Perfil do CRA</CardTitle>
            </CardHeader>
            <CardContent>
                <Mensagens />
                <Empty>
                    <EmptyHeader>
                        <EmptyMedia>
                            <Building2 />
                        </EmptyMedia>
                        <EmptyTitle>Perfil do CRA ainda não cadastrado</EmptyTitle>
                        <EmptyDescription>
                            {podeCadastrar
                                ? 'Cadastre os dados institucionais do CRA.'
                                : 'Somente usuários com perfil CRA podem cadastrar os dados institucionais.'}
                        </EmptyDescription>
                    </EmptyHeader>
                    {podeCadastrar && (
                        <EmptyContent>
                            <Button render={<Link href={route('cra.perfil.create')} />}>
                                <PlusIcon data-icon="inline-start" />
                                Cadastrar
                            </Button>
                        </EmptyContent>
                    )}
                </Empty>
            </CardContent>
            <CardFooter>
                <Button variant="outline" render={<Link href={route('cra')} />}>
                    Voltar
                </Button>
            </CardFooter>
        </Card>
    );
}

export default function Show({ perfil, responsaveis, historico, can = {} }) {
    if (!perfil) {
        return (
            <AppLayout>
                <Head title="Perfil do CRA" />
                <SemPerfil podeCadastrar={can.create} />
            </AppLayout>
        );
    }

    const endereco = [
        [perfil.logradouro, perfil.numero].filter(Boolean).join(', '),
        perfil.complemento,
        perfil.bairro,
        [perfil.cidade, perfil.uf].filter(Boolean).join('/'),
        perfil.cep && `CEP ${formatar('cep', perfil.cep)}`,
    ]
        .filter(Boolean)
        .join(' — ');

    return (
        <AppLayout>
            <Head title="Perfil do CRA" />
            <Card className="max-w-3xl">
                <CardHeader>
                    <CardTitle className="flex flex-wrap items-center gap-2">
                        {perfil.nome}
                        {perfil.sigla && <Badge variant="outline">{perfil.sigla}</Badge>}
                    </CardTitle>
                    {can.update && (
                        <CardAction>
                            <Button variant="outline" size="sm" render={<Link href={route('cra.perfil.edit')} />}>
                                <PencilIcon data-icon="inline-start" />
                                Editar
                            </Button>
                        </CardAction>
                    )}
                </CardHeader>
                <CardContent>
                    <Mensagens />

                    <InfoRow label="Nome" value={perfil.nome} />
                    <InfoRow label="Sigla" value={perfil.sigla} />
                    <InfoRow label="CNPJ" value={formatar('cnpj', perfil.cnpj)} />
                    <Separator className="my-2" />
                    <InfoRow
                        label="Responsável"
                        value={comCargo(nomeResponsavel(responsaveis.responsavel), perfil.responsavel_cargo)}
                    />
                    <InfoRow
                        label="Responsável substituto"
                        value={comCargo(nomeResponsavel(responsaveis.substituto), perfil.responsavel_substituto_cargo)}
                    />
                    <Separator className="my-2" />
                    <InfoRow label="E-mail" value={perfil.email} />
                    <InfoRow label="Telefone" value={formatar('telefone', perfil.telefone)} />
                    <InfoRow
                        label="Site"
                        value={
                            perfil.site && (
                                <a href={perfil.site} target="_blank" rel="noreferrer" className="text-primary underline">
                                    {perfil.site}
                                </a>
                            )
                        }
                    />
                    <InfoRow label="Endereço" value={endereco} />
                    <Separator className="my-2" />
                    <InfoRow label="Horário de atendimento" value={perfil.horario_atendimento} />
                    <InfoRow label="Observação" value={perfil.observacao} />
                    <InfoRow label="Cadastrado em" value={perfil.created_at_formatted} />
                    <InfoRow label="Editado em" value={perfil.updated_at_formatted} />
                </CardContent>
                <CardFooter>
                    <Button variant="outline" render={<Link href={route('cra')} />}>
                        Voltar
                    </Button>
                </CardFooter>
            </Card>
            <Historico historico={historico} />
        </AppLayout>
    );
}
