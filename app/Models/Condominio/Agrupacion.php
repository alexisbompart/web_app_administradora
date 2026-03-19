<?php

namespace App\Models\Condominio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agrupacion extends Model
{
    protected $table = 'cond_agrupaciones';

    protected $fillable = [
        'compania_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }
}
