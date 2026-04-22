<?php

use App\Http\Controllers\Financiero\DataViewController;
use App\Http\Controllers\Financiero\FondoController;
use App\Http\Controllers\Financiero\CobranzaController;
use App\Http\Controllers\Financiero\ConcBancariaController;
use App\Http\Controllers\Financiero\EnvioReciboController;
use App\Http\Controllers\PagoIntegral\PagoIntegralController;
use App\Http\Controllers\Financiero\DeudaImportController;
use App\Http\Controllers\Financiero\DescuentoImportController;
use App\Http\Controllers\Financiero\AbonoImportController;
use App\Http\Controllers\Financiero\GastoImportController;
use App\Http\Controllers\Financiero\MovPrefactImportController;
use App\Http\Controllers\Financiero\MovFactAptoImportController;
use App\Http\Controllers\Financiero\MovFactEdifImportController;
use App\Http\Controllers\Financiero\PagoAptoImportController;
use App\Http\Controllers\Financiero\PagoImportController;
use App\Http\Controllers\Financiero\TasaBcvImportController;
use App\Http\Controllers\Financiero\TasaBcvController;
use App\Http\Controllers\CajaMatic\CajaMaticController;
use Illuminate\Support\Facades\Route;

// Importar Deudas
Route::get('deudas/importar', [DeudaImportController::class, 'showForm'])->name('deudas.importar');
Route::post('deudas/importar/preview', [DeudaImportController::class, 'preview'])->name('deudas.importar.preview');
Route::post('deudas/importar/execute', [DeudaImportController::class, 'execute'])->name('deudas.importar.execute');

// Importar Descuentos
Route::get('descuentos/importar', [DescuentoImportController::class, 'showForm'])->name('descuentos.importar');
Route::post('descuentos/importar/preview', [DescuentoImportController::class, 'preview'])->name('descuentos.importar.preview');
Route::post('descuentos/importar/execute', [DescuentoImportController::class, 'execute'])->name('descuentos.importar.execute');

// Importar Abonos
Route::get('abonos/importar', [AbonoImportController::class, 'showForm'])->name('abonos.importar');
Route::post('abonos/importar/preview', [AbonoImportController::class, 'preview'])->name('abonos.importar.preview');
Route::post('abonos/importar/execute', [AbonoImportController::class, 'execute'])->name('abonos.importar.execute');

// Importar Gastos
Route::get('gastos/importar', [GastoImportController::class, 'showForm'])->name('gastos.importar');
Route::post('gastos/importar/preview', [GastoImportController::class, 'preview'])->name('gastos.importar.preview');
Route::post('gastos/importar/execute', [GastoImportController::class, 'execute'])->name('gastos.importar.execute');

// Importar Pagos
Route::get('pagos/importar', [PagoImportController::class, 'showForm'])->name('pagos.importar');
Route::post('pagos/importar/preview', [PagoImportController::class, 'preview'])->name('pagos.importar.preview');
Route::post('pagos/importar/execute', [PagoImportController::class, 'execute'])->name('pagos.importar.execute');

// Importar Pagos Apto
Route::get('pagoapto/importar', [PagoAptoImportController::class, 'showForm'])->name('pagoapto.importar');
Route::post('pagoapto/importar/preview', [PagoAptoImportController::class, 'preview'])->name('pagoapto.importar.preview');
Route::post('pagoapto/importar/execute', [PagoAptoImportController::class, 'execute'])->name('pagoapto.importar.execute');

// Importar Mov. Facturacion Edificio
Route::get('movfactedif/importar', [MovFactEdifImportController::class, 'showForm'])->name('movfactedif.importar');
Route::post('movfactedif/importar/preview', [MovFactEdifImportController::class, 'preview'])->name('movfactedif.importar.preview');
Route::post('movfactedif/importar/execute', [MovFactEdifImportController::class, 'execute'])->name('movfactedif.importar.execute');

