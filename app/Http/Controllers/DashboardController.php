<?php

namespace App\Http\Controllers;

use App\Models\UnidadeOperacional;
use Inertia\Inertia;

class DashboardController extends Controller
{
    // Página inicial do administrativo
    public function index()
    {
        // Um card por unidade operacional ativa (ícone e nome)
        return Inertia::render('Dashboard', [
            'unidadesOperacionais' => UnidadeOperacional::where('is_ativo', true)
                ->orderBy('nome')
                ->get(['unidade_operacional_id', 'nome']),
        ]);
    }
}
