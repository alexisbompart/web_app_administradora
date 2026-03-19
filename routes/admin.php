<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::resource('usuarios', UserController::class);
Route::resource('roles', RoleController::class);
Route::post('roles/{role}/permisos/{permission}/attach', [RoleController::class, 'attachPermission'])->name('roles.permisos.attach');
Route::delete('roles/{role}/permisos/{permission}/detach', [RoleController::class, 'detachPermission'])->name('roles.permisos.detach');