// Importar Mov. Facturacion Apto
Route::get('movfactapto/importar', [MovFactAptoImportController::class, 'showForm'])->name('movfactapto.importar');
Route::post('movfactapto/importar/preview', [MovFactAptoImportController::class, 'preview'])->name('movfactapto.importar.preview');
Route::post('movfactapto/importar/execute', [MovFactAptoImportController::class, 'execute'])->name('movfactapto.importar.execute');

// Importar Movimientos Pre-facturacion
Route::get('movprefact/importar', [MovPrefactImportController::class, 'showForm'])->name('movprefact.importar');
Route::post('movprefact/importar/preview', [MovPrefactImportController::class, 'preview'])->name('movprefact.importar.preview');
Route::post('movprefact/importar/execute', [MovPrefactImportController::class, 'execute'])->name('movprefact.importar.execute');

// Importar Tasas BCV
Route::get('tasabcv/importar', [TasaBcvImportController::class, 'showForm'])->name('tasabcv.importar');
Route::post('tasabcv/importar/preview', [TasaBcvImportController::class, 'preview'])->name('tasabcv.importar.preview');
Route::post('tasabcv/importar/execute', [TasaBcvImportController::class, 'execute'])->name('tasabcv.importar.execute');

// Tasas BCV CRUD
Route::resource('tasabcv', TasaBcvController::class)->except(['show']);

// Data Views (listados)
Route::get('deudas', [DataViewController::class, 'deudas'])->name('deudas.index');
Route::get('descuentos', [DataViewController::class, 'descuentos'])->name('descuentos.index');
Route::get('abonos', [DataViewController::class, 'abonos'])->name('abonos.index');
Route::get('gastos', [DataViewController::class, 'gastos'])->name('gastos.index');
Route::get('pagos', [DataViewController::class, 'pagos'])->name('pagos.index');
Route::get('pagos-apto', [DataViewController::class, 'pagosApto'])->name('pagos-apto.index');
Route::get('mov-prefact', [DataViewController::class, 'movPrefact'])->name('mov-prefact.index');
Route::get('fact-apto', [DataViewController::class, 'factApto'])->name('fact-apto.index');
Route::get('fact-edif', [DataViewController::class, 'factEdif'])->name('fact-edif.index');

// Fondos
Route::resource('fondos', FondoController::class);
Route::post('fondos/{fondo}/movimiento', [FondoController::class, 'registrarMovimiento'])->name('fondos.movimiento');

// Cobranza
Route::prefix('cobranza')->name('cobranza.')->group(function () {
    Route::get('/', [CobranzaController::class, 'index'])->name('index');
    Route::post('/pago', [CobranzaController::class, 'registrarPago'])->name('registrar-pago');
    Route::get('/morosos', [CobranzaController::class, 'morosos'])->name('morosos');
    Route::get('/judicial', [CobranzaController::class, 'gestionJudicial'])->name('judicial');
    Route::get('/pagos-pendientes', [CobranzaController::class, 'pagosPendientes'])->name('pagos-pendientes');
    Route::post('/pagos-pendientes/{pago}/aprobar', [CobranzaController::class, 'aprobarPago'])->name('aprobar-pago');
    Route::post('/pagos-pendientes/{pago}/rechazar', [CobranzaController::class, 'rechazarPago'])->name('rechazar-pago');
    Route::get('/pagos-pendientes/{pago}', [CobranzaController::class, 'verPago'])->name('ver-pago');
});

// Envio de Recibos
Route::prefix('envio-recibos')->name('envio-recibos.')->group(function () {
    Route::get('/', [EnvioReciboController::class, 'index'])->name('index');
    Route::post('/enviar', [EnvioReciboController::class, 'enviar'])->name('enviar');
});

// Conciliación Bancaria
Route::resource('conciliaciones', ConcBancariaController::class);

