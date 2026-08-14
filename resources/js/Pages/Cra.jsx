import { Head, Link } from '@inertiajs/react';
import { ClipboardListIcon, FileTextIcon, FlaskConicalIcon, ShieldIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';

const funcionalidades = [
    { titulo: 'Receber amostra', icon: ClipboardListIcon, href: route('cra.receber-amostra.index') },
    { titulo: 'Gerar laudo PDF', icon: FileTextIcon },
    { titulo: 'Gerenciar lista de laboratórios', icon: FlaskConicalIcon },
    { titulo: 'Gerenciar permissões de acesso', icon: ShieldIcon },
];

export default function Cra() {
    return (
        <AppLayout>
            <Head title="CRA" />
            <h1 className="font-heading text-lg font-semibold">CRA</h1>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {funcionalidades.map(({ titulo, icon: Icon, href }) => {
                    const card = (
                        <Card>
                            <CardHeader>
                                <Icon className="size-8 text-primary" />
                                <CardTitle>{titulo}</CardTitle>
                            </CardHeader>
                        </Card>
                    );

                    return href ? (
                        <Link key={titulo} href={href} className="block">
                            {card}
                        </Link>
                    ) : (
                        <div key={titulo}>{card}</div>
                    );
                })}
            </div>
        </AppLayout>
    );
}
