<?php

use App\Http\Controllers\Proveedor\ProveedorController;
use App\Http\Controllers\Proveedor\FacturaProveedorController;
use Illuminate\Support\Facades\Route;

Route::resource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor']);

Route::resource('facturas', FacturaProveedorController::class);
Route::post('facturas/{factura}/aprobar', [FacturaProveedorController::class, 'aprobar'])->name('facturas.aprobar');
Route::post('facturas/{factura}/retencion', [FacturaProveedorController::class, 'registrarRetencion'])->name('facturas.retencion');
