@extends('layouts.app')

@section('title', $afiliacion ? 'Editar Afiliacion' : 'Nueva Afiliacion')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-heading font-bold text-navy-800">
            {{ $afiliacion ? 'Editar Afiliacion' : 'Nueva Afiliacion' }}
        </h2>
        <p class="text-sm text-slate_custom-400 mt-1">
            {{ $afiliacion ? 'Actualice los datos de la afiliacion' : 'Complete el formulario para registrar una nueva afiliacion' }}
        </p>
    </div>
    <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Volver
    </a>
</div>

@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST"
    action="{{ $afiliacion ? route('financiero.pago-integral.afiliaciones.update', $afiliacion) : route('financiero.pago-integral.afiliaciones.store') }}">
    @csrf
    @if($afiliacion)
        @method('PUT')
    @endif

    {{-- Section 1: Informacion Personal --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user mr-2 text-burgundy-800"></i>Informacion Personal
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">
                        Letra <span class="text-red-500">*</span>
                    </label>
                    <select name="letra" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        @foreach(['V', 'E', 'J', 'G', 'P'] as $letra)
                        <option value="{{ $letra }}" {{ old('letra', $afiliacion->letra ?? '') === $letra ? 'selected' : '' }}>{{ $letra }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">
                        Cedula / RIF <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="cedula_rif" required maxlength="20"
                        value="{{ old('cedula_rif', $afiliacion->cedula_rif ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">
                        Nombres <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombres" required maxlength="100"
                        value="{{ old('nombres', $afiliacion->nombres ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">
                        Apellidos <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="apellidos" required maxlength="100"
                        value="{{ old('apellidos', $afiliacion->apellidos ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Telefono</label>
                    <input type="text" name="telefono" maxlength="20"
                        value="{{ old('telefono', $afiliacion->telefono ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Celular</label>
                    <input type="text" name="celular" maxlength="20"
                        value="{{ old('celular', $afiliacion->celular ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Fax</label>
                    <input type="text" name="fax" maxlength="20"
                        value="{{ old('fax', $afiliacion->fax ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Telefono Alternativo</label>
                    <input type="text" name="otro" maxlength="20"
                        value="{{ old('otro', $afiliacion->otro ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

            </div>
        </div>
    </div>

    {{-- Section 2: Informacion de Contacto --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-map-marker-alt mr-2 text-burgundy-800"></i>Informacion de Contacto
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Calle / Avenida</label>
                    <input type="text" name="calle_avenida" maxlength="200"
                        value="{{ old('calle_avenida', $afiliacion->calle_avenida ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Edificio / Casa</label>
                    <input type="text" name="edif_casa" maxlength="100"
                        value="{{ old('edif_casa', $afiliacion->edif_casa ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Piso / Apto</label>
                    <input type="text" name="piso_apto" maxlength="50"
                        value="{{ old('piso_apto', $afiliacion->piso_apto ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Urbanizacion</label>
                    <input type="text" name="urbanizacion" maxlength="100"
                        value="{{ old('urbanizacion', $afiliacion->urbanizacion ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Ciudad</label>
                    <input type="text" name="ciudad" maxlength="100"
                        value="{{ old('ciudad', $afiliacion->ciudad ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Estado</label>
                    <select name="estado_id"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        <option value="">-- Seleccione --</option>
                        @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ old('estado_id', $afiliacion->estado_id ?? '') == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Section 3: Informacion de Correo --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-envelope mr-2 text-burgundy-800"></i>Informacion de Correo Electronico
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Correo Electronico</label>
                    <input type="email" name="email" maxlength="100"
                        value="{{ old('email', $afiliacion->email ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Correo Alternativo</label>
                    <input type="email" name="email_alterno" maxlength="100"
                        value="{{ old('email_alterno', $afiliacion->email_alterno ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

            </div>
        </div>
    </div>

    {{-- Section 4: Inmueble --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-building mr-2 text-burgundy-800"></i>Informacion del Inmueble
            </h3>
        </div>
        <div class="card-body">
            @if($afiliacion && $afiliacion->afilapto && !$afiliacion->afilapto->apartamento_id)
            <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-xs text-amber-800 mb-3 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                Este registro no tiene inmueble vinculado. Puede asignarlo seleccionando uno abajo, o dejarlo en blanco para no modificarlo.
            </div>
            @endif
            <div>
                <label class="block text-sm font-semibold text-navy-800 mb-1">
                    Inmueble (Edificio / Apto)
                    @if(!$afiliacion) <span class="text-red-500">*</span> @endif
                </label>
                <select name="apartamento_id" {{ !$afiliacion ? 'required' : '' }}
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                    <option value="">-- {{ $afiliacion ? 'Sin cambio / Sin inmueble' : 'Seleccione un inmueble' }} --</option>
                    @foreach($apartamentos as $apto)
                    <option value="{{ $apto->id }}"
                        {{ old('apartamento_id', $afiliacion->afilapto->apartamento_id ?? '') == $apto->id ? 'selected' : '' }}>
                        {{ $apto->edificio->nombre ?? 'Edificio' }} — Apto {{ $apto->num_apto }}
                    </option>
                    @endforeach
                </select>
                @if($afiliacion && $afiliacion->afilapto?->apartamento_id)
                <p class="text-xs text-slate_custom-400 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Actual: {{ $afiliacion->afilapto->edificio->nombre ?? '' }} — Apto {{ $afiliacion->afilapto->apartamento->num_apto ?? '' }}
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Section 5: Banco --}}
    <div class="card mb-6"
         x-data="{
             bancoId: '{{ old('banco_id', $afiliacion->banco_id ?? '') }}',
             cuenta: '{{ old('cta_bancaria', $afiliacion->cta_bancaria ?? '') }}',
             prefijos: { '8': '0134', '3': '0105', '5': '0114' },
             error: '',
             get prefijo() { return this.prefijos[this.bancoId] ?? null; },
             get nombreBanco() {
                 return { '8': 'Banesco', '3': 'Mercantil', '5': 'Bancaribe' }[this.bancoId] ?? '';
             },
             onBancoChange() {
                 this.error = '';
                 if (this.prefijo && this.cuenta && !this.cuenta.startsWith(this.prefijo)) {
                     this.cuenta = this.prefijo;
                 } else if (this.prefijo && !this.cuenta) {
                     this.cuenta = this.prefijo;
                 }
             },
             onCuentaInput() {
                 this.error = '';
                 if (!this.prefijo) return;
                 if (this.cuenta.length >= 4 && !this.cuenta.startsWith(this.prefijo)) {
                     this.error = 'La cuenta de ' + this.nombreBanco + ' debe comenzar con ' + this.prefijo;
                 }
             },
             onCuentaBlur() {
                 if (!this.prefijo) return;
                 if (!this.cuenta.startsWith(this.prefijo)) {
                     this.error = 'La cuenta de ' + this.nombreBanco + ' debe comenzar con ' + this.prefijo;
                 } else if (this.cuenta.length !== 20) {
                     this.error = 'El numero de cuenta debe tener exactamente 20 digitos';
                 } else {
                     this.error = '';
                 }
             }
         }">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-university mr-2 text-burgundy-800"></i>Informacion Bancaria
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Banco</label>
                    <select name="banco_id" x-model="bancoId" @change="onBancoChange()"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        <option value="">-- Seleccione --</option>
                        @foreach($bancos as $banco)
                        <option value="{{ $banco->id }}">{{ $banco->nombre }}</option>
                        @endforeach
                    </select>
                    <p x-show="prefijo" class="text-xs text-slate_custom-400 mt-1">
                        Prefijo requerido: <span class="font-mono font-bold text-navy-800" x-text="prefijo"></span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Numero de Cuenta</label>
                    <input type="text" name="cta_bancaria" maxlength="20"
                        x-model="cuenta"
                        @input="onCuentaInput()"
                        @blur="onCuentaBlur()"
                        placeholder="20 digitos"
                        :class="error ? 'border-red-400 focus:ring-red-400' : 'border-slate-300 focus:ring-burgundy-800'"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 font-mono">
                    <p x-show="error" x-text="error" class="text-xs text-red-600 mt-1"></p>
                    <p x-show="!error && cuenta.length > 0"
                       class="text-xs mt-1"
                       :class="cuenta.length === 20 ? 'text-green-600' : 'text-slate_custom-400'">
                        <span x-text="cuenta.length"></span>/20 digitos
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Tipo de Cuenta</label>
                    <select name="tipo_cta"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        <option value="">-- Seleccione --</option>
                        <option value="Ahorro" {{ old('tipo_cta', $afiliacion->tipo_cta ?? '') === 'Ahorro' ? 'selected' : '' }}>Ahorro</option>
                        <option value="Corriente" {{ old('tipo_cta', $afiliacion->tipo_cta ?? '') === 'Corriente' ? 'selected' : '' }}>Corriente</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Codigo Sucursal</label>
                    <input type="text" name="cod_sucursal" maxlength="20"
                        value="{{ old('cod_sucursal', $afiliacion->cod_sucursal ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

            </div>
        </div>
    </div>

    {{-- Section 6: Acceso y Configuracion --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-cog mr-2 text-burgundy-800"></i>Configuracion y Acceso
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Usuario</label>
                    <input type="text" name="nom_usuario" maxlength="100"
                        value="{{ old('nom_usuario', $afiliacion->nom_usuario ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Estatus</label>
                    <select name="estatus" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        <option value="A" {{ old('estatus', $afiliacion->estatus ?? 'A') === 'A' ? 'selected' : '' }}>Activo</option>
                        <option value="I" {{ old('estatus', $afiliacion->estatus ?? '') === 'I' ? 'selected' : '' }}>Inactivo</option>
                        <option value="P" {{ old('estatus', $afiliacion->estatus ?? '') === 'P' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Observaciones</label>
                    <textarea name="observaciones" rows="3"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">{{ old('observaciones', $afiliacion->observaciones ?? '') }}</textarea>
                </div>

            </div>
        </div>
    </div>

    {{-- Section 7: Proceso Mercantil (solo en edicion y si es banco Mercantil) --}}
    @if($afiliacion && $afiliacion->esMercantil())
    <div class="card mb-6 border-l-4 border-l-amber-500">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-university mr-2 text-amber-600"></i>Proceso Mercantil
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Tipo de Operacion</label>
                    <select name="tipo_operacion"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        <option value="A" {{ old('tipo_operacion', $afiliacion->tipo_operacion ?? 'A') === 'A' ? 'selected' : '' }}>Afiliacion</option>
                        <option value="D" {{ old('tipo_operacion', $afiliacion->tipo_operacion ?? '') === 'D' ? 'selected' : '' }}>Desafiliacion</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Estatus Proceso Mercantil</label>
                    <select name="mercantil_estatus_proceso"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        <option value="" {{ old('mercantil_estatus_proceso', $afiliacion->mercantil_estatus_proceso ?? '') === '' ? 'selected' : '' }}>— Sin proceso iniciado —</option>
                        <option value="P" {{ old('mercantil_estatus_proceso', $afiliacion->mercantil_estatus_proceso ?? '') === 'P' ? 'selected' : '' }}>P — Pendiente de respuesta</option>
                        <option value="A" {{ old('mercantil_estatus_proceso', $afiliacion->mercantil_estatus_proceso ?? '') === 'A' ? 'selected' : '' }}>A — Aprobado</option>
                        <option value="R" {{ old('mercantil_estatus_proceso', $afiliacion->mercantil_estatus_proceso ?? '') === 'R' ? 'selected' : '' }}>R — Rechazado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Archivo Enviado</label>
                    <input type="text" name="mercantil_archivo_enviado" maxlength="100"
                        value="{{ old('mercantil_archivo_enviado', $afiliacion->mercantil_archivo_enviado ?? '') }}"
                        placeholder="Ej: Mdomi5.txt"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                    <p class="text-xs text-slate_custom-400 mt-1">Dejar vacio para que el registro vuelva a estar disponible al generar archivo</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Fecha de Envio</label>
                    <input type="date" name="mercantil_fecha_envio"
                        value="{{ old('mercantil_fecha_envio', $afiliacion->mercantil_fecha_envio?->format('Y-m-d') ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Codigo de Respuesta</label>
                    <input type="text" name="mercantil_cod_respuesta" maxlength="10"
                        value="{{ old('mercantil_cod_respuesta', $afiliacion->mercantil_cod_respuesta ?? '') }}"
                        placeholder="Ej: 0074"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Fecha de Respuesta</label>
                    <input type="date" name="mercantil_fecha_respuesta"
                        value="{{ old('mercantil_fecha_respuesta', $afiliacion->mercantil_fecha_respuesta?->format('Y-m-d') ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 mb-1">Mensaje de Respuesta</label>
                    <input type="text" name="mercantil_mensaje" maxlength="200"
                        value="{{ old('mercantil_mensaje', $afiliacion->mercantil_mensaje ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                </div>

            </div>
        </div>
    </div>
    @endif

    <div class="flex gap-3">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-2"></i>Guardar
        </button>
        <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="btn-secondary">
            Cancelar
        </a>
    </div>

</form>
@endsection
