# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Commands

```bash
# Database
php artisan migrate:fresh --seed     # Reset and reseed all data (use during development)
php artisan db:seed                  # Seed without migrating
php artisan migrate                  # Run pending migrations

# Assets
npm run build                        # Production build (run after editing Blade/CSS/JS)
npm run dev                          # Dev server with hot reload

# Application
php artisan serve                    # Start dev server at localhost:8000
php artisan route:list               # List all routes with middleware
php artisan tinker                   # Interactive REPL

# Tests
php artisan test                     # Run all tests
php artisan test --filter=TestName   # Run a single test class
```

## Architecture Overview

### Route Structure (RouteServiceProvider.php)
Routes are split into module files and loaded with role middleware:

| Prefix | File | Roles Allowed |
|--------|------|--------------|
| `/admin` | `routes/admin.php` | super-admin, administrador |
| `/condominio` | `routes/condominio.php` | all authenticated |
| `/personal` | `routes/personal.php` | super-admin, administrador, personal-rrhh |
| `/proveedores` | `routes/proveedores.php` | super-admin, administrador, gerente-contador, proveedor |
| `/financiero` | `routes/financiero.php` | super-admin, administrador, gerente-contador, cobranza |
| `/servicios` | `routes/atencion.php` | all authenticated |
| `/mi-condominio` | `routes/propietario.php` | super-admin, administrador, cliente-propietario |

Route names follow the pattern `{prefix}.{resource}.{action}` (e.g., `financiero.fondos.index`).

### Role-Permission System (Spatie)
- Middleware aliases: `role`, `permission`, `role_or_permission` (registered in `Kernel.php`)
- `Gate::before` in `AuthServiceProvider` gives `super-admin` all permissions automatically
- 7 roles: `super-admin`, `administrador`, `gerente-contador`, `personal-rrhh`, `proveedor`, `cliente-propietario`, `cobranza`
- Permission naming: `modulo.accion` (e.g., `fondos.ver`, `personal.aprobar-nomina`)
- Seeded via `RolePermissionSeeder`

### Test Users
| Email | Password | Role |
|-------|----------|------|
| admin@admin.com | password | super-admin |
| administrador@test.com | password | administrador |
| gerente@test.com | password | gerente-contador |
| rrhh@test.com | password | personal-rrhh |
| proveedor@test.com | password | proveedor |
| cliente@test.com | password | cliente-propietario |
| cobranza@test.com | password | cobranza |

### Model Organization (`app/Models/`)
Models are grouped in subdirectories:
- `Condominio/` — Compania, Edificio, Apartamento, Propietario, Afilapto, Afilpagointegral, Agrupacion
- `Financiero/` — CondDeudaApto, CondPago, CondPagoApto, CondMovFactApto, CondMovFactEdif, CondMovPrefact, CondGasto, Banco, Fondo, PagoIntegral, ConcBancaria, etc.
- `Personal/` — Trabajador, Nomina, NominaDetalle, Vacacion, PrestacionSocial, etc.
- `Proveedor/` — Proveedor, FacturaProveedor, Retencion, CronogramaPago
- `Catalogo/` — Estado, Parametro, Modulo, TasaBcv

Key relationship: `Propietario` ↔ `Apartamento` is a BelongsToMany with pivot fields `fecha_desde`, `fecha_hasta`, `propietario_actual`. Use `wherePivot('propietario_actual', true)` to get current apartments only.

### Propietario Portal (`MiCondominioController`)
`app/Http/Controllers/Propietario/MiCondominioController.php` handles the entire owner-facing portal. A propietario can own multiple apartments. Key private helpers:
- `getPropietario()` — resolves the linked `Propietario` from `auth()->user()->propietario`
- `getApartamentos()` — returns current apartments via pivot `propietario_actual = true`
- `buildGastoCatalog()` — resolves gas expense descriptions from `cond_gastos`

Payment registration validates chronological order (oldest debt must be paid first) and wraps DB writes in a transaction.

### Frontend Stack
- **Tailwind CSS** with custom design tokens — compile with `npm run build` after any class changes
- **Alpine.js** — used inline for dropdowns, modals, dynamic totals (checkboxes in Pago Integral)
- **Font Awesome 6** — icon library loaded via CDN
- **Fonts**: Poppins (headings, `font-heading`) and Rubik (body, `font-body`) from bunny.net

### CSS Utility Classes (defined in `resources/css/app.css`)
Use these consistently across Blade views:
- Layout: `.card`, `.card-header`, `.card-body`
- Buttons: `.btn-primary` (burgundy pill), `.btn-secondary` (slate pill)
- Tables: `.table-custom` (navy header, hoverable rows)
- Status: `.badge-success`, `.badge-danger`, `.badge-warning`, `.badge-info`
- Stats: `.stat-card`, `.stat-value`, `.stat-label`
- Navigation: `.sidebar-link`, `.sidebar-link.active`

### Design Tokens
- Primary brand color: `navy-800` (#273272) — headers, sidebar background, table headers
- Accent color: `burgundy-800` (#680c3e) — CTAs, active state, badges
- Body text: `slate_custom-500` (#565872)

### Spanish Route Parameter Fix
Laravel's inflector mangles Spanish plurals (e.g., `trabajadores` → `trabajadore`). Always add `->parameters()` on resource routes with Spanish plural nouns:
```php
Route::resource('trabajadores', TrabajadorController::class)->parameters(['trabajadores' => 'trabajador']);
```
Affected resources: `trabajadores`, `vacaciones`, `companias`, `propietarios`, `proveedores`.

### Import Controllers
`app/Http/Controllers/Condominio/ApartamentoImportController` and `EdificioImportController` handle CSV/Excel imports. Import routes must be declared **before** the `Route::resource()` call to avoid route conflicts (`GET /apartamentos/importar` vs `GET /apartamentos/{apartamento}`).

### View Naming Convention
Views follow `{module}/{resource}-{action}.blade.php`:
- `index` lists: `trabajadores.blade.php`
- `create/edit` forms: `trabajadores-form.blade.php`
- `show` detail: `trabajadores-show.blade.php`

All views extend `layouts.app` which includes `layouts.sidebar` and `layouts.navigation`. The sidebar renders role-based navigation using `@can`/`@hasRole` directives.
