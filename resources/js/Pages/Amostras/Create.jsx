import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AmostraFields from './AmostraFields';

export default function Create({ solicitacoes }) {
    const { data, setData, post, processing, errors } = useForm({
        descricao: '',
        solicitacao_id: '',
        validade_dias: '',
        condicao_armazenamento: '',
        numero_cra: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('amostras.store'));
    }

    return (
        <AppLayout>
            <Head title="Cadastrar Amostra" />
            <Card className="max-w-2xl">
                <form onSubmit={submit}>
                    <CardHeader>
                        <CardTitle>Cadastrar Amostra</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <AmostraFields
                            data={data}
                            setData={setData}
                            errors={errors}
                            solicitacoes={solicitacoes}
                        />
                    </CardContent>
                    <CardFooter className="justify-end gap-2">
                        <Button variant="outline" render={<Link href={route('amostras.index')} />}>
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
