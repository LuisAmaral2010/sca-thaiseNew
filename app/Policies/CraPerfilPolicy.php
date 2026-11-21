<?php

namespace App\Policies;

use App\Models\CraPerfil;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// Só usuários USUARIO_CRA (Gate acessar-cra) consultam, cadastram (uma única
// vez — só existe um perfil) e editam o perfil do CRA. Não há exclusão.
class CraPerfilPolicy
{
    public function view(User $user, CraPerfil $craPerfil): bool
    {
        return $this->isUsuarioCra($user);
    }

    public function create(User $user): bool
    {
        return !CraPerfil::query()->exists() && $this->isUsuarioCra($user);
    }

    public function update(User $user, CraPerfil $craPerfil): bool
    {
        return $this->isUsuarioCra($user);
    }

    private function isUsuarioCra(User $user): bool
    {
        return Gate::forUser($user)->allows('acessar-cra');
    }
}
