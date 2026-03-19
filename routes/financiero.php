<?php

use App\Http\Controllers\Financiero\FondoController;
use App\Http\Controllers\Financiero\CobranzaController;
use App\Http\Controllers\Financiero\ConcBancariaController;
use App\Http\Controllers\Financiero\EnvioReciboController;
use App\Http\Controllers\PagoIntegral\PagoIntegralController;
use App\Http\Controllers\CajaMatic\CajaMaticController;
use Illuminate\Support\Facades\Route;

// Fondos
Route::resource('fondos', FondoController::class);
Route::post('fondos/{fondo}/movimiento', [FondoController::class, 'registrarMovimiento'])->name('fondos.movimiento');

// Cobranza
Route::prefix('cobranza')->name('cobranza.')->group(function () {
    Route::get('/', [CobranzaController::class, 'index'])->name('index');
    Route::post('/pago', [CobranzaController::class, 'registrarPago'])->name('registrar-pago');
    Route::get('/morosos', [CobranzaController::class, 'morosos'])->name('morosos');
    Route::get('/judicial', [CobranzaController::class, 'gestionJudicial'])->name('judicial');
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
    Route::get('/saldo', [PagoIntegralController::class, 'consultarSaldo'])->name('consultar-saldo');
    Route::post('/procesar', [PagoIntegralController::class, 'procesarPago'])->name('procesar');
    Route::get('/comprobante/{pago}', [PagoIntegralController::class, 'comprobante'])->name('comprobante');
});

// CajaMatic
Route::prefix('cajamatic')->name('cajamatic.')->group(function () {
    Route::get('/', [CajaMaticController::class, 'index'])->name('index');
    Route::post('/depositar', [CajaMaticController::class, 'depositar'])->name('depositar');
    Route::get('/disponibilidad', [CajaMaticController::class, 'disponibilidad'])->name('disponibilidad');
});
