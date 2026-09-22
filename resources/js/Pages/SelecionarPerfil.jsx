import { Head, Link } from '@inertiajs/react';
import { Building2, FlaskConical, ShieldCheck, UserCircle, UserCog } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

const todosPerfis = [
    {
        chave: 'cra',
        titulo: 'CRA',
        href: '/cra',
        icon: Building2,
        descricao: 'Recebe amostra, gera laudo pdf, gerencia lista de laboratórios e gerencia permissões de acesso.',
        inertia: true,
    },
    {
        chave: 'administrador',
        titulo: 'Administrador',
        href: '/perfis_acessos',
        icon: ShieldCheck,
        descricao: 'Gerencia os perfis de acesso dos usuários do CRA.',
        inertia: false,
    },
    {
        chave: 'resptec',
        titulo: 'Resp Tec',
        href: '/resptec',
        icon: UserCog,
        descricao: 'Aprova laudo, gerencia permissões de laboratório e gerencia cadastro de análises.',
        inertia: true,
    },
    {
        chave: 'laboratorio',
        titulo: 'Laboratório',
        href: '/laboratorio',
        icon: FlaskConical,
        descricao: 'Aceita amostra e emite laudo doc.',
        inertia: true,
    },
    {
        titulo: 'Solicitante',
        href: '/solicitacoes_servicos',
        icon: UserCircle,
        descricao: 'Gerencia suas requisições.',
        inertia: false,
    },
];

export default function SelecionarPerfil({ perfis = {} }) {
    const perfisDisponiveis = todosPerfis.filter(({ chave }) => !chave || perfis[chave]);

    return (
        <div className="flex min-h-screen flex-col items-center justify-center gap-8 p-4">
            <Head title="Selecionar Perfil" />

            <p className="text-lg text-muted-foreground">Selecione o perfil desejado:</p>

            <div className="flex w-full max-w-5xl flex-wrap justify-center gap-4">
                {perfisDisponiveis.map(({ titulo, href, icon: Icon, descricao, inertia }) => {
                    const CardLink = (
                        <Card className="h-full w-64 transition-shadow hover:shadow-md">
                            <CardHeader>
                                <Icon className="size-8 text-primary" />
                                <CardTitle>{titulo}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <CardDescription>{descricao}</CardDescription>
                            </CardContent>
                        </Card>
                    );

                    return inertia ? (
                        <Link key={href} href={href} className="block">
                            {CardLink}
                        </Link>
                    ) : (
                        <a key={href} href={href} className="block">
                            {CardLink}
                        </a>
                    );
                })}
            </div>
        </div>
    );
}
