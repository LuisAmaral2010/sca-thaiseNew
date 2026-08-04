import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

export default function Welcome() {
    return (
        <div className="flex min-h-svh w-full items-center justify-center p-6 md:p-10">
            <Head title="Bem-vindo" />
            <div className="w-full max-w-sm">
                <Card>
                    <CardHeader>
                        <CardTitle>Bem-vindo ao Controle de Amostras</CardTitle>
                        <CardDescription>
                            Sistema de gerenciamento de amostras, laudos e ordens de serviço da Embrapa Agroindústria de
                            Alimentos.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button className="w-full" render={<Link href={route('login')} />}>
                            Entrar
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
