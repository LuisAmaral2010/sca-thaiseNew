import { useEffect, useId, useRef, useState } from 'react';
import axios from 'axios';
import { ExternalLinkIcon, SearchIcon, XIcon } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const http = axios.create({
    headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
});

function TipoPessoa({ tipo }) {
    return <Badge variant="outline">{tipo === 'J' ? 'PJ' : 'PF'}</Badge>;
}

// Busca de cliente externo cadastrado (ativo) por nome, nome fantasia ou CPF/CNPJ.
// value: cliente selecionado (ou null); onChange(cliente | null)
export default function BuscaClienteExterno({ id, value, onChange, disabled = false, invalid = false }) {
    const listaId = useId();
    const [termo, setTermo] = useState('');
    const [resultados, setResultados] = useState([]);
    const [carregando, setCarregando] = useState(false);
    const [erro, setErro] = useState(null);
    const [aberto, setAberto] = useState(false);
    const [ativo, setAtivo] = useState(-1);
    const ultimaBusca = useRef(0);
    const campo = useRef(null);

    // Busca com atraso de 300 ms; respostas antigas são descartadas
    useEffect(() => {
        const texto = termo.trim();

        if (texto.length < 2) {
            setResultados([]);
            setCarregando(false);
            setErro(null);
            return undefined;
        }

        const numeroBusca = ++ultimaBusca.current;
        setCarregando(true);

        const timer = setTimeout(() => {
            http.get(route('clientes_externos.buscar'), { params: { q: texto } })
                .then(({ data }) => {
                    if (numeroBusca !== ultimaBusca.current) return;
                    setResultados(Array.isArray(data) ? data : []);
                    setErro(null);
                    setAtivo(-1);
                })
                .catch(() => {
                    if (numeroBusca !== ultimaBusca.current) return;
                    setResultados([]);
                    setErro('Erro ao buscar clientes.');
                })
                .finally(() => {
                    if (numeroBusca === ultimaBusca.current) setCarregando(false);
                });
        }, 300);

        return () => clearTimeout(timer);
    }, [termo]);

    function selecionar(cliente) {
        onChange(cliente);
        setTermo('');
        setResultados([]);
        setAberto(false);
    }

    function trocar() {
        onChange(null);
        // Devolve o foco para a busca depois que o campo reaparecer
        setTimeout(() => campo.current?.focus(), 0);
    }

    function aoTeclar(e) {
        if (!aberto || resultados.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setAtivo((i) => (i + 1) % resultados.length);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setAtivo((i) => (i <= 0 ? resultados.length - 1 : i - 1));
        } else if (e.key === 'Enter' && ativo >= 0) {
            e.preventDefault();
            selecionar(resultados[ativo]);
        } else if (e.key === 'Escape') {
            setAberto(false);
        }
    }

    // Cliente já escolhido
    if (value) {
        return (
            <div
                className={`flex items-start justify-between gap-3 rounded-lg border px-3 py-2 ${invalid ? 'border-destructive' : ''}`}
            >
                <div className="min-w-0 text-sm">
                    <div className="flex flex-wrap items-center gap-2 font-medium">
                        <span className="break-words">{value.nome}</span>
                        <TipoPessoa tipo={value.tipo_pessoa} />
                    </div>
                    <div className="text-muted-foreground">
                        {[value.cpf_cnpj_formatado, value.nome_fantasia, value.cidade_uf].filter(Boolean).join(' · ')}
                    </div>
                </div>
                {!disabled && (
                    <Button type="button" variant="ghost" size="sm" onClick={trocar}>
                        <XIcon data-icon="inline-start" />
                        Trocar
                    </Button>
                )}
            </div>
        );
    }

    const mostrarLista = aberto && termo.trim().length >= 2;

    return (
        <div className="relative">
            <SearchIcon className="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
            <Input
                ref={campo}
                id={id}
                className="pl-8"
                value={termo}
                placeholder="Digite o nome, nome fantasia, CPF ou CNPJ do cliente"
                autoComplete="off"
                role="combobox"
                aria-expanded={mostrarLista}
                aria-controls={listaId}
                aria-autocomplete="list"
                aria-activedescendant={ativo >= 0 ? `${listaId}-${ativo}` : undefined}
                aria-invalid={invalid}
                disabled={disabled}
                onChange={(e) => {
                    setTermo(e.target.value);
                    setAberto(true);
                }}
                onFocus={() => setAberto(true)}
                // Atraso para permitir o clique em um resultado antes de fechar
                onBlur={() => setTimeout(() => setAberto(false), 150)}
                onKeyDown={aoTeclar}
            />

            {mostrarLista && (
                <div className="absolute z-20 mt-1 w-full overflow-hidden rounded-lg border bg-popover text-sm shadow-md">
                    {carregando ? (
                        <p className="px-3 py-2 text-muted-foreground">Buscando…</p>
                    ) : erro ? (
                        <p className="px-3 py-2 text-destructive">{erro}</p>
                    ) : resultados.length === 0 ? (
                        <div className="flex flex-wrap items-center justify-between gap-2 px-3 py-2">
                            <span className="text-muted-foreground">Nenhum cliente ativo encontrado.</span>
                            <a
                                href={route('clientes_externos.create')}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center gap-1 text-primary underline-offset-4 hover:underline"
                            >
                                Cadastrar cliente externo
                                <ExternalLinkIcon className="size-3.5" />
                            </a>
                        </div>
                    ) : (
                        <ul id={listaId} role="listbox" className="max-h-72 overflow-y-auto py-1">
                            {resultados.map((cliente, indice) => (
                                <li
                                    key={cliente.cliente_externo_id}
                                    id={`${listaId}-${indice}`}
                                    role="option"
                                    aria-selected={indice === ativo}
                                    className={`cursor-pointer px-3 py-2 ${indice === ativo ? 'bg-muted' : 'hover:bg-muted'}`}
                                    onMouseDown={(e) => e.preventDefault()}
                                    onClick={() => selecionar(cliente)}
                                    onMouseEnter={() => setAtivo(indice)}
                                >
                                    <div className="flex flex-wrap items-center gap-2 font-medium">
                                        {cliente.nome}
                                        <TipoPessoa tipo={cliente.tipo_pessoa} />
                                    </div>
                                    <div className="text-muted-foreground">
                                        {[cliente.cpf_cnpj_formatado, cliente.nome_fantasia, cliente.cidade_uf]
                                            .filter(Boolean)
                                            .join(' · ')}
                                    </div>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            )}
        </div>
    );
}
