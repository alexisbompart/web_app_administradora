<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\WelcomeContentController;
use App\Models\WelcomeSlider;
use App\Models\WelcomeService;
use App\Models\WelcomeResidence;
use App\Models\WelcomeProduct;
use App\Models\WelcomeSetting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $sliders = WelcomeSlider::activo()->get();
    $services = WelcomeService::activo()->get();
    $residences = WelcomeResidence::activo()->get();
    $products = WelcomeProduct::activo()->get();
    $settings = WelcomeSetting::all()->pluck('valor', 'clave');
    return view('welcome', compact('sliders', 'services', 'residences', 'products', 'settings'));
});

Route::get('/api/companias-mapa', function () {
    return \App\Models\Condominio\Compania::where('activo', true)
        ->whereNotNull('latitud')
        ->whereNotNull('longitud')
        ->select('id', 'nombre', 'rif', 'direccion', 'telefono', 'email', 'latitud', 'longitud')
        ->get();
});

Route::get('/api/edificios-mapa', function () {
    return \App\Models\Condominio\Edificio::where('activo', true)
        ->whereNotNull('latitud')
        ->whereNotNull('longitud')
        ->select('id', 'nombre', 'direccion', 'ciudad', 'total_aptos', 'latitud', 'longitud')
        ->get();
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Welcome Content Management
    Route::get('/admin/welcome-content', [WelcomeContentController::class, 'index'])->name('admin.welcome.index');

    Route::post('/admin/welcome/sliders', [WelcomeContentController::class, 'storeSlider'])->name('admin.welcome.sliders.store');
    Route::put('/admin/welcome/sliders/{slider}', [WelcomeContentController::class, 'updateSlider'])->name('admin.welcome.sliders.update');
    Route::delete('/admin/welcome/sliders/{slider}', [WelcomeContentController::class, 'destroySlider'])->name('admin.welcome.sliders.destroy');

    Route::post('/admin/welcome/services', [WelcomeContentController::class, 'storeService'])->name('admin.welcome.services.store');
    Route::put('/admin/welcome/services/{service}', [WelcomeContentController::class, 'updateService'])->name('admin.welcome.services.update');
    Route::delete('/admin/welcome/services/{service}', [WelcomeContentController::class, 'destroyService'])->name('admin.welcome.services.destroy');

    Route::post('/admin/welcome/residences', [WelcomeContentController::class, 'storeResidence'])->name('admin.welcome.residences.store');
    Route::put('/admin/welcome/residences/{residence}', [WelcomeContentController::class, 'updateResidence'])->name('admin.welcome.residences.update');
    Route::delete('/admin/welcome/residences/{residence}', [WelcomeContentController::class, 'destroyResidence'])->name('admin.welcome.residences.destroy');

    Route::post('/admin/welcome/products', [WelcomeContentController::class, 'storeProduct'])->name('admin.welcome.products.store');
    Route::put('/admin/welcome/products/{product}', [WelcomeContentController::class, 'updateProduct'])->name('admin.welcome.products.update');
    Route::delete('/admin/welcome/products/{product}', [WelcomeContentController::class, 'destroyProduct'])->name('admin.welcome.products.destroy');

    Route::put('/admin/welcome/settings', [WelcomeContentController::class, 'updateSettings'])->name('admin.welcome.settings.update');
});

require __DIR__.'/auth.php';
