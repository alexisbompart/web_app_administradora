<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Tasas BCV</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Historico de tasas del Banco Central de Venezuela (USD/Bs)</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('financiero.tasabcv.importar') }}" class="btn-secondary"><i class="fas fa-file-import mr-2"></i>Importar</a>
                <a href="{{ route('financiero.tasabcv.create') }}" class="btn-primary"><i class="fas fa-plus mr-2"></i>Registrar Tasa</a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Tasa del dia --}}
    @if($tasaHoy)
    <div class="card mb-6">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate_custom-400">Tasa vigente ({{ $tasaHoy->fecha->format('d/m/Y') }})</p>
                    <p class="text-3xl font-heading font-bold text-burgundy-800">Bs. {{ number_format($tasaHoy->tasa, 2, ',', '.') }}</p>
                    <p class="text-xs text-slate_custom-400 mt-1">1 USD = {{ number_format($tasaHoy->tasa, 4, ',', '.') }} Bs</p>
                </div>
                <div class="w-16 h-16 bg-burgundy-800/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-2xl text-burgundy-800"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filtro --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" class="flex items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-800 mb-1">Filtrar por mes</label>
                    <input type="month" name="mes" value="{{ request('mes') }}"
                           class="rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-filter mr-2"></i>Filtrar</button>
                @if(request('mes'))
                <a href="{{ route('financiero.tasabcv.index') }}" class="btn-secondary">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-chart-line mr-2 text-burgundy-800"></i>Listado ({{ $tasas->total() }})
            </h3>
        </div>
        <div class="card-body p-0">
            @if($tasas->count())
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Dia</th>
                            <th class="text-right">Tasa (Bs/USD)</th>
                            <th>Fuente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasas as $t)
                        <tr>
                            <td class="font-medium text-navy-800">{{ $t->fecha->format('d/m/Y') }}</td>
                            <td class="text-xs text-slate_custom-400">{{ $t->fecha->locale('es')->isoFormat('dddd') }}</td>
                            <td class="text-right font-mono font-semibold text-burgundy-800">{{ number_format($t->tasa, 4, ',', '.') }}</td>
                            <td><span class="badge-info text-xs">{{ $t->fuente ?? 'BCV' }}</span></td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('financiero.tasabcv.edit', $t) }}" class="btn-secondary text-xs px-2 py-1"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('financiero.tasabcv.destroy', $t) }}" method="POST" onsubmit="return confirm('Eliminar esta tasa?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-secondary text-xs px-2 py-1 text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $tasas->links() }}</div>
            @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-dollar-sign text-2xl text-slate_custom-400"></i></div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay tasas registradas</h3>
                <p class="text-sm text-slate_custom-400 mb-4">Importe el archivo cond_tasa_bcv.csv o registre una manualmente.</p>
                <a href="{{ route('financiero.tasabcv.importar') }}" class="btn-primary"><i class="fas fa-file-import mr-2"></i>Importar</a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
