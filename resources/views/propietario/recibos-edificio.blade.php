<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Recibos del Edificio</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Facturacion general de los edificios donde resido</p>
            </div>
        </div>
    </x-slot>

    {{-- Edificios del propietario --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($apartamentos as $apto)
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-building text-burgundy-800"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-navy-800">{{ $apto->edificio->nombre }}</p>
                    <p class="text-xs text-slate_custom-400">Apto {{ $apto->num_apto }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-building mr-2 text-burgundy-800"></i>Recibos de Condominio - Edificio
            </h3>
        </div>
        <div class="card-body p-0">
            @if($recibos->count())
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Edificio</th>
                            <th>Fecha Facturacion</th>
                            <th>Facturacion</th>
                            <th>Cobranza</th>
                            <th>Deuda Actual</th>
                            <th>Deuda Anterior</th>
                            <th>Fdo Reserva</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recibos as $recibo)
                        <tr>
                            <td class="font-medium text-navy-800">{{ $recibo->edificio?->nombre ?? $recibo->cod_edif_legacy }}</td>
                            <td class="text-xs">{{ $recibo->fecha_fact?->format('d/m/Y') }}</td>
                            <td class="text-xs font-semibold">{{ number_format($recibo->facturacion_edif ?? 0, 2, ',', '.') }}</td>
                            <td class="text-xs">{{ number_format($recibo->cobranza_edif ?? 0, 2, ',', '.') }}</td>
                            <td class="text-xs font-semibold text-red-600">{{ number_format($recibo->deuda_act_edif ?? 0, 2, ',', '.') }}</td>
                            <td class="text-xs">{{ number_format($recibo->deuda_ant_edif ?? 0, 2, ',', '.') }}</td>
                            <td class="text-xs">{{ number_format($recibo->sdo_act_fdo_res ?? 0, 2, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('mi-condominio.ver-recibo-edificio', $recibo->id) }}" class="text-burgundy-800 hover:text-navy-800 text-xs font-semibold">
                                    <i class="fas fa-file-pdf mr-1"></i>Ver Recibo
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $recibos->links() }}</div>
            @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-2xl text-slate_custom-400"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin recibos</h3>
                <p class="text-sm text-slate_custom-400">No hay recibos de edificio disponibles.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
