<?php

namespace App\Http\Controllers;

use App\Models\Laudo;
use Inertia\Inertia;

class LaudoController extends Controller
{
    // Listar os dados da tabela laudo
    public function index()
    {
        $laudos = Laudo::orderBy('laudo_id', 'DESC')->paginate(10)->withQueryString();

        return Inertia::render('Laudos/Index', ['laudos' => $laudos]);
    }
}
