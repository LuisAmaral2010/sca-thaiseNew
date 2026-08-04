<?php

namespace App\Http\Controllers;
use App\Models\Empregado;
use Illuminate\Http\Request;

class EmpregadoController extends Controller
{
    // Listar os dados da tabela arquivo_laboratorio
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $empregados= Empregado::OrderBy('empregado_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('empregados.index', ['empregados' => $empregados]);
    }

    // Visualizar a empregado
    public function show(Empregado $empregado)
    {
        //dd($empregado);
        // Carregar a view
        return view('empregados.show', ['empregado' => $empregado]);
    }

        // Carregar o formulário cadastrar novo curso
    public function create()
    {
        // Carregar a view
        return view('empregados.create');
    }
}

