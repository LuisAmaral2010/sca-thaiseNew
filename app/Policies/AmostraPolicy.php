<?php

namespace App\Policies;

use App\Models\Amostra;
use App\Models\PermissaoAtividade;
use App\Models\User;

class AmostraPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Amostra $amostra): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * Without a target solicitação/atividade yet (e.g. rendering the "Nova Amostra"
     * button or the create form), this only checks that the user holds permissão
     * for at least one atividade. The precise per-solicitação check happens in
     * AmostraController::store() via authorize('create', [Amostra::class, $solicitacaoId]).
     */
    public function create(User $user, ?int $solicitacaoAtividadeId = null): bool
    {
        if ($solicitacaoAtividadeId !== null) {
            return $this->userHasActivityPermission($user, $solicitacaoAtividadeId);
        }

        return PermissaoAtividade::where('usuario_matricula', $user->empregado?->matricula)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Amostra $amostra): bool
    {
        return $this->userHasActivityPermission($user, $amostra->solicitacaoServico?->atividade_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Amostra $amostra): bool
    {
        return $this->userHasActivityPermission($user, $amostra->solicitacaoServico?->atividade_id);
    }

    /**
     * Whether the user holds a permissão_atividade grant for the given atividade.
     *
     * NOTE: only checks that a grant row exists for (usuario_matricula, atividade_id).
     * `permissao_resultado` / `permissao_todos_resultados` are not factored in yet —
     * their exact business meaning needs confirmation before layering finer-grained
     * checks on top of this.
     */
    private function userHasActivityPermission(User $user, ?int $atividadeId): bool
    {
        $matricula = $user->empregado?->matricula;

        if (! $matricula || ! $atividadeId) {
            return false;
        }

        return PermissaoAtividade::where('usuario_matricula', $matricula)
            ->where('atividade_id', $atividadeId)
            ->exists();
    }
}
