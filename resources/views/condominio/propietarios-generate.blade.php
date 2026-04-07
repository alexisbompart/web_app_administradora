<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Generar Propietarios y Usuarios</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Crear usuarios y propietarios desde las afiliaciones de pago integral</p>
            </div>
            <a href="{{ route('condominio.propietarios.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
        </div>
    </x-slot>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
    @endif

    {{-- PREVIEW --}}
    @if(isset($toCreate))
    <div x-data="generateProcess()" class="space-y-6">

        {{-- Info cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Por crear</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-user-plus text-green-600"></i></div>
                </div>
                <div class="stat-value text-green-600">{{ number_format(count($toCreate)) }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Propietarios + Usuarios nuevos</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Omitidos (preview)</div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-forward text-amber-600"></i></div>
                </div>
                <div class="stat-value text-amber-600">{{ number_format(count($skipped)) }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Duplicados o invalidos</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Fuente</div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-blue-600"></i></div>
                </div>
                <div class="stat-value text-blue-600">{{ number_format(count($toCreate) + count($skipped)) }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Total afiliaciones procesadas</p>
            </div>
        </div>

        {{-- Explicacion --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm" x-show="!running && !finished">
            <i class="fas fa-info-circle mr-2"></i>
            Se creara un <strong>Usuario</strong> (email como login, cedula como clave) y un <strong>Propietario</strong> por cada afiliacion. El rol asignado sera <strong>cliente-propietario</strong>.
        </div>

        @if(count($toCreate) > 0)

        {{-- ═══════ BARRA DE PROGRESO ═══════ --}}
        <div x-show="running || finished" x-transition class="card">
            <div class="card-body space-y-4">
                {{-- Header progreso --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="finished ? 'bg-green-100' : 'bg-burgundy-800/10'">
                            <i class="fas" :class="finished ? 'fa-check-circle text-green-600' : 'fa-spinner fa-spin text-burgundy-800'"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-navy-800" x-text="finished ? 'Generacion completada' : 'Generando propietarios y usuarios...'"></h3>
                            <p class="text-xs text-slate_custom-400" x-text="statusText"></p>
                        </div>
                    </div>
                    <span class="text-2xl font-heading font-bold" :class="finished ? 'text-green-600' : 'text-burgundy-800'" x-text="percent + '%'"></span>
                </div>

                {{-- Barra --}}
                <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden">
                    <div class="h-4 rounded-full transition-all duration-500 ease-out"
                         :class="finished ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-burgundy-800 to-navy-800'"
                         :style="'width: ' + percent + '%'">
                        <div class="h-full w-full rounded-full" :class="!finished && 'animate-pulse'" style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);"></div>
                    </div>
                </div>

                {{-- Detalles en tiempo real --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-slate-50 rounded-xl px-4 py-3 text-center">
                        <p class="text-lg font-heading font-bold text-navy-800" x-text="processed.toLocaleString()"></p>
                        <p class="text-[11px] text-slate_custom-400 font-medium">Procesados</p>
                    </div>
                    <div class="bg-green-50 rounded-xl px-4 py-3 text-center">
                        <p class="text-lg font-heading font-bold text-green-600" x-text="totalCreated.toLocaleString()"></p>
                        <p class="text-[11px] text-slate_custom-400 font-medium">Creados</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl px-4 py-3 text-center">
                        <p class="text-lg font-heading font-bold text-amber-600" x-text="totalSkipped.toLocaleString()"></p>
                        <p class="text-[11px] text-slate_custom-400 font-medium">Omitidos</p>
                    </div>
                    <div class="bg-red-50 rounded-xl px-4 py-3 text-center">
                        <p class="text-lg font-heading font-bold text-red-600" x-text="totalErrors.toLocaleString()"></p>
                        <p class="text-[11px] text-slate_custom-400 font-medium">Errores</p>
                    </div>
                </div>

                {{-- Registro actual --}}
                <div x-show="currentName && !finished" class="flex items-center gap-2 text-xs text-slate_custom-400">
                    <i class="fas fa-user text-slate_custom-300"></i>
                    <span>Procesando: <strong class="text-navy-800" x-text="currentName"></strong></span>
                </div>
            </div>
        </div>

        {{-- Errores acumulados --}}
        <template x-if="finished && allErrors.length > 0">
            <div class="card" x-data="{ show: false }">
                <div class="card-header cursor-pointer" @click="show = !show">
                    <h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full">
                        <span><i class="fas fa-exclamation-triangle mr-2"></i>Errores (<span x-text="allErrors.length"></span>)</span>
                        <i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </h3>
                </div>
                <div class="card-body p-0" x-show="show" x-transition>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto">
                        <table class="table-custom">
                            <thead><tr><th>Cedula</th><th>Razon</th></tr></thead>
                            <tbody>
                                <template x-for="err in allErrors.slice(0, 100)" :key="err.cedula">
                                    <tr>
                                        <td class="text-xs font-medium" x-text="err.cedula"></td>
                                        <td class="text-red-600 text-xs" x-text="err.reason"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>

        {{-- Botones finales --}}
        <div x-show="finished" x-transition class="flex gap-3">
            <a href="{{ route('condominio.propietarios.index') }}" class="btn-primary"><i class="fas fa-users mr-2"></i>Ver Propietarios</a>
            <a href="{{ route('condominio.propietarios.generate.preview') }}" class="btn-secondary"><i class="fas fa-redo mr-2"></i>Generar mas</a>
        </div>

        {{-- Preview table --}}
        <div x-show="!running && !finished" x-transition class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-eye mr-2 text-burgundy-800"></i>Vista previa ({{ min(count($toCreate), 50) }} de {{ count($toCreate) }})
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cedula (clave)</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email (usuario)</th>
                                <th>Telefono</th>
                                <th>Apto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($toCreate, 0, 50) as $i => $row)
                            <tr>
                                <td class="text-xs text-slate_custom-400">{{ $i + 1 }}</td>
                                <td class="font-medium text-navy-800 text-xs">{{ $row['cedula'] }}</td>
                                <td class="text-sm">{{ $row['nombres'] }}</td>
                                <td class="text-sm">{{ $row['apellidos'] }}</td>
                                <td class="text-xs text-blue-600">{{ $row['email'] }}</td>
                                <td class="text-xs">{{ $row['telefono'] ?? '--' }}</td>
                                <td class="text-xs">{{ $row['apartamento_id'] ? 'ID:'.$row['apartamento_id'] : '--' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Skipped --}}
        @if(count($skipped) > 0)
        <div x-show="!running && !finished" class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer" @click="show = !show">
                <h3 class="text-sm font-heading font-semibold text-amber-600 flex items-center justify-between w-full">
                    <span><i class="fas fa-forward mr-2"></i>Omitidos ({{ count($skipped) }})</span>
                    <i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="show" x-transition>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="table-custom">
                        <thead><tr><th>Cedula</th><th>Nombre</th><th>Razon</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($skipped, 0, 100) as $s)
                            <tr>
                                <td class="text-xs font-medium">{{ $s['cedula'] }}</td>
                                <td class="text-xs">{{ $s['nombre'] }}</td>
                                <td class="text-xs text-amber-600">{{ $s['razon'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Boton Iniciar --}}
        <div x-show="!running && !finished" x-transition class="flex items-center gap-3">
            <button @click="startGeneration()" class="btn-primary">
                <i class="fas fa-users-cog mr-2"></i>Generar {{ number_format(count($toCreate)) }} Propietarios y Usuarios
            </button>
            <a href="{{ route('condominio.propietarios.index') }}" class="btn-secondary">Cancelar</a>
        </div>

        @else
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            No hay afiliaciones nuevas para generar. Todos los registros ya tienen propietario/usuario creado o no cumplen los requisitos.
        </div>
        @endif
    </div>

    <script>
    function generateProcess() {
        return {
            running: false,
            finished: false,
            percent: 0,
            processed: 0,
            totalCreated: 0,
            totalSkipped: 0,
            totalErrors: 0,
            allErrors: [],
            currentName: '',
            statusText: '',
            total: {{ count($toCreate) }},

            async startGeneration() {
                if (!confirm('Se crearan hasta {{ count($toCreate) }} usuarios y propietarios. ¿Desea continuar?')) return;

                this.running = true;
                this.finished = false;
                this.percent = 0;
                this.processed = 0;
                this.totalCreated = 0;
                this.totalSkipped = 0;
                this.totalErrors = 0;
                this.allErrors = [];

                let offset = 0;

                while (true) {
                    this.statusText = 'Procesando lote ' + (Math.floor(offset / 50) + 1) + ' de ' + Math.ceil(this.total / 50) + '...';

                    try {
                        const response = await fetch('{{ route("condominio.propietarios.generate.batch") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ offset: offset })
                        });

                        const data = await response.json();

                        if (data.error) {
                            this.statusText = 'Error: ' + data.error;
                            this.running = false;
                            return;
                        }

                        this.totalCreated += data.created;
                        this.totalSkipped += data.skipped;
                        this.totalErrors += data.errors.length;
                        this.allErrors = this.allErrors.concat(data.errors);
                        this.processed = data.processed;
                        this.percent = data.percent;
                        this.currentName = data.current_name;

                        if (data.finished) {
                            this.finished = true;
                            this.running = false;
                            this.percent = 100;
                            this.statusText = 'Listo. ' + this.totalCreated.toLocaleString() + ' propietarios y usuarios creados exitosamente.';
                            return;
                        }

                        offset = data.processed;
                    } catch (err) {
                        this.statusText = 'Error de conexion. Intente nuevamente.';
                        this.running = false;
                        return;
                    }
                }
            }
        };
    }
    </script>
    @endif
</x-app-layout>
