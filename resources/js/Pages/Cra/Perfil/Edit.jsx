import { Head, Link, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import CraPerfilFields from './CraPerfilFields';
import { dadosIniciais } from './campos';

export default function Edit({ perfil, responsaveis }) {
    const { flash } = usePage().props;
    const { data, setData, put, processing, errors } = useForm(dadosIniciais(perfil));

    function submit(e) {
        e.preventDefault();
        put(route('cra.perfil.update'));
    }

    return (
        <AppLayout>
            <Head title="Editar Perfil do CRA" />
            <Card className="max-w-3xl">
                <form onSubmit={submit}>
                    <CardHeader>
                        <CardTitle>Editar Perfil do CRA</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {flash?.error && (
                            <Alert variant="destructive" className="mb-4">
                                <AlertDescription>{flash.error}</AlertDescription>
                            </Alert>
                        )}
                        <CraPerfilFields data={data} setData={setData} errors={errors} responsaveis={responsaveis} />
                    </CardContent>
                    <CardFooter className="justify-end gap-2">
                        <Button variant="outline" render={<Link href={route('cra.perfil.show')} />}>
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
