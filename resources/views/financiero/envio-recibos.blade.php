<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Envio de Recibos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Envio masivo o selectivo de recibos de condominio por correo</p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('errores_envio') && count(session('errores_envio')) > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-yellow-700 mb-1">Algunos envios no se completaron:</p>
                    <ul class="text-sm text-yellow-600 list-disc ml-4">
                        @foreach(session('errores_envio') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-filter mr-2 text-burgundy-800"></i>Seleccionar Edificio y Periodo
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('financiero.envio-recibos.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Edificio</label>
                        <select name="edificio_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">-- Seleccione Edificio --</option>
                            @foreach($edificios as $edif)
                                <option value="{{ $edif->id }}" {{ request('edificio_id') == $edif->id ? 'selected' : '' }}>
                                    {{ $edif->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Periodo</label>
                        <input type="month" name="periodo" value="{{ request('periodo', now()->format('Y-m')) }}"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-search mr-2"></i>Consultar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($edificio && $deudas->count() > 0)
        <!-- Resumen -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Recibos</p>
                        <p class="text-2xl font-heading font-bold text-navy-800">{{ $deudas->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Con Email</p>
                        <p class="text-2xl font-heading font-bold text-green-600">{{ $deudas->where('tiene_email', true)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-envelope-open text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Sin Email</p>
                        <p class="text-2xl font-heading font-bold text-red-600">{{ $deudas->where('tiene_email', false)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-envelope text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Recibos -->
        <form method="POST" action="{{ route('financiero.envio-recibos.enviar') }}" id="form-envio">
            @csrf
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-list mr-2 text-burgundy-800"></i>Recibos Pendientes - {{ $edificio->nombre }}
                    </h3>
                    <div class="flex items-center gap-3">
                        <button type="button" id="btn-seleccionar-todos" class="btn-secondary text-xs">
                            <i class="fas fa-check-double mr-1"></i>Seleccionar Todos
                        </button>
                        <button type="button" id="btn-seleccionar-con-email" class="btn-secondary text-xs">
                            <i class="fas fa-at mr-1"></i>Solo con Email
                        </button>
                        <button type="button" id="btn-deseleccionar" class="btn-secondary text-xs">
                            <i class="fas fa-times mr-1"></i>Deseleccionar
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th class="w-10">
                                        <input type="checkbox" id="check-all" class="rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                                    </th>
                                    <th>Apartamento</th>
                                    <th>Propietario</th>
                                    <th>Email</th>
                                    <th>Periodo</th>
                                    <th class="text-right">Monto</th>
                                    <th class="text-right">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deudas as $deuda)
                                <tr class="{{ !$deuda->tiene_email ? 'bg-red-50/50' : '' }}">
                                    <td>
                                        <input type="checkbox" name="deudas[]" value="{{ $deuda->id }}"
                                               class="check-deuda rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800"
                                               data-tiene-email="{{ $deuda->tiene_email ? '1' : '0' }}"
                                               {{ !$deuda->tiene_email ? 'disabled' : '' }}>
                                    </td>
                                    <td class="font-medium text-navy-800">{{ $deuda->num_apto }}</td>
                                    <td>{{ $deuda->propietario_nombre }}</td>
                                    <td>
                                        @if($deuda->tiene_email)
                                            <span class="text-sm text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>{{ $deuda->propietario_email }}
                                            </span>
                                        @else
                                            <span class="text-sm text-red-500">
                                                <i class="fas fa-times-circle mr-1"></i>No registrado
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $deuda->periodo }}</td>
                                    <td class="text-right font-semibold">{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                                    <td class="text-right font-semibold text-burgundy-800">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-body border-t border-slate_custom-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate_custom-500">
                            <span id="count-seleccionados">0</span> recibo(s) seleccionado(s)
                        </p>
                        <button type="submit" class="btn-primary" id="btn-enviar" disabled>
                            <i class="fas fa-paper-plane mr-2"></i>Enviar Recibos Seleccionados
                        </button>
                    </div>
                </div>
            </div>
        </form>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkAll = document.getElementById('check-all');
                const checkboxes = document.querySelectorAll('.check-deuda:not([disabled])');
                const btnEnviar = document.getElementById('btn-enviar');
                const countSpan = document.getElementById('count-seleccionados');

                function updateCount() {
                    const checked = document.querySelectorAll('.check-deuda:checked').length;
                    countSpan.textContent = checked;
                    btnEnviar.disabled = checked === 0;
                }

                checkAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateCount();
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateCount);
                });

                document.getElementById('btn-seleccionar-todos').addEventListener('click', function() {
                    checkboxes.forEach(cb => cb.checked = true);
                    checkAll.checked = true;
                    updateCount();
                });

                document.getElementById('btn-seleccionar-con-email').addEventListener('click', function() {
                    document.querySelectorAll('.check-deuda').forEach(cb => {
                        cb.checked = cb.dataset.tieneEmail === '1';
                    });
                    updateCount();
                });

                document.getElementById('btn-deseleccionar').addEventListener('click', function() {
                    document.querySelectorAll('.check-deuda').forEach(cb => cb.checked = false);
                    checkAll.checked = false;
                    updateCount();
                });

                document.getElementById('form-envio').addEventListener('submit', function(e) {
                    const checked = document.querySelectorAll('.check-deuda:checked').length;
                    if (checked === 0) {
                        e.preventDefault();
                        alert('Debe seleccionar al menos un recibo para enviar.');
                        return;
                    }
                    if (!confirm('¿Esta seguro de enviar ' + checked + ' recibo(s) por correo electronico?')) {
                        e.preventDefault();
                    }
                });
            });
        </script>
        @endpush
    @elseif(request()->has('edificio_id'))
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-check-circle text-4xl text-green-400 mb-4 block"></i>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin recibos pendientes</h3>
                <p class="text-slate_custom-400">No se encontraron recibos pendientes para el edificio y periodo seleccionados.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-mail-bulk text-4xl text-slate_custom-300 mb-4 block"></i>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Envio Masivo de Recibos</h3>
                <p class="text-slate_custom-400">Seleccione un edificio y periodo para ver los recibos disponibles para enviar.</p>
            </div>
        </div>
    @endif
</x-app-layout>
