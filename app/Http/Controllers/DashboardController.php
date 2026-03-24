<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect clients to their portal
        if ($user->hasRole('cliente-propietario')) {
            return redirect()->route('mi-condominio.dashboard');
        }

        // Redirect providers to their invoices
        if ($user->hasRole('proveedor')) {
            return redirect()->route('proveedores.facturas.index');
        }

        return view('dashboard');
    }
}
