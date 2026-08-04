import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

export default function Dashboard() {
    return (
        <AppLayout>
            <Head title="Dashboard" />
            <Card>
                <CardHeader>
                    <CardTitle>Dashboard</CardTitle>
                    <CardDescription>Painel administrativo do SCA.</CardDescription>
                </CardHeader>
                <CardContent>
                    <p className="text-sm text-muted-foreground">
                        Use o menu lateral para navegar entre os módulos disponíveis.
                    </p>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
