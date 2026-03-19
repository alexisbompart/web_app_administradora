<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Financiero\Banco;
use App\Models\Proveedor\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:proveedores.ver')->only(['index', 'show']);
        $this->middleware('permission:proveedores.crear')->only(['create', 'store']);
        $this->middleware('permission:proveedores.editar')->only(['edit', 'update']);
        $this->middleware('permission:proveedores.eliminar')->only(['destroy']);
    }

    public function index()
    {
        $proveedores = Proveedor::with('banco')->paginate(15);

        return view('proveedores.proveedores', compact('proveedores'));
    }

    public function create()
    {
        $bancos = Banco::all();

        return view('proveedores.proveedores-form', compact('bancos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rif'                => 'required|string|max:20|unique:proveedores,rif',
            'razon_social'       => 'required|string|max:255',
            'nombre_comercial'   => 'nullable|string|max:255',
            'direccion'          => 'nullable|string',
            'telefono'           => 'nullable|string|max:20',
            'celular'            => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'contacto'           => 'nullable|string|max:255',
            'tipo_contribuyente' => 'nullable|in:ordinario,especial,formal',
            'cuenta_bancaria'    => 'nullable|string|max:30',
            'banco_id'           => 'nullable|exists:bancos,id',
            'activo'             => 'boolean',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load('facturasProveedores', 'banco');

        return view('proveedores.proveedores-show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        $bancos = Banco::all();

        return view('proveedores.proveedores-form', compact('proveedor', 'bancos'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'rif'                => 'required|string|max:20|unique:proveedores,rif,' . $proveedor->id,
            'razon_social'       => 'required|string|max:255',
            'nombre_comercial'   => 'nullable|string|max:255',
            'direccion'          => 'nullable|string',
            'telefono'           => 'nullable|string|max:20',
            'celular'            => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'contacto'           => 'nullable|string|max:255',
            'tipo_contribuyente' => 'nullable|in:ordinario,especial,formal',
            'cuenta_bancaria'    => 'nullable|string|max:30',
            'banco_id'           => 'nullable|exists:bancos,id',
            'activo'             => 'boolean',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('proveedores.proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}
