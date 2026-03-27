<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Afiliaciones de Apartamentos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestion de afiliaciones por apartamento (afilapto)</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.afilapto.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar
                </a>
                <a href="{{ route('condominio.afilapto.create') }}" class="btn-primary">
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
                <i class="fas fa-link mr-2 text-burgundy-800"></i>Listado de Afiliaciones ({{ $afilaptos->total() }})
            </h3>
        </div>
        <div class="card-body p-0">
            @if($afilaptos->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Edificio</th>
                                <th>Apartamento</th>
                                <th>Compania</th>
                                <th>Estatus</th>
                                <th>Fecha Afil.</th>
                                <th>Pago Integral</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($afilaptos as $afil)
                            <tr>
                                <td class="text-xs text-slate_custom-400">{{ $afil->id }}</td>
                                <td>{{ $afil->edificio?->nombre ?? $afil->edificio?->cod_edif ?? '--' }}</td>
                                <td class="font-medium text-navy-800">{{ $afil->apartamento?->num_apto ?? '--' }}</td>
                                <td class="text-xs">{{ $afil->compania?->nombre ?? '--' }}</td>
                                <td>
                                    @if($afil->estatus_afil === 'A')<span class="badge-success text-xs">Activo</span>
                                    @elseif($afil->estatus_afil === 'D')<span class="badge-danger text-xs">Desact.</span>
                                    @else<span class="badge-info text-xs">{{ $afil->estatus_afil ?? '--' }}</span>@endif
                                </td>
                                <td class="text-xs">{{ $afil->fecha_afiliacion?->format('d/m/Y') ?? '--' }}</td>
                                <td>
                                    @if($afil->afilpagointegral)
                                        <span class="badge-success text-xs"><i class="fas fa-check mr-1"></i>Si</span>
                                    @else
                                        <span class="badge-warning text-xs">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('condominio.afilapto.edit', $afil) }}" class="btn-secondary text-xs px-2 py-1" title="Editar"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('condominio.afilapto.destroy', $afil) }}" method="POST" onsubmit="return confirm('Eliminar esta afiliacion y su pago integral asociado?')">
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
                <div class="p-4">{{ $afilaptos->links() }}</div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-link text-2xl text-slate_custom-400"></i></div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay afiliaciones registradas</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Importe el archivo afilapto.csv o cree una manualmente.</p>
                    <a href="{{ route('condominio.afilapto.importar') }}" class="btn-primary"><i class="fas fa-file-import mr-2"></i>Importar</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
