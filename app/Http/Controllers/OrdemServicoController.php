<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use Inertia\Inertia;

class OrdemServicoController extends Controller
{
    // Listar os dados da tabela ordem_servico
    public function index()
    {
        $ordensServicos = OrdemServico::orderBy('ordem_servico_id', 'DESC')->paginate(10)->withQueryString();

        return Inertia::render('OrdensServicos/Index', ['ordensServicos' => $ordensServicos]);
    }
}
