<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">{{ $fondo->nombre }}</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Detalle del fondo y movimientos</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('financiero.fondos.edit', $fondo) }}" class="btn-secondary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('financiero.fondos.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Fund Detail Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-piggy-bank mr-2 text-burgundy-800"></i>Informaci&oacute;n del Fondo
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Nombre</p>
                        <p class="text-sm font-semibold text-navy-800 mt-1">{{ $fondo->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Tipo</p>
                        <p class="text-sm font-semibold text-navy-800 mt-1 capitalize">{{ $fondo->tipo }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Compa&ntilde;&iacute;a</p>
                        <p class="text-sm font-semibold text-navy-800 mt-1">{{ $fondo->compania?->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Estado</p>
                        <p class="mt-1">
                            @if($fondo->activo)
                                <span class="badge-success">Activo</span>
                            @else
                                <span class="badge-danger">Inactivo</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Descripci&oacute;n</p>
                        <p class="text-sm text-navy-800 mt-1">{{ $fondo->descripcion ?? 'Sin descripci&oacute;n' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-chart-line mr-2 text-burgundy-800"></i>Saldos
                </h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Saldo Actual</p>
                    <p class="text-3xl font-bold text-navy-800 mt-1">{{ number_format($fondo->saldo_actual, 2, ',', '.') }} <span class="text-sm">Bs</span></p>
                </div>
                @if($fondo->meta > 0)
                    <div class="text-center">
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Meta</p>
                        <p class="text-lg font-semibold text-slate_custom-500 mt-1">{{ number_format($fondo->meta, 2, ',', '.') }} Bs</p>
                        @php
                            $porcentaje = $fondo->meta > 0 ? min(100, ($fondo->saldo_actual / $fondo->meta) * 100) : 0;
                        @endphp
                        <div class="w-full bg-slate_custom-100 rounded-full h-2 mt-2">
                            <div class="bg-burgundy-800 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <p class="text-xs text-slate_custom-400 mt-1">{{ number_format($porcentaje, 1) }}% completado</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Registrar Movimiento Form -->
    <div class="card mb-8">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-plus-circle mr-2 text-burgundy-800"></i>Registrar Movimiento
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('financiero.fondos.movimiento', $fondo) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="tipo_movimiento" class="block text-sm font-medium text-navy-800 mb-1">Tipo <span class="text-red-500">*</span></label>
                        <select name="tipo_movimiento" id="tipo_movimiento" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">Seleccione</option>
                            <option value="I" {{ old('tipo_movimiento') == 'I' ? 'selected' : '' }}>Ingreso</option>
                            <option value="E" {{ old('tipo_movimiento') == 'E' ? 'selected' : '' }}>Egreso</option>
                        </select>
                        @error('tipo_movimiento')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="monto" class="block text-sm font-medium text-navy-800 mb-1">Monto <span class="text-red-500">*</span></label>
                        <input type="number" name="monto" id="monto" value="{{ old('monto') }}" step="0.01" min="0.01" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('monto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="referencia" class="block text-sm font-medium text-navy-800 mb-1">Referencia</label>
                        <input type="text" name="referencia" id="referencia" value="{{ old('referencia') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label for="descripcion_mov" class="block text-sm font-medium text-navy-800 mb-1">Descripci&oacute;n <span class="text-red-500">*</span></label>
                        <input type="text" name="descripcion" id="descripcion_mov" value="{{ old('descripcion') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Registrar Movimiento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Movimientos Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-exchange-alt mr-2 text-burgundy-800"></i>&Uacute;ltimos Movimientos
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Saldo Anterior</th>
                            <th>Saldo Posterior</th>
                            <th>Descripci&oacute;n</th>
                            <th>Referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fondo->movimientosFondo->sortByDesc('created_at')->take(20) as $movimiento)
                        <tr>
                            <td>{{ $movimiento->fecha_movimiento?->format('d/m/Y') ?? $movimiento->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($movimiento->tipo_movimiento === 'I')
                                    <span class="badge-success">Ingreso</span>
                                @else
                                    <span class="badge-danger">Egreso</span>
                                @endif
                            </td>
                            <td class="font-semibold">{{ number_format($movimiento->monto, 2, ',', '.') }} Bs</td>
                            <td>{{ number_format($movimiento->saldo_anterior, 2, ',', '.') }} Bs</td>
                            <td>{{ number_format($movimiento->saldo_posterior, 2, ',', '.') }} Bs</td>
                            <td>{{ Str::limit($movimiento->descripcion, 50) }}</td>
                            <td>{{ $movimiento->referencia }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay movimientos registrados para este fondo
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
