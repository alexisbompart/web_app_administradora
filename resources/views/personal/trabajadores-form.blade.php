<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($trabajador) ? 'Editar Trabajador' : 'Crear Trabajador' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($trabajador) ? 'Modificar datos del trabajador' : 'Registrar un nuevo trabajador en el sistema' }}
                </p>
            </div>
            <a href="{{ route('personal.trabajadores.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user-edit mr-2 text-burgundy-800"></i>Datos del Trabajador
            </h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($trabajador) ? route('personal.trabajadores.update', $trabajador) : route('personal.trabajadores.store') }}" method="POST">
                @csrf
                @if(isset($trabajador))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Compania --}}
                    <div>
                        <label for="compania_id" class="block text-sm font-medium text-navy-800 mb-1">Compa&ntilde;&iacute;a</label>
                        <select name="compania_id" id="compania_id" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="">Seleccione...</option>
                            @foreach($companias as $compania)
                                <option value="{{ $compania->id }}" {{ old('compania_id', $trabajador->compania_id ?? '') == $compania->id ? 'selected' : '' }}>
                                    {{ $compania->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Cedula --}}
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-navy-800 mb-1">C&eacute;dula <span class="text-red-500">*</span></label>
                        <input type="text" name="cedula" id="cedula" value="{{ old('cedula', $trabajador->cedula ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Nombres --}}
                    <div>
                        <label for="nombres" class="block text-sm font-medium text-navy-800 mb-1">Nombres <span class="text-red-500">*</span></label>
                        <input type="text" name="nombres" id="nombres" value="{{ old('nombres', $trabajador->nombres ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Apellidos --}}
                    <div>
                        <label for="apellidos" class="block text-sm font-medium text-navy-800 mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos', $trabajador->apellidos ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Fecha Nacimiento --}}
                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-navy-800 mb-1">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento', isset($trabajador) && $trabajador->fecha_nacimiento ? $trabajador->fecha_nacimiento->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                    </div>

                    {{-- Sexo --}}
                    <div>
                        <label for="sexo" class="block text-sm font-medium text-navy-800 mb-1">Sexo</label>
                        <select name="sexo" id="sexo" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="">Seleccione...</option>
                            <option value="M" {{ old('sexo', $trabajador->sexo ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $trabajador->sexo ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>

                    {{-- Telefono --}}
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-navy-800 mb-1">Tel&eacute;fono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $trabajador->telefono ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                    </div>

                    {{-- Celular --}}
                    <div>
                        <label for="celular" class="block text-sm font-medium text-navy-800 mb-1">Celular</label>
                        <input type="text" name="celular" id="celular" value="{{ old('celular', $trabajador->celular ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-navy-800 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $trabajador->email ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                    </div>

                    {{-- Cargo --}}
                    <div>
                        <label for="cargo" class="block text-sm font-medium text-navy-800 mb-1">Cargo <span class="text-red-500">*</span></label>
                        <input type="text" name="cargo" id="cargo" value="{{ old('cargo', $trabajador->cargo ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Departamento --}}
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-navy-800 mb-1">Departamento</label>
                        <input type="text" name="departamento" id="departamento" value="{{ old('departamento', $trabajador->departamento ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                    </div>

                    {{-- Fecha Ingreso --}}
                    <div>
                        <label for="fecha_ingreso" class="block text-sm font-medium text-navy-800 mb-1">Fecha de Ingreso <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_ingreso" id="fecha_ingreso" value="{{ old('fecha_ingreso', isset($trabajador) && $trabajador->fecha_ingreso ? $trabajador->fecha_ingreso->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Salario Basico --}}
                    <div>
                        <label for="salario_basico" class="block text-sm font-medium text-navy-800 mb-1">Salario B&aacute;sico <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="salario_basico" id="salario_basico" value="{{ old('salario_basico', $trabajador->salario_basico ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Tipo Contrato --}}
                    <div>
                        <label for="tipo_contrato" class="block text-sm font-medium text-navy-800 mb-1">Tipo de Contrato</label>
                        <select name="tipo_contrato" id="tipo_contrato" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="">Seleccione...</option>
                            <option value="fijo" {{ old('tipo_contrato', $trabajador->tipo_contrato ?? '') == 'fijo' ? 'selected' : '' }}>Fijo</option>
                            <option value="temporal" {{ old('tipo_contrato', $trabajador->tipo_contrato ?? '') == 'temporal' ? 'selected' : '' }}>Temporal</option>
                            <option value="pasante" {{ old('tipo_contrato', $trabajador->tipo_contrato ?? '') == 'pasante' ? 'selected' : '' }}>Pasante</option>
                        </select>
                    </div>

                    {{-- Estatus --}}
                    <div>
                        <label for="estatus" class="block text-sm font-medium text-navy-800 mb-1">Estatus</label>
                        <select name="estatus" id="estatus" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="A" {{ old('estatus', $trabajador->estatus ?? 'A') == 'A' ? 'selected' : '' }}>Activo</option>
                            <option value="I" {{ old('estatus', $trabajador->estatus ?? '') == 'I' ? 'selected' : '' }}>Inactivo</option>
                            <option value="V" {{ old('estatus', $trabajador->estatus ?? '') == 'V' ? 'selected' : '' }}>Vacaciones</option>
                        </select>
                    </div>
                </div>

                {{-- Direccion --}}
                <div class="mt-6">
                    <label for="direccion" class="block text-sm font-medium text-navy-800 mb-1">Direcci&oacute;n</label>
                    <textarea name="direccion" id="direccion" rows="3" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">{{ old('direccion', $trabajador->direccion ?? '') }}</textarea>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('personal.trabajadores.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($trabajador) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
