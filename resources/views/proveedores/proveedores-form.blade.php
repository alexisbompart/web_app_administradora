<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($proveedor) ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($proveedor) ? 'Modificar datos del proveedor' : 'Registrar un nuevo proveedor' }}
                </p>
            </div>
            <a href="{{ route('proveedores.proveedores.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card max-w-4xl mx-auto">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-truck mr-2 text-burgundy-800"></i>Datos del Proveedor
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($proveedor) ? route('proveedores.proveedores.update', $proveedor) : route('proveedores.proveedores.store') }}" method="POST">
                @csrf
                @if(isset($proveedor))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- RIF -->
                    <div>
                        <label for="rif" class="block text-sm font-medium text-navy-800 mb-1">RIF <span class="text-red-500">*</span></label>
                        <input type="text" name="rif" id="rif" value="{{ old('rif', $proveedor->rif ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" placeholder="J-12345678-9" required>
                        @error('rif')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Razon Social -->
                    <div>
                        <label for="razon_social" class="block text-sm font-medium text-navy-800 mb-1">Raz&oacute;n Social <span class="text-red-500">*</span></label>
                        <input type="text" name="razon_social" id="razon_social" value="{{ old('razon_social', $proveedor->razon_social ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('razon_social')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre Comercial -->
                    <div>
                        <label for="nombre_comercial" class="block text-sm font-medium text-navy-800 mb-1">Nombre Comercial</label>
                        <input type="text" name="nombre_comercial" id="nombre_comercial" value="{{ old('nombre_comercial', $proveedor->nombre_comercial ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('nombre_comercial')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telefono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-navy-800 mb-1">Tel&eacute;fono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $proveedor->telefono ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('telefono')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Celular -->
                    <div>
                        <label for="celular" class="block text-sm font-medium text-navy-800 mb-1">Celular</label>
                        <input type="text" name="celular" id="celular" value="{{ old('celular', $proveedor->celular ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('celular')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-navy-800 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $proveedor->email ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contacto -->
                    <div>
                        <label for="contacto" class="block text-sm font-medium text-navy-800 mb-1">Persona de Contacto</label>
                        <input type="text" name="contacto" id="contacto" value="{{ old('contacto', $proveedor->contacto ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('contacto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo Contribuyente -->
                    <div>
                        <label for="tipo_contribuyente" class="block text-sm font-medium text-navy-800 mb-1">Tipo de Contribuyente</label>
                        <select name="tipo_contribuyente" id="tipo_contribuyente" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">Seleccione</option>
                            @foreach(['ordinario' => 'Ordinario', 'especial' => 'Especial', 'formal' => 'Formal'] as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo_contribuyente', $proveedor->tipo_contribuyente ?? '') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_contribuyente')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Banco -->
                    <div>
                        <label for="banco_id" class="block text-sm font-medium text-navy-800 mb-1">Banco</label>
                        <select name="banco_id" id="banco_id" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">Seleccione un banco</option>
                            @foreach($bancos as $banco)
                                <option value="{{ $banco->id }}" {{ old('banco_id', $proveedor->banco_id ?? '') == $banco->id ? 'selected' : '' }}>
                                    {{ $banco->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('banco_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cuenta Bancaria -->
                    <div>
                        <label for="cuenta_bancaria" class="block text-sm font-medium text-navy-800 mb-1">Cuenta Bancaria</label>
                        <input type="text" name="cuenta_bancaria" id="cuenta_bancaria" value="{{ old('cuenta_bancaria', $proveedor->cuenta_bancaria ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" placeholder="0000-0000-00-0000000000">
                        @error('cuenta_bancaria')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Activo -->
                    <div class="flex items-center pt-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" value="1" class="form-checkbox rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800" {{ old('activo', $proveedor->activo ?? true) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-medium text-navy-800">Activo</span>
                        </label>
                    </div>
                </div>

                <!-- Direccion -->
                <div class="mt-6">
                    <label for="direccion" class="block text-sm font-medium text-navy-800 mb-1">Direcci&oacute;n</label>
                    <textarea name="direccion" id="direccion" rows="3" class="form-textarea w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">{{ old('direccion', $proveedor->direccion ?? '') }}</textarea>
                    @error('direccion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate_custom-100">
                    <a href="{{ route('proveedores.proveedores.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($proveedor) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
