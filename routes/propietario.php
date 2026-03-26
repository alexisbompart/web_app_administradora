<?php

use App\Http\Controllers\Propietario\MiCondominioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MiCondominioController::class, 'dashboard'])->name('dashboard');
Route::get('/deudas', [MiCondominioController::class, 'deudas'])->name('deudas');
Route::get('/pagos', [MiCondominioController::class, 'pagos'])->name('pagos');
Route::get('/recibo/{pagoApto}', [MiCondominioController::class, 'recibo'])->name('recibo');
Route::get('/recibo-condominio/{deuda}', [MiCondominioController::class, 'reciboCondominio'])->name('recibo-condominio');
Route::get('/recibos-edificio', [MiCondominioController::class, 'recibosEdificio'])->name('recibos-edificio');
Route::get('/recibos-apartamento', [MiCondominioController::class, 'recibosApartamento'])->name('recibos-apartamento');
Route::get('/ver-recibo/{factApto}', [MiCondominioController::class, 'verRecibo'])->name('ver-recibo');
Route::get('/ver-recibo-edificio/{factEdif}', [MiCondominioController::class, 'verReciboEdificio'])->name('ver-recibo-edificio');
Route::get('/estadisticas', [MiCondominioController::class, 'estadisticas'])->name('estadisticas');
Route::get('/registrar-pago', [MiCondominioController::class, 'registrarPagoForm'])->name('registrar-pago');
Route::post('/registrar-pago', [MiCondominioController::class, 'registrarPago'])->name('registrar-pago.store');

// Pago Integral para cliente-propietario
Route::get('/pago-integral', [MiCondominioController::class, 'pagoIntegralForm'])->name('pago-integral');
Route::post('/pago-integral', [MiCondominioController::class, 'pagoIntegralStore'])->name('pago-integral.store');
