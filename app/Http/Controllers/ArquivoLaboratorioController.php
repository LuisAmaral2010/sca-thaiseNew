<?php

namespace App\Http\Controllers;
use App\Models\ArquivoLaboratorio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArquivoLaboratorioController extends Controller
{
    // Listar os dados da tabela arquivo_laboratorio
    public function index()
    {
        $arquivos_laboratorios = ArquivoLaboratorio::OrderBy('arquivo_laboratorio_id', 'DESC')->paginate(3);

        return Inertia::render('ArquivosLaboratorios/Index', ['arquivos_laboratorios' => $arquivos_laboratorios]);
    }
}
