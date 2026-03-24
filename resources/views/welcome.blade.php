<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $settings['titulo_sitio'] ?? config('app.name', 'Administradora Integral') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|rubik:400,500&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .carousel-container { position: relative; width: 100%; height: 600px; overflow: hidden; background: linear-gradient(to right, #1f2937, #111827); }
            .carousel-track { display: flex; height: 100%; transition: transform 0.7s ease-in-out; }
            .carousel-slide { min-width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; position: relative; background-size: cover; background-position: center; background-repeat: no-repeat; }
            .carousel-slide .overlay { position: absolute; inset: 0; }
            .carousel-slide .content { position: relative; z-index: 10; text-align: center; padding: 0 1.5rem; max-width: 56rem; }
            .carousel-dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: all 0.3s; border: none; }
            .carousel-dot.active { background: #fff; transform: scale(1.3); box-shadow: 0 0 10px rgba(255,255,255,0.5); }
            .service-card { background: #fff; border-radius: 16px; padding: 28px 20px; text-align: center; border: 1px solid #e2e8f0; transition: all 0.3s; }
            .service-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.1); }
            .service-icon { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
            .residence-card { position: relative; border-radius: 16px; overflow: hidden; height: 280px; }
            .residence-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
            .residence-card:hover img { transform: scale(1.05); }
            .residence-overlay { position: absolute; bottom: 0; inset-inline: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 20px; }
        </style>
    </head>
    <body class="font-body antialiased bg-white" x-data="{ mobileMenu: false }">

        <!-- Header -->
        <header class="bg-white border-b border-slate_custom-200 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <a href="/" class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-9 h-9 bg-burgundy-800 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-sm">AI</span>
                        </div>
                        <div class="leading-tight hidden sm:block">
                            <span class="font-heading font-bold text-navy-800 text-sm block">{{ $settings['nombre_empresa'] ?? 'Administradora' }}</span>
                            <span class="font-heading font-bold text-burgundy-800 text-xs block">{{ $settings['subtitulo_empresa'] ?? 'Integral' }}</span>
                        </div>
                    </a>

                    <!-- Nav Desktop -->
                    <nav class="hidden md:flex items-center gap-0.5 lg:gap-1">
                        <a href="#inicio" class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-navy-800 font-medium hover:text-burgundy-800 transition rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_1'] ?? 'Home' }}</a>
                        <a href="#productos" class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-slate_custom-500 font-medium hover:text-burgundy-800 transition rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_2'] ?? 'Productos' }}</a>
                        <a href="#residencias" class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-slate_custom-500 font-medium hover:text-burgundy-800 transition rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_3'] ?? 'Residencias' }}</a>
                        <a href="#servicios" class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-slate_custom-500 font-medium hover:text-burgundy-800 transition rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_4'] ?? 'Servicios' }}</a>
                        <a href="#mapa" class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-slate_custom-500 font-medium hover:text-burgundy-800 transition rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_5'] ?? 'Ubicaciones' }}</a>
                        <a href="#contacto" class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-slate_custom-500 font-medium hover:text-burgundy-800 transition rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_6'] ?? 'Contactanos' }}</a>
                    </nav>

                    <!-- Right -->
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-burgundy-800 text-white text-sm font-heading font-semibold rounded-lg hover:bg-burgundy-700 transition">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 bg-burgundy-800 text-white text-sm font-heading font-semibold rounded-lg hover:bg-burgundy-700 transition">
                                <i class="fas fa-sign-in-alt mr-2"></i>{{ $settings['nav_boton_login'] ?? 'Iniciar Sesion' }}
                            </a>
                        @endauth
                        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate_custom-500 hover:text-navy-800">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Nav -->
                <div x-show="mobileMenu" x-transition class="md:hidden pb-4 border-t border-slate_custom-100 mt-2 pt-3">
                    <div class="flex flex-col gap-1">
                        <a href="#inicio" @click="mobileMenu=false" class="px-3 py-2 text-sm text-navy-800 font-medium rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_1'] ?? 'Home' }}</a>
                        <a href="#productos" @click="mobileMenu=false" class="px-3 py-2 text-sm text-slate_custom-500 font-medium rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_2'] ?? 'Productos' }}</a>
                        <a href="#residencias" @click="mobileMenu=false" class="px-3 py-2 text-sm text-slate_custom-500 font-medium rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_3'] ?? 'Residencias' }}</a>
                        <a href="#servicios" @click="mobileMenu=false" class="px-3 py-2 text-sm text-slate_custom-500 font-medium rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_4'] ?? 'Servicios' }}</a>
                        <a href="#mapa" @click="mobileMenu=false" class="px-3 py-2 text-sm text-slate_custom-500 font-medium rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_5'] ?? 'Ubicaciones' }}</a>
                        <a href="#contacto" @click="mobileMenu=false" class="px-3 py-2 text-sm text-slate_custom-500 font-medium rounded-lg hover:bg-slate_custom-50">{{ $settings['nav_link_6'] ?? 'Contactanos' }}</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Carousel -->
        <section class="carousel-container" id="inicio">
            @if($sliders->count() > 0)
            <div id="carousel-track" class="carousel-track">
                @foreach($sliders as $index => $slider)
                <div class="carousel-slide" style="background-image: url('{{ asset('storage/' . $slider->imagen) }}');">
                    <div class="overlay bg-gradient-to-br from-navy-800/80 via-navy-800/70 to-black/80"></div>
                    <div class="content">
                        @if($slider->titulo)
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-white leading-tight mb-6 drop-shadow-lg">
                            {{ $slider->titulo }}
                        </h1>
                        @endif
                        @if($slider->subtitulo)
                        <p class="text-xl sm:text-2xl md:text-3xl text-white/90 font-light mb-8 leading-relaxed drop-shadow-lg">
                            {{ $slider->subtitulo }}
                        </p>
                        @endif
                        @if($slider->boton_texto)
                        <a href="{{ $slider->boton_url ?? '#' }}" class="inline-flex items-center px-8 py-3.5 bg-burgundy-800 text-white font-heading font-semibold rounded-full hover:bg-burgundy-700 hover:scale-105 transition-all duration-300 shadow-lg shadow-burgundy-800/30">
                            {{ $slider->boton_texto }} <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Controls -->
            @if($sliders->count() > 1)
            <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 z-10 bg-white/20 hover:bg-white/30 text-white p-4 rounded-full backdrop-blur-sm transition-all">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 z-10 bg-white/20 hover:bg-white/30 text-white p-4 rounded-full backdrop-blur-sm transition-all">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Indicators -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-10">
                @foreach($sliders as $index => $slider)
                <button class="carousel-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></button>
                @endforeach
            </div>
            @endif

            @else
            <!-- Fallback -->
            <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600&h=700&fit=crop');">
                <div class="overlay bg-gradient-to-br from-navy-800/80 via-navy-800/70 to-black/80"></div>
                <div class="content">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-bold text-white leading-tight mb-6 drop-shadow-lg">
                        {{ $settings['hero_titulo'] ?? 'Administradora Integral' }}
                    </h1>
                    <p class="text-xl sm:text-2xl md:text-3xl text-white/90 font-light mb-8 leading-relaxed drop-shadow-lg">
                        {{ $settings['hero_subtitulo'] ?? 'Compania lider en el mercado inmobiliario' }}
                    </p>
                </div>
            </div>
            @endif
        </section>

        <!-- Nuestros Productos -->
        @if($products->count() > 0 && ($settings['seccion_productos_visible'] ?? '1') === '1')
        <section class="mt-8 lg:mt-12 py-16 lg:py-20 bg-white" id="productos">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-slate_custom-400 mb-2">{{ $settings['productos_subtitulo'] ?? 'SOLUCIONES A TU MEDIDA' }}</p>
                    <h3 class="text-3xl lg:text-4xl font-heading font-bold text-navy-800">{{ $settings['productos_titulo'] ?? 'Nuestros Productos' }}</h3>
                    <div class="w-16 h-1 bg-burgundy-800 rounded mt-4 mx-auto"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($products as $product)
                    <div class="relative bg-white rounded-2xl border border-slate_custom-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group {{ $product->detalle ? 'cursor-pointer' : '' }}"
                         @if($product->detalle) onclick="document.getElementById('modal-product-detail-{{ $product->id }}').classList.remove('hidden')" @endif>
                        {{-- Color bar top --}}
                        <div class="h-1.5 w-full" style="background: {{ $product->color }};"></div>
                        <div class="p-8">
                            {{-- Icon --}}
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5 transition-colors" style="background: {{ $product->color }}15;">
                                <i class="{{ $product->icono }} text-2xl" style="color: {{ $product->color }};"></i>
                            </div>
                            {{-- Title --}}
                            <h4 class="font-heading font-bold text-xl text-navy-800 mb-2">{{ $product->titulo }}</h4>
                            {{-- Slogan --}}
                            @if($product->slogan)
                            <p class="text-sm font-semibold italic mb-4" style="color: {{ $product->color }};">
                                "{{ $product->slogan }}"
                            </p>
                            @endif
                            {{-- Description --}}
                            <p class="text-slate_custom-500 text-sm leading-relaxed">{{ $product->descripcion }}</p>
                            {{-- Ver mas link --}}
                            @if($product->detalle)
                            <div class="mt-5 pt-4 border-t border-slate_custom-100">
                                <span class="inline-flex items-center text-sm font-semibold group-hover:gap-2 transition-all" style="color: {{ $product->color }};">
                                    Ver detalle completo <i class="fas fa-arrow-right ml-1"></i>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Modals de detalle de productos --}}
                @foreach($products as $product)
                @if($product->detalle)
                <div id="modal-product-detail-{{ $product->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                    <div class="relative bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl">
                        {{-- Header --}}
                        <div class="sticky top-0 z-10 bg-white border-b border-slate_custom-100 p-6 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: {{ $product->color }}15;">
                                    <i class="{{ $product->icono }} text-xl" style="color: {{ $product->color }};"></i>
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-lg text-navy-800">{{ $product->titulo }}</h3>
                                    @if($product->slogan)
                                    <p class="text-sm italic" style="color: {{ $product->color }};">"{{ $product->slogan }}"</p>
                                    @endif
                                </div>
                            </div>
                            <button onclick="this.closest('[id^=modal-product-detail]').classList.add('hidden')" class="w-10 h-10 rounded-full bg-slate_custom-100 hover:bg-slate_custom-200 flex items-center justify-center text-slate_custom-500 hover:text-slate_custom-700 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        {{-- Body --}}
                        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 90px);">
                            <p class="text-slate_custom-500 text-sm leading-relaxed mb-6">{{ $product->descripcion }}</p>
                            <div class="border-t border-slate_custom-100 pt-6">
                                {!! $product->detalle !!}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </section>
        @endif

        <!-- Tarjetas de Accion -->
        @if(($settings['seccion_acciones_visible'] ?? '1') === '1')
        <section class="mt-8 pt-16 pb-16 lg:mt-12 lg:pt-20 lg:pb-20 bg-slate_custom-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Pagar --}}
                    <a href="{{ route('login') }}" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-slate_custom-200">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-burgundy-800 rounded-l-2xl"></div>
                        <div class="p-8 sm:p-10 flex flex-col sm:flex-row items-start gap-6 pl-8 sm:pl-10">
                            <div class="w-20 h-20 bg-burgundy-800/10 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-burgundy-800 group-hover:scale-110 transition-all duration-500">
                                <i class="{{ $settings['cta_pagar_icono'] ?? 'fas fa-file-invoice-dollar' }} text-3xl text-burgundy-800 group-hover:text-white transition-all duration-500"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-heading font-bold text-navy-800 mb-3 group-hover:text-burgundy-800 transition">{{ $settings['cta_pagar_titulo'] ?? 'Pague su recibo de condominio' }}</h3>
                                <p class="text-sm text-slate_custom-500 leading-relaxed mb-5">{{ $settings['cta_pagar_texto'] ?? 'Registre su pago por transferencia o deposito bancario de forma rapida y segura.' }}</p>
                                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-burgundy-800/5 text-burgundy-800 text-sm font-heading font-semibold rounded-lg group-hover:bg-burgundy-800 group-hover:text-white transition-all duration-300">
                                    {{ $settings['cta_pagar_boton'] ?? 'Pagar Aqui' }}
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                    {{-- Consultar --}}
                    <a href="{{ route('login') }}" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-slate_custom-200">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-navy-800 rounded-l-2xl"></div>
                        <div class="p-8 sm:p-10 flex flex-col sm:flex-row items-start gap-6 pl-8 sm:pl-10">
                            <div class="w-20 h-20 bg-navy-800/10 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-navy-800 group-hover:scale-110 transition-all duration-500">
                                <i class="{{ $settings['cta_consultar_icono'] ?? 'fas fa-search-dollar' }} text-3xl text-navy-800 group-hover:text-white transition-all duration-500"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-heading font-bold text-navy-800 mb-3 group-hover:text-navy-700 transition">{{ $settings['cta_consultar_titulo'] ?? 'Consulte su recibo de condominio' }}</h3>
                                <p class="text-sm text-slate_custom-500 leading-relaxed mb-5">{{ $settings['cta_consultar_texto'] ?? 'Revise sus estados de cuenta, deudas pendientes e historial de pagos realizados.' }}</p>
                                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-navy-800/5 text-navy-800 text-sm font-heading font-semibold rounded-lg group-hover:bg-navy-800 group-hover:text-white transition-all duration-300">
                                    {{ $settings['cta_consultar_boton'] ?? 'Consultar Aqui' }}
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
        @endif

        <!-- Nuestras Residencias -->
        @if($residences->count() > 0 && ($settings['seccion_residencias_visible'] ?? '1') === '1')
        <section class="mt-8 lg:mt-12 py-16 lg:py-20" id="residencias" style="background-color: #1e293b;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-slate_custom-400 mb-2">{{ $settings['residencias_subtitulo'] ?? 'COMUNIDADES QUE CONFIAN EN NOSOTROS' }}</p>
                    <h3 class="text-3xl lg:text-4xl font-heading font-bold text-white">{{ $settings['residencias_titulo'] ?? 'Nuestros Clientes' }}</h3>
                    <div class="w-16 h-1 bg-burgundy-800 rounded mt-4 mx-auto"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($residences as $residence)
                    <div class="residence-card group">
                        <img src="{{ asset('storage/' . $residence->imagen) }}" alt="{{ $residence->nombre }}">
                        <div class="residence-overlay">
                            <h4 class="text-white font-heading font-bold text-lg">{{ $residence->nombre }}</h4>
                            @if($residence->ubicacion)
                            <p class="text-white/70 text-sm flex items-center gap-1 mt-1">
                                <i class="fas fa-map-marker-alt text-xs"></i> {{ $residence->ubicacion }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Nuestros Servicios -->
        @if($services->count() > 0 && ($settings['seccion_servicios_visible'] ?? '1') === '1')
        <section class="mt-8 lg:mt-12 py-16 lg:py-20 bg-slate_custom-50" id="servicios">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-slate_custom-400 mb-2">{{ $settings['servicios_subtitulo'] ?? 'ADAPTADOS A TUS NECESIDADES' }}</p>
                    <h3 class="text-3xl lg:text-4xl font-heading font-bold text-navy-800">{{ $settings['servicios_titulo'] ?? 'Nuestros Servicios' }}</h3>
                    <div class="w-16 h-1 bg-burgundy-800 rounded mt-4 mx-auto"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($services as $service)
                    <div class="service-card">
                        <div class="service-icon" style="background: {{ $service->color_icono }}20;">
                            <i class="{{ $service->icono }} text-2xl" style="color: {{ $service->color_icono }};"></i>
                        </div>
                        <h4 class="font-heading font-bold text-navy-800 text-sm mb-2">{{ $service->titulo }}</h4>
                        <p class="text-slate_custom-400 text-xs leading-relaxed">{{ $service->descripcion }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Mapa de Sedes -->
        @if(($settings['seccion_mapa_visible'] ?? '1') === '1')
        <section class="mt-8 lg:mt-12 py-16 bg-white" id="mapa">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h3 class="text-3xl font-heading font-bold text-navy-800 mb-3">{{ $settings['mapa_titulo'] ?? 'Nuestras Sedes' }}</h3>
                    <p class="text-slate_custom-400 max-w-2xl mx-auto">{{ $settings['mapa_subtitulo'] ?? 'Encuentra nuestras oficinas y sedes en todo el pais' }}</p>
                </div>

                <!-- Buscador -->
                <div class="max-w-2xl mx-auto mb-8">
                    <div style="background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); border:2px solid #e2e8f0; padding:6px; display:flex; align-items:center;" id="buscador-container">
                        <div style="width:44px; height:44px; background:#7f1d1d; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-right:12px;">
                            <i class="fas fa-search" style="color:#fff; font-size:16px;"></i>
                        </div>
                        <input type="text" id="buscador-mapa" placeholder="Buscar sede por nombre, ciudad o direccion..."
                               style="flex:1; border:none; outline:none; font-size:15px; color:#1e293b; font-family:inherit; background:transparent; padding:10px 0;">
                        <button type="button" id="btn-limpiar-busqueda" style="display:none; width:36px; height:36px; border-radius:50%; border:none; background:#f1f5f9; cursor:pointer; margin-left:8px; color:#64748b;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="resultados-busqueda" style="display:none; position:absolute; left:0; right:0; background:#fff; border-radius:16px; box-shadow:0 12px 40px rgba(0,0,0,0.15); z-index:50; max-height:320px; overflow-y:auto; border:1px solid #e2e8f0;" class="relative"></div>
                </div>

                <!-- Mapa -->
                <div class="rounded-2xl overflow-hidden shadow-xl border border-slate_custom-200" style="height: 450px;">
                    <div id="mapa-sedes" style="width:100%; height:100%;"></div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-8">
                    <div class="flex items-center gap-3 bg-slate_custom-50 rounded-xl p-4">
                        <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-briefcase text-burgundy-800"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate_custom-400">Total Sedes</p>
                            <p class="text-lg font-heading font-bold text-navy-800" id="total-sedes">0</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate_custom-50 rounded-xl p-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate_custom-400">Ciudades</p>
                            <p class="text-lg font-heading font-bold text-navy-800" id="total-ciudades">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Footer -->
        <footer style="background-color:#1e293b;" class="py-12" id="contacto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
                    <!-- Oficina Principal -->
                    <div>
                        <h5 class="font-heading font-semibold mb-5" style="color:#fff; font-size:16px;">{{ $settings['footer_oficina_titulo'] ?? 'Oficina Principal' }}</h5>
                        <div class="space-y-3">
                            <p style="color:#cbd5e1; font-size:14px;">{{ $settings['footer_ciudad'] ?? 'Caracas' }}</p>
                            <p style="color:#cbd5e1; font-size:14px;">{{ $settings['footer_direccion_1'] ?? 'Av. Las Mercedes y Calle Guaicaipuro' }}</p>
                            <p style="color:#cbd5e1; font-size:14px;">{{ $settings['footer_direccion_2'] ?? 'Edif. Torre Forum, Piso PB. Local A' }}</p>
                            <p style="color:#cbd5e1; font-size:14px;">{{ $settings['footer_direccion_3'] ?? 'El Rosal, Chacao, Edo. Miranda' }}</p>
                            <div class="flex items-center gap-3 pt-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#22c55e;">
                                    <i class="fas fa-phone text-white text-xs"></i>
                                </div>
                                <span style="color:#e2e8f0; font-size:14px;">{{ $settings['footer_telefono'] ?? '(0212) 951-56-11' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#ef4444;">
                                    <i class="fas fa-envelope text-white text-xs"></i>
                                </div>
                                <span style="color:#e2e8f0; font-size:14px;">{{ $settings['footer_email'] ?? 'info@administradoraintegral.com' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- La Empresa -->
                    <div>
                        <h5 class="font-heading font-semibold mb-5" style="color:#fff; font-size:16px;">{{ $settings['footer_empresa_titulo'] ?? 'La Empresa' }}</h5>
                        <ul class="space-y-2.5">
                            <li><a href="#inicio" style="color:#cbd5e1; font-size:14px;" class="hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs" style="color:#f87171;"></i>{{ $settings['nav_link_1'] ?? 'Home' }}</a></li>
                            <li><a href="#productos" style="color:#cbd5e1; font-size:14px;" class="hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs" style="color:#f87171;"></i>{{ $settings['nav_link_2'] ?? 'Productos' }}</a></li>
                            <li><a href="#residencias" style="color:#cbd5e1; font-size:14px;" class="hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs" style="color:#f87171;"></i>{{ $settings['nav_link_3'] ?? 'Residencias' }}</a></li>
                            <li><a href="#servicios" style="color:#cbd5e1; font-size:14px;" class="hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs" style="color:#f87171;"></i>{{ $settings['nav_link_4'] ?? 'Servicios' }}</a></li>
                            <li><a href="#mapa" style="color:#cbd5e1; font-size:14px;" class="hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs" style="color:#f87171;"></i>{{ $settings['nav_link_5'] ?? 'Ubicaciones' }}</a></li>
                            <li><a href="#contacto" style="color:#cbd5e1; font-size:14px;" class="hover:text-white transition"><i class="fas fa-chevron-right mr-2 text-xs" style="color:#f87171;"></i>{{ $settings['nav_link_6'] ?? 'Contactanos' }}</a></li>
                        </ul>
                    </div>

                    <!-- Solicita tu oferta -->
                    <div>
                        <h5 class="font-heading font-semibold mb-5" style="color:#fff; font-size:16px;">{{ $settings['footer_cta_titulo'] ?? 'Solicita tu oferta de servicio' }}</h5>
                        <p style="color:#94a3b8; font-size:14px; margin-bottom:16px;">
                            {{ $settings['footer_cta_texto'] ?? 'Tiene alguna pregunta? Llamenos o contactenos para mayor informacion.' }}
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#3b82f6;">
                                    <i class="fas fa-map-marker-alt text-white text-xs"></i>
                                </div>
                                <span style="color:#e2e8f0; font-size:14px;">{{ $settings['footer_ubicacion'] ?? 'Caracas, Venezuela' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#22c55e;">
                                    <i class="fas fa-phone text-white text-xs"></i>
                                </div>
                                <span style="color:#e2e8f0; font-size:14px;">{{ $settings['footer_telefono'] ?? '(0212) 951-56-11' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#ef4444;">
                                    <i class="fas fa-envelope text-white text-xs"></i>
                                </div>
                                <span style="color:#e2e8f0; font-size:14px;">{{ $settings['footer_email'] ?? 'info@administradoraintegral.com' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="pt-8 text-center" style="border-top:1px solid #334155;">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-burgundy-800 rounded-lg flex items-center justify-center">
                            <span class="font-heading font-bold text-white text-xs">AI</span>
                        </div>
                        <span class="font-heading font-semibold" style="color:#fff; font-size:14px;">{{ $settings['footer_razon_social'] ?? 'Administradora Integral E.L.B., C.A.' }}</span>
                    </div>
                    <p style="color:#64748b; font-size:13px;">
                        &copy; {{ date('Y') }} Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>

        <!-- Leaflet JS + Map Script -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============ CAROUSEL (translateX slide) ============
            var track = document.getElementById('carousel-track');
            var dots = document.querySelectorAll('.carousel-dot');
            var currentSlide = 0;
            var totalSlides = dots.length || (track ? track.children.length : 0);
            var carouselInterval;

            function updateCarousel() {
                if (track) {
                    track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
                }
                dots.forEach(function(dot, i) {
                    if (i === currentSlide) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }

            if (totalSlides > 1) {
                carouselInterval = setInterval(function() { nextSlide(); }, 5000);
            }

            window.goToSlide = function(index) {
                currentSlide = index;
                updateCarousel();
                clearInterval(carouselInterval);
                carouselInterval = setInterval(function() { nextSlide(); }, 5000);
            };

            window.nextSlide = function() {
                goToSlide((currentSlide + 1) % totalSlides);
            };

            window.prevSlide = function() {
                goToSlide((currentSlide - 1 + totalSlides) % totalSlides);
            };

            // ============ MAP ============
            var map = L.map('mapa-sedes').setView([8.0, -66.0], 7);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap', maxZoom: 18
            }).addTo(map);

            var markers = [];
            var companias = [];

            var iconoPin = L.divIcon({
                html: '<div style="position:relative; width:40px; height:52px;">' +
                      '<svg viewBox="0 0 40 52" width="40" height="52" style="filter: drop-shadow(0 3px 6px rgba(0,0,0,0.35));">' +
                      '<path d="M20 0C8.95 0 0 8.95 0 20c0 14 20 32 20 32s20-18 20-32C40 8.95 31.05 0 20 0z" fill="#7f1d1d"/>' +
                      '<circle cx="20" cy="19" r="13" fill="white"/></svg>' +
                      '<i class="fas fa-briefcase" style="position:absolute; top:10px; left:50%; transform:translateX(-50%); color:#7f1d1d; font-size:15px;"></i></div>',
                className: '', iconSize: [40, 52], iconAnchor: [20, 52], popupAnchor: [0, -48]
            });

            fetch('/api/companias-mapa')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    companias = data;
                    var ciudades = {};

                    data.forEach(function(c) {
                        var marker = L.marker([c.latitud, c.longitud], { icon: iconoPin })
                            .addTo(map)
                            .bindPopup(
                                '<div style="min-width:220px; font-family:Poppins,sans-serif;">' +
                                '<h4 style="margin:0 0 6px; font-weight:700; color:#1e293b; font-size:15px;">' + c.nombre + '</h4>' +
                                '<p style="margin:0 0 4px; color:#64748b; font-size:13px;"><i class="fas fa-id-card" style="color:#7f1d1d; margin-right:6px;"></i>RIF: ' + (c.rif || '') + '</p>' +
                                '<p style="margin:0 0 4px; color:#64748b; font-size:13px;"><i class="fas fa-map-marker-alt" style="color:#7f1d1d; margin-right:6px;"></i>' + (c.direccion || 'Sin direccion') + '</p>' +
                                (c.telefono ? '<p style="margin:0 0 4px; color:#64748b; font-size:13px;"><i class="fas fa-phone" style="color:#22c55e; margin-right:6px;"></i>' + c.telefono + '</p>' : '') +
                                (c.email ? '<p style="margin:0; color:#64748b; font-size:13px;"><i class="fas fa-envelope" style="color:#ef4444; margin-right:6px;"></i>' + c.email + '</p>' : '') +
                                '</div>'
                            );
                        marker._compania = c;
                        markers.push(marker);
                        if (c.direccion) {
                            var ciudad = c.direccion.split(',').pop().trim();
                            ciudades[ciudad] = true;
                        }
                    });

                    document.getElementById('total-sedes').textContent = data.length;
                    document.getElementById('total-ciudades').textContent = Object.keys(ciudades).length || data.length;

                    if (markers.length > 0) {
                        map.fitBounds(L.featureGroup(markers).getBounds().pad(0.2));
                    }
                });

            // Buscador
            var input = document.getElementById('buscador-mapa');
            var resultados = document.getElementById('resultados-busqueda');
            var container = document.getElementById('buscador-container');
            var btnLimpiar = document.getElementById('btn-limpiar-busqueda');

            input.addEventListener('focus', function() {
                container.style.borderColor = '#7f1d1d';
                container.style.boxShadow = '0 4px 20px rgba(127,29,29,0.15)';
            });
            input.addEventListener('blur', function() {
                container.style.borderColor = '#e2e8f0';
                container.style.boxShadow = '0 4px 20px rgba(0,0,0,0.08)';
            });
            btnLimpiar.addEventListener('click', function() {
                input.value = '';
                resultados.style.display = 'none';
                btnLimpiar.style.display = 'none';
            });

            input.addEventListener('input', function() {
                var q = this.value.toLowerCase().trim();
                btnLimpiar.style.display = q.length > 0 ? 'block' : 'none';
                if (q.length < 2) { resultados.style.display = 'none'; return; }

                var filtrados = companias.filter(function(c) {
                    return (c.nombre && c.nombre.toLowerCase().includes(q)) ||
                           (c.direccion && c.direccion.toLowerCase().includes(q)) ||
                           (c.rif && c.rif.toLowerCase().includes(q));
                });

                if (filtrados.length === 0) {
                    resultados.innerHTML = '<div style="padding:16px; text-align:center; color:#94a3b8; font-size:14px;"><i class="fas fa-search" style="margin-right:6px;"></i>No se encontraron resultados</div>';
                    resultados.style.display = 'block';
                    return;
                }

                var html = '';
                filtrados.forEach(function(c) {
                    html += '<button onclick="irASede(' + c.latitud + ',' + c.longitud + ',' + c.id + ')" ' +
                            'style="display:flex; align-items:center; gap:12px; width:100%; padding:12px 16px; border:none; background:none; cursor:pointer; text-align:left; border-bottom:1px solid #f1f5f9;" ' +
                            'onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'none\'">' +
                            '<div style="width:36px; height:36px; background:#fef2f2; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">' +
                            '<i class="fas fa-briefcase" style="color:#7f1d1d; font-size:14px;"></i></div>' +
                            '<div><p style="margin:0; font-weight:600; color:#1e293b; font-size:14px;">' + c.nombre + '</p>' +
                            '<p style="margin:2px 0 0; color:#94a3b8; font-size:12px;">' + (c.direccion || c.rif || '') + '</p></div></button>';
                });
                resultados.innerHTML = html;
                resultados.style.display = 'block';
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !resultados.contains(e.target)) resultados.style.display = 'none';
            });

            window.irASede = function(lat, lng, id) {
                map.setView([lat, lng], 16);
                resultados.style.display = 'none';
                input.value = '';
                btnLimpiar.style.display = 'none';
                markers.forEach(function(m) {
                    if (m._compania && m._compania.id === id) m.openPopup();
                });
            };
        });
        </script>
    </body>
</html>
