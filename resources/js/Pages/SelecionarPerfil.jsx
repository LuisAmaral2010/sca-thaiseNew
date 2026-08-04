import { Head, Link } from '@inertiajs/react';
import { Building2, FlaskConical, UserCog, UserCircle } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

const perfis = [
    {
        titulo: 'CRA',
        href: '/cra',
        icon: Building2,
        descricao: 'Recebe amostra, gera laudo pdf, gerencia lista de laboratórios e gerencia permissões de acesso.',
    },
    {
        titulo: 'Laboratório',
        href: '/laboratorio',
        icon: FlaskConical,
        descricao: 'Aceita amostra e emite laudo doc.',
    },
    {
        titulo: 'Resp Tec',
        href: '/resptec',
        icon: UserCog,
        descricao: 'Aprova laudo, gerencia permissões de laboratório e gerencia cadastro de análises.',
    },
    {
        titulo: 'Solicitante',
        href: '/solicitacoes_servicos',
        icon: UserCircle,
        descricao: 'Gerencia suas requisições.',
    },
];

export default function SelecionarPerfil() {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center gap-8 p-4">
            <Head title="Selecionar Perfil" />

            <p className="text-lg text-muted-foreground">Selecione o perfil desejado:</p>

            <div className="grid w-full max-w-5xl grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {perfis.map(({ titulo, href, icon: Icon, descricao }) => (
                    <Link key={href} href={href} className="block">
                        <Card className="h-full transition-shadow hover:shadow-md">
                            <CardHeader>
                                <Icon className="size-8 text-primary" />
                                <CardTitle>{titulo}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <CardDescription>{descricao}</CardDescription>
                            </CardContent>
                        </Card>
                    </Link>
                ))}
            </div>
        </div>
    );
}
