<?php

namespace App\Http\Controllers;

use App\Models\FracaoAmostra;
use Illuminate\Http\Request;

class FracaoAmostraController extends Controller
{
    // Listar os dados da tabela fracao_amostra
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $fracoes_amostras= FracaoAmostra::OrderBy('fracao_amostra_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('fracoes_amostras.index', ['fracoes_amostras' => $fracoes_amostras]);
    }
}
