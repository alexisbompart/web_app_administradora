<!-- Desktop Sidebar -->
<aside class="fixed inset-y-0 left-0 z-40 bg-navy-800 text-white transition-all duration-300 hidden lg:block"
       :class="sidebarOpen ? 'w-64' : 'w-20'">

    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-navy-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 bg-burgundy-800 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-heading font-bold text-lg">AI</span>
            </div>
            <span x-show="sidebarOpen" x-transition class="font-heading font-bold text-sm text-white whitespace-nowrap">
                Administradora<br>Integral
            </span>
        </a>
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate_custom-300 hover:text-white p-1">
            <i class="fas fa-chevron-left transition-transform duration-300" :class="!sidebarOpen && 'rotate-180'"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="mt-4 px-3 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 8rem);">
        <!-- Dashboard (hidden for cliente-propietario) -->
        @unless(auth()->user()->hasRole('cliente-propietario'))
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>
        @endunless

        <!-- Mi Condominio - Solo para cliente-propietario -->
        @if(auth()->user()->hasRole('cliente-propietario'))
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Mi Condominio</p>
        </div>
        <a href="{{ route('mi-condominio.dashboard') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Mi Panel</span>
        </a>
        <a href="{{ route('mi-condominio.deudas') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.deudas') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Mis Deudas</span>
        </a>
        <a href="{{ route('mi-condominio.registrar-pago') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.registrar-pago*') ? 'active' : '' }}">
            <i class="fas fa-money-check-alt w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Registrar Pago</span>
        </a>
        <a href="{{ route('mi-condominio.pago-integral') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.pago-integral*') ? 'active' : '' }}">
            <i class="fas fa-credit-card w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Pago Integral</span>
        </a>
        <a href="{{ route('mi-condominio.pagos') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.pagos') ? 'active' : '' }}">
            <i class="fas fa-receipt w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Mis Pagos</span>
        </a>
        <a href="{{ route('mi-condominio.recibos-edificio') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.recibos-edificio') ? 'active' : '' }}">
            <i class="fas fa-building w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Recibo Edificio</span>
        </a>
        <a href="{{ route('mi-condominio.recibos-apartamento') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.recibos-apartamento') ? 'active' : '' }}">
            <i class="fas fa-file-invoice w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Recibo Apartamento</span>
        </a>
        <a href="{{ route('mi-condominio.estadisticas') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.estadisticas') ? 'active' : '' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Estadisticas</span>
        </a>
        @endif

        <!-- Mis Facturas - Solo para proveedor -->
        @if(auth()->user()->hasRole('proveedor'))
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Mis Facturas</p>
        </div>
        <a href="{{ route('proveedores.facturas.index') }}" class="sidebar-link {{ request()->routeIs('proveedores.facturas.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Mis Facturas</span>
        </a>
        @endif

        <!-- Condominio Section - Hidden from cliente-propietario and proveedor -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        <!-- Separator -->
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Condominio</p>
        </div>

        @can('sistema.ver-dashboard')
        <a href="{{ route('condominio.companias.index') }}"
           class="sidebar-link {{ request()->routeIs('condominio.companias.*') ? 'active' : '' }}">
            <i class="fas fa-building w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Companias</span>
        </a>
        <a href="{{ route('condominio.edificios.index') }}"
           class="sidebar-link {{ request()->routeIs('condominio.edificios.*') ? 'active' : '' }}">
            <i class="fas fa-city w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Edificios</span>
        </a>
        <a href="{{ route('condominio.apartamentos.index') }}"
           class="sidebar-link {{ request()->routeIs('condominio.apartamentos.*') ? 'active' : '' }}">
            <i class="fas fa-door-open w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Apartamentos</span>
        </a>
        <a href="{{ route('condominio.propietarios.index') }}"
           class="sidebar-link {{ request()->routeIs('condominio.propietarios.*') ? 'active' : '' }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Propietarios</span>
        </a>
        <a href="{{ route('condominio.afilapto.index') }}"
           class="sidebar-link {{ request()->routeIs('condominio.afilapto.*') || request()->routeIs('condominio.afilpagointegral.*') ? 'active' : '' }}">
            <i class="fas fa-link w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Afiliaciones</span>
        </a>
        @endcan
        @endunless

        <!-- Personal Section - Hidden from cliente-propietario and proveedor -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['personal.ver', 'personal.crear'])
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Personal</p>
        </div>
        <a href="{{ route('personal.trabajadores.index') }}"
           class="sidebar-link {{ request()->routeIs('personal.trabajadores.*') ? 'active' : '' }}">
            <i class="fas fa-id-badge w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Trabajadores</span>
        </a>
        <a href="{{ route('personal.nominas.index') }}"
           class="sidebar-link {{ request()->routeIs('personal.nominas.*') ? 'active' : '' }}">
            <i class="fas fa-money-check-alt w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Nominas</span>
        </a>
        <a href="{{ route('personal.vacaciones.index') }}"
           class="sidebar-link {{ request()->routeIs('personal.vacaciones.*') ? 'active' : '' }}">
            <i class="fas fa-umbrella-beach w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Vacaciones</span>
        </a>
        @endcanany
        @endunless

        <!-- Proveedores Section - Hidden from cliente-propietario, visible to admin roles -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['proveedores.ver', 'proveedores.crear'])
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Proveedores</p>
        </div>
        <a href="{{ route('proveedores.proveedores.index') }}"
           class="sidebar-link {{ request()->routeIs('proveedores.proveedores.*') ? 'active' : '' }}">
            <i class="fas fa-truck w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Proveedores</span>
        </a>
        <a href="{{ route('proveedores.facturas.index') }}"
           class="sidebar-link {{ request()->routeIs('proveedores.facturas.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Facturas</span>
        </a>
        @endcanany
        @endunless

        <!-- Finanzas Section - Hidden from cliente-propietario and proveedor -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['fondos.ver', 'cobranza.ver'])
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Finanzas</p>
        </div>
        @endcanany

        @can('fondos.ver')
        <a href="{{ route('financiero.fondos.index') }}"
           class="sidebar-link {{ request()->routeIs('financiero.fondos.*') ? 'active' : '' }}">
            <i class="fas fa-piggy-bank w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Fondos</span>
        </a>
        @endcan

        @can('cobranza.ver')
        <a href="{{ route('financiero.cobranza.index') }}"
           class="sidebar-link {{ request()->routeIs('financiero.cobranza.*') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Cobranza</span>
        </a>
        @endcan

        @can('cobranza.ver')
        <a href="{{ route('financiero.envio-recibos.index') }}"
           class="sidebar-link {{ request()->routeIs('financiero.envio-recibos.*') ? 'active' : '' }}">
            <i class="fas fa-mail-bulk w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Envio Recibos</span>
        </a>
        @endcan

        @can('fondos.conciliar')
        <a href="{{ route('financiero.conciliaciones.index') }}"
           class="sidebar-link {{ request()->routeIs('financiero.conciliaciones.*') ? 'active' : '' }}">
            <i class="fas fa-balance-scale w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Conciliacion</span>
        </a>
        @endcan

        <a href="{{ route('financiero.tasabcv.index') }}"
           class="sidebar-link {{ request()->routeIs('financiero.tasabcv.*') ? 'active' : '' }}">
            <i class="fas fa-dollar-sign w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Tasas BCV</span>
        </a>

        @can('pago-integral.ver')
        <a href="{{ route('financiero.pago-integral.index') }}"
           class="sidebar-link {{ request()->routeIs('financiero.pago-integral.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Pago Integral</span>
        </a>
        @endcan

        @hasanyrole('super-admin|administrador')
        <a href="{{ route('financiero.pago-integral.afiliaciones') }}"
           class="sidebar-link {{ request()->routeIs('financiero.pago-integral.afiliaciones*') ? 'active' : '' }}">
            <i class="fas fa-user-check w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Afiliaciones</span>
        </a>
        @endhasanyrole

        @can('cajamatic.ver')
        <a href="{{ route('financiero.cajamatic.index') }}"
           class="sidebar-link {{ request()->routeIs('financiero.cajamatic.*') ? 'active' : '' }}">
            <i class="fas fa-cash-register w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>CajaMatic</span>
        </a>
        @endcan
        @endunless

        <!-- Servicios Section - Hidden from cliente-propietario and proveedor -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['atencion-cliente.ver', 'informes.ver'])
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Servicios</p>
        </div>
        @endcanany

        @can('atencion-cliente.ver')
        <a href="{{ route('servicios.atencion-cliente.index') }}"
           class="sidebar-link {{ request()->routeIs('servicios.atencion-cliente.*') ? 'active' : '' }}">
            <i class="fas fa-headset w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Atencion al Cliente</span>
        </a>
        @endcan

        @can('informes.ver')
        <a href="{{ route('servicios.informes.index') }}"
           class="sidebar-link {{ request()->routeIs('servicios.informes.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Informes</span>
        </a>
        @endcan
        @endunless

        <!-- Administracion Section - Hidden from cliente-propietario and proveedor -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['sistema.gestionar-usuarios', 'sistema.gestionar-roles'])
        <div class="pt-4 pb-2" x-show="sidebarOpen">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Administracion</p>
        </div>
        <a href="{{ route('admin.usuarios.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <i class="fas fa-user-shield w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Usuarios</span>
        </a>
        <a href="{{ route('admin.roles.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="fas fa-user-tag w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Roles</span>
        </a>
        <a href="{{ route('admin.importaciones.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.importaciones.*') ? 'active' : '' }}">
            <i class="fas fa-file-import w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Importaciones</span>
        </a>
        <a href="{{ route('admin.welcome.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.welcome.*') ? 'active' : '' }}">
            <i class="fas fa-globe w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Pagina Web</span>
        </a>
        @endcanany
        @endunless
    </nav>
