<?php

use App\Http\Controllers\Personal\TrabajadorController;
use App\Http\Controllers\Personal\NominaController;
use App\Http\Controllers\Personal\VacacionController;
use Illuminate\Support\Facades\Route;

Route::resource('trabajadores', TrabajadorController::class)->parameters(['trabajadores' => 'trabajador']);

Route::resource('nominas', NominaController::class);
Route::post('nominas/{nomina}/procesar', [NominaController::class, 'procesar'])->name('nominas.procesar');
Route::post('nominas/{nomina}/aprobar', [NominaController::class, 'aprobar'])->name('nominas.aprobar');

Route::resource('vacaciones', VacacionController::class)->parameters(['vacaciones' => 'vacacion']);
