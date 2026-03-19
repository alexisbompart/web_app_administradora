<?php

use App\Http\Controllers\Condominio\CompaniaController;
use App\Http\Controllers\Condominio\EdificioController;
use App\Http\Controllers\Condominio\ApartamentoController;
use App\Http\Controllers\Condominio\PropietarioController;
use Illuminate\Support\Facades\Route;

Route::resource('companias', CompaniaController::class)->parameters(['companias' => 'compania']);
Route::resource('edificios', EdificioController::class);
Route::resource('apartamentos', ApartamentoController::class);
Route::resource('propietarios', PropietarioController::class)->parameters(['propietarios' => 'propietario']);
