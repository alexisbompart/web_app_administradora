<?php

use App\Http\Controllers\AtencionCliente\AtencionClienteController;
use App\Http\Controllers\Informe\InformeController;
use Illuminate\Support\Facades\Route;

// Atención al Cliente
Route::resource('atencion-cliente', AtencionClienteController::class);

// Informes a la Comunidad
Route::prefix('informes')->name('informes.')->group(function () {
    Route::get('/', [InformeController::class, 'index'])->name('index');
    Route::get('/estado-cuenta', [InformeController::class, 'estadoCuenta'])->name('estado-cuenta');
    Route::get('/morosos', [InformeController::class, 'morosos'])->name('morosos');
    Route::post('/generar', [InformeController::class, 'generarInforme'])->name('generar');
    Route::get('/relacion-gastos', [InformeController::class, 'relacionGastos'])->name('relacion-gastos');
    Route::get('/informe-anual', [InformeController::class, 'informeAnual'])->name('informe-anual');
    Route::get('/plan-operativo', [InformeController::class, 'planOperativo'])->name('plan-operativo');
    Route::get('/circulares', [InformeController::class, 'circulares'])->name('circulares');
    Route::post('/circulares', [InformeController::class, 'enviarCircular'])->name('circulares.enviar');
});
