<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $settings['titulo_sitio'] ?? config('app.name', 'Administradora Integral') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800,900|rubik:400,500&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            html { scroll-behavior: smooth; }

            /* ── HEADER ── */
            .header-glass {
                background: rgba(255,255,255,0.95);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }

            /* ── HERO ── */
            .hero-carousel { position:relative; width:100%; height:92vh; min-height:560px; overflow:hidden; }
            .hero-track { display:flex; height:100%; transition:transform 0.8s cubic-bezier(.77,0,.18,1); }
            .hero-slide {
                min-width:100%; height:100%;
                display:flex; align-items:center; justify-content:center;
                background-size:cover; background-position:center; position:relative;
            }
            .hero-slide-overlay {
                position:absolute; inset:0;
                background: linear-gradient(135deg, rgba(39,50,114,0.88) 0%, rgba(104,12,62,0.60) 60%, rgba(0,0,0,0.65) 100%);
            }
            .hero-content { position:relative; z-index:10; text-align:center; padding:0 1.5rem; max-width:860px; }

            /* Carousel dots */
            .hero-dot { width:10px; height:10px; border-radius:50%; background:rgba(255,255,255,0.35); cursor:pointer; border:none; transition:all .3s; }
            .hero-dot.active { background:#fff; width:28px; border-radius:5px; }

            /* ── CARDS ── */
            .product-card { transition: transform .3s, box-shadow .3s; }
            .product-card:hover { transform: translateY(-6px); box-shadow: 0 20px 50px rgba(39,50,114,.13); }

            .action-card { transition: transform .35s, box-shadow .35s; }
            .action-card:hover { transform: translateY(-4px); box-shadow: 0 24px 60px rgba(0,0,0,.12); }

            .service-card {
                background:#fff; border-radius:20px; padding:30px 22px; text-align:center;
                border:1px solid #e9eaee; transition:transform .3s, box-shadow .3s;
            }
            .service-card:hover { transform:translateY(-5px); box-shadow:0 16px 44px rgba(39,50,114,.10); }

            .residence-card { position:relative; border-radius:18px; overflow:hidden; height:270px; cursor:pointer; }
            .residence-card img { width:100%; height:100%; object-fit:cover; transition:transform .6s cubic-bezier(.25,.46,.45,.94); }
            .residence-card:hover img { transform:scale(1.07); }
            .residence-overlay {
                position:absolute; bottom:0; inset-inline:0;
                background:linear-gradient(to top, rgba(0,0,0,.82) 0%, rgba(0,0,0,.2) 60%, transparent 100%);
                padding:24px 20px 20px;
            }

            /* ── STATS BAR ── */
            .stats-bar { background: linear-gradient(90deg, #273272 0%, #680c3e 100%); }

            /* ── SECTION TITLE ── */
            .section-eyebrow { font-size:.7rem; font-weight:700; letter-spacing:.18em; text-transform:uppercase; color:#9d9ec0; }
            .section-divider { width:48px; height:3px; background:linear-gradient(90deg,#680c3e,#273272); border-radius:2px; margin:14px auto 0; }

            /* ── BADGE (floating on hero) ── */
            .hero-badge {
                display:inline-flex; align-items:center; gap:8px;
                background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.25);
                backdrop-filter:blur(6px); border-radius:40px;
                padding:8px 18px; color:#fff; font-size:.75rem; font-weight:600;
                letter-spacing:.08em; text-transform:uppercase; margin-bottom:24px;
            }

            /* ── ANIMATE ON SCROLL ── */
            .fade-in { opacity:0; transform:translateY(28px); transition:opacity .65s ease, transform .65s ease; }
            .fade-in.visible { opacity:1; transform:translateY(0); }
        </style>
    </head>
    <body class="font-body antialiased bg-white" x-data="{ mobileMenu: false }">

        <!-- ══════════════════════════════════════
             HEADER
        ══════════════════════════════════════ -->
        <header class="header-glass border-b border-slate_custom-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-[68px]">

                    <!-- Logo -->
                    <a href="/" class="flex items-center gap-3 flex-shrink-0 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-burgundy-800 to-navy-800 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition">
                            <span class="text-white font-heading font-bold text-sm">AI</span>
                        </div>
                        <div class="leading-tight hidden sm:block">
                            <span class="font-heading font-bold text-navy-800 text-sm block tracking-tight">{{ $settings['nombre_empresa'] ?? 'Administradora' }}</span>
                            <span class="font-heading font-semibold text-burgundy-800 text-xs block tracking-wide">{{ $settings['subtitulo_empresa'] ?? 'Integral' }}</span>
                        </div>
                    </a>

                    <!-- Nav Desktop -->
                    <nav class="hidden md:flex items-center gap-0.5">
                        <a href="#inicio"     class="px-3 py-2 text-xs font-semibold text-navy-800 hover:text-burgundy-800 transition rounded-lg hover:bg-burgundy-800/5">{{ $settings['nav_link_1'] ?? 'Home' }}</a>
                        <a href="#productos"  class="px-3 py-2 text-xs font-semibold text-slate_custom-500 hover:text-burgundy-800 transition rounded-lg hover:bg-burgundy-800/5">{{ $settings['nav_link_2'] ?? 'Productos' }}</a>
                        <a href="#residencias" class="px-3 py-2 text-xs font-semibold text-slate_custom-500 hover:text-burgundy-800 transition rounded-lg hover:bg-burgundy-800/5">{{ $settings['nav_link_3'] ?? 'Residencias' }}</a>
                        <a href="#servicios"  class="px-3 py-2 text-xs font-semibold text-slate_custom-500 hover:text-burgundy-800 transition rounded-lg hover:bg-burgundy-800/5">{{ $settings['nav_link_4'] ?? 'Servicios' }}</a>
                        <a href="#mapa"       class="px-3 py-2 text-xs font-semibold text-slate_custom-500 hover:text-burgundy-800 transition rounded-lg hover:bg-burgundy-800/5">{{ $settings['nav_link_5'] ?? 'Ubicaciones' }}</a>
                        <a href="#contacto"   class="px-3 py-2 text-xs font-semibold text-slate_custom-500 hover:text-burgundy-800 transition rounded-lg hover:bg-burgundy-800/5">{{ $settings['nav_link_6'] ?? 'Contactanos' }}</a>
                    </nav>

                    <!-- CTA + mobile toggle -->
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-burgundy-800 to-navy-800 text-white text-xs font-heading font-semibold rounded-xl hover:opacity-90 transition shadow-md shadow-burgundy-800/20">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-burgundy-800 to-navy-800 text-white text-xs font-heading font-semibold rounded-xl hover:opacity-90 transition shadow-md shadow-burgundy-800/20">
                                <i class="fas fa-sign-in-alt"></i>{{ $settings['nav_boton_login'] ?? 'Iniciar Sesion' }}
                            </a>
                        @endauth
                        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-slate_custom-500 hover:bg-slate_custom-100 transition">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Nav -->
                <div x-show="mobileMenu" x-transition class="md:hidden pb-4 border-t border-slate_custom-100 mt-1 pt-3">
                    <div class="flex flex-col gap-0.5">
                        <a href="#inicio"     @click="mobileMenu=false" class="px-3 py-2.5 text-sm font-semibold text-navy-800 rounded-lg hover:bg-slate_custom-100">{{ $settings['nav_link_1'] ?? 'Home' }}</a>
                        <a href="#productos"  @click="mobileMenu=false" class="px-3 py-2.5 text-sm text-slate_custom-500 rounded-lg hover:bg-slate_custom-100">{{ $settings['nav_link_2'] ?? 'Productos' }}</a>
                        <a href="#residencias" @click="mobileMenu=false" class="px-3 py-2.5 text-sm text-slate_custom-500 rounded-lg hover:bg-slate_custom-100">{{ $settings['nav_link_3'] ?? 'Residencias' }}</a>
                        <a href="#servicios"  @click="mobileMenu=false" class="px-3 py-2.5 text-sm text-slate_custom-500 rounded-lg hover:bg-slate_custom-100">{{ $settings['nav_link_4'] ?? 'Servicios' }}</a>
                        <a href="#mapa"       @click="mobileMenu=false" class="px-3 py-2.5 text-sm text-slate_custom-500 rounded-lg hover:bg-slate_custom-100">{{ $settings['nav_link_5'] ?? 'Ubicaciones' }}</a>
                        <a href="#contacto"   @click="mobileMenu=false" class="px-3 py-2.5 text-sm text-slate_custom-500 rounded-lg hover:bg-slate_custom-100">{{ $settings['nav_link_6'] ?? 'Contactanos' }}</a>
                    </div>
                </div>
            </div>
        </header>


        <!-- ══════════════════════════════════════
             HERO CAROUSEL
        ══════════════════════════════════════ -->
        <section class="hero-carousel" id="inicio">
            @if($sliders->count() > 0)
            <div id="hero-track" class="hero-track">
                @foreach($sliders as $index => $slider)
                <div class="hero-slide" style="background-image:url('{{ asset('storage/' . $slider->imagen) }}');">
                    <div class="hero-slide-overlay"></div>
                    <div class="hero-content">
                        <div class="hero-badge">
                            <span class="w-1.5 h-1.5 bg-burgundy-400 rounded-full animate-pulse"></span>
                            {{ $settings['hero_badge'] ?? 'Administradora Integral' }}
                        </div>
                        @if($slider->titulo)
                        <h1 class="text-4xl sm:text-5xl lg:text-7xl font-heading font-black text-white leading-[1.08] mb-6 drop-shadow-xl tracking-tight">
                            {!! nl2br(e($slider->titulo)) !!}
                        </h1>
                        @endif
                        @if($slider->subtitulo)
                        <p class="text-lg sm:text-xl text-white/80 font-light mb-10 leading-relaxed max-w-2xl mx-auto">
                            {{ $slider->subtitulo }}
                        </p>
                        @endif
                        @if($slider->boton_texto)
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="{{ $slider->boton_url ?? '#' }}"
                               class="inline-flex items-center gap-2 px-8 py-4 bg-burgundy-800 text-white font-heading font-semibold rounded-2xl hover:bg-burgundy-700 hover:scale-105 transition-all duration-300 shadow-xl shadow-burgundy-800/30 text-sm">
                                {{ $slider->boton_texto }} <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="#servicios"
                               class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 border border-white/25 text-white font-heading font-semibold rounded-2xl hover:bg-white/20 transition-all duration-300 backdrop-blur-sm text-sm">
                                Ver Servicios <i class="fas fa-chevron-down"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($sliders->count() > 1)
            <!-- Arrows -->
            <button onclick="prevSlide()"
                    class="absolute left-5 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-white/15 hover:bg-white/30 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition-all hover:scale-110">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <button onclick="nextSlide()"
                    class="absolute right-5 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-white/15 hover:bg-white/30 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition-all hover:scale-110">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
            <!-- Dots -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
                @foreach($sliders as $index => $slider)
                <button class="hero-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></button>
                @endforeach
            </div>
            @endif

            @else
            <!-- Fallback -->
            <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600&h=900&fit=crop&q=80');">
                <div class="hero-slide-overlay"></div>
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="w-1.5 h-1.5 bg-burgundy-400 rounded-full animate-pulse"></span>
                        {{ $settings['hero_badge'] ?? 'Administradora Integral' }}
                    </div>
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-heading font-black text-white leading-[1.08] mb-6 drop-shadow-xl tracking-tight">
                        {{ $settings['hero_titulo'] ?? 'Administradora Integral' }}
                    </h1>
                    <p class="text-lg sm:text-xl text-white/80 font-light mb-10 leading-relaxed max-w-2xl mx-auto">
                        {{ $settings['hero_subtitulo'] ?? 'Compania lider en el mercado inmobiliario de Venezuela' }}
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-8 py-4 bg-burgundy-800 text-white font-heading font-semibold rounded-2xl hover:bg-burgundy-700 transition-all duration-300 shadow-xl text-sm">
                            Ingresar al Portal <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#servicios"
                           class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 border border-white/25 text-white font-heading font-semibold rounded-2xl hover:bg-white/20 transition-all duration-300 backdrop-blur-sm text-sm">
                            Ver Servicios <i class="fas fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 right-8 flex flex-col items-center gap-2 z-10 hidden lg:flex">
                <span class="text-white/40 text-[10px] font-semibold tracking-widest uppercase rotate-90 mb-2">Scroll</span>
                <div class="w-px h-14 bg-gradient-to-b from-white/40 to-transparent"></div>
            </div>
        </section>


        <!-- ══════════════════════════════════════
             STATS BAR
        ══════════════════════════════════════ -->
        <div class="stats-bar py-6 fade-in">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <p class="text-3xl font-heading font-black text-white">+20</p>
                    <p class="text-white/50 text-xs uppercase tracking-widest mt-1 font-medium">Años de exp.</p>
                </div>
                <div>
                    <p class="text-3xl font-heading font-black text-white" id="stat-sedes">—</p>
                    <p class="text-white/50 text-xs uppercase tracking-widest mt-1 font-medium">Sedes</p>
                </div>
                <div>
                    <p class="text-3xl font-heading font-black text-white">+500</p>
                    <p class="text-white/50 text-xs uppercase tracking-widest mt-1 font-medium">Condominios</p>
                </div>
                <div>
                    <p class="text-3xl font-heading font-black text-white">24/7</p>
                    <p class="text-white/50 text-xs uppercase tracking-widest mt-1 font-medium">Soporte</p>
                </div>
            </div>
        </div>


        <!-- ══════════════════════════════════════
             NUESTROS PRODUCTOS
        ══════════════════════════════════════ -->
        @if($products->count() > 0 && ($settings['seccion_productos_visible'] ?? '1') === '1')
        <section class="py-20 lg:py-28 bg-white" id="productos">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in">
                    <p class="section-eyebrow">{{ $settings['productos_subtitulo'] ?? 'Soluciones a tu medida' }}</p>
                    <h2 class="text-3xl lg:text-5xl font-heading font-black text-navy-800 mt-3">
                        {{ $settings['productos_titulo'] ?? 'Nuestros Productos' }}
                    </h2>
                    <div class="section-divider"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($products as $product)
                    <div class="product-card relative bg-white rounded-2xl border border-slate_custom-200 overflow-hidden {{ $product->detalle ? 'cursor-pointer' : '' }} fade-in"
                         @if($product->detalle) onclick="document.getElementById('modal-product-detail-{{ $product->id }}').classList.remove('hidden')" @endif>
                        <!-- Top accent -->
                        <div class="h-1 w-full" style="background:linear-gradient(90deg, {{ $product->color }}, {{ $product->color }}aa);"></div>
                        <div class="p-8">
                            <!-- Icon -->
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background:{{ $product->color }}18;">
                                <i class="{{ $product->icono }} text-xl" style="color:{{ $product->color }};"></i>
                            </div>
                            <h3 class="font-heading font-bold text-xl text-navy-800 mb-2">{{ $product->titulo }}</h3>
                            @if($product->slogan)
                            <p class="text-xs font-semibold italic mb-4 tracking-wide" style="color:{{ $product->color }};">"{{ $product->slogan }}"</p>
                            @endif
                            <p class="text-slate_custom-500 text-sm leading-relaxed">{{ $product->descripcion }}</p>
                            @if($product->detalle)
                            <div class="mt-6 pt-5 border-t border-slate_custom-100 flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider" style="color:{{ $product->color }};">Ver detalle</span>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:{{ $product->color }}18;">
                                    <i class="fas fa-arrow-right text-xs" style="color:{{ $product->color }};"></i>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Modals --}}
            @foreach($products as $product)
            @if($product->detalle)
            <div id="modal-product-detail-{{ $product->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
                 onclick="if(event.target===this)this.classList.add('hidden')">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                <div class="relative bg-white rounded-3xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl">
                    <div class="sticky top-0 z-10 bg-white border-b border-slate_custom-100 p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:{{ $product->color }}18;">
                                <i class="{{ $product->icono }} text-xl" style="color:{{ $product->color }};"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-bold text-lg text-navy-800">{{ $product->titulo }}</h3>
                                @if($product->slogan)
                                <p class="text-xs italic" style="color:{{ $product->color }};">"{{ $product->slogan }}"</p>
                                @endif
                            </div>
                        </div>
                        <button onclick="this.closest('[id^=modal-product-detail]').classList.add('hidden')"
                                class="w-10 h-10 rounded-full bg-slate_custom-100 hover:bg-slate_custom-200 flex items-center justify-center text-slate_custom-500 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-y-auto" style="max-height:calc(90vh - 90px);">
                        <p class="text-slate_custom-500 text-sm leading-relaxed mb-6">{{ $product->descripcion }}</p>
                        <div class="border-t border-slate_custom-100 pt-6">{!! $product->detalle !!}</div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </section>
        @endif


        <!-- ══════════════════════════════════════
             TARJETAS DE ACCION
        ══════════════════════════════════════ -->
        @if(($settings['seccion_acciones_visible'] ?? '1') === '1')
        <section class="py-20 lg:py-28" style="background: linear-gradient(135deg, #f8f9ff 0%, #fdf2f8 100%);">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 fade-in">
                    <p class="section-eyebrow">Portal del propietario</p>
                    <h2 class="text-3xl lg:text-4xl font-heading font-black text-navy-800 mt-3">Accede a tu cuenta</h2>
                    <div class="section-divider"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pagar -->
                    <a href="{{ route('login') }}"
                       class="action-card group relative bg-white rounded-3xl border border-slate_custom-200 overflow-hidden flex items-stretch fade-in">
                        <div class="w-1.5 bg-gradient-to-b from-burgundy-800 to-burgundy-600 flex-shrink-0"></div>
                        <div class="p-8 flex flex-col sm:flex-row items-start gap-5 flex-1">
                            <div class="w-16 h-16 bg-burgundy-800/8 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-burgundy-800 group-hover:scale-110 transition-all duration-400">
                                <i class="{{ $settings['cta_pagar_icono'] ?? 'fas fa-file-invoice-dollar' }} text-2xl text-burgundy-800 group-hover:text-white transition-all duration-400"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-heading font-bold text-navy-800 mb-2 group-hover:text-burgundy-800 transition">
                                    {{ $settings['cta_pagar_titulo'] ?? 'Pague su recibo de condominio' }}
                                </h3>
                                <p class="text-sm text-slate_custom-400 leading-relaxed mb-5">
                                    {{ $settings['cta_pagar_texto'] ?? 'Registre su pago por transferencia o deposito bancario de forma rapida y segura.' }}
                                </p>
                                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-burgundy-800/6 text-burgundy-800 text-xs font-heading font-bold rounded-xl group-hover:bg-burgundy-800 group-hover:text-white transition-all duration-300 uppercase tracking-wide">
                                    {{ $settings['cta_pagar_boton'] ?? 'Pagar Aqui' }}
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
                                </span>
                            </div>
                        </div>
                    </a>

                    <!-- Consultar -->
                    <a href="{{ route('login') }}"
                       class="action-card group relative bg-white rounded-3xl border border-slate_custom-200 overflow-hidden flex items-stretch fade-in">
                        <div class="w-1.5 bg-gradient-to-b from-navy-800 to-navy-600 flex-shrink-0"></div>
                        <div class="p-8 flex flex-col sm:flex-row items-start gap-5 flex-1">
                            <div class="w-16 h-16 bg-navy-800/8 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-navy-800 group-hover:scale-110 transition-all duration-400">
                                <i class="{{ $settings['cta_consultar_icono'] ?? 'fas fa-search-dollar' }} text-2xl text-navy-800 group-hover:text-white transition-all duration-400"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-heading font-bold text-navy-800 mb-2 group-hover:text-navy-700 transition">
                                    {{ $settings['cta_consultar_titulo'] ?? 'Consulte su recibo de condominio' }}
                                </h3>
                                <p class="text-sm text-slate_custom-400 leading-relaxed mb-5">
                                    {{ $settings['cta_consultar_texto'] ?? 'Revise sus estados de cuenta, deudas pendientes e historial de pagos realizados.' }}
                                </p>
                                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-navy-800/6 text-navy-800 text-xs font-heading font-bold rounded-xl group-hover:bg-navy-800 group-hover:text-white transition-all duration-300 uppercase tracking-wide">
                                    {{ $settings['cta_consultar_boton'] ?? 'Consultar Aqui' }}
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Afiliacion CTA -->
                <div class="mt-6 fade-in">
                    <a href="{{ route('afiliacion.publica') }}"
                       class="action-card group relative bg-gradient-to-r from-navy-800 to-burgundy-800 rounded-3xl overflow-hidden flex items-stretch">
                        <div class="p-8 flex flex-col sm:flex-row items-center gap-6 flex-1">
                            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-white/20 group-hover:scale-110 transition-all duration-400">
                                <i class="fas fa-user-plus text-2xl text-white"></i>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="text-lg font-heading font-bold text-white mb-1">
                                    Solicite su Afiliacion al Pago Integral
                                </h3>
                                <p class="text-sm text-white/70 leading-relaxed">
                                    Afiliese a nuestro servicio de debito automatico y olvide las colas. Complete el formulario en linea y un asesor le contactara.
                                </p>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 bg-white text-navy-800 text-xs font-heading font-bold rounded-2xl group-hover:bg-burgundy-800 group-hover:text-white transition-all duration-300 uppercase tracking-wide shadow-lg">
                                Afiliarme Ahora
                                <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </section>
        @endif


        <!-- ══════════════════════════════════════
             NUESTRAS RESIDENCIAS
        ══════════════════════════════════════ -->
        @if($residences->count() > 0 && ($settings['seccion_residencias_visible'] ?? '1') === '1')
        <section class="py-20 lg:py-28 bg-navy-900" id="residencias">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 fade-in">
                    <p class="section-eyebrow" style="color:#6475d0;">{{ $settings['residencias_subtitulo'] ?? 'Comunidades que confian en nosotros' }}</p>
                    <h2 class="text-3xl lg:text-5xl font-heading font-black text-white mt-3">
                        {{ $settings['residencias_titulo'] ?? 'Nuestros Clientes' }}
                    </h2>
                    <div class="section-divider"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($residences as $residence)
                    <div class="residence-card group fade-in">
                        <img src="{{ asset('storage/' . $residence->imagen) }}" alt="{{ $residence->nombre }}" loading="lazy">
                        <div class="residence-overlay">
                            <h4 class="text-white font-heading font-bold text-base">{{ $residence->nombre }}</h4>
                            @if($residence->ubicacion)
                            <p class="text-white/60 text-xs flex items-center gap-1.5 mt-1.5">
                                <i class="fas fa-map-marker-alt text-burgundy-400"></i> {{ $residence->ubicacion }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif


        <!-- ══════════════════════════════════════
             NUESTROS SERVICIOS
        ══════════════════════════════════════ -->
        @if($services->count() > 0 && ($settings['seccion_servicios_visible'] ?? '1') === '1')
        <section class="py-20 lg:py-28 bg-slate_custom-100/60" id="servicios">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 fade-in">
                    <p class="section-eyebrow">{{ $settings['servicios_subtitulo'] ?? 'Adaptados a tus necesidades' }}</p>
                    <h2 class="text-3xl lg:text-5xl font-heading font-black text-navy-800 mt-3">
                        {{ $settings['servicios_titulo'] ?? 'Nuestros Servicios' }}
                    </h2>
                    <div class="section-divider"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($services as $service)
                    <div class="service-card fade-in">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5"
                             style="background:{{ $service->color_icono }}18;">
                            <i class="{{ $service->icono }} text-2xl" style="color:{{ $service->color_icono }};"></i>
                        </div>
                        <h4 class="font-heading font-bold text-navy-800 text-sm mb-2 leading-snug">{{ $service->titulo }}</h4>
                        <p class="text-slate_custom-400 text-xs leading-relaxed">{{ $service->descripcion }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif


        <!-- ══════════════════════════════════════
             MAPA DE SEDES
        ══════════════════════════════════════ -->
        @if(($settings['seccion_mapa_visible'] ?? '1') === '1')
        <section class="py-20 lg:py-28 bg-white" id="mapa">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 fade-in">
                    <p class="section-eyebrow">Cobertura nacional</p>
                    <h2 class="text-3xl lg:text-4xl font-heading font-black text-navy-800 mt-3">
                        {{ $settings['mapa_titulo'] ?? 'Nuestras Sedes' }}
                    </h2>
                    <p class="text-slate_custom-400 text-sm mt-3 max-w-xl mx-auto">{{ $settings['mapa_subtitulo'] ?? 'Encuentra nuestras oficinas y sedes en todo el pais' }}</p>
                    <div class="section-divider"></div>
                </div>

                <!-- Stats + Search row -->
                <div class="flex flex-col md:flex-row items-center gap-4 mb-8">
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <div class="flex items-center gap-3 bg-slate_custom-100 rounded-2xl px-5 py-3">
                            <div class="w-9 h-9 bg-burgundy-800/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-briefcase text-burgundy-800 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate_custom-400 uppercase tracking-wide font-semibold">Sedes</p>
                                <p class="text-lg font-heading font-black text-navy-800 leading-none" id="total-sedes">—</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-slate_custom-100 rounded-2xl px-5 py-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate_custom-400 uppercase tracking-wide font-semibold">Ciudades</p>
                                <p class="text-lg font-heading font-black text-navy-800 leading-none" id="total-ciudades">—</p>
                            </div>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="flex-1 w-full relative" id="buscador-wrapper">
                        <div class="flex items-center bg-white rounded-2xl border-2 border-slate_custom-200 px-3 gap-3 transition" id="buscador-container" style="height:52px;">
                            <div class="w-9 h-9 bg-burgundy-800 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-search text-white text-xs"></i>
                            </div>
                            <input type="text" id="buscador-mapa" placeholder="Buscar sede por nombre, ciudad o dirección..."
                                   class="flex-1 border-none outline-none text-sm text-navy-800 bg-transparent placeholder-slate_custom-300 font-body">
                            <button type="button" id="btn-limpiar-busqueda" class="hidden w-8 h-8 rounded-full bg-slate_custom-100 hover:bg-slate_custom-200 text-slate_custom-400 flex items-center justify-center transition">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div id="resultados-busqueda" class="hidden absolute left-0 right-0 top-[56px] bg-white rounded-2xl shadow-2xl z-50 max-h-72 overflow-y-auto border border-slate_custom-200"></div>
                    </div>
                </div>

                <!-- Map -->
                <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate_custom-200 fade-in" style="height:460px;">
                    <div id="mapa-sedes" style="width:100%; height:100%;"></div>
                </div>
            </div>
        </section>
        @endif


        <!-- ══════════════════════════════════════
             FOOTER
        ══════════════════════════════════════ -->
        <footer class="bg-navy-900 pt-16 pb-8" id="contacto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-12 pb-12 border-b border-white/10">

                    <!-- Brand + address -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-11 h-11 bg-gradient-to-br from-burgundy-800 to-navy-700 rounded-2xl flex items-center justify-center">
                                <span class="font-heading font-black text-white text-sm">AI</span>
                            </div>
                            <div>
                                <p class="font-heading font-bold text-white text-sm">{{ $settings['footer_razon_social'] ?? 'Administradora Integral' }}</p>
                                <p class="text-white/40 text-xs">E.L.B., C.A.</p>
                            </div>
                        </div>
                        <div class="space-y-2.5">
                            <p class="text-white/55 text-sm">{{ $settings['footer_ciudad'] ?? 'Caracas' }}</p>
                            <p class="text-white/55 text-sm">{{ $settings['footer_direccion_1'] ?? 'Av. Las Mercedes y Calle Guaicaipuro' }}</p>
                            <p class="text-white/55 text-sm">{{ $settings['footer_direccion_2'] ?? 'Edif. Torre Forum, Piso PB. Local A' }}</p>
                            <p class="text-white/55 text-sm">{{ $settings['footer_direccion_3'] ?? 'El Rosal, Chacao, Edo. Miranda' }}</p>
                            <div class="flex items-center gap-2.5 pt-1">
                                <span class="w-7 h-7 bg-emerald-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-emerald-400 text-xs"></i>
                                </span>
                                <span class="text-white/70 text-sm">{{ $settings['footer_telefono'] ?? '(0212) 951-56-11' }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 bg-red-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-red-400 text-xs"></i>
                                </span>
                                <span class="text-white/70 text-sm">{{ $settings['footer_email'] ?? 'info@administradoraintegral.com' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick links -->
                    <div>
                        <h5 class="font-heading font-semibold text-white text-sm mb-6">{{ $settings['footer_empresa_titulo'] ?? 'La Empresa' }}</h5>
                        <ul class="space-y-2.5">
                            @foreach([
                                ['#inicio',      $settings['nav_link_1'] ?? 'Home'],
                                ['#productos',   $settings['nav_link_2'] ?? 'Productos'],
                                ['#residencias', $settings['nav_link_3'] ?? 'Residencias'],
                                ['#servicios',   $settings['nav_link_4'] ?? 'Servicios'],
                                ['#mapa',        $settings['nav_link_5'] ?? 'Ubicaciones'],
                                ['#contacto',    $settings['nav_link_6'] ?? 'Contactanos'],
                            ] as [$href, $label])
                            <li>
                                <a href="{{ $href }}" class="flex items-center gap-2.5 text-white/50 hover:text-white text-sm transition group">
                                    <span class="w-1 h-1 rounded-full bg-burgundy-800 group-hover:w-4 transition-all duration-300"></span>
                                    {{ $label }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- CTA -->
                    <div>
                        <h5 class="font-heading font-semibold text-white text-sm mb-4">{{ $settings['footer_cta_titulo'] ?? 'Solicita tu oferta de servicio' }}</h5>
                        <p class="text-white/40 text-sm mb-6 leading-relaxed">
                            {{ $settings['footer_cta_texto'] ?? 'Tiene alguna pregunta? Llamenos o contactenos para mayor informacion.' }}
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-blue-400 text-xs"></i>
                                </span>
                                <span class="text-white/60 text-sm">{{ $settings['footer_ubicacion'] ?? 'Caracas, Venezuela' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-emerald-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-emerald-400 text-xs"></i>
                                </span>
                                <span class="text-white/60 text-sm">{{ $settings['footer_telefono'] ?? '(0212) 951-56-11' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-red-400 text-xs"></i>
                                </span>
                                <span class="text-white/60 text-sm">{{ $settings['footer_email'] ?? 'info@administradoraintegral.com' }}</span>
                            </div>
                        </div>
                        <!-- Login CTA -->
                        <a href="{{ route('login') }}"
                           class="mt-8 inline-flex items-center gap-2 w-full justify-center px-6 py-3 bg-gradient-to-r from-burgundy-800 to-navy-700 text-white text-xs font-heading font-bold rounded-2xl hover:opacity-90 transition shadow-lg">
                            <i class="fas fa-sign-in-alt"></i> Ingresar al Portal
                        </a>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="text-center">
                    <p class="text-white/25 text-xs">
                        &copy; {{ date('Y') }} {{ $settings['footer_razon_social'] ?? 'Administradora Integral E.L.B., C.A.' }} — Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>


        <!-- ══════════════════════════════════════
             SCRIPTS
        ══════════════════════════════════════ -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── CAROUSEL ──────────────────────────────
            var track       = document.getElementById('hero-track');
            var dots        = document.querySelectorAll('.hero-dot');
            var currentSlide = 0;
            var totalSlides  = dots.length || (track ? track.children.length : 0);
            var timer;

            function updateCarousel() {
                if (track) track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
                dots.forEach(function(d,i){ d.classList.toggle('active', i === currentSlide); });
            }
            if (totalSlides > 1) timer = setInterval(function(){ window.nextSlide(); }, 5500);

            window.goToSlide = function(i) {
                currentSlide = i; updateCarousel();
                clearInterval(timer);
                timer = setInterval(function(){ window.nextSlide(); }, 5500);
            };
            window.nextSlide = function(){ window.goToSlide((currentSlide+1) % totalSlides); };
            window.prevSlide = function(){ window.goToSlide((currentSlide-1+totalSlides) % totalSlides); };

            // ── FADE-IN ON SCROLL ─────────────────────
            var fadeEls = document.querySelectorAll('.fade-in');
            var io = new IntersectionObserver(function(entries) {
                entries.forEach(function(e){ if(e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
            }, { threshold: 0.12 });
            fadeEls.forEach(function(el){ io.observe(el); });

            // ── MAP ───────────────────────────────────
            var map = L.map('mapa-sedes').setView([8.0, -66.0], 7);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap', maxZoom: 18
            }).addTo(map);

            var markers  = [];
            var companias = [];

            var iconoPin = L.divIcon({
                html: '<div style="position:relative;width:40px;height:52px;">' +
                      '<svg viewBox="0 0 40 52" width="40" height="52" style="filter:drop-shadow(0 3px 6px rgba(0,0,0,.35));">' +
                      '<path d="M20 0C8.95 0 0 8.95 0 20c0 14 20 32 20 32s20-18 20-32C40 8.95 31.05 0 20 0z" fill="#680c3e"/>' +
                      '<circle cx="20" cy="19" r="13" fill="white"/></svg>' +
                      '<i class="fas fa-briefcase" style="position:absolute;top:10px;left:50%;transform:translateX(-50%);color:#680c3e;font-size:14px;"></i></div>',
                className:'', iconSize:[40,52], iconAnchor:[20,52], popupAnchor:[0,-48]
            });

            fetch('/api/companias-mapa')
                .then(function(r){ return r.json(); })
                .then(function(data) {
                    companias = data;
                    var ciudades = {};
                    data.forEach(function(c) {
                        var m = L.marker([c.latitud, c.longitud], { icon: iconoPin }).addTo(map)
                            .bindPopup(
                                '<div style="min-width:220px;font-family:Poppins,sans-serif;">' +
                                '<h4 style="margin:0 0 6px;font-weight:700;color:#1e293b;font-size:15px;">' + c.nombre + '</h4>' +
                                '<p style="margin:0 0 4px;color:#64748b;font-size:13px;"><i class="fas fa-id-card" style="color:#680c3e;margin-right:6px;"></i>RIF: ' + (c.rif||'') + '</p>' +
                                '<p style="margin:0 0 4px;color:#64748b;font-size:13px;"><i class="fas fa-map-marker-alt" style="color:#680c3e;margin-right:6px;"></i>' + (c.direccion||'Sin direccion') + '</p>' +
                                (c.telefono ? '<p style="margin:0 0 4px;color:#64748b;font-size:13px;"><i class="fas fa-phone" style="color:#22c55e;margin-right:6px;"></i>' + c.telefono + '</p>' : '') +
                                (c.email    ? '<p style="margin:0;color:#64748b;font-size:13px;"><i class="fas fa-envelope" style="color:#ef4444;margin-right:6px;"></i>' + c.email + '</p>' : '') +
                                '</div>'
                            );
                        m._compania = c;
                        markers.push(m);
                        if(c.direccion){ ciudades[c.direccion.split(',').pop().trim()] = true; }
                    });
                    var statSedes = document.getElementById('stat-sedes');
                    if(statSedes) statSedes.textContent = data.length || '—';
                    document.getElementById('total-sedes').textContent    = data.length;
                    document.getElementById('total-ciudades').textContent = Object.keys(ciudades).length || data.length;
                    if(markers.length > 0) map.fitBounds(L.featureGroup(markers).getBounds().pad(0.2));
                });

            // ── BUSCADOR ──────────────────────────────
            var input      = document.getElementById('buscador-mapa');
            var resultados = document.getElementById('resultados-busqueda');
            var container  = document.getElementById('buscador-container');
            var btnLimpiar = document.getElementById('btn-limpiar-busqueda');

            input.addEventListener('focus', function(){ container.style.borderColor='#680c3e'; container.style.boxShadow='0 0 0 4px rgba(104,12,62,.08)'; });
            input.addEventListener('blur',  function(){ container.style.borderColor='#e9eaee'; container.style.boxShadow='none'; });

            btnLimpiar.addEventListener('click', function(){
                input.value=''; resultados.classList.add('hidden'); btnLimpiar.classList.add('hidden');
            });

            input.addEventListener('input', function() {
                var q = this.value.toLowerCase().trim();
                btnLimpiar.classList.toggle('hidden', q.length === 0);
                if(q.length < 2){ resultados.classList.add('hidden'); return; }

                var filtrados = companias.filter(function(c){
                    return (c.nombre && c.nombre.toLowerCase().includes(q)) ||
                           (c.direccion && c.direccion.toLowerCase().includes(q)) ||
                           (c.rif && c.rif.toLowerCase().includes(q));
                });

                if(filtrados.length === 0){
                    resultados.innerHTML = '<div style="padding:20px;text-align:center;color:#9d9ec0;font-size:13px;"><i class="fas fa-search mr-2"></i>Sin resultados</div>';
                    resultados.classList.remove('hidden'); return;
                }

                resultados.innerHTML = filtrados.map(function(c){
                    return '<button type="button" onclick="(function(){' +
                        'document.getElementById(\'buscador-mapa\').value=\'' + c.nombre.replace(/'/g,"\\'") + '\';' +
                        'document.getElementById(\'resultados-busqueda\').classList.add(\'hidden\');' +
                        'var mk=window._sedeMarkers&&window._sedeMarkers.find(function(m){return m._compania&&m._compania.id===' + (c.id||0) + ';});' +
                        'if(mk){window._mapaInstance.setView(mk.getLatLng(),15);mk.openPopup();}' +
                        '})()" ' +
                        'style="display:flex;align-items:center;gap:12px;width:100%;padding:12px 16px;border:none;background:none;cursor:pointer;text-align:left;border-bottom:1px solid #f1f5f9;" ' +
                        'onmouseover="this.style.background=\'#f8f9ff\'" onmouseout="this.style.background=\'none\'">' +
                        '<div style="width:36px;height:36px;background:#680c3e18;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                        '<i class="fas fa-map-marker-alt" style="color:#680c3e;font-size:13px;"></i></div>' +
                        '<div><p style="font-weight:600;color:#273272;font-size:13px;margin:0;">' + c.nombre + '</p>' +
                        '<p style="color:#9d9ec0;font-size:11px;margin:2px 0 0;">' + (c.direccion||'Sin direccion') + '</p></div></button>';
                }).join('');
                resultados.classList.remove('hidden');

                window._mapaInstance   = map;
                window._sedeMarkers    = markers;
            });

            document.addEventListener('click', function(e){
                if(!document.getElementById('buscador-wrapper').contains(e.target)){
                    resultados.classList.add('hidden');
                }
            });
        });
        </script>
    </body>
</html>
