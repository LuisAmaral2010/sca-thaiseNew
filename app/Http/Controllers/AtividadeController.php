<?php

namespace App\Http\Controllers;
use App\Models\Atividade;
use Illuminate\Http\Request;

class AtividadeController extends Controller
{
    // Listar os dados da tabela arquivo_laboratorio
    public function index()
    {
        //dd('Aqui');
        // Recuperar os registros do Banco de Dados
        $atividades= Atividade::OrderBy('atividade_id', 'DESC')->paginate(3);
        // Carregar a view
        return view('atividades.index', ['atividades' => $atividades]);
    }

    // Visualizar a atividade
    public function show(Atividade $atividade)
    {
        dd($atividade);
        // Carregar a view
        return view('atividades.show', ['atividade' => $atividade]);
    }

        // Carregar o formulário cadastrar novo curso
    public function create()
    {
        // Carregar a view
        return view('atividades.create');
    }
}
