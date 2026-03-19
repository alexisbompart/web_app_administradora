<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">N&oacute;minas</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gesti&oacute;n de n&oacute;minas del personal</p>
            </div>
            <a href="{{ route('personal.nominas.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear N&oacute;mina
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-money-check-alt mr-2 text-burgundy-800"></i>Listado de N&oacute;minas
            </h3>
        </div>
        <div class="card-body p-0">
            @if($nominas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>C&oacute;digo</th>
                                <th>Periodo</th>
                                <th>Tipo</th>
                                <th>Total Asignaciones</th>
                                <th>Total Deducciones</th>
                                <th>Neto</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nominas as $nomina)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $nomina->codigo }}</td>
                                    <td>{{ $nomina->periodo_inicio?->format('d/m/Y') }} - {{ $nomina->periodo_fin?->format('d/m/Y') }}</td>
                                    <td class="capitalize">{{ $nomina->tipo }}</td>
                                    <td class="text-right">{{ number_format($nomina->total_asignaciones, 2, ',', '.') }} Bs</td>
                                    <td class="text-right">{{ number_format($nomina->total_deducciones, 2, ',', '.') }} Bs</td>
                                    <td class="text-right font-semibold">{{ number_format($nomina->total_neto, 2, ',', '.') }} Bs</td>
                                    <td>
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
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('personal.nominas.show', $nomina) }}" class="btn-secondary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('personal.nominas.edit', $nomina) }}" class="btn-secondary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($nomina->estatus === 'borrador')
                                                <form action="{{ route('personal.nominas.procesar', $nomina) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de procesar esta n&oacute;mina?')">
                                                    @csrf
                                                    <button type="submit" class="btn-secondary btn-sm text-blue-600 hover:text-blue-800" title="Procesar">
                                                        <i class="fas fa-cogs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($nomina->estatus === 'procesada')
                                                <form action="{{ route('personal.nominas.aprobar', $nomina) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de aprobar esta n&oacute;mina?')">
                                                    @csrf
                                                    <button type="submit" class="btn-secondary btn-sm text-green-600 hover:text-green-800" title="Aprobar">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('personal.nominas.destroy', $nomina) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de eliminar esta n&oacute;mina?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary btn-sm text-red-600 hover:text-red-800" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $nominas->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-money-check-alt text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay n&oacute;minas registradas</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comience creando la primera n&oacute;mina del periodo.</p>
                    <a href="{{ route('personal.nominas.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear N&oacute;mina
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
