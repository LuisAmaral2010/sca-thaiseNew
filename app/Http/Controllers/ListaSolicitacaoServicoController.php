<?php

namespace App\Http\Controllers;

use App\Models\ListaSolicitacaoServico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListaSolicitacaoServicoController extends Controller
{
    public function index()
    {
        $items = ListaSolicitacaoServico::orderBy('created_at', 'desc')->get();

        return Inertia::render('ListaSolicitacaoServico/Index', compact('items'));
    }
}