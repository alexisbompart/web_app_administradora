<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($factura) ? 'Editar Factura' : 'Nueva Factura de Proveedor' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($factura) ? 'Modificar datos de la factura' : 'Registrar una nueva factura de proveedor' }}
                </p>
            </div>
            <a href="{{ route('proveedores.facturas.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card max-w-4xl mx-auto" x-data="{
        base_imponible: {{ old('base_imponible', $factura->base_imponible ?? 0) }},
        iva_porcentaje: {{ old('iva_porcentaje', $factura->iva_porcentaje ?? 16) }},
        subtotal: {{ old('subtotal', $factura->subtotal ?? 0) }},
        get iva_monto() { return (this.base_imponible * this.iva_porcentaje / 100).toFixed(2); },
        get total() { return (parseFloat(this.subtotal) + parseFloat(this.iva_monto)).toFixed(2); }
    }">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Datos de la Factura
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($factura) ? route('proveedores.facturas.update', $factura) : route('proveedores.facturas.store') }}" method="POST">
                @csrf
                @if(isset($factura))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Proveedor -->
                    <div>
                        <label for="proveedor_id" class="block text-sm font-medium text-navy-800 mb-1">Proveedor <span class="text-red-500">*</span></label>
                        <select name="proveedor_id" id="proveedor_id" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">Seleccione un proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $factura->proveedor_id ?? '') == $proveedor->id ? 'selected' : '' }}>
                                    {{ $proveedor->rif }} - {{ $proveedor->razon_social }}
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Compania -->
                    <div>
                        <label for="compania_id" class="block text-sm font-medium text-navy-800 mb-1">Compa&ntilde;&iacute;a <span class="text-red-500">*</span></label>
                        <select name="compania_id" id="compania_id" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">Seleccione una compa&ntilde;&iacute;a</option>
                            @foreach($companias as $compania)
                                <option value="{{ $compania->id }}" {{ old('compania_id', $factura->compania_id ?? '') == $compania->id ? 'selected' : '' }}>
                                    {{ $compania->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('compania_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Numero Factura -->
                    <div>
                        <label for="numero_factura" class="block text-sm font-medium text-navy-800 mb-1">N&uacute;mero de Factura <span class="text-red-500">*</span></label>
                        <input type="text" name="numero_factura" id="numero_factura" value="{{ old('numero_factura', $factura->numero_factura ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('numero_factura')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Numero Control -->
                    <div>
                        <label for="numero_control" class="block text-sm font-medium text-navy-800 mb-1">N&uacute;mero de Control</label>
                        <input type="text" name="numero_control" id="numero_control" value="{{ old('numero_control', $factura->numero_control ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('numero_control')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha Factura -->
                    <div>
                        <label for="fecha_factura" class="block text-sm font-medium text-navy-800 mb-1">Fecha de Factura <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_factura" id="fecha_factura" value="{{ old('fecha_factura', isset($factura) && $factura->fecha_factura ? $factura->fecha_factura->format('Y-m-d') : '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('fecha_factura')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha Recepcion -->
                    <div>
                        <label for="fecha_recepcion" class="block text-sm font-medium text-navy-800 mb-1">Fecha de Recepci&oacute;n</label>
                        <input type="date" name="fecha_recepcion" id="fecha_recepcion" value="{{ old('fecha_recepcion', isset($factura) && $factura->fecha_recepcion ? $factura->fecha_recepcion->format('Y-m-d') : '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('fecha_recepcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha Vencimiento -->
                    <div>
                        <label for="fecha_vencimiento" class="block text-sm font-medium text-navy-800 mb-1">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento', isset($factura) && $factura->fecha_vencimiento ? $factura->fecha_vencimiento->format('Y-m-d') : '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('fecha_vencimiento')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Montos Section -->
                <div class="mt-8 pt-6 border-t border-slate_custom-100">
                    <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4">
                        <i class="fas fa-calculator mr-2 text-burgundy-800"></i>Montos
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Subtotal -->
                        <div>
                            <label for="subtotal" class="block text-sm font-medium text-navy-800 mb-1">Subtotal <span class="text-red-500">*</span></label>
                            <input type="number" name="subtotal" id="subtotal" x-model="subtotal" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            @error('subtotal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Base Imponible -->
                        <div>
                            <label for="base_imponible" class="block text-sm font-medium text-navy-800 mb-1">Base Imponible <span class="text-red-500">*</span></label>
                            <input type="number" name="base_imponible" id="base_imponible" x-model="base_imponible" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            @error('base_imponible')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Monto Exento -->
                        <div>
                            <label for="monto_exento" class="block text-sm font-medium text-navy-800 mb-1">Monto Exento</label>
                            <input type="number" name="monto_exento" id="monto_exento" value="{{ old('monto_exento', $factura->monto_exento ?? '0.00') }}" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                            @error('monto_exento')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- IVA Porcentaje -->
                        <div>
                            <label for="iva_porcentaje" class="block text-sm font-medium text-navy-800 mb-1">IVA %</label>
                            <input type="number" name="iva_porcentaje" id="iva_porcentaje" x-model="iva_porcentaje" step="0.01" min="0" max="100" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                            @error('iva_porcentaje')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- IVA Monto (auto-calculated) -->
                        <div>
                            <label for="iva_monto" class="block text-sm font-medium text-navy-800 mb-1">IVA Monto</label>
                            <input type="number" name="iva_monto" id="iva_monto" x-bind:value="iva_monto" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 bg-slate_custom-50 focus:border-burgundy-800 focus:ring-burgundy-800" readonly>
                            @error('iva_monto')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total (auto-calculated) -->
                        <div>
                            <label for="total" class="block text-sm font-medium text-navy-800 mb-1">Total</label>
                            <input type="number" name="total" id="total" x-bind:value="total" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 bg-slate_custom-50 font-bold text-navy-800 focus:border-burgundy-800 focus:ring-burgundy-800" readonly>
                            @error('total')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="mt-6">
                    <label for="observaciones" class="block text-sm font-medium text-navy-800 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3" class="form-textarea w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">{{ old('observaciones', $factura->observaciones ?? '') }}</textarea>
                    @error('observaciones')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate_custom-100">
                    <a href="{{ route('proveedores.facturas.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($factura) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
