<?php

namespace Database\Seeders;

use App\Models\WelcomeProduct;
use App\Models\WelcomeService;
use App\Models\WelcomeSetting;
use App\Models\WelcomeSlider;
use Illuminate\Database\Seeder;

class WelcomeContentSeeder extends Seeder
{
    public function run(): void
    {
        // ============ SLIDERS (CARRUSEL) ============
        $sliders = [
            [
                'titulo' => "Administradora\nIntegral",
                'subtitulo' => 'Compania lider en el mercado inmobiliario de Venezuela. Excelencia, compromiso y responsabilidad.',
                'imagen' => 'sliders/slider-1.jpg',
                'boton_texto' => 'Conocenos',
                'boton_url' => '#servicios',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'titulo' => "Gestion Integral\nde Condominios",
                'subtitulo' => 'Administramos su comunidad con transparencia, eficiencia y tecnologia de vanguardia.',
                'imagen' => 'sliders/slider-2.jpg',
                'boton_texto' => 'Ver Productos',
                'boton_url' => '#productos',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'titulo' => "Mas de 20 anos\nde Experiencia",
                'subtitulo' => 'Personal altamente calificado para satisfacer las necesidades de nuestros clientes.',
                'imagen' => 'sliders/slider-3.jpg',
                'boton_texto' => 'Contactanos',
                'boton_url' => '#contacto',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            WelcomeSlider::updateOrCreate(
                ['orden' => $slider['orden']],
                $slider
            );
        }

        // ============ PRODUCTOS ============
        $products = [
            [
                'titulo' => 'Integral Pago',
                'slogan' => 'Tu condominio al dia, sin complicaciones',
                'descripcion' => 'Servicio de debito automatico para el pago de condominio. Olvide las colas y los retrasos, su recibo se paga automaticamente cada mes.',
                'detalle' => '<div class="space-y-4">
                    <h4 class="font-heading font-bold text-navy-800">Beneficios del Pago Integral</h4>
                    <ul class="list-disc pl-5 space-y-2 text-sm text-slate_custom-500">
                        <li>Debito automatico mensual sin comisiones adicionales</li>
                        <li>Evite recargos por mora y gestiones de cobranza</li>
                        <li>Reciba su comprobante de pago por correo electronico</li>
                        <li>Consulte su estado de cuenta en linea las 24 horas</li>
                        <li>Proceso de afiliacion rapido y sencillo</li>
                    </ul>
                    <p class="text-sm text-slate_custom-400 italic">Disponible para todos los bancos nacionales.</p>
                </div>',
                'icono' => 'fas fa-credit-card',
                'color' => '#680c3e',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'titulo' => 'Integral Web',
                'slogan' => 'Tu comunidad en la palma de tu mano',
                'descripcion' => 'Portal web exclusivo para propietarios. Consulte su estado de cuenta, registre pagos, descargue recibos y mantenga el control de su condominio.',
                'detalle' => '<div class="space-y-4">
                    <h4 class="font-heading font-bold text-navy-800">Funcionalidades del Portal Web</h4>
                    <ul class="list-disc pl-5 space-y-2 text-sm text-slate_custom-500">
                        <li>Consulta de estado de cuenta en tiempo real</li>
                        <li>Registro de pagos por transferencia o deposito</li>
                        <li>Descarga de recibos de condominio en PDF</li>
                        <li>Historial completo de pagos realizados</li>
                        <li>Notificaciones de nuevos recibos emitidos</li>
                        <li>Acceso con usuario y clave personalizada</li>
                    </ul>
                    <p class="text-sm text-slate_custom-400 italic">Cada propietario recibe sus credenciales de acceso al momento de la afiliacion.</p>
                </div>',
                'icono' => 'fas fa-globe',
                'color' => '#273272',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'titulo' => 'Integral Cobranza',
                'slogan' => 'Gestion efectiva para su comunidad',
                'descripcion' => 'Departamento especializado en cobranza con estrategias puerta a puerta, telefonica, WhatsApp, correo electronico y gestion extrajudicial.',
                'detalle' => '<div class="space-y-4">
                    <h4 class="font-heading font-bold text-navy-800">Estrategias de Cobranza</h4>
                    <ul class="list-disc pl-5 space-y-2 text-sm text-slate_custom-500">
                        <li>Cobranza preventiva: recordatorios antes del vencimiento</li>
                        <li>Cobranza administrativa: llamadas, SMS y WhatsApp</li>
                        <li>Cobranza puerta a puerta en la residencia</li>
                        <li>Gestion extrajudicial con nuestro departamento legal</li>
                        <li>Convenios de pago personalizados</li>
                        <li>Reportes de morosidad para la junta de condominio</li>
                    </ul>
                    <p class="text-sm text-slate_custom-400 italic">Nuestro equipo legal esta conformado por 5 abogados especializados en materia condominial.</p>
                </div>',
                'icono' => 'fas fa-hand-holding-usd',
                'color' => '#d4a017',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($products as $product) {
            WelcomeProduct::updateOrCreate(
                ['titulo' => $product['titulo']],
                $product
            );
        }

        // ============ SERVICIOS ============
        $services = [
            ['titulo' => 'Registros subalternos, Entidades Bancarias, etc.', 'descripcion' => 'Gestion ante registros subalternos, entidades bancarias y organismos publicos para tramites legales de la comunidad.', 'icono' => 'fas fa-landmark', 'color_icono' => '#d4a017', 'orden' => 1],
            ['titulo' => 'Pago de servicios Publicos', 'descripcion' => 'Pago de servicios publicos de CANTV, CORPOELEC, HIDROCAPITAL, y otros servicios basicos para su comunidad.', 'icono' => 'fas fa-file-invoice-dollar', 'color_icono' => '#d4a017', 'orden' => 2],
            ['titulo' => 'Inscripcion y desincorporacion ante BANAVIH', 'descripcion' => 'Inscripcion y desincorporacion de obrero ante BANAVIH (Fondo de Ahorro Obligatorio para la Vivienda), I.V.S.S, Mpppst, Otros.', 'icono' => 'fas fa-university', 'color_icono' => '#d4a017', 'orden' => 3],
            ['titulo' => 'Servicio de SMS y WhatsApp', 'descripcion' => 'Servicio de SMS y envio de avisos de cobro por WhatsApp para mantener informada a la comunidad.', 'icono' => 'fas fa-mobile-alt', 'color_icono' => '#d4a017', 'orden' => 4],
            ['titulo' => 'Elaboracion de avisos de cobro', 'descripcion' => 'Elaboracion y envio de avisos de cobro, reportes de cartelera, tripticos de interes para la comunidad a peticion de la junta de condominio.', 'icono' => 'fas fa-clipboard-list', 'color_icono' => '#d4a017', 'orden' => 5],
            ['titulo' => 'Asistencia a asambleas', 'descripcion' => 'Asistencia y asesoramiento sin costo alguno a las asambleas de propietarios y copropietarios.', 'icono' => 'fas fa-users', 'color_icono' => '#d4a017', 'orden' => 6],
            ['titulo' => 'Servicio de pagina web gratuito', 'descripcion' => 'Servicio de pagina web gratuito, donde cada propietario tendra una clave y usuario para ingresar a su portal web.', 'icono' => 'fas fa-globe', 'color_icono' => '#d4a017', 'orden' => 7],
            ['titulo' => 'Elaboracion de nominas', 'descripcion' => 'Elaboracion de nominas y recibos de pago para obreros y empleados, todo lo relacionado a tramites legales de los trabajadores del conjunto residencial.', 'icono' => 'fas fa-calculator', 'color_icono' => '#d4a017', 'orden' => 8],
            ['titulo' => 'Jornadas de cobranza', 'descripcion' => 'Jornadas de cobranza puerta a puerta, telefonica, via WhatsApp y correo electronico.', 'icono' => 'fas fa-hand-holding-usd', 'color_icono' => '#d4a017', 'orden' => 9],
            ['titulo' => 'Departamento legal', 'descripcion' => 'Departamento legal conformado por 5 abogados condominiales para asesoramiento y gestion legal.', 'icono' => 'fas fa-balance-scale', 'color_icono' => '#d4a017', 'orden' => 10],
            ['titulo' => 'Asesoramiento sin costo', 'descripcion' => 'Asesoramiento sin costo alguno por parte de nuestro departamento legal en materia condominial.', 'icono' => 'fas fa-gavel', 'color_icono' => '#d4a017', 'orden' => 11],
            ['titulo' => 'Elaboracion de informes contables', 'descripcion' => 'Elaboracion de informes contables detallados para la transparencia financiera de la comunidad.', 'icono' => 'fas fa-chart-bar', 'color_icono' => '#d4a017', 'orden' => 12],
        ];

        foreach ($services as $service) {
            WelcomeService::updateOrCreate(['titulo' => $service['titulo']], array_merge($service, ['activo' => true]));
        }

        // ============ SETTINGS ============
        $settings = [
            // ── General ──
            ['clave' => 'titulo_sitio', 'valor' => 'Administradora Integral', 'tipo' => 'text', 'seccion' => 'general', 'etiqueta' => 'Titulo del Sitio'],
            ['clave' => 'nombre_empresa', 'valor' => 'Administradora', 'tipo' => 'text', 'seccion' => 'general', 'etiqueta' => 'Nombre Empresa (Header)'],
            ['clave' => 'subtitulo_empresa', 'valor' => 'Integral', 'tipo' => 'text', 'seccion' => 'general', 'etiqueta' => 'Subtitulo Empresa (Header)'],

            // ── Navegacion ──
            ['clave' => 'nav_link_1', 'valor' => 'Home', 'tipo' => 'text', 'seccion' => 'navegacion', 'etiqueta' => 'Nav Link 1'],
            ['clave' => 'nav_link_2', 'valor' => 'Productos', 'tipo' => 'text', 'seccion' => 'navegacion', 'etiqueta' => 'Nav Link 2'],
            ['clave' => 'nav_link_3', 'valor' => 'Residencias', 'tipo' => 'text', 'seccion' => 'navegacion', 'etiqueta' => 'Nav Link 3'],
            ['clave' => 'nav_link_4', 'valor' => 'Servicios', 'tipo' => 'text', 'seccion' => 'navegacion', 'etiqueta' => 'Nav Link 4'],
            ['clave' => 'nav_link_5', 'valor' => 'Ubicaciones', 'tipo' => 'text', 'seccion' => 'navegacion', 'etiqueta' => 'Nav Link 5'],
            ['clave' => 'nav_link_6', 'valor' => 'Contactanos', 'tipo' => 'text', 'seccion' => 'navegacion', 'etiqueta' => 'Nav Link 6'],
            ['clave' => 'nav_boton_login', 'valor' => 'Iniciar Sesion', 'tipo' => 'text', 'seccion' => 'navegacion', 'etiqueta' => 'Texto Boton Login'],

            // ── Hero ──
            ['clave' => 'hero_badge', 'valor' => 'Administradora Integral', 'tipo' => 'text', 'seccion' => 'hero', 'etiqueta' => 'Badge Hero'],
            ['clave' => 'hero_titulo', 'valor' => 'Administradora Integral', 'tipo' => 'text', 'seccion' => 'hero', 'etiqueta' => 'Titulo Hero (fallback sin slides)'],
            ['clave' => 'hero_subtitulo', 'valor' => 'Compania lider en el mercado inmobiliario de Venezuela', 'tipo' => 'text', 'seccion' => 'hero', 'etiqueta' => 'Subtitulo Hero (fallback sin slides)'],

            // ── Empresa (Mision / Vision) ──
            ['clave' => 'mision_titulo', 'valor' => 'Nuestra', 'tipo' => 'text', 'seccion' => 'empresa', 'etiqueta' => 'Titulo Mision (linea 1)'],
            ['clave' => 'mision_titulo_2', 'valor' => 'Mision', 'tipo' => 'text', 'seccion' => 'empresa', 'etiqueta' => 'Titulo Mision (linea 2)'],
            ['clave' => 'mision_texto', 'valor' => 'En Administradora Integral trabajamos para llegar a ser la Compania lider en el mercado inmobiliario, donde la excelencia, el compromiso y la responsabilidad son la guia de nuestra Organizacion.', 'tipo' => 'textarea', 'seccion' => 'empresa', 'etiqueta' => 'Texto Mision (parrafo 1)'],
            ['clave' => 'mision_texto_2', 'valor' => 'Contamos con personal altamente calificado para satisfacer las necesidades de nuestros clientes.', 'tipo' => 'textarea', 'seccion' => 'empresa', 'etiqueta' => 'Texto Mision (parrafo 2)'],
            ['clave' => 'vision_titulo', 'valor' => 'Nuestra Vision', 'tipo' => 'text', 'seccion' => 'empresa', 'etiqueta' => 'Titulo Vision'],
            ['clave' => 'vision_texto', 'valor' => 'Nuestra vision es proveer un servicio basado en la filosofia de la excelencia, responsabilidad y compromiso que le permitan a Administradora Integral y a sus clientes grandes oportunidades de crecimiento.', 'tipo' => 'textarea', 'seccion' => 'empresa', 'etiqueta' => 'Texto Vision'],

            // ── Visibilidad de secciones ──
            ['clave' => 'seccion_productos_visible', 'valor' => '1', 'tipo' => 'boolean', 'seccion' => 'visibilidad', 'etiqueta' => 'Mostrar Seccion Productos'],
            ['clave' => 'seccion_acciones_visible', 'valor' => '1', 'tipo' => 'boolean', 'seccion' => 'visibilidad', 'etiqueta' => 'Mostrar Seccion Acciones (Pagar/Consultar)'],
            ['clave' => 'seccion_residencias_visible', 'valor' => '1', 'tipo' => 'boolean', 'seccion' => 'visibilidad', 'etiqueta' => 'Mostrar Seccion Residencias'],
            ['clave' => 'seccion_servicios_visible', 'valor' => '1', 'tipo' => 'boolean', 'seccion' => 'visibilidad', 'etiqueta' => 'Mostrar Seccion Servicios'],
            ['clave' => 'seccion_mapa_visible', 'valor' => '1', 'tipo' => 'boolean', 'seccion' => 'visibilidad', 'etiqueta' => 'Mostrar Seccion Mapa'],

            // ── Productos ──
            ['clave' => 'productos_titulo', 'valor' => 'Nuestros Productos', 'tipo' => 'text', 'seccion' => 'productos', 'etiqueta' => 'Titulo Seccion Productos'],
            ['clave' => 'productos_subtitulo', 'valor' => 'Soluciones a tu medida', 'tipo' => 'text', 'seccion' => 'productos', 'etiqueta' => 'Subtitulo Seccion Productos'],

            // ── CTAs (Pagar / Consultar) ──
            ['clave' => 'cta_pagar_icono', 'valor' => 'fas fa-file-invoice-dollar', 'tipo' => 'text', 'seccion' => 'acciones', 'etiqueta' => 'Icono - Pagar Recibo'],
            ['clave' => 'cta_pagar_titulo', 'valor' => 'Pague su recibo de condominio', 'tipo' => 'text', 'seccion' => 'acciones', 'etiqueta' => 'Titulo - Pagar Recibo'],
            ['clave' => 'cta_pagar_texto', 'valor' => 'Registre su pago por transferencia o deposito bancario de forma rapida y segura.', 'tipo' => 'textarea', 'seccion' => 'acciones', 'etiqueta' => 'Texto - Pagar Recibo'],
            ['clave' => 'cta_pagar_boton', 'valor' => 'Pagar Aqui', 'tipo' => 'text', 'seccion' => 'acciones', 'etiqueta' => 'Boton - Pagar Recibo'],
            ['clave' => 'cta_consultar_icono', 'valor' => 'fas fa-search-dollar', 'tipo' => 'text', 'seccion' => 'acciones', 'etiqueta' => 'Icono - Consultar Recibo'],
            ['clave' => 'cta_consultar_titulo', 'valor' => 'Consulte su recibo de condominio', 'tipo' => 'text', 'seccion' => 'acciones', 'etiqueta' => 'Titulo - Consultar Recibo'],
            ['clave' => 'cta_consultar_texto', 'valor' => 'Revise sus estados de cuenta, deudas pendientes e historial de pagos realizados.', 'tipo' => 'textarea', 'seccion' => 'acciones', 'etiqueta' => 'Texto - Consultar Recibo'],
            ['clave' => 'cta_consultar_boton', 'valor' => 'Consultar Aqui', 'tipo' => 'text', 'seccion' => 'acciones', 'etiqueta' => 'Boton - Consultar Recibo'],

            // ── Residencias ──
            ['clave' => 'residencias_titulo', 'valor' => 'Nuestros Clientes', 'tipo' => 'text', 'seccion' => 'residencias', 'etiqueta' => 'Titulo Seccion Residencias'],
            ['clave' => 'residencias_subtitulo', 'valor' => 'Comunidades que confian en nosotros', 'tipo' => 'text', 'seccion' => 'residencias', 'etiqueta' => 'Subtitulo Seccion Residencias'],

            // ── Servicios ──
            ['clave' => 'servicios_titulo', 'valor' => 'Nuestros Servicios', 'tipo' => 'text', 'seccion' => 'servicios', 'etiqueta' => 'Titulo Seccion Servicios'],
            ['clave' => 'servicios_subtitulo', 'valor' => 'Adaptados a tus necesidades', 'tipo' => 'text', 'seccion' => 'servicios', 'etiqueta' => 'Subtitulo Seccion Servicios'],

            // ── Mapa ──
            ['clave' => 'mapa_titulo', 'valor' => 'Nuestras Sedes', 'tipo' => 'text', 'seccion' => 'mapa', 'etiqueta' => 'Titulo Seccion Mapa'],
            ['clave' => 'mapa_subtitulo', 'valor' => 'Encuentra nuestras oficinas y sedes en todo el pais', 'tipo' => 'text', 'seccion' => 'mapa', 'etiqueta' => 'Subtitulo Seccion Mapa'],

            // ── Footer ──
            ['clave' => 'footer_empresa_titulo', 'valor' => 'La Empresa', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Titulo Links Footer'],
            ['clave' => 'footer_oficina_titulo', 'valor' => 'Oficina Principal', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Titulo Oficina'],
            ['clave' => 'footer_ciudad', 'valor' => 'Caracas', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Ciudad'],
            ['clave' => 'footer_direccion_1', 'valor' => 'Av. Las Mercedes y Calle Guaicaipuro', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Direccion Linea 1'],
            ['clave' => 'footer_direccion_2', 'valor' => 'Edif. Torre Forum, Piso PB. Local A', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Direccion Linea 2'],
            ['clave' => 'footer_direccion_3', 'valor' => 'El Rosal, Chacao, Edo. Miranda', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Direccion Linea 3'],
            ['clave' => 'footer_telefono', 'valor' => '(0212) 951-56-11', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Telefono'],
            ['clave' => 'footer_email', 'valor' => 'info@administradoraintegral.com', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Email'],
            ['clave' => 'footer_ubicacion', 'valor' => 'Caracas, Venezuela', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Ubicacion General'],
            ['clave' => 'footer_razon_social', 'valor' => 'Administradora Integral E.L.B., C.A.', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Razon Social'],
            ['clave' => 'footer_cta_titulo', 'valor' => 'Solicita tu oferta de servicio', 'tipo' => 'text', 'seccion' => 'footer', 'etiqueta' => 'Titulo CTA Footer'],
            ['clave' => 'footer_cta_texto', 'valor' => 'Tiene alguna pregunta? Llamenos o contactenos para mayor informacion.', 'tipo' => 'textarea', 'seccion' => 'footer', 'etiqueta' => 'Texto CTA Footer'],
        ];

        foreach ($settings as $setting) {
            WelcomeSetting::updateOrCreate(['clave' => $setting['clave']], $setting);
        }
    }
}
