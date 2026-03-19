<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">{{ $proveedor->razon_social }}</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Detalle del proveedor</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proveedores.proveedores.edit', $proveedor) }}" class="btn-secondary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('proveedores.proveedores.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Proveedor Detail Card -->
    <div class="card mb-8">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-truck mr-2 text-burgundy-800"></i>Informaci&oacute;n del Proveedor
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">RIF</p>
                    <p class="text-sm font-semibold text-navy-800 mt-1">{{ $proveedor->rif }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Raz&oacute;n Social</p>
                    <p class="text-sm font-semibold text-navy-800 mt-1">{{ $proveedor->razon_social }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Nombre Comercial</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->nombre_comercial ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Tel&eacute;fono</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->telefono ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Celular</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->celular ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Email</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Contacto</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->contacto ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Tipo Contribuyente</p>
                    <p class="text-sm text-navy-800 mt-1 capitalize">{{ $proveedor->tipo_contribuyente ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Estado</p>
                    <p class="mt-1">
                        @if($proveedor->activo)
                            <span class="badge-success">Activo</span>
                        @else
                            <span class="badge-danger">Inactivo</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Banco</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->banco?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Cuenta Bancaria</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->cuenta_bancaria ?? 'N/A' }}</p>
                </div>
                <div class="md:col-span-3">
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Direcci&oacute;n</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $proveedor->direccion ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Facturas Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>&Uacute;ltimas Facturas
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Nro Factura</th>
                            <th>Fecha</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedor->facturasProveedores->sortByDesc('created_at')->take(10) as $factura)
                        <tr>
                            <td class="font-medium text-navy-800">{{ $factura->numero_factura }}</td>
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
                                <a href="{{ route('proveedores.facturas.show', $factura) }}" class="btn-secondary btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay facturas registradas para este proveedor
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
