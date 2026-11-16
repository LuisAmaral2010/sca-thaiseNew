import { useEffect, useRef, useState } from 'react';
import axios from 'axios';
import { EditorContent, useEditor, useEditorState } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import { TableKit } from '@tiptap/extension-table';
import { Placeholder } from '@tiptap/extensions';
import {
    BetweenHorizontalEndIcon,
    BetweenVerticalEndIcon,
    BoldIcon,
    Columns2Icon,
    Heading2Icon,
    Heading3Icon,
    ImagePlusIcon,
    ItalicIcon,
    ListIcon,
    ListOrderedIcon,
    LoaderCircleIcon,
    PanelTopIcon,
    QuoteIcon,
    Redo2Icon,
    Rows2Icon,
    StrikethroughIcon,
    TableIcon,
    Trash2Icon,
    UnderlineIcon,
    Undo2Icon,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

const TIPOS_IMAGEM = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

function BotaoFerramenta({ titulo, icon: Icon, onClick, ativo = false, disabled = false }) {
    return (
        <Tooltip>
            <TooltipTrigger
                render={
                    <Button
                        type="button"
                        size="icon-sm"
                        variant={ativo ? 'secondary' : 'ghost'}
                        aria-label={titulo}
                        aria-pressed={ativo}
                        disabled={disabled}
                        onMouseDown={(e) => e.preventDefault()}
                        onClick={onClick}
                    />
                }
            >
                <Icon />
            </TooltipTrigger>
            <TooltipContent>{titulo}</TooltipContent>
        </Tooltip>
    );
}

function Separador() {
    return <span className="mx-1 h-5 w-px bg-border" aria-hidden="true" />;
}

// Editor de conteúdo rico para o resultado de uma análise: texto formatado,
// listas, tabelas e imagens. As imagens são enviadas para o servidor
// (urlEnvioImagem) e inseridas pela URL devolvida; colar ou arrastar uma
// imagem também a envia.
export default function EditorResultado({ id, value, onChange, urlEnvioImagem, disabled = false, invalido = false }) {
    const inputImagemRef = useRef(null);
    const [enviandoImagem, setEnviandoImagem] = useState(false);
    const [erroImagem, setErroImagem] = useState(null);

    // Referência estável para os handlers de colar/arrastar (criados uma vez com o editor)
    const enviarImagensRef = useRef(null);

    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                heading: { levels: [2, 3, 4] },
                code: false,
                codeBlock: false,
                link: false,
            }),
            Image.configure({ allowBase64: false }),
            TableKit.configure({ table: { resizable: true } }),
            Placeholder.configure({ placeholder: 'Digite o resultado, insira tabelas ou imagens…' }),
        ],
        content: value || '',
        editable: !disabled,
        editorProps: {
            attributes: {
                id,
                class: 'editor-resultado-conteudo',
                'aria-invalid': invalido ? 'true' : 'false',
            },
            handlePaste: (_view, event) => {
                const arquivos = Array.from(event.clipboardData?.files ?? []).filter((f) =>
                    TIPOS_IMAGEM.includes(f.type)
                );
                if (arquivos.length === 0) return false;
                event.preventDefault();
                enviarImagensRef.current?.(arquivos);
                return true;
            },
            handleDrop: (view, event, _slice, moved) => {
                if (moved) return false;
                const arquivos = Array.from(event.dataTransfer?.files ?? []).filter((f) =>
                    TIPOS_IMAGEM.includes(f.type)
                );
                if (arquivos.length === 0) return false;
                event.preventDefault();
                const posicao = view.posAtCoords({ left: event.clientX, top: event.clientY })?.pos;
                enviarImagensRef.current?.(arquivos, posicao);
                return true;
            },
        },
        onUpdate: ({ editor }) => {
            onChange(editor.isEmpty ? '' : editor.getHTML());
        },
    });

    useEffect(() => {
        editor?.setEditable(!disabled);
    }, [editor, disabled]);

    async function enviarImagens(arquivos, posicao) {
        if (!editor || disabled) return;

        setErroImagem(null);
        setEnviandoImagem(true);

        try {
            for (const arquivo of arquivos) {
                const formData = new FormData();
                formData.append('imagem', arquivo);

                const { data } = await axios.post(urlEnvioImagem, formData, {
                    headers: { Accept: 'application/json' },
                });

                const cadeia = editor.chain().focus();
                (posicao != null
                    ? cadeia.insertContentAt(posicao, { type: 'image', attrs: { src: data.url, alt: arquivo.name } })
                    : cadeia.setImage({ src: data.url, alt: arquivo.name })
                ).run();
            }
        } catch (erro) {
            setErroImagem(
                erro.response?.data?.errors?.imagem?.[0] ??
                    erro.response?.data?.message ??
                    'Não foi possível enviar a imagem.'
            );
        } finally {
            setEnviandoImagem(false);
        }
    }

    enviarImagensRef.current = enviarImagens;

    const estado = useEditorState({
        editor,
        selector: ({ editor }) =>
            editor
                ? {
                      negrito: editor.isActive('bold'),
                      italico: editor.isActive('italic'),
                      sublinhado: editor.isActive('underline'),
                      tachado: editor.isActive('strike'),
                      titulo2: editor.isActive('heading', { level: 2 }),
                      titulo3: editor.isActive('heading', { level: 3 }),
                      listaMarcadores: editor.isActive('bulletList'),
                      listaNumerada: editor.isActive('orderedList'),
                      citacao: editor.isActive('blockquote'),
                      emTabela: editor.isActive('table'),
                      podeDesfazer: editor.can().undo(),
                      podeRefazer: editor.can().redo(),
                  }
                : null,
    });

    if (!editor || !estado) {
        return null;
    }

    const cmd = () => editor.chain().focus();

    return (
        <div
            className={cn(
                'editor-resultado overflow-hidden rounded-lg border border-input bg-transparent focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50',
                invalido && 'border-destructive ring-3 ring-destructive/20',
                disabled && 'opacity-70'
            )}
        >
            {!disabled && (
                <div className="flex flex-wrap items-center gap-0.5 border-b bg-muted/40 p-1" role="toolbar">
                    <BotaoFerramenta titulo="Negrito" icon={BoldIcon} ativo={estado.negrito} onClick={() => cmd().toggleBold().run()} />
                    <BotaoFerramenta titulo="Itálico" icon={ItalicIcon} ativo={estado.italico} onClick={() => cmd().toggleItalic().run()} />
                    <BotaoFerramenta titulo="Sublinhado" icon={UnderlineIcon} ativo={estado.sublinhado} onClick={() => cmd().toggleUnderline().run()} />
                    <BotaoFerramenta titulo="Tachado" icon={StrikethroughIcon} ativo={estado.tachado} onClick={() => cmd().toggleStrike().run()} />
                    <Separador />
                    <BotaoFerramenta titulo="Título" icon={Heading2Icon} ativo={estado.titulo2} onClick={() => cmd().toggleHeading({ level: 2 }).run()} />
                    <BotaoFerramenta titulo="Subtítulo" icon={Heading3Icon} ativo={estado.titulo3} onClick={() => cmd().toggleHeading({ level: 3 }).run()} />
                    <BotaoFerramenta titulo="Lista com marcadores" icon={ListIcon} ativo={estado.listaMarcadores} onClick={() => cmd().toggleBulletList().run()} />
                    <BotaoFerramenta titulo="Lista numerada" icon={ListOrderedIcon} ativo={estado.listaNumerada} onClick={() => cmd().toggleOrderedList().run()} />
                    <BotaoFerramenta titulo="Citação" icon={QuoteIcon} ativo={estado.citacao} onClick={() => cmd().toggleBlockquote().run()} />
                    <Separador />
                    <BotaoFerramenta
                        titulo="Inserir tabela"
                        icon={TableIcon}
                        onClick={() => cmd().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()}
                    />
                    <BotaoFerramenta
                        titulo={enviandoImagem ? 'Enviando imagem…' : 'Inserir imagem'}
                        icon={enviandoImagem ? LoaderCircleIcon : ImagePlusIcon}
                        disabled={enviandoImagem}
                        onClick={() => inputImagemRef.current?.click()}
                    />
                    {estado.emTabela && (
                        <>
                            <Separador />
                            <BotaoFerramenta titulo="Adicionar linha abaixo" icon={BetweenHorizontalEndIcon} onClick={() => cmd().addRowAfter().run()} />
                            <BotaoFerramenta titulo="Adicionar coluna à direita" icon={BetweenVerticalEndIcon} onClick={() => cmd().addColumnAfter().run()} />
                            <BotaoFerramenta titulo="Excluir linha" icon={Rows2Icon} onClick={() => cmd().deleteRow().run()} />
                            <BotaoFerramenta titulo="Excluir coluna" icon={Columns2Icon} onClick={() => cmd().deleteColumn().run()} />
                            <BotaoFerramenta titulo="Linha de cabeçalho" icon={PanelTopIcon} onClick={() => cmd().toggleHeaderRow().run()} />
                            <BotaoFerramenta titulo="Excluir tabela" icon={Trash2Icon} onClick={() => cmd().deleteTable().run()} />
                        </>
                    )}
                    <Separador />
                    <BotaoFerramenta titulo="Desfazer" icon={Undo2Icon} disabled={!estado.podeDesfazer} onClick={() => cmd().undo().run()} />
                    <BotaoFerramenta titulo="Refazer" icon={Redo2Icon} disabled={!estado.podeRefazer} onClick={() => cmd().redo().run()} />

                    <input
                        ref={inputImagemRef}
                        type="file"
                        accept={TIPOS_IMAGEM.join(',')}
                        multiple
                        className="hidden"
                        onChange={(e) => {
                            const arquivos = Array.from(e.target.files ?? []);
                            e.target.value = '';
                            if (arquivos.length > 0) enviarImagens(arquivos);
                        }}
                    />
                </div>
            )}

            <EditorContent editor={editor} />

            {erroImagem && <p className="border-t px-3 py-2 text-sm text-destructive">{erroImagem}</p>}
        </div>
    );
}
