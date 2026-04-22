<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Dashboard</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Resumen general del sistema de condominio</p>
            </div>
            <div class="text-sm text-slate_custom-400">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ now()->format('d M, Y') }}
            </div>
        </div>
    </x-slot>

    <!-- Modal Extrajudicial -->
    <div
        x-data="{
            open: false,
            edificioAbierto: null,
            todosEdificios: @js($extrajudicialData['por_edificio']),
            porPagina: 10,
            paginaActual: 1,
            get edificios() {
                return this.todosEdificios.slice(
                    (this.paginaActual - 1) * this.porPagina,
                    this.paginaActual * this.porPagina
                );
            },
            get totalPaginas() {
                return Math.ceil(this.todosEdificios.length / this.porPagina);
            },
            irPagina(n) {
                if (n < 1 || n > this.totalPaginas) return;
                this.paginaActual = n;
                this.edificioAbierto = null;
                this.$nextTick(() => this.$refs.cuerpoModal.scrollTop = 0);
            },
            toggleEdificio(id) {
                this.edificioAbierto = this.edificioAbierto === id ? null : id;
            }
        }"
        x-on:open-extrajudicial.window="open = true; paginaActual = 1; edificioAbierto = null"
    >
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false; edificioAbierto = null"></div>

            <!-- Panel -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col z-10">

                <!-- Header modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate_custom-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-gavel text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-navy-800 text-base">Apartamentos en Extrajudicial</h3>
                            <p class="text-xs text-slate_custom-400">Más de {{ $extrajudicialData['umbral_meses'] }} meses de deuda pendiente &middot; Click en edificio para ver detalle</p>
                        </div>
                    </div>
                    <button @click="open = false; edificioAbierto = null"
                            class="w-8 h-8 rounded-lg hover:bg-slate_custom-100 flex items-center justify-center text-slate_custom-400 hover:text-navy-800 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Cuerpo scrollable -->
                <div x-ref="cuerpoModal" class="overflow-y-auto flex-1 px-6 py-4 space-y-3">
                    <template x-if="edificios.length === 0">
                        <div class="text-center py-12 text-slate_custom-400">
                            <i class="fas fa-check-circle text-4xl text-green-400 mb-3 block"></i>
                            <p class="font-medium">No hay apartamentos en extrajudicial</p>
                            <p class="text-sm mt-1">Ningún apartamento supera los {{ $extrajudicialData['umbral_meses'] }} meses de deuda</p>
                        </div>
                    </template>

                    <template x-for="edif in edificios" :key="edif.edificio_id">
                        <div class="border border-slate_custom-200 rounded-xl overflow-hidden">
                            <!-- Fila edificio (click para abrir/cerrar) -->
                            <button
                                class="w-full flex items-center justify-between px-4 py-3 bg-slate_custom-50 hover:bg-burgundy-800/5 transition text-left"
                                @click="toggleEdificio(edif.edificio_id)">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-building text-navy-800 text-sm w-4"></i>
                                    <div>
                                        <span class="font-semibold text-navy-800 text-sm" x-text="edif.edificio_nombre"></span>
                                        <span class="ml-2 text-xs text-slate_custom-400">
                                            (<span x-text="edif.total_aptos"></span> aptos)
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full"
                                          x-text="'Bs ' + Number(edif.total_saldo).toLocaleString('es-VE', {minimumFractionDigits:2})"></span>
                                    <i class="fas text-slate_custom-400 text-xs transition-transform duration-200"
                                       :class="edificioAbierto === edif.edificio_id ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </div>
                            </button>

                            <!-- Detalle apartamentos -->
                            <div x-show="edificioAbierto === edif.edificio_id"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="divide-y divide-slate_custom-100">
                                <template x-for="apto in edif.apartamentos" :key="apto.num_apto">
                                    <div class="flex items-center justify-between px-6 py-2.5 hover:bg-red-50/40 transition">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-door-open text-slate_custom-300 text-xs w-4"></i>
                                            <span class="text-sm font-medium text-navy-800" x-text="'Apto ' + apto.num_apto"></span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-xs text-slate_custom-400">
                                                <i class="fas fa-calendar-times text-red-400 mr-1"></i>
                                                <span x-text="apto.meses_deuda + ' meses'"></span>
                                            </span>
                                            <span class="text-xs font-bold text-red-600"
                                                  x-text="'Bs ' + Number(apto.total_saldo).toLocaleString('es-VE', {minimumFractionDigits:2})"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer: paginador + cerrar -->
                <div class="px-6 py-3 border-t border-slate_custom-100 bg-slate_custom-50 rounded-b-2xl flex items-center justify-between gap-4 flex-wrap">

                    <!-- Info total -->
                    <span class="text-xs text-slate_custom-400 whitespace-nowrap">
                        Total: <strong class="text-navy-800">{{ $extrajudicialData['total_apartamentos'] }}</strong> aptos &middot;
                        Pág. <span x-text="paginaActual"></span> de <span x-text="totalPaginas"></span>
                    </span>

                    <!-- Controles paginador -->
                    <div class="flex items-center gap-1">
                        <!-- Primera -->
                        <button @click="irPagina(1)" :disabled="paginaActual === 1"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition
                                       disabled:opacity-30 disabled:cursor-not-allowed
                                       hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                        <!-- Anterior -->
                        <button @click="irPagina(paginaActual - 1)" :disabled="paginaActual === 1"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition
                                       disabled:opacity-30 disabled:cursor-not-allowed
                                       hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-left"></i>
                        </button>

                        <!-- Números de página (ventana de 5) -->
                        <template x-for="n in Array.from({length: totalPaginas}, (_,i) => i+1).filter(n =>
                            n === 1 || n === totalPaginas ||
                            (n >= paginaActual - 1 && n <= paginaActual + 1)
                        )" :key="n">
                            <button @click="irPagina(n)"
                                    :class="paginaActual === n
                                        ? 'bg-burgundy-800 text-white'
                                        : 'text-slate_custom-500 hover:bg-navy-800 hover:text-white'"
                                    class="w-7 h-7 rounded-lg text-xs font-semibold flex items-center justify-center transition"
                                    x-text="n">
                            </button>
                        </template>

                        <!-- Siguiente -->
                        <button @click="irPagina(paginaActual + 1)" :disabled="paginaActual === totalPaginas"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition
                                       disabled:opacity-30 disabled:cursor-not-allowed
                                       hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-right"></i>
                        </button>
                        <!-- Última -->
                        <button @click="irPagina(totalPaginas)" :disabled="paginaActual === totalPaginas"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition
                                       disabled:opacity-30 disabled:cursor-not-allowed
                                       hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </div>

                    <button @click="open = false; edificioAbierto = null"
                            class="px-4 py-1.5 text-xs font-semibold bg-navy-800 text-white rounded-lg hover:bg-burgundy-800 transition whitespace-nowrap">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Deudas Pendientes -->
    <div
        x-data="{
            open: false,
            edificioAbierto: null,
            todosEdificios: @js($deudasData['por_edificio']),
            porPagina: 10,
            paginaActual: 1,
            get edificios() {
                return this.todosEdificios.slice(
                    (this.paginaActual - 1) * this.porPagina,
                    this.paginaActual * this.porPagina
                );
            },
            get totalPaginas() {
                return Math.ceil(this.todosEdificios.length / this.porPagina);
            },
            irPagina(n) {
                if (n < 1 || n > this.totalPaginas) return;
                this.paginaActual = n;
                this.edificioAbierto = null;
                this.$nextTick(() => this.$refs.cuerpoModalDeudas.scrollTop = 0);
            },
            toggleEdificio(id) {
                this.edificioAbierto = this.edificioAbierto === id ? null : id;
            }
        }"
        x-on:open-deudas.window="open = true; paginaActual = 1; edificioAbierto = null"
    >
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false; edificioAbierto = null"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col z-10">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate_custom-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-navy-800 text-base">Deudas Pendientes</h3>
                            <p class="text-xs text-slate_custom-400">
                                {{ $deudasData['total_apartamentos'] }} aptos &middot;
                                {{ number_format($deudasData['total_saldo'], 2, ',', '.') }} Bs &middot;
                                Click en edificio para ver detalle
                            </p>
                        </div>
                    </div>
                    <button @click="open = false; edificioAbierto = null"
                            class="w-8 h-8 rounded-lg hover:bg-slate_custom-100 flex items-center justify-center text-slate_custom-400 hover:text-navy-800 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Cuerpo -->
                <div x-ref="cuerpoModalDeudas" class="overflow-y-auto flex-1 px-6 py-4 space-y-3">
                    <template x-if="todosEdificios.length === 0">
                        <div class="text-center py-12 text-slate_custom-400">
                            <i class="fas fa-check-circle text-4xl text-green-400 mb-3 block"></i>
                            <p class="font-medium">No hay deudas pendientes</p>
                        </div>
                    </template>

                    <template x-for="edif in edificios" :key="edif.edificio_id">
                        <div class="border border-slate_custom-200 rounded-xl overflow-hidden">
                            <!-- Fila edificio -->
                            <button
                                class="w-full flex items-center justify-between px-4 py-3 bg-slate_custom-50 hover:bg-red-50 transition text-left"
                                @click="toggleEdificio(edif.edificio_id)">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-building text-navy-800 text-sm w-4"></i>
                                    <div>
                                        <span class="font-semibold text-navy-800 text-sm" x-text="edif.edificio_nombre"></span>
                                        <span class="ml-2 text-xs text-slate_custom-400">
                                            (<span x-text="edif.total_aptos"></span> aptos &middot; <span x-text="edif.total_meses"></span> meses)
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full"
                                          x-text="'Bs ' + Number(edif.total_saldo).toLocaleString('es-VE', {minimumFractionDigits:2})"></span>
                                    <i class="fas text-slate_custom-400 text-xs transition-transform duration-200"
                                       :class="edificioAbierto === edif.edificio_id ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </div>
                            </button>

                            <!-- Detalle apartamentos con paginador propio -->
                            <div x-show="edificioAbierto === edif.edificio_id"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-data="{
                                     pApto: 1,
                                     ppApto: 8,
                                     get aptos() {
                                         return edif.apartamentos.slice((this.pApto-1)*this.ppApto, this.pApto*this.ppApto);
                                     },
                                     get totalPApto() {
                                         return Math.ceil(edif.apartamentos.length / this.ppApto);
                                     },
                                     irApto(n) {
                                         if (n < 1 || n > this.totalPApto) return;
                                         this.pApto = n;
                                     }
                                 }"
                                 x-init="$watch('edificioAbierto', v => { if(v === edif.edificio_id) pApto = 1; })">

                                <!-- Filas de apartamentos -->
                                <template x-for="apto in aptos" :key="apto.num_apto">
                                    <div class="flex items-center justify-between px-6 py-2.5 border-b border-slate_custom-100 hover:bg-red-50/40 transition last:border-0">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-door-open text-slate_custom-300 text-xs w-4"></i>
                                            <span class="text-sm font-medium text-navy-800" x-text="'Apto ' + apto.num_apto"></span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-xs text-slate_custom-400">
                                                <i class="fas fa-calendar-times text-red-400 mr-1"></i>
                                                <span x-text="apto.meses_deuda + ' meses'"></span>
                                            </span>
                                            <span class="text-xs font-bold text-red-600"
                                                  x-text="'Bs ' + Number(apto.total_saldo).toLocaleString('es-VE', {minimumFractionDigits:2})"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Paginador apartamentos (solo si hay más de una página) -->
                                <div x-show="totalPApto > 1"
                                     class="flex items-center justify-between px-4 py-2 bg-slate_custom-50 border-t border-slate_custom-100">
                                    <span class="text-xs text-slate_custom-400">
                                        Pág. <span x-text="pApto"></span> de <span x-text="totalPApto"></span>
                                        &middot; <span x-text="edif.apartamentos.length"></span> aptos
                                    </span>
                                    <div class="flex items-center gap-1">
                                        <button @click="irApto(1)" :disabled="pApto === 1"
                                                class="w-6 h-6 rounded text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                                            <i class="fas fa-angle-double-left"></i>
                                        </button>
                                        <button @click="irApto(pApto - 1)" :disabled="pApto === 1"
                                                class="w-6 h-6 rounded text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                                            <i class="fas fa-angle-left"></i>
                                        </button>
                                        <template x-for="n in Array.from({length: totalPApto}, (_,i) => i+1).filter(n =>
                                            n === 1 || n === totalPApto || (n >= pApto - 1 && n <= pApto + 1)
                                        )" :key="n">
                                            <button @click="irApto(n)"
                                                    :class="pApto === n ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-navy-800 hover:text-white'"
                                                    class="w-6 h-6 rounded text-xs font-semibold flex items-center justify-center transition"
                                                    x-text="n">
                                            </button>
                                        </template>
                                        <button @click="irApto(pApto + 1)" :disabled="pApto === totalPApto"
                                                class="w-6 h-6 rounded text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                                            <i class="fas fa-angle-right"></i>
                                        </button>
                                        <button @click="irApto(totalPApto)" :disabled="pApto === totalPApto"
                                                class="w-6 h-6 rounded text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                                            <i class="fas fa-angle-double-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer paginador -->
                <div class="px-6 py-3 border-t border-slate_custom-100 bg-slate_custom-50 rounded-b-2xl flex items-center justify-between gap-4 flex-wrap">
                    <span class="text-xs text-slate_custom-400 whitespace-nowrap">
                        Total: <strong class="text-navy-800">{{ $deudasData['total_apartamentos'] }}</strong> aptos &middot;
                        Pág. <span x-text="paginaActual"></span> de <span x-text="totalPaginas"></span>
                    </span>

                    <div class="flex items-center gap-1">
                        <button @click="irPagina(1)" :disabled="paginaActual === 1"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                        <button @click="irPagina(paginaActual - 1)" :disabled="paginaActual === 1"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-left"></i>
                        </button>
                        <template x-for="n in Array.from({length: totalPaginas}, (_,i) => i+1).filter(n =>
                            n === 1 || n === totalPaginas || (n >= paginaActual - 1 && n <= paginaActual + 1)
                        )" :key="n">
                            <button @click="irPagina(n)"
                                    :class="paginaActual === n ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-navy-800 hover:text-white'"
                                    class="w-7 h-7 rounded-lg text-xs font-semibold flex items-center justify-center transition"
                                    x-text="n">
                            </button>
                        </template>
                        <button @click="irPagina(paginaActual + 1)" :disabled="paginaActual === totalPaginas"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-right"></i>
                        </button>
                        <button @click="irPagina(totalPaginas)" :disabled="paginaActual === totalPaginas"
                                class="w-7 h-7 rounded-lg text-xs flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed hover:bg-navy-800 hover:text-white text-slate_custom-500">
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </div>

                    <button @click="open = false; edificioAbierto = null"
                            class="px-4 py-1.5 text-xs font-semibold bg-navy-800 text-white rounded-lg hover:bg-burgundy-800 transition whitespace-nowrap">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Edificios</div>
                <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-city text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Condominio\Edificio::count() }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Registrados en el sistema</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Apartamentos</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-door-open text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Condominio\Apartamento::count() }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Total de unidades</p>
        </div>

        @php
            $morososQuery = \App\Models\Financiero\CondDeudaApto::whereHas('edificio', function($q) {
                $q->where('activo', true);
            })->where(function($q) {
                $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01');
            })->where(function($q) {
                $q->whereNull('serial')->orWhere('serial', 'N');
            });
            $totalDeudas = $morososQuery->count();
            $totalMontoPendiente = $morososQuery->sum('saldo');
            $aptosEnMora = (clone $morososQuery)->distinct('apartamento_id')->count('apartamento_id');
        @endphp
        <button @click="$dispatch('open-deudas')"
                class="stat-card hover:border-red-400 transition text-left group cursor-pointer w-full">
            <div class="flex items-center justify-between">
                <div class="stat-label">Deudas Pendientes</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ $totalDeudas }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">
                {{ number_format($totalMontoPendiente, 2, ',', '.') }} Bs en {{ $aptosEnMora }} aptos &middot;
                <span class="text-red-500 group-hover:underline">Ver detalle</span>
            </p>
        </button>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagos Aprobados</div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="stat-value text-green-600">{{ number_format($totalPagosAprobados, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">Total cobrado</p>
        </div>
        <a href="{{ route('financiero.cobranza.pagos-pendientes') }}" class="stat-card hover:border-amber-400 transition">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagos Pendientes</div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
            <div class="stat-value text-amber-600">{{ $countPagosPendientes }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Por aprobar</p>
        </a>

        {{-- KPI Extrajudicial --}}
        <button
            @click="$dispatch('open-extrajudicial')"
            class="stat-card hover:border-red-400 transition text-left group cursor-pointer w-full">
            <div class="flex items-center justify-between">
                <div class="stat-label">Extrajudicial</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition">
                    <i class="fas fa-gavel text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ $extrajudicialData['total_apartamentos'] }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">
                +{{ $extrajudicialData['umbral_meses'] }} meses deuda &middot; <span class="text-red-500 group-hover:underline">Ver detalle</span>
            </p>
        </button>
    </div>

    <!-- Second row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Fondos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-piggy-bank mr-2 text-burgundy-800"></i>Fondos
                </h3>
            </div>
            <div class="card-body space-y-4">
                @foreach(\App\Models\Financiero\Fondo::where('activo', true)->get() as $fondo)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-navy-800">{{ $fondo->nombre }}</p>
                        <p class="text-xs text-slate_custom-400 capitalize">{{ $fondo->tipo }}</p>
                    </div>
                    <span class="text-sm font-bold text-navy-800">
                        {{ number_format($fondo->saldo_actual, 2, ',', '.') }} Bs
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Morosos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>Apartamentos Morosos
                </h3>
            </div>
            <div class="card-body">
                @php
                    $morosos = \App\Models\Financiero\CondDeudaApto::whereHas('edificio', function($q) {
                            $q->where('activo', true);
                        })
                        ->where(function($q) {
                            $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01');
                        })
                        ->where(function($q) {
                            $q->whereNull('serial')->orWhere('serial', 'N');
                        })
                        ->selectRaw('apartamento_id, COUNT(*) as meses, SUM(saldo) as total_deuda')
                        ->groupBy('apartamento_id')
                        ->orderByDesc('total_deuda')
                        ->take(10)
                        ->get();
                @endphp
                @forelse($morosos as $moroso)
                    @php $apto = \App\Models\Condominio\Apartamento::with('edificio')->find($moroso->apartamento_id); @endphp
                    <div class="flex items-center justify-between py-2 border-b border-slate_custom-200 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-navy-800">{{ $apto?->edificio?->nombre }} - {{ $apto?->num_apto }}</p>
                            <p class="text-xs text-red-500">{{ $moroso->meses }} {{ $moroso->meses == 1 ? 'mes' : 'meses' }} pendiente{{ $moroso->meses > 1 ? 's' : '' }}</p>
                        </div>
                        <span class="badge-danger text-xs">{{ number_format($moroso->total_deuda, 2, ',', '.') }} Bs</span>
                    </div>
                @empty
                    <p class="text-sm text-slate_custom-400 text-center py-4">No hay morosos registrados</p>
                @endforelse
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>Acceso Rápido
                </h3>
            </div>
            <div class="card-body grid grid-cols-2 gap-3">
                @can('cobranza.registrar-pago')
                <a href="{{ route('financiero.cobranza.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-hand-holding-usd text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Registrar Pago</span>
                </a>
                @endcan
                @can('informes.ver')
                <a href="{{ route('servicios.informes.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-chart-bar text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Ver Informes</span>
                </a>
                @endcan
                @can('proveedores.ver')
                <a href="{{ route('proveedores.facturas.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-file-invoice-dollar text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Facturas</span>
                </a>
                @endcan
                @can('personal.ver')
                <a href="{{ route('personal.nominas.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-money-check-alt text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Nóminas</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Pagos por mes + Pendientes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Gráfico pagos por mes --}}
        <div class="card lg:col-span-2">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-chart-bar mr-2 text-burgundy-800"></i>Pagos Recibidos por Mes
                </h3>
                <span class="text-xs text-slate_custom-400">Últimos 12 meses</span>
            </div>
            <div class="card-body">
                <canvas id="chartPagosMes" height="110"></canvas>
            </div>
        </div>

        {{-- Pagos pendientes --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-clock mr-2 text-amber-500"></i>Pagos por Aprobar
                </h3>
                @if($countPagosPendientes > 0)
                <span class="text-xs bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">{{ $countPagosPendientes }}</span>
                @endif
            </div>
            <div class="card-body p-0">
                @forelse($pagosPendientes as $pago)
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate_custom-100 last:border-0 hover:bg-slate_custom-50 transition">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-navy-800 truncate">
                            {{ $pago->registradoPor?->name ?? 'Propietario' }}
                        </p>
                        <p class="text-xs text-slate_custom-400">
                            {{ $pago->fecha_pago?->format('d/m/Y') }}
                            · Ref: {{ $pago->numero_referencia }}
                        </p>
                        <p class="text-xs text-slate_custom-400 truncate">
                            @foreach($pago->condPagoAptos->unique('apartamento_id')->take(2) as $pa)
                                {{ $pa->apartamento?->num_apto }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right ml-3">
                        <p class="text-sm font-bold text-amber-600">{{ number_format($pago->monto_total, 2, ',', '.') }}</p>
                        <a href="{{ route('financiero.cobranza.pagos-pendientes') }}"
                           class="text-xs text-burgundy-800 hover:underline font-medium">Revisar</a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate_custom-400">
                    <i class="fas fa-check-circle text-2xl text-green-400 mb-2 block"></i>
                    <p class="text-sm">Sin pagos pendientes</p>
                </div>
                @endforelse
                @if($countPagosPendientes > 0)
                <div class="px-4 py-2 border-t border-slate_custom-100">
                    <a href="{{ route('financiero.cobranza.pagos-pendientes') }}" class="text-xs text-burgundy-800 font-semibold hover:underline">
                        Ver todos ({{ $countPagosPendientes }}) <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         SECCIÓN KPI CHARTS
    ══════════════════════════════════════ -->
    <div class="mb-2 mt-2">
        <p class="section-eyebrow">Indicadores de Gestión</p>
        <h2 class="text-xl font-heading font-bold text-navy-800 mt-1">Panel de Control KPI</h2>
        <div class="section-divider" style="margin:10px 0 0 0;"></div>
    </div>

    {{-- Fila 1: Estado apartamentos (dona) + Distribución morosos (barras) + Fondos (dona) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 mt-6">

        {{-- 1. Estado de apartamentos --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-door-open mr-2 text-burgundy-800"></i>Estado de Apartamentos
                </h3>
                <span class="text-xs text-slate_custom-400">Total: {{ $kpiData['estadoAptos']['total'] }}</span>
            </div>
            <div class="card-body flex flex-col items-center">
                <div class="relative w-48 h-48">
                    <canvas id="chartEstadoAptos"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-bold text-navy-800">{{ $kpiData['estadoAptos']['total'] }}</span>
                        <span class="text-xs text-slate_custom-400">aptos</span>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-3 mt-4">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-xs text-slate_custom-500">Al día <strong class="text-navy-800">{{ $kpiData['estadoAptos']['al_dia'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                        <span class="text-xs text-slate_custom-500">Morosos <strong class="text-navy-800">{{ $kpiData['estadoAptos']['morosos'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-600 inline-block"></span>
                        <span class="text-xs text-slate_custom-500">Extrajudicial <strong class="text-navy-800">{{ $kpiData['estadoAptos']['extrajudicial'] }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Morosos por rango de meses --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-calendar-times mr-2 text-red-500"></i>Morosos por Antigüedad
                </h3>
            </div>
            <div class="card-body">
                <canvas id="chartRangosMorosos" height="190"></canvas>
            </div>
        </div>

        {{-- 3. Saldo de fondos --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-piggy-bank mr-2 text-burgundy-800"></i>Distribución de Fondos
                </h3>
            </div>
            <div class="card-body flex flex-col items-center">
                @php $totalFondos = array_sum($kpiData['fondos']['values']); @endphp
                <div class="relative w-44 h-44">
                    <canvas id="chartFondos"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xs font-bold text-navy-800">{{ number_format($totalFondos, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate_custom-400">Bs total</span>
                    </div>
                </div>
                <div class="w-full mt-4 space-y-1.5">
                    @foreach($kpiData['fondos']['labels'] as $i => $label)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:{{ ['#273272','#680c3e','#d4a017','#2d8a4e'][$i % 4] }}"></span>
                            <span class="text-xs text-slate_custom-500">{{ $label }}</span>
                        </div>
                        <span class="text-xs font-semibold text-navy-800">{{ number_format($kpiData['fondos']['values'][$i], 2, ',', '.') }} Bs</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 2: Top edificios (barras horizontales) + Eficiencia cobranza (dona) + Pagos 6 meses (línea) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- 4. Top edificios con mayor deuda --}}
        <div class="card lg:col-span-2">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-building mr-2 text-navy-800"></i>Top Edificios con Mayor Deuda
                </h3>
                <span class="text-xs text-slate_custom-400">Saldo pendiente (Bs)</span>
            </div>
            <div class="card-body">
                <canvas id="chartTopEdificios" height="160"></canvas>
            </div>
        </div>

        {{-- 5. Eficiencia de cobranza --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-percentage mr-2 text-green-600"></i>Eficiencia de Cobranza
                </h3>
            </div>
            <div class="card-body flex flex-col items-center">
                @php
                    $deuda   = $kpiData['cobranza']['deuda'];
                    $pagado  = $kpiData['cobranza']['pagado'];
                    $total   = $deuda + $pagado;
                    $pct     = $total > 0 ? round($pagado / $total * 100, 1) : 0;
                @endphp
                <div class="relative w-44 h-44">
                    <canvas id="chartCobranza"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl font-bold text-green-600">{{ $pct }}%</span>
                        <span class="text-xs text-slate_custom-400">recaudado</span>
                    </div>
                </div>
                <div class="w-full mt-5 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                            <span class="text-xs text-slate_custom-500">Cobrado</span>
                        </div>
                        <span class="text-xs font-semibold text-green-700">{{ number_format($pagado, 2, ',', '.') }} Bs</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>
                            <span class="text-xs text-slate_custom-500">Pendiente</span>
                        </div>
                        <span class="text-xs font-semibold text-red-600">{{ number_format($deuda, 2, ',', '.') }} Bs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 3: Tendencia de pagos últimos 6 meses (ancho completo) --}}
    <div class="grid grid-cols-1 gap-6 mb-6">
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-chart-line mr-2 text-burgundy-800"></i>Tendencia de Pagos — Últimos 6 Meses
                </h3>
                <span class="text-xs text-slate_custom-400">Monto (Bs) y cantidad de pagos</span>
            </div>
            <div class="card-body">
                <canvas id="chartTendencia" height="80"></canvas>
            </div>
        </div>
    </div>

    {{-- Fila 4: Montos cobrados por mes — Año actual --}}
    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-calendar-check mr-2 text-burgundy-800"></i>Montos Cobrados por Mes — {{ $kpiData['pagosAnio']['anio'] }}
                    </h3>
                </div>
                <div class="flex items-center gap-4">
                    @php
                        $totalAnio   = array_sum($kpiData['pagosAnio']['montos']);
                        $totalPagosN = array_sum($kpiData['pagosAnio']['counts']);
                    @endphp
                    <span class="text-xs text-slate_custom-400">
                        Total: <strong class="text-navy-800">{{ number_format($totalAnio, 2, ',', '.') }} Bs</strong>
                        en <strong class="text-navy-800">{{ number_format($totalPagosN, 0, ',', '.') }}</strong> pagos
                    </span>
                </div>
            </div>
            <div class="card-body">
                <canvas id="chartPagosAnio" height="75"></canvas>
            </div>
        </div>
    </div>

    {{-- Fila 5: Comparativo por compañía --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Líneas múltiples: edificios por compañía mes a mes --}}
        <div class="card lg:col-span-2">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-sitemap mr-2 text-navy-800"></i>Edificios Facturados por Compañía — {{ $kpiData['companiasMeses']['anio'] }}
                </h3>
                <span class="text-xs text-slate_custom-400">Cantidad de edificios por mes</span>
            </div>
            <div class="card-body">
                <canvas id="chartCompaniaEdif" height="160"></canvas>
            </div>
        </div>

        {{-- Dona: distribución de edificios --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-chart-pie mr-2 text-burgundy-800"></i>Distribución de Edificios
                </h3>
                @php
                    $totalEdif   = array_sum($kpiData['companiaData']['edificios']);
                    $coloresComp = ['#273272','#680c3e','#d4a017','#2d8a4e','#7c3aed'];
                @endphp
                <span class="text-xs font-bold text-navy-800 bg-navy-800/8 px-2 py-0.5 rounded-full">
                    {{ $totalEdif }} total
                </span>
            </div>
            <div class="card-body">
                {{-- Dona centrada --}}
                <div class="flex justify-center mb-4">
                    <div class="relative w-36 h-36">
                        <canvas id="chartCompaniaDona"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-xl font-bold text-navy-800">{{ $totalEdif }}</span>
                            <span class="text-[10px] text-slate_custom-400 leading-tight">edificios</span>
                        </div>
                    </div>
                </div>

                {{-- Leyenda con barra de progreso --}}
                <div class="space-y-3">
                    @foreach($kpiData['companiaData']['nombres_full'] as $i => $nombre)
                    @php
                        $edif = $kpiData['companiaData']['edificios'][$i];
                        $pct  = $totalEdif > 0 ? round($edif / $totalEdif * 100) : 0;
                        $color = $coloresComp[$i % 5];
                        // Abreviación legible del nombre
                        $partes = explode(' ', $nombre);
                        $abrev  = collect($partes)->filter(fn($p) => !in_array(strtolower($p), ['administradora','integral','c.a.','(',')','-']))->join(' ');
                        $etiq   = $abrev ?: $nombre;
                        $etiq   = strlen($etiq) > 22 ? substr($etiq, 0, 22).'…' : $etiq;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-sm flex-shrink-0" style="background:{{ $color }}"></span>
                                <span class="text-xs font-medium text-navy-800 truncate" title="{{ $nombre }}">{{ $etiq }}</span>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                <span class="text-xs font-bold text-navy-800">{{ $edif }}</span>
                                <span class="text-[10px] text-slate_custom-400 w-6 text-right">{{ $pct }}%</span>
                            </div>
                        </div>
                        <div class="w-full h-1.5 bg-slate_custom-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width:{{ $pct }}%; background:{{ $color }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 6: Morosos vs Total aptos por compañía (barras apiladas) --}}
    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-balance-scale mr-2 text-red-500"></i>Aptos Morosos vs Al Día por Compañía
                </h3>
                <span class="text-xs text-slate_custom-400">Comparativo de morosidad por compañía</span>
            </div>
            <div class="card-body">
                <canvas id="chartCompaniaMorosos" height="75"></canvas>
            </div>
        </div>
    </div>

    <!-- Info banner -->
    <div class="card bg-gradient-to-r from-burgundy-800 to-navy-800 text-white p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <div>
                <h3 class="font-heading font-bold text-lg text-white">Bienvenido, {{ Auth::user()->name }}</h3>
                <p class="text-white/70 text-sm mt-1">
                    Rol: <span class="font-semibold text-white">{{ Auth::user()->roles->first()?->name ?? 'Sin rol' }}</span>
                    &mdash; Sistema de Administración de Condominios v1.0
                </p>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const labels  = @json($mesesLabels);
        const montos  = @json($mesesMonto);
        const counts  = @json($mesesCantidad);

        new Chart(document.getElementById('chartPagosMes'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Monto (Bs)',
                        data: montos,
                        backgroundColor: 'rgba(127,0,55,0.75)',
                        borderColor: 'rgba(127,0,55,1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'yMonto',
                    },
                    {
                        label: 'Cantidad',
                        data: counts,
                        type: 'line',
                        borderColor: '#1e3a5f',
                        backgroundColor: 'rgba(30,58,95,0.1)',
                        pointBackgroundColor: '#1e3a5f',
                        pointRadius: 4,
                        tension: 0.3,
                        yAxisID: 'yCant',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.yAxisID === 'yMonto'
                                ? ' ' + ctx.parsed.y.toLocaleString('es-VE', {minimumFractionDigits:2}) + ' Bs'
                                : ' ' + ctx.parsed.y + ' pagos'
                        }
                    }
                },
                scales: {
                    yMonto: { position: 'left',  grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                    yCant:  { position: 'right', grid: { display: false },   ticks: { font: { size: 10 } } }
                }
            }
        });
    })();

    // ── Colores base ──
    const NAVY     = '#273272';
    const BURGUNDY = '#680c3e';
    const GOLD     = '#d4a017';
    const SLATE    = '#565872';

    // ── 1. Estado de Apartamentos (dona) ──
    new Chart(document.getElementById('chartEstadoAptos'), {
        type: 'doughnut',
        data: {
            labels: ['Al día', 'Morosos', 'Extrajudicial'],
            datasets: [{
                data: [
                    {{ $kpiData['estadoAptos']['al_dia'] }},
                    {{ $kpiData['estadoAptos']['morosos'] }},
                    {{ $kpiData['estadoAptos']['extrajudicial'] }}
                ],
                backgroundColor: ['#22c55e','#fbbf24','#dc2626'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' aptos' } }
            }
        }
    });

    // ── 2. Morosos por rango de antigüedad (barras) ──
    new Chart(document.getElementById('chartRangosMorosos'), {
        type: 'bar',
        data: {
            labels: @json($kpiData['rangos']['labels']),
            datasets: [{
                label: 'Apartamentos',
                data: @json($kpiData['rangos']['values']),
                backgroundColor: ['#22c55e','#fbbf24','#f97316','#ef4444','#7f1d1d'],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.y + ' aptos' } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, precision: 0 } }
            }
        }
    });

    // ── 3. Distribución de fondos (dona) ──
    new Chart(document.getElementById('chartFondos'), {
        type: 'doughnut',
        data: {
            labels: @json($kpiData['fondos']['labels']),
            datasets: [{
                data: @json($kpiData['fondos']['values']),
                backgroundColor: [NAVY, BURGUNDY, GOLD, '#2d8a4e'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + Number(ctx.parsed).toLocaleString('es-VE',{minimumFractionDigits:2}) + ' Bs'
                    }
                }
            }
        }
    });

    // ── 4. Top edificios con mayor deuda (barras horizontales) ──
    new Chart(document.getElementById('chartTopEdificios'), {
        type: 'bar',
        data: {
            labels: @json($kpiData['topEdificios']['labels']),
            datasets: [{
                label: 'Saldo deuda (Bs)',
                data: @json($kpiData['topEdificios']['values']),
                backgroundColor: 'rgba(104,12,62,0.80)',
                borderColor: BURGUNDY,
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => @json($kpiData['topEdificios']['nombres_completos'])[items[0].dataIndex],
                        label: ctx => [
                            ' Deuda: Bs ' + Number(ctx.parsed.x).toLocaleString('es-VE',{minimumFractionDigits:2}),
                            ' Aptos: ' + @json($kpiData['topEdificios']['aptos'])[ctx.dataIndex]
                        ]
                    }
                }
            },
            scales: {
                x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 9 }, callback: v => (v/1000000).toFixed(1)+'M' } },
                y: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // ── 5. Eficiencia de cobranza (dona) ──
    new Chart(document.getElementById('chartCobranza'), {
        type: 'doughnut',
        data: {
            labels: ['Cobrado', 'Pendiente'],
            datasets: [{
                data: [{{ $kpiData['cobranza']['pagado'] }}, {{ $kpiData['cobranza']['deuda'] }}],
                backgroundColor: ['#22c55e','#fca5a5'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': Bs ' + Number(ctx.parsed).toLocaleString('es-VE',{minimumFractionDigits:2})
                    }
                }
            }
        }
    });

    // ── Compañías: datos comunes ──
    const compLabels      = @json($kpiData['companiaData']['labels']);
    const compNombresFull = @json($kpiData['companiaData']['nombres_full']);
    const compEdificios   = @json($kpiData['companiaData']['edificios']);
    const compAptos       = @json($kpiData['companiaData']['aptos']);
    const compMorosos     = @json($kpiData['companiaData']['aptos_morosos']);
    const compSaldo       = @json($kpiData['companiaData']['saldo_deuda']);
    const compAlDia       = compAptos.map((a, i) => Math.max(0, a - compMorosos[i]));
    const coloresComp     = ['#273272','#680c3e','#d4a017','#2d8a4e','#7c3aed'];

    // ── Comp-1. Líneas múltiples: edificios por compañía mes a mes ──
    (function() {
        const mesesAnio   = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        const series      = @json($kpiData['companiasMeses']['series']);
        const mesActualIdx = new Date().getMonth(); // 0-based

        const paleta = [
            { border: '#273272', bg: 'rgba(39,50,114,0.12)'  },
            { border: '#680c3e', bg: 'rgba(104,12,62,0.12)'  },
            { border: '#d4a017', bg: 'rgba(212,160,23,0.12)' },
            { border: '#2d8a4e', bg: 'rgba(45,138,78,0.12)'  },
            { border: '#7c3aed', bg: 'rgba(124,58,237,0.12)' },
        ];

        const datasets = series.map((s, i) => ({
            label: s.nombre,
            data: s.data,
            borderColor: paleta[i % paleta.length].border,
            backgroundColor: paleta[i % paleta.length].bg,
            pointBackgroundColor: s.data.map((v, m) =>
                m === mesActualIdx ? '#fff' : paleta[i % paleta.length].border
            ),
            pointBorderColor: paleta[i % paleta.length].border,
            pointRadius: s.data.map((v, m) => v > 0 ? (m === mesActualIdx ? 6 : 4) : 0),
            pointBorderWidth: 2,
            tension: 0.35,
            fill: false,
        }));

        new Chart(document.getElementById('chartCompaniaEdif'), {
            type: 'line',
            data: { labels: mesesAnio, datasets },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 10 },
                            usePointStyle: true,
                            boxWidth: 8,
                            generateLabels: chart => chart.data.datasets.map((ds, i) => ({
                                text: ds.label.length > 30 ? ds.label.substring(0,30)+'…' : ds.label,
                                fillStyle: ds.borderColor,
                                strokeStyle: ds.borderColor,
                                pointStyle: 'circle',
                                datasetIndex: i,
                                hidden: !chart.isDatasetVisible(i),
                            }))
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const v = ctx.parsed.y;
                                return v > 0 ? ' ' + ctx.dataset.label.substring(0,28) + ': ' + v + ' edif.' : null;
                            },
                            afterBody: items => items[0].dataIndex === mesActualIdx ? ['── Mes en curso ──'] : []
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 10 }, precision: 0 },
                        title: { display: true, text: 'N° Edificios', font: { size: 10 } },
                        beginAtZero: true,
                    }
                }
            }
        });
    })();

    // ── Comp-2. Dona: distribución de edificios ──
    new Chart(document.getElementById('chartCompaniaDona'), {
        type: 'doughnut',
        data: {
            labels: compNombresFull,
            datasets: [{
                data: compEdificios,
                backgroundColor: coloresComp,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed + ' edificios (' + Math.round(ctx.parsed / compEdificios.reduce((a,b)=>a+b,0) * 100) + '%)'
                    }
                }
            }
        }
    });

    // ── Comp-3. Barras apiladas: morosos vs al día por compañía ──
    new Chart(document.getElementById('chartCompaniaMorosos'), {
        type: 'bar',
        data: {
            labels: compLabels,
            datasets: [
                {
                    label: 'Al día',
                    data: compAlDia,
                    backgroundColor: 'rgba(34,197,94,0.80)',
                    borderColor: '#16a34a',
                    borderWidth: 1,
                    borderRadius: { topLeft:0, topRight:0, bottomLeft:5, bottomRight:5 },
                    stack: 'aptos',
                },
                {
                    label: 'Morosos',
                    data: compMorosos,
                    backgroundColor: 'rgba(239,68,68,0.80)',
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    borderRadius: { topLeft:5, topRight:5, bottomLeft:0, bottomRight:0 },
                    stack: 'aptos',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { font:{size:11}, usePointStyle:true } },
                tooltip: {
                    callbacks: {
                        title: items => compNombresFull[items[0].dataIndex],
                        label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' aptos',
                        afterBody: items => {
                            const i = items[0].dataIndex;
                            const pct = compAptos[i] > 0 ? (compMorosos[i]/compAptos[i]*100).toFixed(1) : 0;
                            return [
                                ' Total: ' + compAptos[i] + ' aptos',
                                ' Morosidad: ' + pct + '%',
                                ' Saldo deuda: Bs ' + Number(compSaldo[i]).toLocaleString('es-VE',{minimumFractionDigits:2})
                            ];
                        }
                    }
                }
            },
            scales: {
                x: { stacked: true, grid:{ display:false }, ticks:{ font:{size:10} } },
                y: { stacked: true, grid:{ color:'#f1f5f9' }, ticks:{ font:{size:10}, precision:0 },
                     title:{ display:true, text:'Apartamentos', font:{size:10} } }
            }
        }
    });

    // ── 6b. Montos cobrados por mes — Año actual ──
    (function() {
        const labelsAnio  = @json($kpiData['pagosAnio']['labels']);
        const montosAnio  = @json($kpiData['pagosAnio']['montos']);
        const countsAnio  = @json($kpiData['pagosAnio']['counts']);
        const mesActual   = new Date().getMonth(); // 0-based

        const bgColors = labelsAnio.map((_, i) =>
            i < mesActual      ? 'rgba(39,50,114,0.75)'  // meses pasados — navy
            : i === mesActual  ? 'rgba(104,12,62,0.90)'  // mes actual    — burgundy
            : 'rgba(39,50,114,0.15)'                     // meses futuros — gris
        );
        const borderColors = labelsAnio.map((_, i) =>
            i < mesActual     ? NAVY : i === mesActual ? BURGUNDY : '#d1d5db'
        );

        new Chart(document.getElementById('chartPagosAnio'), {
            type: 'bar',
            data: {
                labels: labelsAnio,
                datasets: [
                    {
                        label: 'Monto cobrado (Bs)',
                        data: montosAnio,
                        backgroundColor: bgColors,
                        borderColor: borderColors,
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                        yAxisID: 'yMonto',
                        order: 2,
                    },
                    {
                        label: 'N° Pagos',
                        data: countsAnio,
                        type: 'line',
                        borderColor: GOLD,
                        backgroundColor: 'rgba(212,160,23,0.10)',
                        pointBackgroundColor: countsAnio.map((_, i) => i === mesActual ? BURGUNDY : GOLD),
                        pointRadius: countsAnio.map((v, i) => v > 0 || i <= mesActual ? 5 : 0),
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'yCant',
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 }, usePointStyle: true } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.yAxisID === 'yMonto'
                                ? ' Monto: Bs ' + Number(ctx.parsed.y).toLocaleString('es-VE', {minimumFractionDigits:2})
                                : ' Pagos: ' + ctx.parsed.y
                        },
                        afterBody: (items) => {
                            const i = items[0]?.dataIndex;
                            if (i === mesActual) return ['── Mes en curso ──'];
                            if (i > mesActual)   return ['(Sin datos aún)'];
                            return [];
                        }
                    }
                },
                scales: {
                    yMonto: {
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 10 },
                            callback: v => v >= 1000000
                                ? (v/1000000).toFixed(1) + 'M'
                                : v >= 1000 ? (v/1000).toFixed(0)+'K' : v
                        }
                    },
                    yCant: {
                        position: 'right',
                        grid: { display: false },
                        ticks: { font: { size: 10 }, precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    })();

    // ── 6. Tendencia pagos últimos 6 meses (línea + barras) ──
    new Chart(document.getElementById('chartTendencia'), {
        type: 'bar',
        data: {
            labels: @json($kpiData['pagosMes6']['labels']),
            datasets: [
                {
                    label: 'Monto (Bs)',
                    data: @json($kpiData['pagosMes6']['montos']),
                    backgroundColor: 'rgba(39,50,114,0.20)',
                    borderColor: NAVY,
                    borderWidth: 2,
                    borderRadius: 5,
                    yAxisID: 'yMonto',
                    order: 2,
                },
                {
                    label: 'N° Pagos',
                    data: @json($kpiData['pagosMes6']['counts']),
                    type: 'line',
                    borderColor: BURGUNDY,
                    backgroundColor: 'rgba(104,12,62,0.12)',
                    pointBackgroundColor: BURGUNDY,
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'yCant',
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 }, usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.yAxisID === 'yMonto'
                            ? ' Monto: Bs ' + Number(ctx.parsed.y).toLocaleString('es-VE',{minimumFractionDigits:2})
                            : ' Pagos: ' + ctx.parsed.y
                    }
                }
            },
            scales: {
                yMonto: {
                    position: 'left',
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { size: 10 }, callback: v => (v/1000000).toFixed(1)+'M' }
                },
                yCant: {
                    position: 'right',
                    grid: { display: false },
                    ticks: { font: { size: 10 }, precision: 0 }
                }
            }
        }
    });

    </script>
    @endpush
</x-app-layout>
