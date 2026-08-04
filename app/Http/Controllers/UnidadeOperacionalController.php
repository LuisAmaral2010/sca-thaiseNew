<?php

namespace App\Http\Controllers;
use App\Models\UnidadeOperacional;
use Illuminate\Http\Request;

class UnidadeOperacionalController extends Controller
{
    // Listar as unidade_operacional
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $unidades_operacionais= UnidadeOperacional::OrderBy('unidade_operacional_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('unidades_operacionais.index', ['unidades_operacionais' => $unidades_operacionais]);
    }
}
