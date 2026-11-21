// Botões de aprovação e rejeição do laudo pelo RespTec, usados na lista
// (/resptec/aprovar-laudo) e na visualização do laudo
// (/resptec/aprovar-laudo/{laudo}/visualizar)
import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { CheckIcon, XIcon } from 'lucide-react';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

// Botão de aprovação com confirmação antes de gravar
export function BotaoAprovar({
    ordem,
    aprovandoId,
    onAprovar,
    desabilitado = false,
    rotulo = 'Aprovar',
    complemento = 'e sairá desta lista',
    className = '',
}) {
    const aprovando = aprovandoId === ordem.laudo_id;

    if (!ordem.laudo_id) {
        return (
            <Button size="sm" className={className} disabled>
                <CheckIcon />
                {rotulo}
            </Button>
        );
    }

    return (
        <AlertDialog>
            <AlertDialogTrigger
                render={<Button size="sm" className={className} disabled={desabilitado || aprovandoId !== null} />}
            >
                <CheckIcon />
                {aprovando ? 'Aprovando...' : rotulo}
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Aprovar o laudo {ordem.numero_laudo}?</AlertDialogTitle>
                    <AlertDialogDescription>
                        O laudo da Ordem de Serviço #{ordem.ordem_servico_id} será marcado como aprovado pelo
                        RespTec {complemento}.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction onClick={() => onAprovar(ordem)}>Aprovar</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}

// Botão de rejeição: abre uma caixa que exige o motivo antes de gravar.
// dadosExtras vai junto no POST (ex.: { voltar_para_lista: 1 }).
export function BotaoRejeitar({
    ordem,
    desabilitado = false,
    rotulo = 'Rejeitar',
    complemento = 'e sairá desta lista',
    dadosExtras = {},
    className = '',
}) {
    const [aberto, setAberto] = useState(false);
    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({ motivo: '', ...dadosExtras });

    if (!ordem.laudo_id) {
        return (
            <Button size="sm" variant="destructive" className={className} disabled>
                <XIcon />
                {rotulo}
            </Button>
        );
    }

    function alterarAberto(valor) {
        setAberto(valor);
        if (!valor) {
            reset();
            clearErrors();
        }
    }

    function submit(e) {
        e.preventDefault();
        post(route('resptec.aprovar-laudo.rejeitar', ordem.laudo_id), {
            preserveScroll: true,
            onSuccess: () => alterarAberto(false),
        });
    }

    return (
        <Dialog open={aberto} onOpenChange={alterarAberto}>
            <DialogTrigger
                render={
                    <Button size="sm" variant="destructive" className={className} disabled={desabilitado || processing} />
                }
            >
                <XIcon />
                {processing ? 'Rejeitando...' : rotulo}
            </DialogTrigger>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>Rejeitar o laudo {ordem.numero_laudo}?</DialogTitle>
                        <DialogDescription>
                            O laudo da Ordem de Serviço #{ordem.ordem_servico_id} será marcado como rejeitado pelo
                            RespTec {complemento}. Informe o motivo da rejeição.
                        </DialogDescription>
                    </DialogHeader>
                    <Field className="mt-4" data-invalid={!!errors.motivo}>
                        <FieldLabel htmlFor={`motivo-${ordem.laudo_id}`}>Motivo da Rejeição</FieldLabel>
                        <Input
                            id={`motivo-${ordem.laudo_id}`}
                            value={data.motivo}
                            maxLength={255}
                            required
                            aria-invalid={!!errors.motivo}
                            onChange={(e) => setData('motivo', e.target.value)}
                            placeholder="Descreva o motivo da rejeição"
                        />
                        {errors.motivo && <FieldError>{errors.motivo}</FieldError>}
                    </Field>
                    <DialogFooter className="mt-4">
                        <DialogClose render={<Button type="button" variant="outline" />}>Cancelar</DialogClose>
                        <Button type="submit" variant="destructive" disabled={processing}>
                            Confirmar Rejeição
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
