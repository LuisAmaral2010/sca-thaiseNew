<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Página inicial do site
    public function index()
    {
        // Carregar a VIEW
        return view('home.index');
    }
}