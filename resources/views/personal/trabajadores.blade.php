<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Trabajadores</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gesti&oacute;n del personal del condominio</p>
            </div>
            <a href="{{ route('personal.trabajadores.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear Trabajador
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-users mr-2 text-burgundy-800"></i>Listado de Trabajadores
            </h3>
        </div>
        <div class="card-body p-0">
            @if($trabajadores->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>C&eacute;dula</th>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Departamento</th>
                                <th>Salario</th>
                                <th>Fecha Ingreso</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trabajadores as $trabajador)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $trabajador->cedula }}</td>
                                    <td>{{ $trabajador->nombre_completo }}</td>
                                    <td>{{ $trabajador->cargo }}</td>
                                    <td>{{ $trabajador->departamento }}</td>
                                    <td class="text-right">{{ number_format($trabajador->salario_basico, 2, ',', '.') }} Bs</td>
                                    <td>{{ $trabajador->fecha_ingreso?->format('d/m/Y') }}</td>
                                    <td>
                                        @switch($trabajador->estatus)
                                            @case('A')
                                                <span class="badge-success">Activo</span>
                                                @break
                                            @case('I')
                                                <span class="badge-danger">Inactivo</span>
                                                @break
                                            @case('V')
                                                <span class="badge-warning">Vacaciones</span>
                                                @break
                                            @default
                                                <span class="badge-secondary">{{ $trabajador->estatus }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('personal.trabajadores.show', $trabajador) }}" class="btn-secondary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('personal.trabajadores.edit', $trabajador) }}" class="btn-secondary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('personal.trabajadores.destroy', $trabajador) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de eliminar este trabajador?')">
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
                    {{ $trabajadores->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay trabajadores registrados</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comience agregando el primer trabajador al sistema.</p>
                    <a href="{{ route('personal.trabajadores.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear Trabajador
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
