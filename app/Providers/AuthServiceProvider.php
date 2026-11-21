<?php

namespace App\Providers;

use App\Models\Amostra;
use App\Models\CraPerfil;
use App\Models\PerfilAcesso;
use App\Models\PermissaoUnidadeOperacional;
use App\Models\User;
use App\Policies\AmostraPolicy;
use App\Policies\CraPerfilPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Amostra::class => AmostraPolicy::class,
        CraPerfil::class => CraPerfilPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // CRA: só quem tem perfil USUARIO_CRA em perfil_acesso
        Gate::define('acessar-cra', function (User $user): bool {
            $matricula = $user->empregado?->matricula;

            return $matricula !== null
                && PerfilAcesso::where('tipo_perfil', 'USUARIO_CRA')
                    ->where('usuario_matricula', $matricula)
                    ->exists();
        });

        // Laboratório: só quem tem a matrícula em permissao_unidade_operacional.usuario_matricula
        Gate::define('acessar-laboratorio', function (User $user): bool {
            $matricula = $user->empregado?->matricula;

            return $matricula !== null
                && PermissaoUnidadeOperacional::where('usuario_matricula', $matricula)->exists();
        });
    }
}
