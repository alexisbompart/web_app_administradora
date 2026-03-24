<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondDescuentoApto;
use App\Models\Financiero\CondAbonoApto;
use App\Models\Financiero\CondGasto;
use App\Models\Financiero\CondPago;
use App\Models\Financiero\CondPagoApto;
use App\Models\Financiero\CondMovPrefact;
use App\Models\Financiero\CondMovFactApto;
use App\Models\Financiero\CondMovFactEdif;

class DataViewController extends Controller
{
    public function deudas()
    {
        $items = CondDeudaApto::with(['edificio', 'apartamento'])->latest()->paginate(20);
        $totalCount = CondDeudaApto::count();
        $ultimaCarga = CondDeudaApto::max('updated_at');
        return view('financiero.deudas', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function descuentos()
    {
        $items = CondDescuentoApto::with(['edificio', 'apartamento'])->latest()->paginate(20);
        $totalCount = CondDescuentoApto::count();
        $ultimaCarga = CondDescuentoApto::max('updated_at');
        return view('financiero.descuentos', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function abonos()
    {
        $items = CondAbonoApto::with(['edificio', 'apartamento'])->latest()->paginate(20);
        $totalCount = CondAbonoApto::count();
        $ultimaCarga = CondAbonoApto::max('updated_at');
        return view('financiero.abonos', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function gastos()
    {
        $items = CondGasto::latest()->paginate(20);
        $totalCount = CondGasto::count();
        $ultimaCarga = CondGasto::max('updated_at');
        return view('financiero.gastos', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function pagos()
    {
        $items = CondPago::latest()->paginate(20);
        $totalCount = CondPago::count();
        $ultimaCarga = CondPago::max('updated_at');
        return view('financiero.pagos', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function pagosApto()
    {
        $items = CondPagoApto::with(['edificio', 'apartamento'])->latest()->paginate(20);
        $totalCount = CondPagoApto::count();
        $ultimaCarga = CondPagoApto::max('updated_at');
        return view('financiero.pagos-apto', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function movPrefact()
    {
        $items = CondMovPrefact::with(['edificio', 'apartamento'])->latest()->paginate(20);
        $totalCount = CondMovPrefact::count();
        $ultimaCarga = CondMovPrefact::max('updated_at');
        return view('financiero.mov-prefact', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function factApto()
    {
        $items = CondMovFactApto::with(['edificio', 'apartamento'])->latest()->paginate(20);
        $totalCount = CondMovFactApto::count();
        $ultimaCarga = CondMovFactApto::max('updated_at');
        return view('financiero.fact-apto', compact('items', 'totalCount', 'ultimaCarga'));
    }

    public function factEdif()
    {
        $items = CondMovFactEdif::with(['edificio'])->latest()->paginate(20);
        $totalCount = CondMovFactEdif::count();
        $ultimaCarga = CondMovFactEdif::max('updated_at');
        return view('financiero.fact-edif', compact('items', 'totalCount', 'ultimaCarga'));
    }
}
