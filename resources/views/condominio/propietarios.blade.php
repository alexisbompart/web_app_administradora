<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Propietarios</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de propietarios del condominio</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.propietarios.generate.preview') }}" class="btn-secondary">
                    <i class="fas fa-users-cog mr-2"></i>Generar desde Afiliaciones
                </a>
                <a href="{{ route('condominio.propietarios.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Crear nuevo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-users mr-2 text-burgundy-800"></i>Listado de Propietarios
            </h3>
        </div>
        <div class="card-body p-0">
            @if($propietarios->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Cedula</th>
                                <th>Nombre Completo</th>
                                <th>Telefono</th>
                                <th>Email</th>
                                <th>Apartamentos</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($propietarios as $propietario)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $propietario->cedula }}</td>
                                    <td>{{ $propietario->nombres }} {{ $propietario->apellidos }}</td>
                                    <td class="text-xs">{{ $propietario->telefono ?? '--' }}</td>
                                    <td class="text-xs">{{ $propietario->email ?? '--' }}</td>
                                    <td>
                                        @php $aptos = $propietario->apartamentos->where('pivot.propietario_actual', true); @endphp
                                        @if($aptos->count())
                                            @foreach($aptos as $apto)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-navy-800/10 text-navy-800 mr-1 mb-1">
                                                    {{ $apto->edificio?->nombre ?? $apto->edificio?->cod_edif ?? '?' }} - {{ $apto->num_apto }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-slate_custom-300">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($propietario->estatus)
                                            <span class="badge-success text-xs">Activo</span>
                                        @else
                                            <span class="badge-danger text-xs">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('condominio.propietarios.show', $propietario) }}" class="btn-secondary text-xs px-2 py-1" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('condominio.propietarios.edit', $propietario) }}" class="btn-secondary text-xs px-2 py-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('condominio.propietarios.destroy', $propietario) }}" method="POST" onsubmit="return confirm('Eliminar este propietario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary text-xs px-2 py-1 text-red-600 hover:text-red-800" title="Eliminar">
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
                <div class="p-4">
                    {{ $propietarios->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay propietarios registrados</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comienza creando el primer propietario del condominio.</p>
                    <a href="{{ route('condominio.propietarios.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear propietario
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
