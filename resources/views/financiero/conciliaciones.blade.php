<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Conciliaciones Bancarias</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Control y seguimiento de conciliaciones bancarias</p>
            </div>
            <a href="{{ route('financiero.conciliaciones.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Nueva Conciliación
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-balance-scale mr-2 text-burgundy-800"></i>Listado de Conciliaciones
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Banco</th>
                            <th>Periodo</th>
                            <th>Saldo Banco</th>
                            <th>Saldo Libros</th>
                            <th>Diferencia</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conciliaciones as $conciliacion)
                        <tr>
                            <td class="font-medium">{{ $conciliacion->condBanco?->banco?->nombre }}</td>
                            <td>
                                {{ $conciliacion->fecha_desde?->format('d/m/Y') }}
                                -
                                {{ $conciliacion->fecha_hasta?->format('d/m/Y') }}
                            </td>
                            <td>{{ number_format($conciliacion->saldo_banco, 2, ',', '.') }} Bs</td>
                            <td>{{ number_format($conciliacion->saldo_libros, 2, ',', '.') }} Bs</td>
                            <td class="font-semibold {{ $conciliacion->diferencia != 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($conciliacion->diferencia, 2, ',', '.') }} Bs
                            </td>
                            <td>
                                @if($conciliacion->estatus === 'P')
                                    <span class="badge-warning">Pendiente</span>
                                @elseif($conciliacion->estatus === 'A')
                                    <span class="badge-success">Aprobada</span>
                                @elseif($conciliacion->estatus === 'R')
                                    <span class="badge-danger">Rechazada</span>
                                @else
                                    <span class="badge-info">{{ $conciliacion->estatus }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('financiero.conciliaciones.show', $conciliacion) }}" class="text-navy-800 hover:text-burgundy-800 transition" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('financiero.conciliaciones.edit', $conciliacion) }}" class="text-navy-800 hover:text-burgundy-800 transition" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('financiero.conciliaciones.destroy', $conciliacion) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta conciliación?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay conciliaciones registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $conciliaciones->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
