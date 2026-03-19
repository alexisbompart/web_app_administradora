<?php

namespace App\Models\Condominio;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propietario extends Model
{
    use SoftDeletes;

    protected $table = 'propietarios';

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'telefono',
        'celular',
        'email',
        'direccion',
        'user_id',
        'estatus',
    ];

    protected $casts = [
        'estatus' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apartamentos(): BelongsToMany
    {
        return $this->belongsToMany(Apartamento::class, 'propietario_apartamento')
            ->withTimestamps()
            ->withPivot('fecha_desde', 'fecha_hasta', 'propietario_actual');
    }
}
