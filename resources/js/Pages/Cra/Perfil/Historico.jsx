import { HistoryIcon } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ROTULOS, formatar } from './campos';

const ACOES = {
    CRIACAO: { rotulo: 'Cadastro', variant: 'default' },
    ALTERACAO: { rotulo: 'Alteração', variant: 'secondary' },
};

function Valor({ campo, valor }) {
    const texto = formatar(campo, valor);
    return texto === null ? <span className="text-muted-foreground italic">vazio</span> : texto;
}

// Histórico de cadastro e alterações do perfil do CRA
export default function Historico({ historico }) {
    return (
        <Card className="max-w-3xl">
            <CardHeader>
                <div className="flex items-center gap-3">
                    <HistoryIcon className="size-6 text-primary" />
                    <div>
                        <CardTitle>Histórico de alterações</CardTitle>
                        <CardDescription>Últimas alterações do perfil do CRA.</CardDescription>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                {historico.length === 0 ? (
                    <p className="text-sm text-muted-foreground">Nenhuma alteração registrada.</p>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-36">Data</TableHead>
                                <TableHead className="w-48">Usuário</TableHead>
                                <TableHead>Alterações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {historico.map((h) => {
                                const acao = ACOES[h.acao] ?? { rotulo: h.acao, variant: 'outline' };

                                return (
                                    <TableRow key={h.id} className="align-top">
                                        <TableCell className="whitespace-nowrap">{h.data}</TableCell>
                                        <TableCell className="whitespace-normal">
                                            {h.usuario ?? '—'}
                                            <div className="mt-1">
                                                <Badge variant={acao.variant}>{acao.rotulo}</Badge>
                                            </div>
                                        </TableCell>
                                        <TableCell className="whitespace-normal">
                                            <ul className="space-y-1 text-sm">
                                                {h.alteracoes.map(({ campo, anterior, novo }) => (
                                                    <li key={campo} className="break-words">
                                                        <span className="font-medium">{ROTULOS[campo] ?? campo}:</span>{' '}
                                                        {h.acao === 'ALTERACAO' && (
                                                            <>
                                                                <span className="line-through opacity-70">
                                                                    <Valor campo={campo} valor={anterior} />
                                                                </span>
                                                                {' → '}
                                                            </>
                                                        )}
                                                        <Valor campo={campo} valor={novo} />
                                                    </li>
                                                ))}
                                            </ul>
                                        </TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                )}
            </CardContent>
        </Card>
    );
}
