<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    // Página inicial do site
    public function index()
    {
        return Inertia::render('Welcome');
    }
}