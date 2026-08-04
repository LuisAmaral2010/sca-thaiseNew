<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratorio;
use App\Models\Servico;

class DropdownController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        $data['laboratorios'] = Laboratorio::get(["nome", "id"]);
        return view('dropdown', $data);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function fetchServico(Request $request)
    {
        $data['servicos'] = Servico::where("laboratorio_id", $request->laboratorio_id)
                                ->get(["nome", "id"]);
  
        return response()->json($data);
    }
    
}