</aside>

<!-- Mobile Sidebar -->
<aside x-show="mobileSidebar"
       x-transition:enter="transition ease-in-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-navy-800 text-white lg:hidden"
       style="display: none;">

    <!-- Logo & Close -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-navy-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-burgundy-800 rounded-lg flex items-center justify-center">
                <span class="text-white font-heading font-bold text-lg">AI</span>
            </div>
            <span class="font-heading font-bold text-sm text-white">
                Administradora<br>Integral
            </span>
        </a>
        <button @click="mobileSidebar = false" class="text-slate_custom-300 hover:text-white p-2">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Same nav links as desktop but always showing text -->
    <nav class="mt-4 px-3 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 8rem);">
        @unless(auth()->user()->hasRole('cliente-propietario'))
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt w-5 text-center"></i>
            <span>Dashboard</span>
        </a>
        @endunless

        <!-- Mi Condominio - Solo para cliente-propietario (Mobile) -->
        @if(auth()->user()->hasRole('cliente-propietario'))
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Mi Condominio</p>
        </div>
        <a href="{{ route('mi-condominio.dashboard') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home w-5 text-center"></i>
            <span>Mi Panel</span>
        </a>
        <a href="{{ route('mi-condominio.deudas') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.deudas') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
            <span>Mis Deudas</span>
        </a>
        <a href="{{ route('mi-condominio.registrar-pago') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.registrar-pago*') ? 'active' : '' }}">
            <i class="fas fa-money-check-alt w-5 text-center"></i>
            <span>Registrar Pago</span>
        </a>
        <a href="{{ route('mi-condominio.pago-integral') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.pago-integral*') ? 'active' : '' }}">
            <i class="fas fa-credit-card w-5 text-center"></i>
            <span>Pago Integral</span>
        </a>
        <a href="{{ route('mi-condominio.pagos') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.pagos') ? 'active' : '' }}">
            <i class="fas fa-receipt w-5 text-center"></i>
            <span>Mis Pagos</span>
        </a>
        <a href="{{ route('mi-condominio.recibos-edificio') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.recibos-edificio') ? 'active' : '' }}">
            <i class="fas fa-building w-5 text-center"></i>
            <span>Recibo Edificio</span>
        </a>
        <a href="{{ route('mi-condominio.recibos-apartamento') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.recibos-apartamento') ? 'active' : '' }}">
            <i class="fas fa-file-invoice w-5 text-center"></i>
            <span>Recibo Apartamento</span>
        </a>
        <a href="{{ route('mi-condominio.estadisticas') }}" class="sidebar-link {{ request()->routeIs('mi-condominio.estadisticas') ? 'active' : '' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span>Estadisticas</span>
        </a>
        @endif

        <!-- Mis Facturas - Solo para proveedor (Mobile) -->
        @if(auth()->user()->hasRole('proveedor'))
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Mis Facturas</p>
        </div>
        <a href="{{ route('proveedores.facturas.index') }}" class="sidebar-link {{ request()->routeIs('proveedores.facturas.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
            <span>Mis Facturas</span>
        </a>
        @endif

        <!-- Condominio Section (Mobile) -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @can('sistema.ver-dashboard')
        <div class="pt-4 pb-2"><p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Condominio</p></div>
        <a href="{{ route('condominio.companias.index') }}" class="sidebar-link {{ request()->routeIs('condominio.companias.*') ? 'active' : '' }}"><i class="fas fa-building w-5 text-center"></i><span>Companias</span></a>
        <a href="{{ route('condominio.edificios.index') }}" class="sidebar-link {{ request()->routeIs('condominio.edificios.*') ? 'active' : '' }}"><i class="fas fa-city w-5 text-center"></i><span>Edificios</span></a>
        <a href="{{ route('condominio.apartamentos.index') }}" class="sidebar-link {{ request()->routeIs('condominio.apartamentos.*') ? 'active' : '' }}"><i class="fas fa-door-open w-5 text-center"></i><span>Apartamentos</span></a>
        <a href="{{ route('condominio.propietarios.index') }}" class="sidebar-link {{ request()->routeIs('condominio.propietarios.*') ? 'active' : '' }}"><i class="fas fa-users w-5 text-center"></i><span>Propietarios</span></a>
        <a href="{{ route('condominio.afilapto.index') }}" class="sidebar-link {{ request()->routeIs('condominio.afilapto.*') || request()->routeIs('condominio.afilpagointegral.*') ? 'active' : '' }}"><i class="fas fa-link w-5 text-center"></i><span>Afiliaciones</span></a>
        @endcan
        @endunless

        <!-- Personal Section (Mobile) -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['personal.ver', 'personal.crear'])
        <div class="pt-4 pb-2"><p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Personal</p></div>
        <a href="{{ route('personal.trabajadores.index') }}" class="sidebar-link {{ request()->routeIs('personal.trabajadores.*') ? 'active' : '' }}"><i class="fas fa-id-badge w-5 text-center"></i><span>Trabajadores</span></a>
        <a href="{{ route('personal.nominas.index') }}" class="sidebar-link {{ request()->routeIs('personal.nominas.*') ? 'active' : '' }}"><i class="fas fa-money-check-alt w-5 text-center"></i><span>Nominas</span></a>
        <a href="{{ route('personal.vacaciones.index') }}" class="sidebar-link {{ request()->routeIs('personal.vacaciones.*') ? 'active' : '' }}"><i class="fas fa-umbrella-beach w-5 text-center"></i><span>Vacaciones</span></a>
        @endcanany
        @endunless

        <!-- Proveedores Section (Mobile) -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['proveedores.ver', 'proveedores.crear'])
        <div class="pt-4 pb-2"><p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Proveedores</p></div>
        <a href="{{ route('proveedores.proveedores.index') }}" class="sidebar-link {{ request()->routeIs('proveedores.proveedores.*') ? 'active' : '' }}"><i class="fas fa-truck w-5 text-center"></i><span>Proveedores</span></a>
        <a href="{{ route('proveedores.facturas.index') }}" class="sidebar-link {{ request()->routeIs('proveedores.facturas.*') ? 'active' : '' }}"><i class="fas fa-file-invoice-dollar w-5 text-center"></i><span>Facturas</span></a>
        @endcanany
        @endunless

        <!-- Finanzas Section (Mobile) -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['fondos.ver', 'cobranza.ver'])
        <div class="pt-4 pb-2"><p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Finanzas</p></div>
        @endcanany
        @can('fondos.ver')
        <a href="{{ route('financiero.fondos.index') }}" class="sidebar-link {{ request()->routeIs('financiero.fondos.*') ? 'active' : '' }}"><i class="fas fa-piggy-bank w-5 text-center"></i><span>Fondos</span></a>
        @endcan
        @can('cobranza.ver')
        <a href="{{ route('financiero.cobranza.index') }}" class="sidebar-link {{ request()->routeIs('financiero.cobranza.*') ? 'active' : '' }}"><i class="fas fa-hand-holding-usd w-5 text-center"></i><span>Cobranza</span></a>
        @endcan
        @can('cobranza.ver')
        <a href="{{ route('financiero.envio-recibos.index') }}" class="sidebar-link {{ request()->routeIs('financiero.envio-recibos.*') ? 'active' : '' }}"><i class="fas fa-mail-bulk w-5 text-center"></i><span>Envio Recibos</span></a>
        @endcan
        @can('fondos.conciliar')
        <a href="{{ route('financiero.conciliaciones.index') }}" class="sidebar-link {{ request()->routeIs('financiero.conciliaciones.*') ? 'active' : '' }}"><i class="fas fa-balance-scale w-5 text-center"></i><span>Conciliacion</span></a>
        @endcan
        <a href="{{ route('financiero.tasabcv.index') }}" class="sidebar-link {{ request()->routeIs('financiero.tasabcv.*') ? 'active' : '' }}"><i class="fas fa-dollar-sign w-5 text-center"></i><span>Tasas BCV</span></a>
        @can('pago-integral.ver')
        <a href="{{ route('financiero.pago-integral.index') }}" class="sidebar-link {{ request()->routeIs('financiero.pago-integral.*') ? 'active' : '' }}"><i class="fas fa-credit-card w-5 text-center"></i><span>Pago Integral</span></a>
        @endcan
        @hasanyrole('super-admin|administrador')
        <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="sidebar-link {{ request()->routeIs('financiero.pago-integral.afiliaciones*') ? 'active' : '' }}"><i class="fas fa-user-check w-5 text-center"></i><span>Afiliaciones</span></a>
        @endhasanyrole
        @can('cajamatic.ver')
        <a href="{{ route('financiero.cajamatic.index') }}" class="sidebar-link {{ request()->routeIs('financiero.cajamatic.*') ? 'active' : '' }}"><i class="fas fa-cash-register w-5 text-center"></i><span>CajaMatic</span></a>
        @endcan
        @endunless

        <!-- Servicios Section (Mobile) -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['atencion-cliente.ver', 'informes.ver'])
        <div class="pt-4 pb-2"><p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Servicios</p></div>
        @endcanany
        @can('atencion-cliente.ver')
        <a href="{{ route('servicios.atencion-cliente.index') }}" class="sidebar-link {{ request()->routeIs('servicios.atencion-cliente.*') ? 'active' : '' }}"><i class="fas fa-headset w-5 text-center"></i><span>Atencion al Cliente</span></a>
        @endcan
        @can('informes.ver')
        <a href="{{ route('servicios.informes.index') }}" class="sidebar-link {{ request()->routeIs('servicios.informes.*') ? 'active' : '' }}"><i class="fas fa-chart-bar w-5 text-center"></i><span>Informes</span></a>
        @endcan
        @endunless

        <!-- Administracion Section (Mobile) -->
        @unless(auth()->user()->hasRole('cliente-propietario') || auth()->user()->hasRole('proveedor'))
        @canany(['sistema.gestionar-usuarios', 'sistema.gestionar-roles'])
        <div class="pt-4 pb-2"><p class="px-4 text-xs font-semibold text-slate_custom-400 uppercase tracking-wider">Administracion</p></div>
        <a href="{{ route('admin.usuarios.index') }}" class="sidebar-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"><i class="fas fa-user-shield w-5 text-center"></i><span>Usuarios</span></a>
        <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"><i class="fas fa-user-tag w-5 text-center"></i><span>Roles</span></a>
        <a href="{{ route('admin.importaciones.index') }}" class="sidebar-link {{ request()->routeIs('admin.importaciones.*') ? 'active' : '' }}"><i class="fas fa-file-import w-5 text-center"></i><span>Importaciones</span></a>
        <a href="{{ route('admin.welcome.index') }}" class="sidebar-link {{ request()->routeIs('admin.welcome.*') ? 'active' : '' }}"><i class="fas fa-globe w-5 text-center"></i><span>Pagina Web</span></a>
        @endcanany
        @endunless
    </nav>
</aside>
