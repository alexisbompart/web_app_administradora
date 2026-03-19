<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle de N&oacute;mina</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $nomina->codigo }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($nomina->estatus === 'borrador')
                    <form action="{{ route('personal.nominas.procesar', $nomina) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de procesar esta n&oacute;mina?')">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-cogs mr-2"></i>Procesar
                        </button>
                    </form>
                @endif
                @if($nomina->estatus === 'procesada')
                    <form action="{{ route('personal.nominas.aprobar', $nomina) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de aprobar esta n&oacute;mina?')">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check-circle mr-2"></i>Aprobar
                        </button>
                    </form>
                @endif
                <a href="{{ route('personal.nominas.edit', $nomina) }}" class="btn-secondary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('personal.nominas.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Informacion de la Nomina --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-info-circle mr-2 text-burgundy-800"></i>Informaci&oacute;n de la N&oacute;mina
            </h3>
            <div>
                @switch($nomina->estatus)
                    @case('borrador')
                        <span class="badge-info">Borrador</span>
                        @break
                    @case('procesada')
                        <span class="badge-warning">Procesada</span>
                        @break
                    @case('pagada')
                        <span class="badge-success">Pagada</span>
                        @break
                    @case('anulada')
                        <span class="badge-danger">Anulada</span>
                        @break
                    @default
                        <span class="badge-secondary">{{ $nomina->estatus }}</span>
                @endswitch
            </div>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">C&oacute;digo</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $nomina->codigo }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Compa&ntilde;&iacute;a</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $nomina->compania?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Tipo</p>
                    <p class="text-sm font-medium text-navy-800 mt-1 capitalize">{{ $nomina->tipo }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Periodo</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $nomina->periodo_inicio?->format('d/m/Y') }} - {{ $nomina->periodo_fin?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Asignaciones</p>
                    <p class="text-sm font-medium text-green-700 mt-1">{{ number_format($nomina->total_asignaciones, 2, ',', '.') }} Bs</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Deducciones</p>
                    <p class="text-sm font-medium text-red-700 mt-1">{{ number_format($nomina->total_deducciones, 2, ',', '.') }} Bs</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Neto</p>
                    <p class="text-sm font-bold text-navy-800 mt-1">{{ number_format($nomina->total_neto, 2, ',', '.') }} Bs</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha Procesamiento</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $nomina->fecha_procesamiento?->format('d/m/Y H:i') ?? 'Pendiente' }}</p>
                </div>
            </div>
            @if($nomina->observaciones)
                <div class="mt-4 pt-4 border-t border-slate_custom-200">
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Observaciones</p>
                    <p class="text-sm text-navy-800 mt-1">{{ $nomina->observaciones }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Detalles de Nomina --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list-alt mr-2 text-burgundy-800"></i>Detalles por Trabajador
            </h3>
        </div>
        <div class="card-body p-0">
            @if($nomina->nominaDetalles && $nomina->nominaDetalles->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Trabajador</th>
                                <th>D&iacute;as Trab.</th>
                                <th>Salario Base</th>
                                <th>Hrs. Extras</th>
                                <th>Bono Alim.</th>
                                <th>Bono Transp.</th>
                                <th>Otros Ing.</th>
                                <th>Total Asign.</th>
                                <th>SSO</th>
                                <th>LPH</th>
                                <th>ISLR</th>
                                <th>Otros Desc.</th>
                                <th>Total Deduc.</th>
                                <th>Neto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalNeto = 0; $totalAsig = 0; $totalDeduc = 0; @endphp
                            @foreach($nomina->nominaDetalles as $detalle)
                                @php
                                    $totalNeto += $detalle->neto_pagar;
                                    $totalAsig += $detalle->total_asignaciones;
                                    $totalDeduc += $detalle->total_deducciones;
                                @endphp
                                <tr>
                                    <td class="font-medium text-navy-800 whitespace-nowrap">{{ $detalle->trabajador?->nombre_completo ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $detalle->dias_trabajados }}</td>
                                    <td class="text-right">{{ number_format($detalle->salario_base, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->horas_extras, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->bono_alimentacion, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->bono_transporte, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->otros_ingresos, 2, ',', '.') }}</td>
                                    <td class="text-right font-semibold text-green-700">{{ number_format($detalle->total_asignaciones, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->sso_empleado, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->lph_empleado, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->islr, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->otros_descuentos, 2, ',', '.') }}</td>
                                    <td class="text-right font-semibold text-red-700">{{ number_format($detalle->total_deducciones, 2, ',', '.') }}</td>
                                    <td class="text-right font-bold text-navy-800">{{ number_format($detalle->neto_pagar, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate_custom-50 font-bold">
                                <td colspan="7" class="text-right text-navy-800">TOTALES:</td>
                                <td class="text-right text-green-700">{{ number_format($totalAsig, 2, ',', '.') }}</td>
                                <td colspan="4"></td>
                                <td class="text-right text-red-700">{{ number_format($totalDeduc, 2, ',', '.') }}</td>
                                <td class="text-right text-navy-800">{{ number_format($totalNeto, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-slate_custom-400">No hay detalles registrados para esta n&oacute;mina.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
