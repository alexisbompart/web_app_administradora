<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Módulo Admin
            Route::middleware(['web', 'auth', 'role:super-admin|administrador'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            // Módulo Condominio
            Route::middleware(['web', 'auth'])
                ->prefix('condominio')
                ->name('condominio.')
                ->group(base_path('routes/condominio.php'));

            // Módulo Personal / RRHH
            Route::middleware(['web', 'auth', 'role:super-admin|administrador|personal-rrhh'])
                ->prefix('personal')
                ->name('personal.')
                ->group(base_path('routes/personal.php'));

            // Módulo Proveedores
            Route::middleware(['web', 'auth', 'role:super-admin|administrador|gerente-contador|proveedor'])
                ->prefix('proveedores')
                ->name('proveedores.')
                ->group(base_path('routes/proveedores.php'));

            // Módulo Financiero (Fondos, Cobranza, Pago Integral, CajaMatic)
            Route::middleware(['web', 'auth', 'role:super-admin|administrador|gerente-contador|cobranza'])
                ->prefix('financiero')
                ->name('financiero.')
                ->group(base_path('routes/financiero.php'));

            // Módulo Atención al Cliente e Informes
            Route::middleware(['web', 'auth'])
                ->prefix('servicios')
                ->name('servicios.')
                ->group(base_path('routes/atencion.php'));

            // Portal del Propietario
            Route::middleware(['web', 'auth', 'role:super-admin|administrador|cliente-propietario'])
                ->prefix('mi-condominio')
                ->name('mi-condominio.')
                ->group(base_path('routes/propietario.php'));
        });
    }
}
