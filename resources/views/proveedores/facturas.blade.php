<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Facturas de Proveedores</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gesti&oacute;n de facturas recibidas de proveedores</p>
            </div>
            <a href="{{ route('proveedores.facturas.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear Factura
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Listado de Facturas
            </h3>
        </div>
        <div class="card-body p-0">
            @if($facturas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Nro Factura</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facturas as $factura)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $factura->numero_factura }}</td>
                                    <td>{{ $factura->proveedor?->razon_social ?? 'N/A' }}</td>
                                    <td>{{ $factura->fecha_factura?->format('d/m/Y') }}</td>
                                    <td class="text-right">{{ number_format($factura->subtotal, 2, ',', '.') }} Bs</td>
                                    <td class="text-right">{{ number_format($factura->iva_monto, 2, ',', '.') }} Bs</td>
                                    <td class="text-right font-semibold">{{ number_format($factura->total, 2, ',', '.') }} Bs</td>
                                    <td>
                                        @switch($factura->estatus)
                                            @case('pendiente')
                                                <span class="badge-warning">Pendiente</span>
                                                @break
                                            @case('aprobada')
                                                <span class="badge-info">Aprobada</span>
                                                @break
                                            @case('pagada')
                                                <span class="badge-success">Pagada</span>
                                                @break
                                            @case('anulada')
                                                <span class="badge-danger">Anulada</span>
                                                @break
                                            @default
                                                <span class="badge-secondary">{{ $factura->estatus }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('proveedores.facturas.show', $factura) }}" class="btn-secondary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('proveedores.facturas.edit', $factura) }}" class="btn-secondary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($factura->estatus === 'pendiente')
                                                <form action="{{ route('proveedores.facturas.aprobar', $factura) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de aprobar esta factura?')">
                                                    @csrf
                                                    <button type="submit" class="btn-secondary btn-sm text-green-600 hover:text-green-800" title="Aprobar">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('proveedores.facturas.retencion', $factura) }}" class="btn-secondary btn-sm text-purple-600 hover:text-purple-800" title="Retenci&oacute;n" onclick="event.preventDefault(); document.getElementById('retencion-form-{{ $factura->id }}').submit();">
                                                <i class="fas fa-percentage"></i>
                                            </a>
                                            <form id="retencion-form-{{ $factura->id }}" action="{{ route('proveedores.facturas.retencion', $factura) }}" method="POST" class="hidden">
                                                @csrf
                                            </form>
                                            <form action="{{ route('proveedores.facturas.destroy', $factura) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de eliminar esta factura?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary btn-sm text-red-600 hover:text-red-800" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $facturas->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-invoice-dollar text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay facturas registradas</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comience registrando la primera factura de proveedor.</p>
                    <a href="{{ route('proveedores.facturas.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear Factura
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
