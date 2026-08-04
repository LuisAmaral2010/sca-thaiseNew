import { Head } from '@inertiajs/react';
import { ClipboardListIcon, FileCheckIcon, ShieldIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';

const funcionalidades = [
    { titulo: 'Aprovar laudo', icon: FileCheckIcon },
    { titulo: 'Gerenciar permissões de laboratório', icon: ShieldIcon },
    { titulo: 'Gerenciar cadastro de análises', icon: ClipboardListIcon },
];

export default function Resptec() {
    return (
        <AppLayout>
            <Head title="Resp Tec" />
            <h1 className="font-heading text-lg font-semibold">Resp Tec</h1>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {funcionalidades.map(({ titulo, icon: Icon }) => (
                    <Card key={titulo}>
                        <CardHeader>
                            <Icon className="size-8 text-primary" />
                            <CardTitle>{titulo}</CardTitle>
                        </CardHeader>
                    </Card>
                ))}
            </div>
        </AppLayout>
    );
}
