<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    // Página inicial do administrativo
    public function index()
    {
        return Inertia::render('Dashboard');
    }
}
