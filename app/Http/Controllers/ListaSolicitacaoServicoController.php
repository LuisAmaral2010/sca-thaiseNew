<?php

namespace App\Http\Controllers;

use App\Models\ListaSolicitacaoServico;
use Illuminate\Http\Request;

class ListaSolicitacaoServicoController extends Controller
{
    public function index()
    {
        // buscar todos — ajustar paginate() se preferir paginação
        $items = ListaSolicitacaoServico::orderBy('created_at', 'desc')->get();

        return view('listasolicitacaoservico.index', compact('items'));
    }
}