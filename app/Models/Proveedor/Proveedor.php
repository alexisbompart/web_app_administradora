<?php

namespace App\Models\Proveedor;

use App\Models\Financiero\Banco;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'rif',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'telefono',
        'celular',
        'email',
        'contacto',
        'tipo_contribuyente',
        'cuenta_bancaria',
        'banco_id',
        'user_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facturasProveedores(): HasMany
    {
        return $this->hasMany(FacturaProveedor::class);
    }
}
