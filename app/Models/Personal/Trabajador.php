<?php

namespace App\Models\Personal;

use App\Models\Condominio\Compania;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trabajador extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores';

    protected $fillable = [
        'compania_id',
        'cedula',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'direccion',
        'telefono',
        'celular',
        'email',
        'cargo',
        'departamento',
        'fecha_ingreso',
        'fecha_egreso',
        'salario_basico',
        'tipo_contrato',
        'estatus',
        'user_id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_egreso' => 'date',
        'salario_basico' => 'decimal:2',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nominaDetalles(): HasMany
    {
        return $this->hasMany(NominaDetalle::class);
    }

    public function prestacionesSociales(): HasMany
    {
        return $this->hasMany(PrestacionSocial::class);
    }

    public function vacaciones(): HasMany
    {
        return $this->hasMany(Vacacion::class);
    }

    public function fondosBeneficios(): HasMany
    {
        return $this->hasMany(FondoBeneficio::class);
    }

    public function prestamosTrabajadores(): HasMany
    {
        return $this->hasMany(PrestamoTrabajador::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "$this->nombres $this->apellidos";
    }
}
