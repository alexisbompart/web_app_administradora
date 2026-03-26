<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Afiliacion PagoIntegral — Administradora Integral</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|rubik:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --navy:#273272; --burgundy:#680c3e; --slate:#565872; }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family:'Rubik',sans-serif; background:#f1f5f9; color:var(--slate); margin:0; }
        h1,h2,h3,h4,legend,.font-heading { font-family:'Poppins',sans-serif; }

        /* ── MODAL ── */
        #terms-modal {
            position:fixed; inset:0; background:rgba(0,0,0,.65);
            z-index:9999; display:flex; align-items:center; justify-content:center; padding:1rem;
        }
        .modal-box {
            background:#fff; border-radius:1.25rem; padding:0;
            max-width:680px; width:100%;
            box-shadow:0 30px 60px rgba(0,0,0,.3); overflow:hidden;
        }
        .modal-header {
            background:var(--navy); color:#fff;
            padding:1.25rem 1.5rem;
            display:flex; align-items:center; gap:.75rem;
        }
        .modal-header h3 { margin:0; font-size:1.05rem; font-weight:700; }
        .modal-body { padding:1.25rem 1.5rem; }
        .terms-scroll {
            max-height:16rem; overflow-y:auto;
            border:1px solid #e2e8f0; border-radius:.6rem;
            padding:1rem; font-size:.83rem; line-height:1.75; color:var(--slate);
        }
        .terms-scroll p { margin-bottom:.75rem; }
        .terms-scroll p:last-child { margin-bottom:0; }
        .modal-footer {
            padding:1rem 1.5rem;
            display:flex; justify-content:flex-end; gap:.75rem;
            border-top:1px solid #f1f5f9;
        }
        .btn-cancelar {
            padding:.5rem 1.5rem; border-radius:9999px; font-weight:600;
            font-size:.83rem; text-decoration:none;
            background:#e2e8f0; color:var(--slate);
            transition:background .2s;
        }
        .btn-cancelar:hover { background:#cbd5e1; }
        .btn-aceptar {
            padding:.5rem 1.75rem; border-radius:9999px; font-weight:700;
            font-size:.83rem; border:none; cursor:pointer;
            background:var(--burgundy); color:#fff;
            transition:opacity .2s;
        }
        .btn-aceptar:hover { opacity:.85; }

        /* ── PAGE HEADER ── */
        .page-header {
            background:linear-gradient(135deg,var(--navy) 0%,var(--burgundy) 100%);
            padding:1rem 2rem; display:flex; align-items:center; gap:1rem;
        }
        .logo-box {
            width:3rem; height:3rem; background:rgba(255,255,255,.15);
            border:2px solid rgba(255,255,255,.3);
            border-radius:.875rem; display:flex; align-items:center; justify-content:center;
            font-family:'Poppins',sans-serif; font-weight:800; font-size:1rem; color:#fff; flex-shrink:0;
        }
        .page-header h1 { font-size:1.2rem; font-weight:700; color:#fff; margin:0; }
        .page-header p  { font-size:.78rem; margin:0; color:rgba(255,255,255,.75); }

        /* ── FORM WRAPPER ── */
        .form-container { max-width:920px; margin:2rem auto; padding:0 1rem 3rem; }

        /* ── SECTION CARD ── */
        .form-section {
            background:#fff; border-radius:1rem; margin-bottom:1.25rem;
            box-shadow:0 1px 4px rgba(0,0,0,.07); overflow:hidden;
        }
        .section-header {
            display:flex; align-items:center; gap:.6rem;
            padding:.75rem 1.25rem;
            background:var(--navy); color:#fff;
            font-size:.8rem; font-weight:600; letter-spacing:.04em;
        }
        .section-body { padding:1.25rem; }

        /* ── GRID ── */
        .form-grid   { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; }
        .form-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
        .col-span-2  { grid-column:span 2; }
        @media(max-width:640px){
            .form-grid,.form-grid-3 { grid-template-columns:1fr; }
            .col-span-2 { grid-column:span 1; }
        }

        /* ── FIELD ── */
        label { display:block; font-size:.78rem; font-weight:600; color:var(--navy); margin-bottom:.25rem; }
        .required-star { color:var(--burgundy); }
        input[type=text],input[type=email],select,textarea {
            width:100%; border:1px solid #cbd5e1; border-radius:.5rem;
            padding:.5rem .75rem; font-size:.85rem; color:#1e293b;
            font-family:'Rubik',sans-serif; background:#fff;
            transition:border-color .2s, box-shadow .2s;
        }
        input:focus,select:focus,textarea:focus {
            outline:none; border-color:var(--burgundy);
            box-shadow:0 0 0 3px rgba(104,12,62,.1);
        }
        input::placeholder { color:#94a3b8; font-size:.82rem; }

        /* cedula group */
        .input-group { display:flex; gap:.5rem; }
        .input-group select { flex:0 0 4.5rem; }
        .input-group input  { flex:1; }

        /* pint row */
        .pint-row { display:grid; grid-template-columns:repeat(4,1fr); gap:.75rem; }
        @media(max-width:640px){ .pint-row { grid-template-columns:repeat(2,1fr); } }
        .pint-input { position:relative; }
        .pint-label-num {
            position:absolute; left:.65rem; top:50%; transform:translateY(-50%);
            font-size:.7rem; font-weight:700; color:var(--burgundy);
        }
        .pint-input input { padding-left:1.8rem; }

        /* note */
        .note { font-size:.75rem; color:var(--slate); line-height:1.55; margin-top:.6rem;
                display:flex; gap:.4rem; }
        .note i { color:var(--burgundy); flex-shrink:0; margin-top:.1rem; }

        /* ── BANKING INFO BOX ── */
        .info-box {
            background:linear-gradient(135deg,#eff3ff,#fdf2f8);
            border:1px solid #c7d2fe; border-radius:.6rem;
            padding:.85rem 1rem; margin-top:.75rem;
            font-size:.78rem; line-height:1.6;
        }
        .info-box strong { color:var(--navy); }

        /* ── SUBMIT ── */
        .submit-wrap { text-align:center; padding:1rem 0 .5rem; }
        .submit-btn {
            display:inline-flex; align-items:center; gap:.5rem;
            background:var(--burgundy); color:#fff;
            padding:.7rem 2.5rem; border-radius:9999px;
            font-weight:700; font-size:.9rem; font-family:'Poppins',sans-serif;
            border:none; cursor:pointer; transition:opacity .2s, transform .2s;
        }
        .submit-btn:hover { opacity:.88; transform:translateY(-1px); }

        /* ── ALERTS ── */
        .alert-success {
            background:#f0fdf4; border:1px solid #86efac; color:#166534;
            border-radius:.75rem; padding:.875rem 1rem; margin-bottom:1.25rem;
            display:flex; gap:.5rem; align-items:flex-start;
        }
        .alert-error {
            background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;
            border-radius:.75rem; padding:.875rem 1rem; margin-bottom:1.25rem;
        }
        .alert-error ul { list-style:disc; padding-left:1.25rem; font-size:.8rem; margin-top:.35rem; }

        /* ── FOOTER ── */
        footer {
            background:var(--navy); color:rgba(255,255,255,.65);
            text-align:center; padding:1.5rem 1rem;
            font-size:.78rem; line-height:1.85;
        }
        footer strong { color:#fff; }
    </style>
</head>
<body>

{{-- ═══════════════════════ TERMS MODAL ═══════════════════════ --}}
<div id="terms-modal" @if($errors->any() || session('success')) style="display:none" @endif>
    <div class="modal-box">
        <div class="modal-header">
            <i class="fas fa-file-contract text-lg"></i>
            <h3>Terminos y Condiciones del Contrato</h3>
        </div>
        <div class="modal-body">
            <div class="terms-scroll">
                <p>1.- Esta relacion mensual de condominio se caracteriza por ser uno de los instrumentos informativos del condominio; en ella se especifican los cargos y abonos comunes a todos los propietarios y/o aquellos que son particulares a cada uno de los mismos, el movimiento del fondo de reserva, la deuda comun atribuible a la comunidad y el comportamiento del condominio en las principales variables que lo benefician o lo afectan segun sea el caso.</p>
                <p>2.- La cantidad expresada en bolivares en la relacion mensual de condominio corresponde al mes y al anio indicados en las casillas respectivas.</p>
                <p>3.- Los soportes contables originales correspondientes a los cargos y gastos especificados en esta relacion de condominio reposan en nuestras oficinas, hasta la presentacion del informe y cuenta anual, de conformidad con lo previsto por el literal "f" y "h" del articulo 20 de la ley de propiedad horizontal.</p>
                <p>4.- La simple tenencia de la presente relacion mensual de condominio, no acredita en modo alguno pago del gasto mensual atribuible al propietario.</p>
                <p>5.- Al hacer clic en "Aceptar", usted autoriza a PagoIntegral C.A. y a Administradora Integral E.L.B., C.A. a procesar sus datos personales y bancarios para el servicio de domiciliacion de pagos de condominio, de conformidad con la legislacion venezolana vigente.</p>
                <p>6.- Esta autorizacion permite al sistema efectuar el debito correspondiente al gasto mensual de condominio de la cuenta bancaria indicada en este formulario.</p>
            </div>
        </div>
        <div class="modal-footer">
            <a href="/" class="btn-cancelar"><i class="fas fa-times mr-1"></i>Cancelar</a>
            <button onclick="acceptTerms()" class="btn-aceptar">
                <i class="fas fa-check mr-1"></i>Aceptar y Continuar
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════ MAIN FORM ═══════════════════════ --}}
<div id="afiliacion-form" @if(!$errors->any() && !session('success')) style="display:none" @endif>

    {{-- Header --}}
    <header class="page-header">
        <div class="logo-box">AI</div>
        <div>
            <h1>PagoIntegral — Afiliacion al Servicio</h1>
            <p>Administradora Integral E.L.B., C.A. &nbsp;|&nbsp; Complete todos los campos obligatorios (<span style="color:#fca5a5">*</span>)</p>
        </div>
        <div style="margin-left:auto;">
            <a href="/" style="color:rgba(255,255,255,.7); font-size:.8rem; text-decoration:none;">
                <i class="fas fa-home mr-1"></i>Inicio
            </a>
        </div>
    </header>

    <div class="form-container">

        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle" style="font-size:1.1rem; color:#16a34a;"></i>
            <div>
                <p style="font-weight:600; margin:0 0 .2rem;">Solicitud enviada exitosamente</p>
                <p style="margin:0; font-size:.82rem;">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="alert-error">
            <p style="font-weight:600; margin:0 0 .25rem;"><i class="fas fa-exclamation-triangle mr-1"></i>Por favor corrija los siguientes errores:</p>
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('afiliacion.publica.store') }}" novalidate>
            @csrf

            {{-- ══ 1. INFORMACION PERSONAL ══ --}}
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-user"></i>Informacion Personal
                </div>
                <div class="section-body">
                    <div class="form-grid">
                        <div>
                            <label>Primer Nombre <span class="required-star">(*)</span></label>
                            <input type="text" name="nombres" required maxlength="100"
                                   value="{{ old('nombres') }}" placeholder="Introduzca su primer nombre">
                        </div>
                        <div>
                            <label>Primer Apellido <span class="required-star">(*)</span></label>
                            <input type="text" name="apellidos" required maxlength="100"
                                   value="{{ old('apellidos') }}" placeholder="Introduzca su primer apellido">
                        </div>
                        <div>
                            <label>Cedula / RIF <span class="required-star">(*)</span></label>
                            <div class="input-group">
                                <select name="letra" required>
                                    @foreach(['V','E','J','G','P'] as $l)
                                    <option value="{{ $l }}" {{ old('letra','V') === $l ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="cedula_rif" required maxlength="20"
                                       value="{{ old('cedula_rif') }}" placeholder="Introduzca numero cedula o rif">
                            </div>
                        </div>
                        <div>
                            <label>Telefono</label>
                            <input type="text" name="telefono" maxlength="20"
                                   value="{{ old('telefono') }}" placeholder="Ejemplo: 0212-1234567">
                        </div>
                        <div>
                            <label>Celular</label>
                            <input type="text" name="celular" maxlength="20"
                                   value="{{ old('celular') }}" placeholder="Ejemplo: 0424-1234567">
                        </div>
                        <div>
                            <label>Otro Telefono</label>
                            <input type="text" name="otro" maxlength="20"
                                   value="{{ old('otro') }}" placeholder="Telefono alternativo">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ 2. INFORMACION DE CONTACTO ══ --}}
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-map-marker-alt"></i>Informacion de Contacto
                </div>
                <div class="section-body">
                    <div class="form-grid">
                        <div>
                            <label>Calle o Avenida</label>
                            <input type="text" name="calle_avenida" maxlength="200" value="{{ old('calle_avenida') }}">
                        </div>
                        <div>
                            <label>Piso / Apto</label>
                            <input type="text" name="piso_apto" maxlength="50" value="{{ old('piso_apto') }}">
                        </div>
                        <div>
                            <label>Edificio / Casa</label>
                            <input type="text" name="edif_casa" maxlength="100" value="{{ old('edif_casa') }}">
                        </div>
                        <div>
                            <label>Urbanizacion</label>
                            <input type="text" name="urbanizacion" maxlength="100" value="{{ old('urbanizacion') }}">
                        </div>
                        <div>
                            <label>Ciudad</label>
                            <input type="text" name="ciudad" maxlength="100" value="{{ old('ciudad') }}">
                        </div>
                        <div>
                            <label>Estado</label>
                            <select name="estado_id">
                                <option value="">Seleccionar...</option>
                                @foreach($estados as $estado)
                                <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Fax</label>
                            <input type="text" name="fax" maxlength="20" value="{{ old('fax') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ 3. INFORMACION DE CORREO ══ --}}
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-envelope"></i>Informacion de Correo Electronico
                </div>
                <div class="section-body">
                    <div class="form-grid">
                        <div>
                            <label>Correo Electronico Principal <span class="required-star">(*)</span></label>
                            <input type="email" name="email" required maxlength="100"
                                   value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                        </div>
                        <div>
                            <label>Repita Correo Principal <span class="required-star">(*)</span></label>
                            <input type="email" name="email_confirmation" required maxlength="100"
                                   value="{{ old('email_confirmation') }}" placeholder="Repita correo principal">
                        </div>
                        <div>
                            <label>Otro Correo Alternativo</label>
                            <input type="email" name="email_alterno" maxlength="100"
                                   value="{{ old('email_alterno') }}" placeholder="alterno@ejemplo.com">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ 4. INFORMACION DEL INMUEBLE ══ --}}
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-home"></i>Informacion del Inmueble
                </div>
                <div class="section-body">
                    <p style="font-size:.8rem; font-weight:600; color:var(--navy); margin-bottom:.15rem;">Codigo PINT</p>
                    <p style="font-size:.78rem; color:var(--slate); margin-bottom:1rem;">
                        Puede Afiliar hasta 4 Inmuebles. El codigo PINT es el identificador unico de su unidad de condominio.
                    </p>
                    <div class="pint-row">
                        @for($i = 0; $i < 4; $i++)
                        <div class="pint-input">
                            <span class="pint-label-num">{{ $i+1 }}.-</span>
                            <input type="text" name="pint[]" maxlength="20"
                                   value="{{ old('pint.'.$i) }}" placeholder="Codigo PINT">
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- ══ 5. INFORMACION BANCARIA ══ --}}
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-university"></i>Informacion Bancaria
                </div>
                <div class="section-body">
                    <div class="form-grid-3">
                        <div>
                            <label>Banco <span class="required-star">(*)</span></label>
                            <select name="banco_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($bancos as $banco)
                                <option value="{{ $banco->id }}" {{ old('banco_id') == $banco->id ? 'selected' : '' }}>
                                    {{ $banco->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Nro. Cuenta <span class="required-star">(*)</span></label>
                            <input type="text" name="cta_bancaria" required maxlength="20"
                                   value="{{ old('cta_bancaria') }}"
                                   placeholder="Debe ser de 20 Digitos"
                                   style="font-family:monospace;">
                        </div>
                        <div>
                            <label>Tipo</label>
                            <select name="tipo_cta">
                                <option value="">Seleccionar...</option>
                                <option value="Ahorro"    {{ old('tipo_cta') === 'Ahorro'    ? 'selected' : '' }}>Ahorro</option>
                                <option value="Corriente" {{ old('tipo_cta') === 'Corriente' ? 'selected' : '' }}>Corriente</option>
                            </select>
                        </div>
                    </div>
                    <div class="info-box">
                        <i class="fas fa-info-circle" style="color:var(--burgundy); margin-right:.4rem;"></i>
                        Los pagos por transferencias y/o depositos deben ser efectuados en la siguiente Cuenta:<br>
                        <strong>Banco: Banesco</strong> — Cuenta Cte. N°: 0134-1099-2400-0300-1553 —
                        A nombre de: <strong>Administradora Integral E.L.B., C.A.</strong> — RIF: J-00142643-4
                    </div>
                </div>
            </div>

            {{-- ══ 6. INFORMACION PARA ACCEDER ══ --}}
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-key"></i>Informacion para Acceder
                </div>
                <div class="section-body">
                    <div style="max-width:380px;">
                        <label>Usuario</label>
                        <input type="text" name="nom_usuario" maxlength="100"
                               value="{{ old('nom_usuario') }}" placeholder="Nombre de usuario deseado">
                    </div>
                </div>
            </div>

            <div class="submit-wrap">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i>
                    Crear Cuenta
                </button>
            </div>

        </form>
    </div>

    {{-- Footer --}}
    <footer>
        <p>
            <strong>Contáctenos a través del:</strong>
            &nbsp; Telefono: (0212) 951.56.11 Ext: 403
            &nbsp;|&nbsp;
            Correo electronico: info@administradoraintegral.com.ve
        </p>
        <p>
            <strong>PagoIntegral C.A.</strong> J-31356345-5
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Administrador Integral C.A.</strong> J-00142643-4
        </p>
        <p style="margin-top:.5rem; font-size:.7rem; opacity:.5;">
            &copy; {{ date('Y') }} Administradora Integral E.L.B., C.A. Todos los derechos reservados.
        </p>
    </footer>

</div>

<script>
function acceptTerms() {
    document.getElementById('terms-modal').style.display = 'none';
    document.getElementById('afiliacion-form').style.display = 'block';
}
</script>
</body>
</html>
