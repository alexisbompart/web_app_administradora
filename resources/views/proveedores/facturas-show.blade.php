<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Factura #{{ $factura->numero_factura }}</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Detalle de la factura de proveedor</p>
            </div>
            <div class="flex items-center gap-2">
                @if($factura->estatus === 'pendiente')
                    <form action="{{ route('proveedores.facturas.aprobar', $factura) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de aprobar esta factura?')">
                        @csrf
                        <button type="submit" class="btn-primary bg-green-700 hover:bg-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Aprobar
                        </button>
                    </form>
                @endif
                <a href="{{ route('proveedores.facturas.edit', $factura) }}" class="btn-secondary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('proveedores.facturas.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Factura Detail -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Datos de la Factura
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">N&uacute;mero de Factura</p>
                        <p class="text-sm font-semibold text-navy-800 mt-1">{{ $factura->numero_factura }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">N&uacute;mero de Control</p>
                        <p class="text-sm text-navy-800 mt-1">{{ $factura->numero_control ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Proveedor</p>
                        <p class="text-sm font-semibold text-navy-800 mt-1">{{ $factura->proveedor?->razon_social ?? 'N/A' }}</p>
                        <p class="text-xs text-slate_custom-400">{{ $factura->proveedor?->rif }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Compa&ntilde;&iacute;a</p>
                        <p class="text-sm text-navy-800 mt-1">{{ $factura->compania?->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha Factura</p>
                        <p class="text-sm text-navy-800 mt-1">{{ $factura->fecha_factura?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha Recepci&oacute;n</p>
                        <p class="text-sm text-navy-800 mt-1">{{ $factura->fecha_recepcion?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha Vencimiento</p>
                        <p class="text-sm text-navy-800 mt-1">{{ $factura->fecha_vencimiento?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Estatus</p>
                        <p class="mt-1">
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
                        </p>
                    </div>
                    @if($factura->observaciones)
                    <div class="md:col-span-2">
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Observaciones</p>
                        <p class="text-sm text-navy-800 mt-1">{{ $factura->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Montos Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-calculator mr-2 text-burgundy-800"></i>Montos
                </h3>
            </div>
            <div class="card-body space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate_custom-400">Subtotal</span>
                    <span class="text-sm font-medium text-navy-800">{{ number_format($factura->subtotal, 2, ',', '.') }} Bs</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate_custom-400">Base Imponible</span>
                    <span class="text-sm font-medium text-navy-800">{{ number_format($factura->base_imponible, 2, ',', '.') }} Bs</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate_custom-400">Monto Exento</span>
                    <span class="text-sm font-medium text-navy-800">{{ number_format($factura->monto_exento, 2, ',', '.') }} Bs</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate_custom-400">IVA ({{ $factura->iva_porcentaje }}%)</span>
                    <span class="text-sm font-medium text-navy-800">{{ number_format($factura->iva_monto, 2, ',', '.') }} Bs</span>
                </div>
                <div class="border-t border-slate_custom-100 pt-3 flex justify-between items-center">
                    <span class="text-sm font-bold text-navy-800">Total</span>
                    <span class="text-lg font-bold text-navy-800">{{ number_format($factura->total, 2, ',', '.') }} Bs</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Retenciones Table -->
    <div class="card mb-8">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-percentage mr-2 text-burgundy-800"></i>Retenciones
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Porcentaje</th>
                            <th>Base Imponible</th>
                            <th>Monto Retenido</th>
                            <th>Nro Comprobante</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($factura->retenciones as $retencion)
                        <tr>
                            <td>
                                @if($retencion->tipo === 'ISLR')
                                    <span class="badge-info">ISLR</span>
                                @else
                                    <span class="badge-warning">IVA</span>
                                @endif
                            </td>
                            <td>{{ number_format($retencion->porcentaje, 2) }}%</td>
                            <td class="text-right">{{ number_format($retencion->base_imponible, 2, ',', '.') }} Bs</td>
                            <td class="text-right font-semibold">{{ number_format($retencion->monto_retenido, 2, ',', '.') }} Bs</td>
                            <td>{{ $retencion->numero_comprobante ?? 'N/A' }}</td>
                            <td>{{ $retencion->fecha_retencion?->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay retenciones registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Registrar Retencion Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-plus-circle mr-2 text-burgundy-800"></i>Registrar Retenci&oacute;n
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('proveedores.facturas.retencion', $factura) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-navy-800 mb-1">Tipo <span class="text-red-500">*</span></label>
                        <select name="tipo" id="tipo" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">Seleccione</option>
                            <option value="ISLR" {{ old('tipo') == 'ISLR' ? 'selected' : '' }}>ISLR</option>
                            <option value="IVA" {{ old('tipo') == 'IVA' ? 'selected' : '' }}>IVA</option>
                        </select>
                        @error('tipo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="porcentaje" class="block text-sm font-medium text-navy-800 mb-1">Porcentaje <span class="text-red-500">*</span></label>
                        <input type="number" name="porcentaje" id="porcentaje" value="{{ old('porcentaje') }}" step="0.01" min="0" max="100" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('porcentaje')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ret_base_imponible" class="block text-sm font-medium text-navy-800 mb-1">Base Imponible <span class="text-red-500">*</span></label>
                        <input type="number" name="base_imponible" id="ret_base_imponible" value="{{ old('base_imponible') }}" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('base_imponible')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="monto_retenido" class="block text-sm font-medium text-navy-800 mb-1">Monto Retenido <span class="text-red-500">*</span></label>
                        <input type="number" name="monto_retenido" id="monto_retenido" value="{{ old('monto_retenido') }}" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('monto_retenido')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="numero_comprobante" class="block text-sm font-medium text-navy-800 mb-1">Nro Comprobante</label>
                        <input type="text" name="numero_comprobante" id="numero_comprobante" value="{{ old('numero_comprobante') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label for="fecha_retencion" class="block text-sm font-medium text-navy-800 mb-1">Fecha Retenci&oacute;n <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_retencion" id="fecha_retencion" value="{{ old('fecha_retencion') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('fecha_retencion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Registrar Retenci&oacute;n
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
