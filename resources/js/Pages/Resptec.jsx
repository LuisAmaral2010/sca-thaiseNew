import { Head, Link } from '@inertiajs/react';
import { ClipboardListIcon, FileCheckIcon, ShieldIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';

const funcionalidades = [
    { titulo: 'Aprovar laudo', icon: FileCheckIcon, href: route('resptec.aprovar-laudo') },
    { titulo: 'Gerenciar permissões de laboratório', icon: ShieldIcon },
    {
        titulo: 'Gerenciar cadastro de análises',
        icon: ClipboardListIcon,
        href: route('laboratorio.aceitar-amostra'),
    },
];

export default function Resptec() {
    return (
        <AppLayout>
            <Head title="Resp Tec" />
            <h1 className="font-heading text-lg font-semibold">Resp Tec</h1>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {funcionalidades.map(({ titulo, icon: Icon, href }) => {
                    // Celular: ícone ao lado do título (card compacto);
                    // a partir de 640px: ícone acima do título. h-full
                    // iguala a altura dos cards de uma mesma linha da grade.
                    const card = (
                        <Card className={`h-full ${href ? 'transition-colors hover:bg-muted/50' : ''}`}>
                            <CardHeader className="flex items-center gap-3 sm:grid sm:items-start sm:gap-1">
                                <Icon className="size-8 shrink-0 text-primary" />
                                <CardTitle className="leading-snug">{titulo}</CardTitle>
                            </CardHeader>
                        </Card>
                    );

                    return href ? (
                        <Link key={titulo} href={href} className="block h-full">
                            {card}
                        </Link>
                    ) : (
                        <div key={titulo} className="h-full">
                            {card}
                        </div>
                    );
                })}
            </div>
        </AppLayout>
    );
}
