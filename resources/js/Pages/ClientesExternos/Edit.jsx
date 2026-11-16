import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import ClienteExternoFields from './ClienteExternoFields';

export default function Edit({ cliente }) {
    const { data, setData, put, processing, errors } = useForm({
        tipo_pessoa: cliente.tipo_pessoa,
        nome: cliente.nome ?? '',
        nome_fantasia: cliente.nome_fantasia ?? '',
        cpf_cnpj: cliente.cpf_cnpj ?? '',
        rg: cliente.rg ?? '',
        data_nascimento: cliente.data_nascimento ?? '',
        inscricao_estadual: cliente.inscricao_estadual ?? '',
        responsavel_nome: cliente.responsavel_nome ?? '',
        email: cliente.email ?? '',
        telefone: cliente.telefone ?? '',
        cep: cliente.cep ?? '',
        logradouro: cliente.logradouro ?? '',
        numero: cliente.numero ?? '',
        complemento: cliente.complemento ?? '',
        bairro: cliente.bairro ?? '',
        cidade: cliente.cidade ?? '',
        uf: cliente.uf ?? '',
        observacao: cliente.observacao ?? '',
        is_ativo: !!cliente.is_ativo,
    });

    function submit(e) {
        e.preventDefault();
        put(route('clientes_externos.update', cliente.cliente_externo_id));
    }

    return (
        <AppLayout>
            <Head title="Editar Cliente Externo" />
            <Card className="max-w-3xl">
                <form onSubmit={submit}>
                    <CardHeader>
                        <CardTitle>Editar Cliente Externo #{cliente.cliente_externo_id}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ClienteExternoFields data={data} setData={setData} errors={errors} />
                    </CardContent>
                    <CardFooter className="justify-end gap-2">
                        <Button
                            variant="outline"
                            render={<Link href={route('clientes_externos.show', cliente.cliente_externo_id)} />}
                        >
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Salvar
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </AppLayout>
    );
}
