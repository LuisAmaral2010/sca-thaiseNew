import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import ClienteExternoFields from './ClienteExternoFields';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        tipo_pessoa: 'F',
        nome: '',
        nome_fantasia: '',
        cpf_cnpj: '',
        rg: '',
        data_nascimento: '',
        inscricao_estadual: '',
        responsavel_nome: '',
        email: '',
        telefone: '',
        cep: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        uf: '',
        observacao: '',
        is_ativo: true,
    });

    function submit(e) {
        e.preventDefault();
        post(route('clientes_externos.store'));
    }

    return (
        <AppLayout>
            <Head title="Cadastrar Cliente Externo" />
            <Card className="max-w-3xl">
                <form onSubmit={submit}>
                    <CardHeader>
                        <CardTitle>Cadastrar Cliente Externo</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ClienteExternoFields data={data} setData={setData} errors={errors} />
                    </CardContent>
                    <CardFooter className="justify-end gap-2">
                        <Button variant="outline" render={<Link href={route('clientes_externos.index')} />}>
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Cadastrar
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </AppLayout>
    );
}
