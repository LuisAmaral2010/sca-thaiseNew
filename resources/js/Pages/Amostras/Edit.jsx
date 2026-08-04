import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AmostraFields from './AmostraFields';

export default function Edit({ amostra, solicitacoes }) {
    const { data, setData, put, processing, errors } = useForm({
        descricao: amostra.descricao ?? '',
        solicitacao_id: amostra.solicitacao_id ?? '',
        validade_dias: amostra.validade_dias ?? '',
        condicao_armazenamento: amostra.condicao_armazenamento ?? '',
        numero_cra: amostra.numero_cra ?? '',
    });

    function submit(e) {
        e.preventDefault();
        put(route('amostras.update', { amostra: amostra.amostra_id }));
    }

    return (
        <AppLayout>
            <Head title="Editar Amostra" />
            <Card className="max-w-2xl">
                <form onSubmit={submit}>
                    <CardHeader>
                        <CardTitle>Editar Amostra #{amostra.amostra_id}</CardTitle>
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
                        <Button
                            variant="outline"
                            render={<Link href={route('amostras.show', { amostra: amostra.amostra_id })} />}
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
