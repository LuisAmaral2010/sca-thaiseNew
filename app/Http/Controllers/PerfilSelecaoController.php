<?php

namespace App\Http\Controllers;

use App\Models\PerfilAcesso;
use App\Models\UnidadeOperacional;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PerfilSelecaoController extends Controller
{
    // Tela de seleção de perfil exibida logo após o login
    public function index()
    {
        $matricula = Auth::user()->empregado?->matricula;

        $perfis = [
            'resptec' => $matricula !== null
                && UnidadeOperacional::where('responsavel_matricula', $matricula)->exists(),
            'cra' => Auth::user()->can('acessar-cra'),
            'administrador' => $matricula !== null
                && PerfilAcesso::where('tipo_perfil', 'ADMINISTRADOR')->where('usuario_matricula', $matricula)->exists(),
            'laboratorio' => Auth::user()->can('acessar-laboratorio'),
        ];

        return Inertia::render('SelecionarPerfil', ['perfis' => $perfis]);
    }
}
