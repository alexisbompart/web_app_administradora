<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ImportDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('importaciones', [ImportDashboardController::class, 'index'])->name('importaciones.index');

Route::resource('usuarios', UserController::class);
Route::get('apartamentos-por-edificio/{edificio}', [UserController::class, 'apartamentosPorEdificio'])->name('apartamentos-por-edificio');
Route::resource('roles', RoleController::class);
Route::post('roles/{role}/permisos/{permission}/attach', [RoleController::class, 'attachPermission'])->name('roles.permisos.attach');
Route::delete('roles/{role}/permisos/{permission}/detach', [RoleController::class, 'detachPermission'])->name('roles.permisos.detach');
