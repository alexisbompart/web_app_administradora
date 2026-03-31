<?php

namespace App\Models\Condominio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Afilapto extends Model
{
    protected $table = 'afilapto';

    protected $fillable = [
        'apartamento_id',
        'edificio_id',
        'compania_id',
        'cod_pint',
        'estatus_afil',
        'fecha_afiliacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_afiliacion' => 'date',
    ];

    public function apartamento(): BelongsTo
    {
        return $this->belongsTo(Apartamento::class);
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function afilpagointegral(): HasOne
    {
        return $this->hasOne(Afilpagointegral::class);
    }
}
