import { Head, Link, router } from '@inertiajs/react';
import { useMemo, useRef, useState } from 'react';
import axios from 'axios';
import { PlusIcon, Trash2Icon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Field, FieldDescription, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription } from '@/components/ui/alert';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import BuscaClienteExterno from './BuscaClienteExterno';

// /cra/solicitacoes_servicos_externos/create — mesmo cadastro de
// /solicitacoes_servicos/create (resources/views/solicitacoes_servicos/create.blade.php),
// sem o select de atividades e com o cliente externo cadastrado (busca).
// Fluxo: Salvar (status CRIADO) -> resumo -> Finalizar (gera o número) ->
// Cancelar / Enviar para CRA -> volta para a lista do CRA.

const http = axios.create({
    headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
});

const classeTextarea =
    'w-full min-w-0 rounded-lg border border-input bg-transparent px-2.5 py-2 text-base outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:bg-input/50 disabled:opacity-50 aria-invalid:border-destructive md:text-sm dark:bg-input/30';

function mensagemDeErro(erro, padrao) {
    const data = erro?.response?.data;
    if (data?.errors) {
        return Object.values(data.errors).flat().join(' ');
    }
    return data?.message || padrao;
}

// Amostras com algum campo preenchido (linhas totalmente vazias são ignoradas)
function amostrasPreenchidas(amostras) {
    return amostras.filter(
        (a) => a.descricao.trim() !== '' || String(a.validade_dias).trim() !== '' || a.condicao_armazenamento.trim() !== ''
    );
}

// Serviços selecionados agrupados por unidade operacional, na ordem de seleção
function agruparPorUnidade(servicos) {
    const grupos = new Map();
    servicos.forEach((servico) => {
        if (!grupos.has(servico.unidade_nome)) {
            grupos.set(servico.unidade_nome, []);
        }
        grupos.get(servico.unidade_nome).push(servico);
    });
    return [...grupos.entries()].map(([unidade, lista]) => ({ unidade, servicos: lista }));
}

// Relação Amostras x Unidades Operacionais / Serviços (mesma tabela do create)
function TabelaResumo({ amostras, grupos }) {
    const totalLinhas = grupos.reduce((total, grupo) => total + grupo.servicos.length, 0);

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className="w-[35%]">Amostra</TableHead>
                    <TableHead className="w-[25%]">Unidade Operacional</TableHead>
                    <TableHead>Serviço</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {amostras.map((amostra, indiceAmostra) =>
                    grupos.flatMap((grupo, indiceGrupo) =>
                        grupo.servicos.map((servico, indiceServico) => (
                            <TableRow key={`${indiceAmostra}-${servico.servico_id}`}>
                                {indiceGrupo === 0 && indiceServico === 0 && (
                                    <TableCell rowSpan={totalLinhas} className="align-top whitespace-normal">
                                        <strong>{amostra.descricao.trim() || '—'}</strong>
                                        <div className="text-xs text-muted-foreground">
                                            Validade: {amostra.validade_dias || '—'} dias
                                            <br />
                                            Condição: {amostra.condicao_armazenamento.trim() || '—'}
                                        </div>
                                    </TableCell>
                                )}
                                {indiceServico === 0 && (
                                    <TableCell rowSpan={grupo.servicos.length} className="align-top whitespace-normal">
                                        Fração {indiceGrupo + 1}: {grupo.unidade}
                                    </TableCell>
                                )}
                                <TableCell className="whitespace-normal">
                                    {servico.descricao}
                                    {servico.tipo_servico && (
                                        <span className="ml-1 text-xs text-muted-foreground">({servico.tipo_servico})</span>
                                    )}
                                </TableCell>
                            </TableRow>
                        ))
                    )
                )}
            </TableBody>
        </Table>
    );
}

