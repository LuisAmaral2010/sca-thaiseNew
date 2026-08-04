<?php

namespace App\Http\Controllers;
use App\Models\Historico;
use Illuminate\Http\Request;

class HistoricoController extends Controller
{
    // Listar os dados da tabela historico
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $historicos= Historico::OrderBy('historico_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('historicos.index', ['historicos' => $historicos]);
    }
}
