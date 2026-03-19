<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Proveedores</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gesti&oacute;n de proveedores del condominio</p>
            </div>
            <a href="{{ route('proveedores.proveedores.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear Proveedor
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-truck mr-2 text-burgundy-800"></i>Listado de Proveedores
            </h3>
        </div>
        <div class="card-body p-0">
            @if($proveedores->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>RIF</th>
                                <th>Raz&oacute;n Social</th>
                                <th>Tel&eacute;fono</th>
                                <th>Email</th>
                                <th>Tipo Contribuyente</th>
                                <th>Banco</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proveedores as $proveedor)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $proveedor->rif }}</td>
                                    <td>{{ $proveedor->razon_social }}</td>
                                    <td>{{ $proveedor->telefono }}</td>
                                    <td>{{ $proveedor->email }}</td>
                                    <td class="capitalize">{{ $proveedor->tipo_contribuyente }}</td>
                                    <td>{{ $proveedor->banco?->nombre ?? 'N/A' }}</td>
                                    <td>
                                        @if($proveedor->activo)
                                            <span class="badge-success">Activo</span>
                                        @else
                                            <span class="badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('proveedores.proveedores.show', $proveedor) }}" class="btn-secondary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('proveedores.proveedores.edit', $proveedor) }}" class="btn-secondary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('proveedores.proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de eliminar este proveedor?')">
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
                    {{ $proveedores->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay proveedores registrados</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comience agregando el primer proveedor al sistema.</p>
                    <a href="{{ route('proveedores.proveedores.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear Proveedor
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
