<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Compania;
use App\Models\Proveedor\FacturaProveedor;
use App\Models\Proveedor\Proveedor;
use App\Models\Proveedor\Retencion;
use Illuminate\Http\Request;

class FacturaProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:proveedores.ver')->only(['index', 'show']);
        $this->middleware('permission:proveedores.crear')->only(['create', 'store']);
        $this->middleware('permission:proveedores.editar')->only(['edit', 'update']);
        $this->middleware('permission:proveedores.eliminar')->only(['destroy']);
        $this->middleware('permission:proveedores.aprobar-factura')->only(['aprobar']);
    }

    public function index()
    {
        $facturas = FacturaProveedor::with('proveedor')->paginate(15);

        return view('proveedores.facturas', compact('facturas'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $companias = Compania::where('activo', true)->get();

        return view('proveedores.facturas-form', compact('proveedores', 'companias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id'     => 'required|exists:proveedores,id',
            'compania_id'      => 'required|exists:companias,id',
            'numero_factura'   => 'required|string|max:50',
            'numero_control'   => 'nullable|string|max:50',
            'fecha_factura'    => 'required|date',
            'fecha_recepcion'  => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'subtotal'         => 'required|numeric|min:0',
            'base_imponible'   => 'required|numeric|min:0',
            'monto_exento'     => 'nullable|numeric|min:0',
            'iva_porcentaje'   => 'nullable|numeric|min:0|max:100',
            'iva_monto'        => 'nullable|numeric|min:0',
            'total'            => 'nullable|numeric|min:0',
            'observaciones'    => 'nullable|string',
        ]);

        $validated['estatus'] = 'pendiente';
        $validated['registrado_por'] = auth()->id();

        FacturaProveedor::create($validated);

        return redirect()->route('proveedores.facturas.index')
            ->with('success', 'Factura creada exitosamente.');
    }

    public function show(FacturaProveedor $factura)
    {
        $factura->load('proveedor', 'retenciones', 'compania');

        return view('proveedores.facturas-show', compact('factura'));
    }

    public function edit(FacturaProveedor $factura)
    {
        $proveedores = Proveedor::where('activo', true)->get();
        $companias = Compania::where('activo', true)->get();

        return view('proveedores.facturas-form', compact('factura', 'proveedores', 'companias'));
    }

    public function update(Request $request, FacturaProveedor $factura)
    {
        $validated = $request->validate([
            'proveedor_id'     => 'required|exists:proveedores,id',
            'compania_id'      => 'required|exists:companias,id',
            'numero_factura'   => 'required|string|max:50',
            'numero_control'   => 'nullable|string|max:50',
            'fecha_factura'    => 'required|date',
            'fecha_recepcion'  => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'subtotal'         => 'required|numeric|min:0',
            'base_imponible'   => 'required|numeric|min:0',
            'monto_exento'     => 'nullable|numeric|min:0',
            'iva_porcentaje'   => 'nullable|numeric|min:0|max:100',
            'iva_monto'        => 'nullable|numeric|min:0',
            'total'            => 'nullable|numeric|min:0',
            'observaciones'    => 'nullable|string',
        ]);

        $factura->update($validated);

        return redirect()->route('proveedores.facturas.index')
            ->with('success', 'Factura actualizada exitosamente.');
    }

    public function destroy(FacturaProveedor $factura)
    {
        $factura->delete();

        return redirect()->route('proveedores.facturas.index')
            ->with('success', 'Factura eliminada exitosamente.');
    }

    public function aprobar(FacturaProveedor $factura)
    {
        $factura->update(['estatus' => 'aprobada']);

        return redirect()->route('proveedores.facturas.show', $factura)
            ->with('success', 'Factura aprobada exitosamente.');
    }

    public function registrarRetencion(Request $request, FacturaProveedor $factura)
    {
        $validated = $request->validate([
            'tipo'              => 'required|in:ISLR,IVA',
            'porcentaje'        => 'required|numeric|min:0|max:100',
            'base_imponible'    => 'required|numeric|min:0',
            'monto_retenido'    => 'required|numeric|min:0',
            'numero_comprobante' => 'nullable|string|max:50',
            'fecha_retencion'   => 'required|date',
        ]);

        $validated['factura_proveedor_id'] = $factura->id;

        Retencion::create($validated);

        return redirect()->route('proveedores.facturas.show', $factura)
            ->with('success', 'Retención registrada exitosamente.');
    }
}