export default function Create({ unidadesOperacionais, cadastradoPor, dataSolicitacao }) {
    const proximaChave = useRef(1);

    // Dados do formulário
    const [clienteExterno, setClienteExterno] = useState(null);
    const [descricao, setDescricao] = useState('');
    const [amostras, setAmostras] = useState([]);
    const [servicosSelecionados, setServicosSelecionados] = useState([]);
    const [erros, setErros] = useState({});
    const [erroGeral, setErroGeral] = useState(null);

    // Seleção de serviços (dialog)
    const [dialogServicosAberto, setDialogServicosAberto] = useState(false);
    const [unidadeId, setUnidadeId] = useState(null);
    const [servicosDaUnidade, setServicosDaUnidade] = useState([]);
    const [carregandoServicos, setCarregandoServicos] = useState(false);
    const [erroServicos, setErroServicos] = useState(null);

    // Depois de salvar
    const [solicitacaoId, setSolicitacaoId] = useState(null);
    const [numero, setNumero] = useState(null);
    const [dialogResumoAberto, setDialogResumoAberto] = useState(false);
    const [processando, setProcessando] = useState(false);

    const salva = solicitacaoId !== null;
    const finalizada = numero !== null;

    const unidadeItems = unidadesOperacionais.map((unidade) => ({
        label: `${unidade.nome} (ID: ${unidade.unidade_operacional_id})`,
        value: String(unidade.unidade_operacional_id),
    }));

    const amostrasValidas = useMemo(() => amostrasPreenchidas(amostras), [amostras]);
    const grupos = useMemo(() => agruparPorUnidade(servicosSelecionados), [servicosSelecionados]);
    const mostrarServicos = amostrasValidas.length > 0;
    const mostrarResumo = amostrasValidas.length > 0 && servicosSelecionados.length > 0;

    // ---- Amostras ----
    function adicionarAmostra() {
        setAmostras((atuais) => [
            ...atuais,
            { chave: proximaChave.current++, descricao: '', validade_dias: '', condicao_armazenamento: '' },
        ]);
    }

    function alterarAmostra(chave, campo, valor) {
        setAmostras((atuais) => atuais.map((a) => (a.chave === chave ? { ...a, [campo]: valor } : a)));
    }

    function removerAmostra(chave) {
        setAmostras((atuais) => atuais.filter((a) => a.chave !== chave));
    }

    // ---- Serviços ----
    function selecionarUnidade(valor) {
        setUnidadeId(valor);
        setServicosDaUnidade([]);
        setErroServicos(null);

        if (!valor) {
            return;
        }

        setCarregandoServicos(true);
        http.get(route('unidades_operacionais.servicos', valor))
            .then(({ data }) => setServicosDaUnidade(Array.isArray(data) ? data : []))
            .catch(() => setErroServicos('Erro ao carregar serviços.'))
            .finally(() => setCarregandoServicos(false));
    }

    function alternarServico(servico, marcado) {
        const unidade = unidadesOperacionais.find((u) => String(u.unidade_operacional_id) === unidadeId);

        setServicosSelecionados((atuais) =>
            marcado
                ? [
                      ...atuais,
                      {
                          servico_id: servico.servico_id,
                          descricao: servico.descricao,
                          tipo_servico: servico.tipo_servico ?? '',
                          unidade_nome: unidade
                              ? `${unidade.nome} (ID: ${unidade.unidade_operacional_id})`
                              : `Unidade ${unidadeId}`,
                      },
                  ]
                : atuais.filter((s) => s.servico_id !== servico.servico_id)
        );
    }

    // ---- Salvar / Finalizar / Cancelar / Enviar ----
    function salvar(e) {
        e.preventDefault();
        setErros({});
        setErroGeral(null);

        if (!clienteExterno) {
            setErros({ cliente_externo_id: ['Selecione o cliente externo.'] });
            setErroGeral('Selecione o cliente externo.');
            return;
        }

        setProcessando(true);

        http.post(route('cra.solicitacoes_servicos_externos.store'), {
            cliente_externo_id: clienteExterno.cliente_externo_id,
            descricao,
            amostras: amostrasValidas.map(({ descricao: d, validade_dias, condicao_armazenamento }) => ({
                descricao: d,
                validade_dias: validade_dias === '' ? null : validade_dias,
                condicao_armazenamento,
            })),
            servicos: servicosSelecionados.map((s) => s.servico_id),
        })
            .then(({ data }) => {
                setSolicitacaoId(data.id);
                setDialogResumoAberto(true);
            })
            .catch((erro) => {
                setErros(erro?.response?.data?.errors ?? {});
                setErroGeral(mensagemDeErro(erro, 'Erro ao salvar a requisição.'));
            })
            .finally(() => setProcessando(false));
    }

    function finalizar() {
        setProcessando(true);
        setErroGeral(null);

        http.post(route('solicitacoes_servicos.finalizar', solicitacaoId))
            .then(({ data }) => {
                setNumero(data.numero_solicitacao_servico);
                setDialogResumoAberto(false);
            })
            .catch((erro) => setErroGeral(mensagemDeErro(erro, 'Não foi possível finalizar a requisição.')))
            .finally(() => setProcessando(false));
    }

    function acaoFinal(nomeRota, mensagemPadrao) {
        setProcessando(true);
        setErroGeral(null);

        http.post(route(nomeRota, solicitacaoId))
            .then(() => router.visit(route('cra.listar_solicitacoes_servicos.index')))
            .catch((erro) => {
                setErroGeral(mensagemDeErro(erro, mensagemPadrao));
                setProcessando(false);
            });
    }

    return (
        <AppLayout>
            <Head title="Cadastrar Requisição Externa" />
            <form onSubmit={salvar} className="flex flex-col gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>
                            Cadastrar Requisição Externa
                            {finalizada && <span className="ml-2 text-muted-foreground">{numero}</span>}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {erroGeral && (
                            <Alert variant="destructive" className="mb-4">
                                <AlertDescription>{erroGeral}</AlertDescription>
                            </Alert>
                        )}

                        <FieldGroup>
                            <Field data-invalid={!!erros.cliente_externo_id}>
                                <FieldLabel htmlFor="cliente_externo">Cliente externo</FieldLabel>
                                <BuscaClienteExterno
                                    id="cliente_externo"
                                    value={clienteExterno}
                                    onChange={(cliente) => {
                                        setClienteExterno(cliente);
                                        setErros(({ cliente_externo_id, ...resto }) => resto);
                                    }}
                                    disabled={salva}
                                    invalid={!!erros.cliente_externo_id}
                                />
                                {erros.cliente_externo_id && <FieldError>{erros.cliente_externo_id[0]}</FieldError>}
                            </Field>

                            <Field data-invalid={!!erros.descricao}>
                                <FieldLabel htmlFor="descricao">Descrição</FieldLabel>
                                <textarea
                                    id="descricao"
                                    rows={5}
                                    className={classeTextarea}
                                    value={descricao}
                                    onChange={(e) => setDescricao(e.target.value)}
                                    required
                                    disabled={salva}
                                    aria-invalid={!!erros.descricao}
                                />
                                {erros.descricao && <FieldError>{erros.descricao[0]}</FieldError>}
                            </Field>
                        </FieldGroup>
                    </CardContent>
                </Card>

                {/* Amostras */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between gap-2">
                        <CardTitle>Amostras</CardTitle>
                        <Button type="button" variant="secondary" size="sm" onClick={adicionarAmostra} disabled={salva}>
                            <PlusIcon />
                            Adicionar Amostra
                        </Button>
                    </CardHeader>
                    {amostras.length > 0 && (
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Descrição</TableHead>
                                        <TableHead className="w-36">Validade (dias)</TableHead>
                                        <TableHead>Condição Armazenamento</TableHead>
                                        <TableHead className="w-16 text-center">Remover</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {amostras.map((amostra, indice) => (
                                        <TableRow key={amostra.chave}>
                                            <TableCell>
                                                <Input
                                                    aria-label={`Descrição da amostra ${indice + 1}`}
                                                    value={amostra.descricao}
                                                    onChange={(e) => alterarAmostra(amostra.chave, 'descricao', e.target.value)}
                                                    disabled={salva}
                                                />
                                            </TableCell>
                                            <TableCell>
                                                <Input
                                                    type="number"
                                                    aria-label={`Validade em dias da amostra ${indice + 1}`}
                                                    value={amostra.validade_dias}
                                                    onChange={(e) => alterarAmostra(amostra.chave, 'validade_dias', e.target.value)}
                                                    disabled={salva}
                                                />
                                            </TableCell>
                                            <TableCell>
                                                <Input
                                                    aria-label={`Condição de armazenamento da amostra ${indice + 1}`}
                                                    value={amostra.condicao_armazenamento}
                                                    onChange={(e) =>
                                                        alterarAmostra(amostra.chave, 'condicao_armazenamento', e.target.value)
                                                    }
                                                    disabled={salva}
                                                />
                                            </TableCell>
                                            <TableCell className="text-center">
                                                <Button
                                                    type="button"
                                                    variant="destructive"
                                                    size="icon-sm"
                                                    aria-label={`Remover amostra ${indice + 1}`}
                                                    onClick={() => removerAmostra(amostra.chave)}
                                                    disabled={salva}
                                                >
                                                    <Trash2Icon />
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                            <FieldDescription className="mt-2">
                                Você pode adicionar várias amostras. Linhas totalmente vazias serão ignoradas no salvamento.
                            </FieldDescription>
                        </CardContent>
                    )}
                </Card>

                {/* Serviços selecionados: só aparece quando alguma amostra tiver informação */}
                {mostrarServicos && (
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2">
                            <CardTitle>Serviços Selecionados</CardTitle>
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                onClick={() => setDialogServicosAberto(true)}
                                disabled={salva}
                            >
                                Selecionar Serviços
                            </Button>
                        </CardHeader>
                        <CardContent>
                            {servicosSelecionados.length === 0 ? (
                                <p className="text-sm text-muted-foreground">Nenhum serviço selecionado.</p>
                            ) : (
                                <ul className="list-disc pl-5 text-sm">
                                    {servicosSelecionados.map((s) => (
                                        <li key={s.servico_id}>
                                            ID {s.servico_id} - {s.descricao} ({s.tipo_servico})
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </CardContent>
                    </Card>
                )}

                {/* Resumo: só aparece com amostras e serviços */}
                {mostrarResumo && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Relação de Amostras x Unidades Operacionais / Serviços</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <TabelaResumo amostras={amostrasValidas} grupos={grupos} />
                        </CardContent>
                    </Card>
                )}

                {/* Ações */}
                <div className="flex flex-wrap gap-2">
                    {!salva && (
                        <>
                            <Button type="submit" disabled={processando}>
                                {processando ? 'Salvando…' : 'Salvar'}
                            </Button>
                            <Button type="button" variant="outline" render={<Link href={route('cra')} />}>
                                Voltar
                            </Button>
                        </>
                    )}
                    {salva && !finalizada && (
                        <Button type="button" onClick={() => setDialogResumoAberto(true)}>
                            Ver resumo e finalizar
                        </Button>
                    )}
                    {finalizada && (
                        <>
                            <Button
                                type="button"
                                variant="outline"
                                disabled={processando}
                                onClick={() =>
                                    acaoFinal('solicitacoes_servicos.cancelar', 'Não foi possível cancelar a requisição.')
                                }
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="button"
                                disabled={processando}
                                onClick={() =>
                                    acaoFinal('solicitacoes_servicos.enviar_cra', 'Não foi possível enviar a requisição para o CRA.')
                                }
                            >
                                Enviar para CRA
                            </Button>
                        </>
                    )}
                </div>
            </form>

            {/* Dialog: selecionar serviços por unidade operacional */}
            <Dialog open={dialogServicosAberto} onOpenChange={setDialogServicosAberto}>
                <DialogContent className="sm:max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>Selecionar Serviços</DialogTitle>
                    </DialogHeader>

                    <Field>
                        <FieldLabel htmlFor="unidade_operacional">Unidade Operacional</FieldLabel>
                        <Select items={unidadeItems} value={unidadeId} onValueChange={selecionarUnidade}>
                            <SelectTrigger id="unidade_operacional" className="w-full">
                                <SelectValue placeholder="Selecione..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    {unidadeItems.map((item) => (
                                        <SelectItem key={item.value} value={item.value}>
                                            {item.label}
                                        </SelectItem>
                                    ))}
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </Field>

                    <div className="max-h-[50vh] overflow-y-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-12">#</TableHead>
                                    <TableHead>Descrição</TableHead>
                                    <TableHead>Tipo</TableHead>
                                    <TableHead className="w-24 text-center">Selecionar</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {!unidadeId ? (
                                    <TableRow>
                                        <TableCell colSpan={4} className="text-center text-muted-foreground">
                                            Selecione uma unidade operacional.
                                        </TableCell>
                                    </TableRow>
                                ) : carregandoServicos ? (
                                    <TableRow>
                                        <TableCell colSpan={4} className="text-center text-muted-foreground">
                                            Carregando serviços…
                                        </TableCell>
                                    </TableRow>
                                ) : erroServicos ? (
                                    <TableRow>
                                        <TableCell colSpan={4} className="text-center text-destructive">
                                            {erroServicos}
                                        </TableCell>
                                    </TableRow>
                                ) : servicosDaUnidade.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={4} className="text-center text-muted-foreground">
                                            Nenhum serviço encontrado para esta unidade.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    servicosDaUnidade.map((servico) => (
                                        <TableRow key={servico.servico_id}>
                                            <TableCell>{servico.servico_id}</TableCell>
                                            <TableCell className="whitespace-normal">{servico.descricao}</TableCell>
                                            <TableCell>{servico.tipo_servico ?? ''}</TableCell>
                                            <TableCell className="text-center">
                                                <input
                                                    type="checkbox"
                                                    className="size-4 accent-primary"
                                                    aria-label={`Selecionar ${servico.descricao}`}
                                                    checked={servicosSelecionados.some((s) => s.servico_id === servico.servico_id)}
                                                    onChange={(e) => alternarServico(servico, e.target.checked)}
                                                />
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </div>
                    <FieldDescription>
                        Marque os serviços desejados; eles serão enviados junto com a requisição.
                    </FieldDescription>
                </DialogContent>
            </Dialog>

            {/* Dialog: resumo após salvar */}
            <Dialog open={dialogResumoAberto} onOpenChange={setDialogResumoAberto}>
                <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-5xl">
                    <DialogHeader>
                        <DialogTitle>RESUMO: AMOSTRAS x UNIDADES OPERACIONAIS / SERVIÇOS</DialogTitle>
                    </DialogHeader>

                    <div>
                        <h3 className="mb-2 font-medium">Dados da Requisição</h3>
                        <Table>
                            <TableBody>
                                <TableRow>
                                    <TableHead className="w-48">Cliente externo</TableHead>
                                    <TableCell className="whitespace-normal">
                                        {clienteExterno?.nome ?? '—'}
                                        {clienteExterno?.cpf_cnpj_formatado && (
                                            <span className="ml-1 text-muted-foreground">({clienteExterno.cpf_cnpj_formatado})</span>
                                        )}
                                    </TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Descrição</TableHead>
                                    <TableCell className="whitespace-pre-line">{descricao}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Cadastrado por</TableHead>
                                    <TableCell>{cadastradoPor ?? '—'}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Data da Solicitação</TableHead>
                                    <TableCell>{dataSolicitacao}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    {mostrarResumo ? (
                        <TabelaResumo amostras={amostrasValidas} grupos={grupos} />
                    ) : (
                        <p className="text-sm text-muted-foreground">
                            {amostrasValidas.length === 0 ? 'Nenhuma amostra informada.' : 'Nenhum serviço selecionado.'}
                        </p>
                    )}

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={() => setDialogResumoAberto(false)}>
                            Voltar
                        </Button>
                        <Button type="button" onClick={finalizar} disabled={processando}>
                            {processando ? 'Finalizando…' : 'Finalizar'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
