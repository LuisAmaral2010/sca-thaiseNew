import { router } from '@inertiajs/react';
import { Trash2Icon } from 'lucide-react';
import { Button } from '@/components/ui/button';
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

// Botão de exclusão com confirmação; comTexto mostra "Excluir" ao lado do ícone
export default function ExcluirClienteButton({ cliente, comTexto = false }) {
    function excluir() {
        router.delete(route('clientes_externos.destroy', cliente.cliente_externo_id));
    }

    return (
        <AlertDialog>
            <AlertDialogTrigger
                render={<Button variant="destructive" size={comTexto ? 'sm' : 'icon-sm'} aria-label={`Excluir ${cliente.nome}`} />}
            >
                <Trash2Icon data-icon={comTexto ? 'inline-start' : undefined} />
                {comTexto && 'Excluir'}
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Excluir o cliente externo {cliente.nome}?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Esta ação não pode ser desfeita. O registro será removido permanentemente.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction variant="destructive" onClick={excluir}>
                        Excluir
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
