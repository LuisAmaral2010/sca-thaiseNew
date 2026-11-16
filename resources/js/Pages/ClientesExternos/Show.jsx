import { Head, Link, usePage } from '@inertiajs/react';
import { PencilIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardAction, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Separator } from '@/components/ui/separator';
import ExcluirClienteButton from './ExcluirClienteButton';
import { mascaraCep, mascaraTelefone } from './formatos';

function InfoRow({ label, value }) {
    return (
        <div className="grid grid-cols-3 gap-4 py-2 text-sm">
            <span className="text-muted-foreground">{label}</span>
            <span className="col-span-2 break-words whitespace-pre-line">{value || '—'}</span>
        </div>
    );
}

export default function Show({ cliente }) {
    const { flash } = usePage().props;
    const pj = cliente.tipo_pessoa === 'J';

    const endereco = [
        [cliente.logradouro, cliente.numero].filter(Boolean).join(', '),
        cliente.complemento,
        cliente.bairro,
        [cliente.cidade, cliente.uf].filter(Boolean).join('/'),
        cliente.cep && `CEP ${mascaraCep(cliente.cep)}`,
    ]
        .filter(Boolean)
        .join(' — ');

    return (
        <AppLayout>
            <Head title={cliente.nome} />
            <Card className="max-w-3xl">
                <CardHeader>
                    <CardTitle className="flex flex-wrap items-center gap-2">
                        {cliente.nome}
                        <Badge variant="outline">{pj ? 'Pessoa Jurídica' : 'Pessoa Física'}</Badge>
                        <Badge variant={cliente.is_ativo ? 'secondary' : 'outline'}>
                            {cliente.is_ativo ? 'Ativo' : 'Inativo'}
                        </Badge>
                    </CardTitle>
                    <CardAction>
                        <Button
                            variant="outline"
                            size="sm"
                            render={<Link href={route('clientes_externos.edit', cliente.cliente_externo_id)} />}
                        >
                            <PencilIcon data-icon="inline-start" />
                            Editar
                        </Button>
                    </CardAction>
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

                    <InfoRow label={pj ? 'Razão social' : 'Nome completo'} value={cliente.nome} />
                    <InfoRow label={pj ? 'CNPJ' : 'CPF'} value={cliente.cpf_cnpj_formatado} />
                    {pj ? (
                        <>
                            <InfoRow label="Nome fantasia" value={cliente.nome_fantasia} />
                            <InfoRow label="Inscrição estadual" value={cliente.inscricao_estadual} />
                            <InfoRow label="Responsável" value={cliente.responsavel_nome} />
                        </>
                    ) : (
                        <>
                            <InfoRow label="RG" value={cliente.rg} />
                            <InfoRow label="Data de nascimento" value={cliente.data_nascimento_formatada} />
                        </>
                    )}
                    <Separator className="my-2" />
                    <InfoRow label="E-mail" value={cliente.email} />
                    <InfoRow label="Telefone" value={cliente.telefone && mascaraTelefone(cliente.telefone)} />
                    <InfoRow label="Endereço" value={endereco} />
                    <Separator className="my-2" />
                    <InfoRow label="Observação" value={cliente.observacao} />
                    <InfoRow label="Cadastrado em" value={cliente.created_at_formatted} />
                    <InfoRow label="Editado em" value={cliente.updated_at_formatted} />
                </CardContent>
                <CardFooter className="justify-between gap-2">
                    <Button variant="outline" render={<Link href={route('clientes_externos.index')} />}>
                        Voltar
                    </Button>
                    <ExcluirClienteButton cliente={cliente} comTexto />
                </CardFooter>
            </Card>
        </AppLayout>
    );
}
