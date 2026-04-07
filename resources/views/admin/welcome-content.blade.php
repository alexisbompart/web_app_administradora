<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading font-bold text-xl text-navy-800">Gestion de Pagina Web</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Administra el contenido de la pagina de inicio</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ activeTab: 'sliders' }" class="space-y-6">
        {{-- Alerts --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        {{-- Tabs --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate_custom-200 p-1.5 flex flex-wrap gap-1">
            <button @click="activeTab = 'sliders'" :class="activeTab === 'sliders' ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-slate_custom-50'" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-images mr-2"></i>Carrusel
            </button>
            <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-slate_custom-50'" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-box-open mr-2"></i>Productos
            </button>
            <button @click="activeTab = 'services'" :class="activeTab === 'services' ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-slate_custom-50'" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-concierge-bell mr-2"></i>Servicios
            </button>
            <button @click="activeTab = 'residences'" :class="activeTab === 'residences' ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-slate_custom-50'" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-building mr-2"></i>Residencias
            </button>
            <button @click="activeTab = 'popups'" :class="activeTab === 'popups' ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-slate_custom-50'" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-window-restore mr-2"></i>Ventana Emergente
            </button>
            <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-burgundy-800 text-white' : 'text-slate_custom-500 hover:bg-slate_custom-50'" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-cog mr-2"></i>Textos Generales
            </button>
        </div>

        {{-- ===================== SLIDERS TAB ===================== --}}
        <div x-show="activeTab === 'sliders'" x-transition>
            <div class="bg-white rounded-2xl shadow-sm border border-slate_custom-200">
                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-navy-800">Carrusel de Imagenes</h3>
                        <p class="text-sm text-slate_custom-400">Imagenes que se muestran en el hero de la pagina de inicio</p>
                    </div>
                    <button onclick="document.getElementById('modal-slider-new').classList.remove('hidden')" class="btn-primary text-sm">
                        <i class="fas fa-plus mr-2"></i>Agregar Slide
                    </button>
                </div>
                <div class="p-6">
                    @if($sliders->isEmpty())
                    <div class="text-center py-12 text-slate_custom-400">
                        <i class="fas fa-images text-4xl mb-3"></i>
                        <p>No hay slides configurados. Agrega el primero.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($sliders as $slider)
                        <div class="border border-slate_custom-200 rounded-xl overflow-hidden {{ !$slider->activo ? 'opacity-50' : '' }}">
                            <div class="relative h-40">
                                <img src="{{ asset('storage/' . $slider->imagen) }}" alt="{{ $slider->titulo }}" class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2 flex gap-1">
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $slider->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $slider->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold bg-navy-800 text-white">#{{ $slider->orden }}</span>
                                </div>
                            </div>
                            <div class="p-3">
                                <h4 class="font-semibold text-sm text-navy-800">{{ $slider->titulo ?: 'Sin titulo' }}</h4>
                                <p class="text-xs text-slate_custom-400">{{ $slider->subtitulo ?: 'Sin subtitulo' }}</p>
                                <div class="flex gap-2 mt-3">
                                    <button onclick="document.getElementById('modal-slider-{{ $slider->id }}').classList.remove('hidden')" class="text-xs text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit mr-1"></i>Editar
                                    </button>
                                    <form action="{{ route('admin.welcome.sliders.destroy', $slider) }}" method="POST" onsubmit="return confirm('Eliminar este slide?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:text-red-800"><i class="fas fa-trash mr-1"></i>Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Edit Slider --}}
                        <div id="modal-slider-{{ $slider->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                                    <h3 class="font-heading font-bold text-navy-800">Editar Slide</h3>
                                    <button onclick="this.closest('[id^=modal-slider]').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.welcome.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo</label>
                                        <input type="text" name="titulo" value="{{ $slider->titulo }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Subtitulo</label>
                                        <input type="text" name="subtitulo" value="{{ $slider->subtitulo }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Imagen (dejar vacio para mantener actual)</label>
                                        <input type="file" name="imagen" accept="image/*" class="w-full text-sm">
                                        <img src="{{ asset('storage/' . $slider->imagen) }}" class="mt-2 h-20 rounded-lg object-cover">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Texto Boton</label>
                                            <input type="text" name="boton_texto" value="{{ $slider->boton_texto }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">URL Boton</label>
                                            <input type="text" name="boton_url" value="{{ $slider->boton_url }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                            <input type="number" name="orden" value="{{ $slider->orden }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="activo" {{ $slider->activo ? 'checked' : '' }} class="rounded border-slate_custom-300 text-burgundy-800">
                                                <span class="text-sm font-semibold text-navy-800">Activo</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" onclick="this.closest('[id^=modal-slider]').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                                        <button type="submit" class="btn-primary text-sm">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal New Slider --}}
            <div id="modal-slider-new" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                        <h3 class="font-heading font-bold text-navy-800">Nuevo Slide</h3>
                        <button onclick="document.getElementById('modal-slider-new').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.welcome.sliders.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo</label>
                            <input type="text" name="titulo" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Titulo del slide">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Subtitulo</label>
                            <input type="text" name="subtitulo" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Subtitulo del slide">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Imagen *</label>
                            <input type="file" name="imagen" accept="image/*" required class="w-full text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Texto Boton</label>
                                <input type="text" name="boton_texto" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Ej: Ver mas">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">URL Boton</label>
                                <input type="text" name="boton_url" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Ej: #contacto">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                <input type="number" name="orden" value="0" class="w-full rounded-lg border-slate_custom-300 text-sm">
                            </div>
                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="activo" checked class="rounded border-slate_custom-300 text-burgundy-800">
                                    <span class="text-sm font-semibold text-navy-800">Activo</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-slider-new').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===================== PRODUCTS TAB ===================== --}}
        <div x-show="activeTab === 'products'" x-transition style="display:none;">
            <div class="bg-white rounded-2xl shadow-sm border border-slate_custom-200">
                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-navy-800">Nuestros Productos</h3>
                        <p class="text-sm text-slate_custom-400">Productos/servicios principales que se muestran debajo del carrusel</p>
                    </div>
                    <button onclick="document.getElementById('modal-product-new').classList.remove('hidden')" class="btn-primary text-sm">
                        <i class="fas fa-plus mr-2"></i>Agregar Producto
                    </button>
                </div>
                <div class="p-6">
                    @if($products->isEmpty())
                    <div class="text-center py-12 text-slate_custom-400">
                        <i class="fas fa-box-open text-4xl mb-3"></i>
                        <p>No hay productos configurados.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($products as $product)
                        <div class="border border-slate_custom-200 rounded-xl overflow-hidden {{ !$product->activo ? 'opacity-50' : '' }}">
                            <div class="h-1.5 w-full" style="background: {{ $product->color }};"></div>
                            <div class="p-4">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $product->color }}15;">
                                        <i class="{{ $product->icono }} text-lg" style="color: {{ $product->color }};"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-sm text-navy-800">{{ $product->titulo }}</h4>
                                        @if($product->slogan)
                                        <p class="text-xs italic" style="color: {{ $product->color }};">"{{ $product->slogan }}"</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-xs text-slate_custom-400 line-clamp-3 mb-3">{{ $product->descripcion }}</p>
                                <div class="flex items-center justify-between pt-3 border-t border-slate_custom-100">
                                    <span class="text-xs {{ $product->activo ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $product->activo ? 'Activo' : 'Inactivo' }} | Orden: {{ $product->orden }}
                                    </span>
                                    <div class="flex gap-2">
                                        <button onclick="document.getElementById('modal-product-{{ $product->id }}').classList.remove('hidden')" class="text-xs text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.welcome.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Eliminar?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Edit Product --}}
                        <div id="modal-product-{{ $product->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                                    <h3 class="font-heading font-bold text-navy-800">Editar Producto</h3>
                                    <button onclick="this.closest('[id^=modal-product]').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.welcome.products.update', $product) }}" method="POST" class="p-6 space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo *</label>
                                        <input type="text" name="titulo" value="{{ $product->titulo }}" required class="w-full rounded-lg border-slate_custom-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Slogan</label>
                                        <input type="text" name="slogan" value="{{ $product->slogan }}" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Frase corta destacada">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Descripcion *</label>
                                        <textarea name="descripcion" rows="4" required class="w-full rounded-lg border-slate_custom-300 text-sm">{{ $product->descripcion }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Detalle (HTML - se muestra en modal al hacer click)</label>
                                        <textarea name="detalle" rows="6" class="w-full rounded-lg border-slate_custom-300 text-sm font-mono text-xs">{{ $product->detalle }}</textarea>
                                        <p class="text-xs text-slate_custom-400 mt-1">Acepta HTML. Si esta vacio, la tarjeta no sera clickeable.</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Icono FontAwesome *</label>
                                            <input type="text" name="icono" value="{{ $product->icono }}" required class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Color</label>
                                            <input type="color" name="color" value="{{ $product->color }}" class="w-full h-10 rounded-lg border-slate_custom-300">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                            <input type="number" name="orden" value="{{ $product->orden }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="activo" {{ $product->activo ? 'checked' : '' }} class="rounded border-slate_custom-300 text-burgundy-800">
                                                <span class="text-sm font-semibold text-navy-800">Activo</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" onclick="this.closest('[id^=modal-product]').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                                        <button type="submit" class="btn-primary text-sm">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal New Product --}}
            <div id="modal-product-new" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                        <h3 class="font-heading font-bold text-navy-800">Nuevo Producto</h3>
                        <button onclick="document.getElementById('modal-product-new').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.welcome.products.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo *</label>
                            <input type="text" name="titulo" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Nombre del producto">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Slogan</label>
                            <input type="text" name="slogan" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Frase corta destacada entre comillas">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Descripcion *</label>
                            <textarea name="descripcion" rows="4" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Descripcion del producto/servicio"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Detalle (HTML - se muestra en modal al hacer click)</label>
                            <textarea name="detalle" rows="6" class="w-full rounded-lg border-slate_custom-300 text-sm font-mono text-xs" placeholder="Contenido HTML detallado (opcional)"></textarea>
                            <p class="text-xs text-slate_custom-400 mt-1">Acepta HTML. Si esta vacio, la tarjeta no sera clickeable.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Icono FontAwesome *</label>
                                <input type="text" name="icono" value="fas fa-building" required class="w-full rounded-lg border-slate_custom-300 text-sm">
                                <p class="text-xs text-slate_custom-400 mt-1">Ej: fas fa-building, fas fa-cogs</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Color</label>
                                <input type="color" name="color" value="#7f1d1d" class="w-full h-10 rounded-lg border-slate_custom-300">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                <input type="number" name="orden" value="0" class="w-full rounded-lg border-slate_custom-300 text-sm">
                            </div>
                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="activo" checked class="rounded border-slate_custom-300 text-burgundy-800">
                                    <span class="text-sm font-semibold text-navy-800">Activo</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-product-new').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===================== SERVICES TAB ===================== --}}
        <div x-show="activeTab === 'services'" x-transition style="display:none;">
            <div class="bg-white rounded-2xl shadow-sm border border-slate_custom-200">
                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-navy-800">Nuestros Servicios</h3>
                        <p class="text-sm text-slate_custom-400">Servicios que se muestran en la pagina de inicio</p>
                    </div>
                    <button onclick="document.getElementById('modal-service-new').classList.remove('hidden')" class="btn-primary text-sm">
                        <i class="fas fa-plus mr-2"></i>Agregar Servicio
                    </button>
                </div>
                <div class="p-6">
                    @if($services->isEmpty())
                    <div class="text-center py-12 text-slate_custom-400">
                        <i class="fas fa-concierge-bell text-4xl mb-3"></i>
                        <p>No hay servicios configurados.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($services as $service)
                        <div class="border border-slate_custom-200 rounded-xl p-4 {{ !$service->activo ? 'opacity-50' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $service->color_icono }}20;">
                                    <i class="{{ $service->icono }} text-xl" style="color: {{ $service->color_icono }};"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-sm text-navy-800">{{ $service->titulo }}</h4>
                                    <p class="text-xs text-slate_custom-400 mt-1 line-clamp-2">{{ $service->descripcion }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate_custom-100">
                                <span class="text-xs {{ $service->activo ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $service->activo ? 'Activo' : 'Inactivo' }} | Orden: {{ $service->orden }}
                                </span>
                                <div class="flex gap-2">
                                    <button onclick="document.getElementById('modal-service-{{ $service->id }}').classList.remove('hidden')" class="text-xs text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.welcome.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Edit Service --}}
                        <div id="modal-service-{{ $service->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                                    <h3 class="font-heading font-bold text-navy-800">Editar Servicio</h3>
                                    <button onclick="this.closest('[id^=modal-service]').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.welcome.services.update', $service) }}" method="POST" class="p-6 space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo *</label>
                                        <input type="text" name="titulo" value="{{ $service->titulo }}" required class="w-full rounded-lg border-slate_custom-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Descripcion *</label>
                                        <textarea name="descripcion" rows="3" required class="w-full rounded-lg border-slate_custom-300 text-sm">{{ $service->descripcion }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Icono FontAwesome *</label>
                                            <input type="text" name="icono" value="{{ $service->icono }}" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="fas fa-building">
                                            <p class="text-xs text-slate_custom-400 mt-1">Ej: fas fa-building, fas fa-phone</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Color Icono</label>
                                            <input type="color" name="color_icono" value="{{ $service->color_icono }}" class="w-full h-10 rounded-lg border-slate_custom-300">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                            <input type="number" name="orden" value="{{ $service->orden }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="activo" {{ $service->activo ? 'checked' : '' }} class="rounded border-slate_custom-300 text-burgundy-800">
                                                <span class="text-sm font-semibold text-navy-800">Activo</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" onclick="this.closest('[id^=modal-service]').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                                        <button type="submit" class="btn-primary text-sm">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal New Service --}}
            <div id="modal-service-new" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                        <h3 class="font-heading font-bold text-navy-800">Nuevo Servicio</h3>
                        <button onclick="document.getElementById('modal-service-new').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.welcome.services.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo *</label>
                            <input type="text" name="titulo" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Nombre del servicio">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Descripcion *</label>
                            <textarea name="descripcion" rows="3" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Descripcion del servicio"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Icono FontAwesome *</label>
                                <input type="text" name="icono" value="fas fa-cog" required class="w-full rounded-lg border-slate_custom-300 text-sm">
                                <p class="text-xs text-slate_custom-400 mt-1">Ej: fas fa-building, fas fa-phone</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Color Icono</label>
                                <input type="color" name="color_icono" value="#d4a017" class="w-full h-10 rounded-lg border-slate_custom-300">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                <input type="number" name="orden" value="0" class="w-full rounded-lg border-slate_custom-300 text-sm">
                            </div>
                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="activo" checked class="rounded border-slate_custom-300 text-burgundy-800">
                                    <span class="text-sm font-semibold text-navy-800">Activo</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-service-new').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===================== RESIDENCES TAB ===================== --}}
        <div x-show="activeTab === 'residences'" x-transition style="display:none;">
            <div class="bg-white rounded-2xl shadow-sm border border-slate_custom-200">
                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-navy-800">Nuestras Residencias</h3>
                        <p class="text-sm text-slate_custom-400">Fachadas de las residencias que administramos</p>
                    </div>
                    <button onclick="document.getElementById('modal-residence-new').classList.remove('hidden')" class="btn-primary text-sm">
                        <i class="fas fa-plus mr-2"></i>Agregar Residencia
                    </button>
                </div>
                <div class="p-6">
                    @if($residences->isEmpty())
                    <div class="text-center py-12 text-slate_custom-400">
                        <i class="fas fa-building text-4xl mb-3"></i>
                        <p>No hay residencias configuradas.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($residences as $residence)
                        <div class="border border-slate_custom-200 rounded-xl overflow-hidden {{ !$residence->activo ? 'opacity-50' : '' }}">
                            <div class="relative h-40">
                                <img src="{{ asset('storage/' . $residence->imagen) }}" alt="{{ $residence->nombre }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                    <h4 class="text-white font-bold text-sm">{{ $residence->nombre }}</h4>
                                    @if($residence->ubicacion)
                                    <p class="text-white/70 text-xs">{{ $residence->ubicacion }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="p-3 flex items-center justify-between">
                                <span class="text-xs {{ $residence->activo ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $residence->activo ? 'Activo' : 'Inactivo' }} | Orden: {{ $residence->orden }}
                                </span>
                                <div class="flex gap-2">
                                    <button onclick="document.getElementById('modal-residence-{{ $residence->id }}').classList.remove('hidden')" class="text-xs text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.welcome.residences.destroy', $residence) }}" method="POST" onsubmit="return confirm('Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Edit Residence --}}
                        <div id="modal-residence-{{ $residence->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                                    <h3 class="font-heading font-bold text-navy-800">Editar Residencia</h3>
                                    <button onclick="this.closest('[id^=modal-residence]').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.welcome.residences.update', $residence) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Nombre *</label>
                                        <input type="text" name="nombre" value="{{ $residence->nombre }}" required class="w-full rounded-lg border-slate_custom-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Ubicacion</label>
                                        <input type="text" name="ubicacion" value="{{ $residence->ubicacion }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Imagen</label>
                                        <input type="file" name="imagen" accept="image/*" class="w-full text-sm">
                                        <img src="{{ asset('storage/' . $residence->imagen) }}" class="mt-2 h-20 rounded-lg object-cover">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                            <input type="number" name="orden" value="{{ $residence->orden }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="activo" {{ $residence->activo ? 'checked' : '' }} class="rounded border-slate_custom-300 text-burgundy-800">
                                                <span class="text-sm font-semibold text-navy-800">Activo</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" onclick="this.closest('[id^=modal-residence]').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                                        <button type="submit" class="btn-primary text-sm">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal New Residence --}}
            <div id="modal-residence-new" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                        <h3 class="font-heading font-bold text-navy-800">Nueva Residencia</h3>
                        <button onclick="document.getElementById('modal-residence-new').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.welcome.residences.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Nombre *</label>
                            <input type="text" name="nombre" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Nombre de la residencia">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Ubicacion</label>
                            <input type="text" name="ubicacion" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Ej: Caracas, Venezuela">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Imagen de Fachada *</label>
                            <input type="file" name="imagen" accept="image/*" required class="w-full text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Orden</label>
                                <input type="number" name="orden" value="0" class="w-full rounded-lg border-slate_custom-300 text-sm">
                            </div>
                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="activo" checked class="rounded border-slate_custom-300 text-burgundy-800">
                                    <span class="text-sm font-semibold text-navy-800">Activo</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-residence-new').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===================== POPUPS TAB ===================== --}}
        <div x-show="activeTab === 'popups'" x-transition>
            <div class="bg-white rounded-2xl shadow-sm border border-slate_custom-200">
                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-navy-800">Ventana Emergente</h3>
                        <p class="text-sm text-slate_custom-400">Configura un popup que aparece al cargar la pagina de inicio. Solo 1 puede estar activo.</p>
                    </div>
                    <button onclick="document.getElementById('modal-popup-new').classList.remove('hidden')" class="btn-primary text-sm">
                        <i class="fas fa-plus mr-2"></i>Crear Popup
                    </button>
                </div>
                <div class="p-6">
                    @if($popups->isEmpty())
                    <div class="text-center py-12 text-slate_custom-400">
                        <i class="fas fa-window-restore text-4xl mb-3"></i>
                        <p>No hay ventanas emergentes configuradas. Crea la primera.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($popups as $popup)
                        <div class="border border-slate_custom-200 rounded-xl overflow-hidden {{ !$popup->activo ? 'opacity-50' : '' }}">
                            <div class="p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $popup->color }}15;">
                                            <i class="{{ $popup->icono }}" style="color: {{ $popup->color }};"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-sm text-navy-800">{{ $popup->titulo }}</h4>
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $popup->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $popup->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-slate_custom-400 line-clamp-2 mb-3">{{ Str::limit(strip_tags($popup->contenido), 120) }}</p>
                                @if($popup->imagen)
                                <img src="{{ asset('storage/' . $popup->imagen) }}" class="w-full h-28 object-cover rounded-lg mb-3">
                                @endif
                                <div class="flex gap-2">
                                    <button onclick="document.getElementById('modal-popup-{{ $popup->id }}').classList.remove('hidden')" class="text-xs text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit mr-1"></i>Editar
                                    </button>
                                    <form action="{{ route('admin.welcome.popups.destroy', $popup) }}" method="POST" onsubmit="return confirm('Eliminar esta ventana emergente?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:text-red-800"><i class="fas fa-trash mr-1"></i>Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Edit Popup --}}
                        <div id="modal-popup-{{ $popup->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                                <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                                    <h3 class="font-heading font-bold text-navy-800">Editar Ventana Emergente</h3>
                                    <button onclick="this.closest('[id^=modal-popup]').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.welcome.popups.update', $popup) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo *</label>
                                        <input type="text" name="titulo" value="{{ $popup->titulo }}" required class="w-full rounded-lg border-slate_custom-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Contenido *</label>
                                        <textarea name="contenido" rows="4" required class="w-full rounded-lg border-slate_custom-300 text-sm">{{ $popup->contenido }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Imagen (dejar vacio para mantener actual)</label>
                                        <input type="file" name="imagen" accept="image/*" class="w-full text-sm">
                                        @if($popup->imagen)
                                        <img src="{{ asset('storage/' . $popup->imagen) }}" class="mt-2 h-20 rounded-lg object-cover">
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Texto Boton</label>
                                            <input type="text" name="boton_texto" value="{{ $popup->boton_texto }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">URL Boton</label>
                                            <input type="text" name="boton_url" value="{{ $popup->boton_url }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Icono (Font Awesome)</label>
                                            <input type="text" name="icono" value="{{ $popup->icono }}" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="fas fa-bullhorn">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-navy-800 mb-1">Color</label>
                                            <input type="color" name="color" value="{{ $popup->color }}" class="w-full h-10 rounded-lg border-slate_custom-300">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="activo" {{ $popup->activo ? 'checked' : '' }} class="rounded border-slate_custom-300 text-burgundy-800">
                                            <span class="text-sm font-semibold text-navy-800">Activo (visible en pagina de inicio)</span>
                                        </label>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" onclick="this.closest('[id^=modal-popup]').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                                        <button type="submit" class="btn-primary text-sm">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal New Popup --}}
            <div id="modal-popup-new" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-slate_custom-100 flex items-center justify-between">
                        <h3 class="font-heading font-bold text-navy-800">Nueva Ventana Emergente</h3>
                        <button onclick="document.getElementById('modal-popup-new').classList.add('hidden')" class="text-slate_custom-400 hover:text-slate_custom-600"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.welcome.popups.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Titulo *</label>
                            <input type="text" name="titulo" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Ej: Aviso Importante">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Contenido *</label>
                            <textarea name="contenido" rows="4" required class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Escribe el mensaje del popup..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 mb-1">Imagen (opcional)</label>
                            <input type="file" name="imagen" accept="image/*" class="w-full text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Texto Boton</label>
                                <input type="text" name="boton_texto" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Ej: Mas Informacion">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">URL Boton</label>
                                <input type="text" name="boton_url" class="w-full rounded-lg border-slate_custom-300 text-sm" placeholder="Ej: #contacto">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Icono (Font Awesome)</label>
                                <input type="text" name="icono" value="fas fa-bullhorn" class="w-full rounded-lg border-slate_custom-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 mb-1">Color</label>
                                <input type="color" name="color" value="#273272" class="w-full h-10 rounded-lg border-slate_custom-300">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="activo" class="rounded border-slate_custom-300 text-burgundy-800">
                                <span class="text-sm font-semibold text-navy-800">Activo (visible en pagina de inicio)</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-popup-new').classList.add('hidden')" class="btn-secondary text-sm">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm">Crear Popup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===================== SETTINGS TAB ===================== --}}
        <div x-show="activeTab === 'settings'" x-transition style="display:none;">
            <div class="bg-white rounded-2xl shadow-sm border border-slate_custom-200">
                <div class="p-6 border-b border-slate_custom-100">
                    <h3 class="font-heading font-bold text-lg text-navy-800">Textos Generales</h3>
                    <p class="text-sm text-slate_custom-400">Modifica los textos de las secciones de la pagina de inicio</p>
                </div>
                <form action="{{ route('admin.welcome.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf @method('PUT')
                    @php $grouped = $settings ?? collect(); @endphp

                    @php
                        $seccionLabels = [
                            'visibilidad' => ['icon' => 'fas fa-eye', 'label' => 'Visibilidad de Secciones'],
                            'general' => ['icon' => 'fas fa-cog', 'label' => 'General'],
                            'navegacion' => ['icon' => 'fas fa-bars', 'label' => 'Menu de Navegacion'],
                            'hero' => ['icon' => 'fas fa-image', 'label' => 'Hero / Carrusel (fallback)'],
                            'productos' => ['icon' => 'fas fa-box-open', 'label' => 'Seccion Productos'],
                            'acciones' => ['icon' => 'fas fa-mouse-pointer', 'label' => 'Tarjetas de Accion (CTA)'],
                            'residencias' => ['icon' => 'fas fa-building', 'label' => 'Seccion Residencias'],
                            'servicios' => ['icon' => 'fas fa-concierge-bell', 'label' => 'Seccion Servicios'],
                            'mapa' => ['icon' => 'fas fa-map-marked-alt', 'label' => 'Seccion Mapa'],
                            'footer' => ['icon' => 'fas fa-shoe-prints', 'label' => 'Footer / Pie de Pagina'],
                            'empresa' => ['icon' => 'fas fa-info-circle', 'label' => 'La Empresa'],
                        ];
                        $ordenSecciones = ['visibilidad', 'general', 'navegacion', 'hero', 'productos', 'acciones', 'empresa', 'residencias', 'servicios', 'mapa', 'footer'];
                        $sortedGrouped = collect($ordenSecciones)->filter(fn($s) => isset($grouped[$s]))->mapWithKeys(fn($s) => [$s => $grouped[$s]]);
                        // Add any remaining sections not in the order
                        foreach ($grouped as $k => $v) { if (!$sortedGrouped->has($k)) $sortedGrouped[$k] = $v; }
                    @endphp

                    @foreach($sortedGrouped as $seccion => $items)
                    <div class="border border-slate_custom-200 rounded-xl overflow-hidden">
                        <div class="bg-slate_custom-50 px-4 py-3 flex items-center gap-2 border-b border-slate_custom-200">
                            <i class="{{ $seccionLabels[$seccion]['icon'] ?? 'fas fa-cog' }} text-burgundy-800"></i>
                            <h4 class="font-heading font-bold text-navy-800">{{ $seccionLabels[$seccion]['label'] ?? ucfirst(str_replace('_', ' ', $seccion)) }}</h4>
                        </div>
                        <div class="p-4 space-y-4">
                            @foreach($items as $setting)
                            <div>
                                @if($seccion === 'visibilidad')
                                {{-- Toggle switch for visibility --}}
                                <label class="flex items-center justify-between cursor-pointer p-3 rounded-lg hover:bg-slate_custom-50 transition">
                                    <span class="text-sm font-semibold text-navy-800">{{ $setting->etiqueta }}</span>
                                    <div class="relative">
                                        <input type="hidden" name="settings[{{ $setting->clave }}]" value="0">
                                        <input type="checkbox" name="settings[{{ $setting->clave }}]" value="1" {{ $setting->valor === '1' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate_custom-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                    </div>
                                </label>
                                @elseif($setting->tipo === 'textarea')
                                <label class="block text-sm font-semibold text-navy-800 mb-1">{{ $setting->etiqueta }}</label>
                                <textarea name="settings[{{ $setting->clave }}]" rows="3" class="w-full rounded-lg border-slate_custom-300 text-sm">{{ $setting->valor }}</textarea>
                                @elseif($setting->tipo === 'image')
                                <label class="block text-sm font-semibold text-navy-800 mb-1">{{ $setting->etiqueta }}</label>
                                <input type="file" name="settings_files[{{ $setting->clave }}]" accept="image/*" class="w-full text-sm">
                                @if($setting->valor)
                                <img src="{{ asset('storage/' . $setting->valor) }}" class="mt-2 h-16 rounded-lg object-cover">
                                @endif
                                @else
                                <label class="block text-sm font-semibold text-navy-800 mb-1">{{ $setting->etiqueta }}</label>
                                <input type="text" name="settings[{{ $setting->clave }}]" value="{{ $setting->valor }}" class="w-full rounded-lg border-slate_custom-300 text-sm">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if($grouped->isEmpty())
                    <div class="text-center py-12 text-slate_custom-400">
                        <i class="fas fa-cog text-4xl mb-3"></i>
                        <p>No hay configuraciones. Ejecute el seeder para cargar los datos iniciales.</p>
                    </div>
                    @else
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Guardar Cambios</button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
