<?php

namespace App\Http\Controllers;
use App\Models\Historico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoricoController extends Controller
{
    // Listar os dados da tabela historico
    public function index()
    {
        $historicos = Historico::OrderBy('historico_id', 'DESC')->paginate(3);

        return Inertia::render('Historicos/Index', ['historicos' => $historicos]);
    }
}
