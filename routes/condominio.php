<?php

use App\Http\Controllers\Condominio\CompaniaController;
use App\Http\Controllers\Condominio\EdificioController;
use App\Http\Controllers\Condominio\ApartamentoController;
use App\Http\Controllers\Condominio\ApartamentoImportController;
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
Route::resource('propietarios', PropietarioController::class)->parameters(['propietarios' => 'propietario']);
