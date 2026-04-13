<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Solicitudes de Servicio</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión y seguimiento de solicitudes del portal público</p>
            </div>
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
    @if(session('warning'))
    <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-triangle"></i>{{ session('warning') }}
    </div>
    @endif

    {{-- Resumen de totales --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total</div>
                <div class="w-9 h-9 bg-navy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-inbox text-navy-800 text-sm"></i></div>
            </div>
            <div class="stat-value">{{ $totales['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pendientes</div>
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-clock text-amber-600 text-sm"></i></div>
            </div>
            <div class="stat-value text-amber-600">{{ $totales['pendiente'] }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">En Revisión</div>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-search text-blue-600 text-sm"></i></div>
            </div>
            <div class="stat-value text-blue-600">{{ $totales['en_revision'] }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Respondidas</div>
                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600 text-sm"></i></div>
            </div>
            <div class="stat-value text-green-600">{{ $totales['respondida'] }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Cerradas</div>
                <div class="w-9 h-9 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-slate_custom-500 text-sm"></i></div>
            </div>
            <div class="stat-value text-slate_custom-500">{{ $totales['cerrada'] }}</div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card mb-6">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.solicitudes-servicio.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs font-semibold text-slate_custom-400 uppercase mb-1 block">Buscar</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, email, asunto..."
                           class="w-full border border-slate_custom-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-burgundy-800 focus:ring-1 focus:ring-burgundy-800">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate_custom-400 uppercase mb-1 block">Estatus</label>
                    <select name="estatus" class="border border-slate_custom-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-burgundy-800">
                        <option value="">Todos</option>
                        <option value="pendiente"   {{ request('estatus') === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_revision" {{ request('estatus') === 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                        <option value="respondida"  {{ request('estatus') === 'respondida'  ? 'selected' : '' }}>Respondida</option>
                        <option value="cerrada"     {{ request('estatus') === 'cerrada'     ? 'selected' : '' }}>Cerrada</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary text-sm">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                @if(request()->hasAny(['buscar','estatus']))
                <a href="{{ route('admin.solicitudes-servicio.index') }}" class="btn-secondary text-sm">
                    <i class="fas fa-times mr-2"></i>Limpiar
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Listado --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list mr-2 text-burgundy-800"></i>Solicitudes ({{ $solicitudes->total() }})
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Solicitante</th>
                            <th>Contacto</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $sol)
                        <tr>
                            <td class="font-medium text-navy-800">#{{ $sol->id }}</td>
                            <td>
                                <p class="font-semibold text-navy-800 text-sm">{{ $sol->nombres_apellidos }}</p>
                            </td>
                            <td class="text-xs">
                                <p class="text-slate_custom-500"><i class="fas fa-envelope mr-1"></i>{{ $sol->email }}</p>
                                <p class="text-slate_custom-400 mt-0.5"><i class="fas fa-phone mr-1"></i>{{ $sol->telefono }}</p>
                            </td>
                            <td class="text-sm max-w-xs">
                                <p class="font-medium text-navy-800 truncate">{{ $sol->asunto }}</p>
                                @if($sol->descripcion)
                                <p class="text-slate_custom-400 text-xs truncate mt-0.5">{{ Str::limit($sol->descripcion, 60) }}</p>
                                @endif
                            </td>
                            <td class="text-xs text-slate_custom-500">
                                {{ $sol->created_at->format('d/m/Y') }}<br>
                                <span class="text-slate_custom-300">{{ $sol->created_at->format('H:i') }}</span>
                            </td>
                            <td>
                                <span class="{{ $sol->estatusBadgeClass() }}">{{ $sol->estatusLabel() }}</span>
                            </td>
                            <td>
                                <button type="button"
                                        onclick="abrirDetalle({{ $sol->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-navy-800/8 text-navy-800 rounded-lg hover:bg-navy-800 hover:text-white transition">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                            </td>
                        </tr>

                        {{-- Panel de detalle inline (hidden) --}}
                        <tr id="detalle-{{ $sol->id }}" class="hidden bg-slate_custom-50">
                            <td colspan="7" class="px-6 py-5">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                                    {{-- Datos --}}
                                    <div class="lg:col-span-2 space-y-4">
                                        <div>
                                            <p class="text-xs font-semibold text-slate_custom-400 uppercase mb-1">Descripción completa</p>
                                            <p class="text-sm text-navy-800 leading-relaxed bg-white border border-slate_custom-200 rounded-xl p-4 min-h-[80px]">
                                                {{ $sol->descripcion ?? '—' }}
                                            </p>
                                        </div>

                                        {{-- Notas internas --}}
                                        <form action="{{ route('admin.solicitudes-servicio.notas', $sol) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <p class="text-xs font-semibold text-slate_custom-400 uppercase mb-1">Notas internas</p>
                                            <textarea name="notas_internas" rows="3"
                                                      placeholder="Notas internas (solo visibles para el equipo)..."
                                                      class="w-full border border-slate_custom-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-burgundy-800 resize-none">{{ $sol->notas_internas }}</textarea>
                                            <div class="flex justify-end mt-2">
                                                <button type="submit" class="btn-secondary text-xs">
                                                    <i class="fas fa-save mr-1.5"></i>Guardar Notas
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Gestión --}}
                                    <div class="space-y-4">
                                        {{-- Cambiar estatus --}}
                                        <div>
                                            <p class="text-xs font-semibold text-slate_custom-400 uppercase mb-2">Cambiar estatus</p>
                                            <form action="{{ route('admin.solicitudes-servicio.estatus', $sol) }}" method="POST" class="flex gap-2">
                                                @csrf @method('PATCH')
                                                <select name="estatus" class="flex-1 border border-slate_custom-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-burgundy-800">
                                                    <option value="pendiente"   {{ $sol->estatus === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                                                    <option value="en_revision" {{ $sol->estatus === 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                                    <option value="respondida"  {{ $sol->estatus === 'respondida'  ? 'selected' : '' }}>Respondida</option>
                                                    <option value="cerrada"     {{ $sol->estatus === 'cerrada'     ? 'selected' : '' }}>Cerrada</option>
                                                </select>
                                                <button type="submit" class="btn-primary text-xs px-3">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Info atención --}}
                                        @if($sol->atendidoPor || $sol->fecha_respuesta)
                                        <div class="bg-white border border-slate_custom-200 rounded-xl p-3 text-xs space-y-1">
                                            @if($sol->atendidoPor)
                                            <p class="text-slate_custom-500"><i class="fas fa-user mr-1"></i>Atendido por: <strong>{{ $sol->atendidoPor->name }}</strong></p>
                                            @endif
                                            @if($sol->fecha_respuesta)
                                            <p class="text-slate_custom-500"><i class="fas fa-reply mr-1"></i>Respondido: {{ $sol->fecha_respuesta->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                        @endif

                                        {{-- Enviar correo --}}
                                        <div>
                                            <p class="text-xs font-semibold text-slate_custom-400 uppercase mb-2">Enviar correo al solicitante</p>
                                            <button type="button"
                                                    onclick="abrirModalCorreo({{ $sol->id }}, '{{ addslashes($sol->nombres_apellidos) }}', '{{ $sol->email }}')"
                                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-burgundy-800 text-white text-sm font-semibold rounded-xl hover:bg-burgundy-700 transition">
                                                <i class="fas fa-envelope"></i> Responder por Correo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate_custom-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                No hay solicitudes registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($solicitudes->hasPages())
            <div class="px-4 py-4 border-t border-slate_custom-100">
                {{ $solicitudes->links() }}
            </div>
            @endif
        </div>
    </div>


    {{-- Modal Enviar Correo --}}
    <div id="modal-correo" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background:rgba(0,0,0,.55); backdrop-filter:blur(4px);"
         onclick="if(event.target===this)cerrarModalCorreo()">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="bg-gradient-to-r from-navy-800 to-burgundy-800 rounded-t-3xl px-6 py-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center">
                        <i class="fas fa-envelope text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-white text-base">Enviar Correo</h3>
                        <p class="text-white/60 text-xs" id="modal-correo-dest">—</p>
                    </div>
                </div>
                <button onclick="cerrarModalCorreo()" class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center text-white transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form id="form-correo" method="POST" action="" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate_custom-400 uppercase mb-1 block">Cuerpo del Mensaje</label>
                    <textarea name="cuerpo_mensaje" id="correo-cuerpo" rows="7" required
                              placeholder="Escriba aquí su respuesta al solicitante..."
                              class="w-full border border-slate_custom-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-burgundy-800 focus:ring-1 focus:ring-burgundy-800 resize-none"></textarea>
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <button type="button" onclick="cerrarModalCorreo()" class="btn-secondary text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-correo-submit" class="btn-primary text-sm"
                            onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Enviando...'; this.form.submit();">
                        <i class="fas fa-paper-plane mr-2"></i>Enviar Correo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirDetalle(id) {
            const row = document.getElementById('detalle-' + id);
            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        }

        function abrirModalCorreo(id, nombre, email) {
            const base = '{{ url('/admin/solicitudes-servicio') }}';
            document.getElementById('form-correo').action = base + '/' + id + '/correo';
            document.getElementById('modal-correo-dest').textContent = nombre + ' <' + email + '>';
            document.getElementById('correo-cuerpo').value = '';
            const btn = document.getElementById('btn-correo-submit');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Enviar Correo';
            document.getElementById('modal-correo').classList.remove('hidden');
        }

        function cerrarModalCorreo() {
            document.getElementById('modal-correo').classList.add('hidden');
        }
    </script>

</x-app-layout>
