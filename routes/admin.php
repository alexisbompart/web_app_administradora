<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ImportDashboardController;
use App\Http\Controllers\Admin\SolicitudServicioController;
use Illuminate\Support\Facades\Route;

Route::get('importaciones', [ImportDashboardController::class, 'index'])->name('importaciones.index');

// Solicitudes de Servicio
Route::get('solicitudes-servicio', [SolicitudServicioController::class, 'index'])->name('solicitudes-servicio.index');
Route::patch('solicitudes-servicio/{solicitud}/estatus', [SolicitudServicioController::class, 'updateEstatus'])->name('solicitudes-servicio.estatus');
Route::patch('solicitudes-servicio/{solicitud}/notas', [SolicitudServicioController::class, 'guardarNotas'])->name('solicitudes-servicio.notas');
Route::post('solicitudes-servicio/{solicitud}/correo', [SolicitudServicioController::class, 'enviarCorreo'])->name('solicitudes-servicio.correo');

Route::resource('usuarios', UserController::class);
Route::get('apartamentos-por-edificio/{edificio}', [UserController::class, 'apartamentosPorEdificio'])->name('apartamentos-por-edificio');
Route::resource('roles', RoleController::class);
Route::post('roles/{role}/permisos/{permission}/attach', [RoleController::class, 'attachPermission'])->name('roles.permisos.attach');
Route::delete('roles/{role}/permisos/{permission}/detach', [RoleController::class, 'detachPermission'])->name('roles.permisos.detach');
