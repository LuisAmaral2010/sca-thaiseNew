<?php

namespace App\Http\Controllers;
use App\Models\ArquivoLaboratorio;
use Illuminate\Http\Request;

class ArquivoLaboratorioController extends Controller
{
    // Listar os dados da tabela arquivo_laboratorio
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $arquivos_laboratorios= ArquivoLaboratorio::OrderBy('arquivo_laboratorio_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('arquivos_laboratorios.index', ['arquivos_laboratorios' => $arquivos_laboratorios]);
    }
}
