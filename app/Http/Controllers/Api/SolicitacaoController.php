<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $solicitacoes = DB::table('solicitacao_analise')->get();
        $solicitacoes = Solicitacao::all();
 
        return view('solicitacao.index', ['solicitacoes' => $solicitacoes]);
    }

    /**
     * Show the form for creating a new resource.
     * No for API
     */
    public function create()
    {
        return view('solicitacao.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // if (Solicitacao::insert('INSERT INTO series (nome) VALUES (?)', [$nomeSerie])) {
        //     return redirect('/series');
        // } else {
        //     return "Deu erro";
        // }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * No for API
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
