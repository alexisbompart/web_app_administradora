<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">{{ isset($afilpagointegral) ? 'Editar' : 'Crear' }} Pago Integral</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ isset($afilpagointegral) ? 'Modificar registro #'.$afilpagointegral->id : 'Nuevo registro de pago integral' }}</p>
            </div>
            <a href="{{ route('condominio.afilpagointegral.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
        </div>
    </x-slot>

    <form action="{{ isset($afilpagointegral) ? route('condominio.afilpagointegral.update', $afilpagointegral) : route('condominio.afilpagointegral.store') }}" method="POST">
        @csrf
        @if(isset($afilpagointegral)) @method('PUT') @endif

        {{-- Datos personales --}}
        <div class="card mb-6">
            <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-user mr-2 text-burgundy-800"></i>Datos Personales</h3></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Afiliacion ID</label>
                        <input type="number" name="afilapto_id" value="{{ old('afilapto_id', $afilpagointegral->afilapto_id ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('afilapto_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Letra</label>
                        <select name="letra" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">--</option>
                            @foreach(['V','E','J','P','G','R'] as $l)
                                <option value="{{ $l }}" {{ old('letra', $afilpagointegral->letra ?? '') === $l ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Cedula / RIF</label>
                        <input type="text" name="cedula_rif" value="{{ old('cedula_rif', $afilpagointegral->cedula_rif ?? '') }}" maxlength="20"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Fecha</label>
                        <input type="date" name="fecha" value="{{ old('fecha', isset($afilpagointegral) && $afilpagointegral->fecha ? $afilpagointegral->fecha->format('Y-m-d') : '') }}"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Nombres</label>
                        <input type="text" name="nombres" value="{{ old('nombres', $afilpagointegral->nombres ?? '') }}" maxlength="255"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $afilpagointegral->apellidos ?? '') }}" maxlength="255"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $afilpagointegral->email ?? '') }}" maxlength="255"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Email Alterno</label>
                        <input type="email" name="email_alterno" value="{{ old('email_alterno', $afilpagointegral->email_alterno ?? '') }}" maxlength="255"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                </div>
            </div>
        </div>

        {{-- Direccion --}}
        <div class="card mb-6">
            <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-map-marker-alt mr-2 text-burgundy-800"></i>Direccion</h3></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Calle / Avenida</label>
                        <input type="text" name="calle_avenida" value="{{ old('calle_avenida', $afilpagointegral->calle_avenida ?? '') }}" maxlength="255"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Piso / Apto</label>
                        <input type="text" name="piso_apto" value="{{ old('piso_apto', $afilpagointegral->piso_apto ?? '') }}" maxlength="50"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Edif. / Casa</label>
                        <input type="text" name="edif_casa" value="{{ old('edif_casa', $afilpagointegral->edif_casa ?? '') }}" maxlength="255"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Urbanizacion</label>
                        <input type="text" name="urbanizacion" value="{{ old('urbanizacion', $afilpagointegral->urbanizacion ?? '') }}" maxlength="255"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad', $afilpagointegral->ciudad ?? '') }}" maxlength="100"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Estado</label>
                        <select name="estado_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccionar --</option>
                            @foreach($estados as $est)
                                <option value="{{ $est->id }}" {{ old('estado_id', $afilpagointegral->estado_id ?? '') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Telefonos --}}
        <div class="card mb-6">
            <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-phone mr-2 text-burgundy-800"></i>Telefonos</h3></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Telefono</label><input type="text" name="telefono" value="{{ old('telefono', $afilpagointegral->telefono ?? '') }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Fax</label><input type="text" name="fax" value="{{ old('fax', $afilpagointegral->fax ?? '') }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Celular</label><input type="text" name="celular" value="{{ old('celular', $afilpagointegral->celular ?? '') }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Otro</label><input type="text" name="otro" value="{{ old('otro', $afilpagointegral->otro ?? '') }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                </div>
            </div>
        </div>

        {{-- Datos bancarios y acceso --}}
        <div class="card mb-6">
            <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-university mr-2 text-burgundy-800"></i>Datos Bancarios y Acceso</h3></div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Banco</label>
                        <select name="banco_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccionar --</option>
                            @foreach($bancos as $banco)
                                <option value="{{ $banco->id }}" {{ old('banco_id', $afilpagointegral->banco_id ?? '') == $banco->id ? 'selected' : '' }}>{{ $banco->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Cuenta Bancaria</label><input type="text" name="cta_bancaria" value="{{ old('cta_bancaria', $afilpagointegral->cta_bancaria ?? '') }}" maxlength="30" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Tipo Cuenta</label>
                        <select name="tipo_cta" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">--</option>
                            <option value="C" {{ old('tipo_cta', $afilpagointegral->tipo_cta ?? '') === 'C' ? 'selected' : '' }}>Corriente</option>
                            <option value="A" {{ old('tipo_cta', $afilpagointegral->tipo_cta ?? '') === 'A' ? 'selected' : '' }}>Ahorro</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Usuario</label><input type="text" name="nom_usuario" value="{{ old('nom_usuario', $afilpagointegral->nom_usuario ?? '') }}" maxlength="100" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Clave</label><input type="text" name="clave" value="{{ old('clave', $afilpagointegral->clave ?? '') }}" maxlength="100" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Estatus</label>
                        <select name="estatus" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">--</option>
                            <option value="A" {{ old('estatus', $afilpagointegral->estatus ?? '') === 'A' ? 'selected' : '' }}>Activo</option>
                            <option value="D" {{ old('estatus', $afilpagointegral->estatus ?? '') === 'D' ? 'selected' : '' }}>Desactivado</option>
                            <option value="T" {{ old('estatus', $afilpagointegral->estatus ?? '') === 'T' ? 'selected' : '' }}>Temporal</option>
                            <option value="R" {{ old('estatus', $afilpagointegral->estatus ?? '') === 'R' ? 'selected' : '' }}>Rechazado</option>
                            <option value="P" {{ old('estatus', $afilpagointegral->estatus ?? '') === 'P' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-navy-800 mb-1">Fecha Estatus</label><input type="date" name="fecha_estatus" value="{{ old('fecha_estatus', isset($afilpagointegral) && $afilpagointegral->fecha_estatus ? $afilpagointegral->fecha_estatus->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                    <div class="lg:col-span-2"><label class="block text-sm font-medium text-navy-800 mb-1">Observaciones</label><input type="text" name="observaciones" value="{{ old('observaciones', $afilpagointegral->observaciones ?? '') }}" maxlength="500" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('condominio.afilpagointegral.index') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
            <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>{{ isset($afilpagointegral) ? 'Actualizar' : 'Crear' }}</button>
        </div>
    </form>
</x-app-layout>