// Pago Integral
Route::prefix('pago-integral')->name('pago-integral.')->group(function () {
    Route::get('/', [PagoIntegralController::class, 'index'])->name('index');
    Route::get('/aprobacion', [PagoIntegralController::class, 'aprobacion'])->name('aprobacion');
    Route::get('/generar-archivo', [PagoIntegralController::class, 'generarArchivoForm'])->name('generar-archivo');
    Route::post('/generar-archivo', [PagoIntegralController::class, 'generarArchivo'])->name('generar-archivo.post');
    Route::get('/afiliaciones', [PagoIntegralController::class, 'afiliaciones'])->name('afiliaciones');
    Route::get('/afiliaciones/crear', [PagoIntegralController::class, 'afiliacionForm'])->name('afiliaciones.crear');
    Route::post('/afiliaciones', [PagoIntegralController::class, 'storeAfiliacion'])->name('afiliaciones.store');
    Route::get('/afiliaciones/{afiliacion}/editar', [PagoIntegralController::class, 'editAfiliacion'])->name('afiliaciones.edit');
    Route::put('/afiliaciones/{afiliacion}', [PagoIntegralController::class, 'updateAfiliacion'])->name('afiliaciones.update');
    Route::patch('/afiliaciones/{afiliacion}/desafiliar', [PagoIntegralController::class, 'desafiliar'])->name('afiliaciones.desafiliar');
    // Proceso Mercantil dos pasos
    Route::post('/afiliaciones/mercantil/generar-archivo', [PagoIntegralController::class, 'generarArchivoMercantilAfiliacion'])->name('afiliaciones.mercantil.generar');
    Route::delete('/afiliaciones/mercantil/anular-archivo', [PagoIntegralController::class, 'anularArchivoMercantilAfiliacion'])->name('afiliaciones.mercantil.anular');
    Route::get('/afiliaciones/mercantil/respuesta', [PagoIntegralController::class, 'procesarRespuestaMercantilAfiliacionForm'])->name('afiliaciones.mercantil.respuesta.form');
    Route::post('/afiliaciones/mercantil/respuesta', [PagoIntegralController::class, 'procesarRespuestaMercantilAfiliacion'])->name('afiliaciones.mercantil.respuesta');
    Route::get('/saldo', [PagoIntegralController::class, 'consultarSaldo'])->name('consultar-saldo');
    Route::post('/procesar', [PagoIntegralController::class, 'procesarPago'])->name('procesar');
    Route::get('/comprobante/{pago}', [PagoIntegralController::class, 'comprobante'])->name('comprobante');
    Route::post('/{pago}/aprobar', [PagoIntegralController::class, 'aprobarPago'])->name('aprobar');
    Route::post('/{pago}/rechazar', [PagoIntegralController::class, 'rechazarPago'])->name('rechazar');

    // Archivos bancarios - tracking
    Route::get('/archivos', [PagoIntegralController::class, 'archivos'])->name('archivos');
    Route::get('/archivos/{archivo}', [PagoIntegralController::class, 'archivoDetalle'])->name('archivos.detalle');
    Route::patch('/archivos/{archivo}/estatus', [PagoIntegralController::class, 'actualizarEstatusArchivo'])->name('archivos.estatus');
    Route::get('/archivos/{archivo}/procesar-respuesta', [PagoIntegralController::class, 'procesarRespuestaForm'])->name('archivos.procesar-respuesta');
    Route::post('/archivos/{archivo}/procesar-respuesta', [PagoIntegralController::class, 'procesarRespuesta'])->name('archivos.procesar-respuesta.post');
});

// CajaMatic
Route::prefix('cajamatic')->name('cajamatic.')->group(function () {
    Route::get('/', [CajaMaticController::class, 'index'])->name('index');
    Route::post('/depositar', [CajaMaticController::class, 'depositar'])->name('depositar');
    Route::get('/disponibilidad', [CajaMaticController::class, 'disponibilidad'])->name('disponibilidad');
});
