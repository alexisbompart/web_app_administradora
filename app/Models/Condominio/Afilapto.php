<?php

namespace App\Models\Condominio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Afilapto extends Model
{
    protected $table = 'afilapto';

    protected $fillable = [
        'afilpagointegral_id',
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

    public function afilpagointegral(): BelongsTo
    {
        return $this->belongsTo(Afilpagointegral::class);
    }

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
}
