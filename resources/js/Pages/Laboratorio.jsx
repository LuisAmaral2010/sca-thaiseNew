import { Head } from '@inertiajs/react';
import { ClipboardListIcon, FileTextIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';

const funcionalidades = [
    { titulo: 'Aceitar amostra', icon: ClipboardListIcon },
    { titulo: 'Emitir laudo DOC', icon: FileTextIcon },
];

export default function Laboratorio() {
    return (
        <AppLayout>
            <Head title="Laboratório" />
            <h1 className="font-heading text-lg font-semibold">Laboratório</h1>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
