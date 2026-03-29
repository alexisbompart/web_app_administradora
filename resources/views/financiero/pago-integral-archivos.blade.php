<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Archivos Bancarios Generados</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Seguimiento de archivos enviados a cada banco</p>
            </div>
            <a href="{{ route('financiero.pago-integral.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Stats rapidos --}}
    @php
        $statsArchivos = \App\Models\Financiero\PagoIntegralArchivo::selectRaw("
            estatus, COUNT(*) as cantidad, SUM(monto_total) as monto
        ")->groupBy('estatus')->pluck('cantidad', 'estatus');
        $estatusConfig = [
            'GE' => ['label' => 'Generados', 'icon' => 'fas fa-file-alt', 'color' => 'slate_custom', 'bg' => 'bg-slate_custom-100'],
            'EN' => ['label' => 'Enviados', 'icon' => 'fas fa-paper-plane', 'color' => 'blue', 'bg' => 'bg-blue-100'],
            'EP' => ['label' => 'En Proceso', 'icon' => 'fas fa-spinner', 'color' => 'amber', 'bg' => 'bg-amber-100'],
            'PR' => ['label' => 'Procesados', 'icon' => 'fas fa-check-double', 'color' => 'green', 'bg' => 'bg-green-100'],
        ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        @foreach($estatusConfig as $cod => $cfg)
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">{{ $cfg['label'] }}</div>
                <div class="w-10 h-10 {{ $cfg['bg'] }} rounded-lg flex items-center justify-center">
                    <i class="{{ $cfg['icon'] }} text-{{ $cfg['color'] }}-600"></i>
                </div>
            </div>
            <div class="stat-value text-{{ $cfg['color'] }}-600">{{ $statsArchivos[$cod] ?? 0 }}</div>
        </div>
        @endforeach
    </div>

    {{-- Tabla de archivos --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-folder-open mr-2 text-burgundy-800"></i>Archivos Generados
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Banco</th>
                            <th>Archivo</th>
                            <th>Pagos</th>
                            <th>Monto Total</th>
                            <th>Generado</th>
                            <th>Enviado</th>
                            <th>Procesado</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivos as $archivo)
                        <tr>
                            <td class="font-medium">#{{ $archivo->id }}</td>
                            <td>
                                <span class="font-semibold text-navy-800">{{ $archivo->banco->nombre ?? '--' }}</span>
                            </td>
                            <td class="text-sm">
                                <i class="fas fa-file-alt mr-1 text-slate_custom-400"></i>{{ $archivo->nombre_archivo }}
                            </td>
                            <td class="text-center font-semibold">{{ $archivo->cantidad_pagos }}</td>
                            <td class="font-semibold text-burgundy-800">{{ number_format($archivo->monto_total, 2, ',', '.') }} Bs</td>
                            <td class="text-xs text-slate_custom-500">
                                {{ $archivo->fecha_generado?->format('d/m/Y H:i') }}<br>
                                <span class="text-slate_custom-400">{{ $archivo->generadoPor?->name ?? '' }}</span>
                            </td>
                            <td class="text-xs text-slate_custom-500">{{ $archivo->fecha_enviado?->format('d/m/Y H:i') ?? '--' }}</td>
                            <td class="text-xs text-slate_custom-500">{{ $archivo->fecha_procesado?->format('d/m/Y H:i') ?? '--' }}</td>
                            <td>
                                @if($archivo->estatus === 'GE')
                                    <span class="badge-info"><i class="fas fa-file-alt mr-1"></i>Generado</span>
                                @elseif($archivo->estatus === 'EN')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700"><i class="fas fa-paper-plane mr-1"></i>Enviado</span>
                                @elseif($archivo->estatus === 'EP')
                                    <span class="badge-warning"><i class="fas fa-spinner mr-1"></i>En Proceso</span>
                                @elseif($archivo->estatus === 'PR')
                                    <span class="badge-success"><i class="fas fa-check-double mr-1"></i>Procesado</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-1" x-data="{ showMenu: false }">
                                    <a href="{{ route('financiero.pago-integral.archivos.detalle', $archivo) }}"
                                       class="text-navy-800 hover:text-burgundy-800 transition p-1" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($archivo->estatus !== 'PR')
                                    <div class="relative">
                                        <button @click="showMenu = !showMenu" class="text-slate_custom-400 hover:text-navy-800 transition p-1" title="Cambiar estatus">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div x-show="showMenu" @click.outside="showMenu = false" x-transition
                                             class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-slate_custom-200 z-20 py-1">
                                            @if($archivo->estatus === 'GE')
                                            <form action="{{ route('financiero.pago-integral.archivos.estatus', $archivo) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estatus" value="EN">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 text-blue-700">
                                                    <i class="fas fa-paper-plane mr-2"></i>Marcar Enviado
                                                </button>
                                            </form>
                                            @endif
                                            @if(in_array($archivo->estatus, ['GE', 'EN']))
                                            <form action="{{ route('financiero.pago-integral.archivos.estatus', $archivo) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estatus" value="EP">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-amber-50 text-amber-700">
                                                    <i class="fas fa-spinner mr-2"></i>En Proceso
                                                </button>
                                            </form>
                                            @endif
                                            @if(in_array($archivo->estatus, ['EN', 'EP']))
                                            <form action="{{ route('financiero.pago-integral.archivos.estatus', $archivo) }}" method="POST"
                                                  onsubmit="return confirm('Al marcar como Procesado se aprobaran automaticamente todos los pagos incluidos. Continuar?')">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estatus" value="PR">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 text-green-700">
                                                    <i class="fas fa-check-double mr-2"></i>Procesado (Aprobar pagos)
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No se han generado archivos aun
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivos->hasPages())
            <div class="mt-4">{{ $archivos->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
