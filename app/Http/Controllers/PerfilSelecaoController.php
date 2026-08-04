<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PerfilSelecaoController extends Controller
{
    // Tela de seleção de perfil exibida logo após o login
    public function index()
    {
        return Inertia::render('SelecionarPerfil');
    }
}
