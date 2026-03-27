<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Afiliaciones Pago Integral</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestion de datos de afiliados al pago integral</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.afilpagointegral.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar
                </a>
                <a href="{{ route('condominio.afilpagointegral.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Crear nuevo
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-hand-holding-usd mr-2 text-burgundy-800"></i>Listado ({{ $registros->total() }})
            </h3>
        </div>
        <div class="card-body p-0">
            @if($registros->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Afil. ID</th>
                                <th>Cedula</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Banco</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registros as $reg)
                            <tr>
                                <td class="text-xs text-slate_custom-400">{{ $reg->id }}</td>
                                <td class="text-xs">{{ $reg->afilapto_id }}</td>
                                <td class="font-medium text-navy-800">{{ $reg->letra }}-{{ $reg->cedula_rif }}</td>
                                <td class="text-sm">{{ $reg->nombres }}</td>
                                <td class="text-sm">{{ $reg->apellidos }}</td>
                                <td class="text-xs">{{ \Illuminate\Support\Str::limit($reg->email, 25) }}</td>
                                <td class="text-xs">{{ $reg->banco?->nombre ?? '--' }}</td>
                                <td>
                                    @if($reg->estatus === 'A')<span class="badge-success text-xs">Activo</span>
                                    @elseif($reg->estatus === 'D')<span class="badge-danger text-xs">Desact.</span>
                                    @elseif($reg->estatus === 'T')<span class="badge-warning text-xs">Temp.</span>
                                    @else<span class="badge-info text-xs">{{ $reg->estatus ?? '--' }}</span>@endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('condominio.afilpagointegral.edit', $reg) }}" class="btn-secondary text-xs px-2 py-1" title="Editar"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('condominio.afilpagointegral.destroy', $reg) }}" method="POST" onsubmit="return confirm('Eliminar este registro?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-secondary text-xs px-2 py-1 text-red-600 hover:text-red-800" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $registros->links() }}</div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-hand-holding-usd text-2xl text-slate_custom-400"></i></div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay registros</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Importe el archivo afilpagointegral.csv o cree uno manualmente.</p>
                    <a href="{{ route('condominio.afilpagointegral.importar') }}" class="btn-primary"><i class="fas fa-file-import mr-2"></i>Importar</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
