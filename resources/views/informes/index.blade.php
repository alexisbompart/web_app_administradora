<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Informes</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Generación de informes y reportes de la comunidad</p>
            </div>
        </div>
    </x-slot>

    <!-- Report Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('servicios.informes.relacion-gastos') }}" class="card hover:shadow-lg transition-shadow group">
            <div class="card-body flex flex-col items-center text-center py-8">
                <div class="w-16 h-16 bg-burgundy-800/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-burgundy-800 transition">
                    <i class="fas fa-file-invoice text-2xl text-burgundy-800 group-hover:text-white transition"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Relación de Gastos</h3>
                <p class="text-sm text-slate_custom-400">Detalle de gastos realizados por periodo</p>
            </div>
        </a>

        <a href="{{ route('servicios.informes.estado-cuenta') }}" class="card hover:shadow-lg transition-shadow group">
            <div class="card-body flex flex-col items-center text-center py-8">
                <div class="w-16 h-16 bg-navy-800/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-navy-800 transition">
                    <i class="fas fa-file-alt text-2xl text-navy-800 group-hover:text-white transition"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Estado de Cuenta</h3>
                <p class="text-sm text-slate_custom-400">Estado de cuenta por apartamento</p>
            </div>
        </a>

        <a href="{{ route('servicios.informes.morosos') }}" class="card hover:shadow-lg transition-shadow group">
            <div class="card-body flex flex-col items-center text-center py-8">
                <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-red-600 transition">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600 group-hover:text-white transition"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Listado de Morosos</h3>
                <p class="text-sm text-slate_custom-400">Apartamentos con pagos vencidos</p>
            </div>
        </a>

        <a href="{{ route('servicios.informes.informe-anual') }}" class="card hover:shadow-lg transition-shadow group">
            <div class="card-body flex flex-col items-center text-center py-8">
                <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-600 transition">
                    <i class="fas fa-book text-2xl text-green-600 group-hover:text-white transition"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Informe Anual</h3>
                <p class="text-sm text-slate_custom-400">Resumen anual de gestion del condominio</p>
            </div>
        </a>

        <a href="{{ route('servicios.informes.plan-operativo') }}" class="card hover:shadow-lg transition-shadow group">
            <div class="card-body flex flex-col items-center text-center py-8">
                <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-yellow-500 transition">
                    <i class="fas fa-tasks text-2xl text-yellow-600 group-hover:text-white transition"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Plan Operativo</h3>
                <p class="text-sm text-slate_custom-400">Planificacion operativa del periodo</p>
            </div>
        </a>

        <a href="{{ route('servicios.informes.circulares') }}" class="card hover:shadow-lg transition-shadow group">
            <div class="card-body flex flex-col items-center text-center py-8">
                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 transition">
                    <i class="fas fa-envelope text-2xl text-blue-600 group-hover:text-white transition"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Circulares</h3>
                <p class="text-sm text-slate_custom-400">Comunicaciones a la comunidad</p>
            </div>
        </a>
    </div>

    <!-- Recent Informes Table -->
    @if($informes->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-history mr-2 text-burgundy-800"></i>Informes Recientes
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Periodo</th>
                            <th>Estado</th>
                            <th>Fecha Generación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($informes as $informe)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $informe->tipo)) }}</td>
                            <td class="font-medium">{{ Str::limit($informe->titulo, 50) }}</td>
                            <td>{{ $informe->periodo ?? 'N/A' }}</td>
                            <td>
                                @if($informe->enviado)
                                    <span class="badge-success">Enviado</span>
                                @else
                                    <span class="badge-warning">Pendiente</span>
                                @endif
                            </td>
                            <td>{{ $informe->fecha_generacion?->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($informe->archivo_path)
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $informes->links() }}
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
