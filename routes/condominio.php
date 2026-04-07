<?php

use App\Http\Controllers\Condominio\CompaniaController;
use App\Http\Controllers\Condominio\EdificioController;
use App\Http\Controllers\Condominio\ApartamentoController;
use App\Http\Controllers\Condominio\ApartamentoImportController;
use App\Http\Controllers\Condominio\AfilAptoController;
use App\Http\Controllers\Condominio\AfilAptoImportController;
use App\Http\Controllers\Condominio\AfilPagointegralController;
use App\Http\Controllers\Condominio\AfilPagointegralImportController;
use App\Http\Controllers\Condominio\EdificioImportController;
use App\Http\Controllers\Condominio\PropietarioController;
use Illuminate\Support\Facades\Route;

Route::resource('companias', CompaniaController::class)->parameters(['companias' => 'compania']);

// Edificio import routes BEFORE resource
Route::get('edificios/importar', [EdificioImportController::class, 'showForm'])->name('edificios.importar');
Route::post('edificios/importar/preview', [EdificioImportController::class, 'preview'])->name('edificios.importar.preview');
Route::post('edificios/importar/execute', [EdificioImportController::class, 'execute'])->name('edificios.importar.execute');

Route::resource('edificios', EdificioController::class);

// Import routes BEFORE resource to avoid conflict
Route::get('apartamentos/importar', [ApartamentoImportController::class, 'showForm'])->name('apartamentos.importar');
Route::post('apartamentos/importar/preview', [ApartamentoImportController::class, 'preview'])->name('apartamentos.importar.preview');
Route::post('apartamentos/importar/execute', [ApartamentoImportController::class, 'execute'])->name('apartamentos.importar.execute');

Route::resource('apartamentos', ApartamentoController::class);
Route::get('propietarios/generar', [PropietarioController::class, 'previewGenerate'])->name('propietarios.generate.preview');
Route::post('propietarios/generar/batch', [PropietarioController::class, 'executeBatch'])->name('propietarios.generate.batch');
Route::resource('propietarios', PropietarioController::class)->parameters(['propietarios' => 'propietario']);

// Afilapto: import routes BEFORE resource
Route::get('afilapto/importar', [AfilAptoImportController::class, 'showForm'])->name('afilapto.importar');
Route::post('afilapto/importar/preview', [AfilAptoImportController::class, 'preview'])->name('afilapto.importar.preview');
Route::post('afilapto/importar/execute', [AfilAptoImportController::class, 'execute'])->name('afilapto.importar.execute');

Route::resource('afilapto', AfilAptoController::class)->except(['show']);

// Afilpagointegral: import routes BEFORE resource
Route::get('afilpagointegral/importar', [AfilPagointegralImportController::class, 'showForm'])->name('afilpagointegral.importar');
Route::post('afilpagointegral/importar/preview', [AfilPagointegralImportController::class, 'preview'])->name('afilpagointegral.importar.preview');
Route::post('afilpagointegral/importar/execute', [AfilPagointegralImportController::class, 'execute'])->name('afilpagointegral.importar.execute');

Route::resource('afilpagointegral', AfilPagointegralController::class)->except(['show']);
