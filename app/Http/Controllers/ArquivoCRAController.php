<?php

namespace App\Http\Controllers;
use Exception;
use App\Models\ArquivoCRA;
use Illuminate\Http\Request;

class ArquivoCRAController extends Controller
{
    // Listar os dados da tabela arquivo_cra
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $arquivos_cra= ArquivoCRA::OrderBy('arquivo_cra_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('arquivos_cra.index', ['arquivos_cra' => $arquivos_cra]);
    }
}
