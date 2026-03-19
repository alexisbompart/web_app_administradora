<?php

namespace App\Models\Financiero;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondCajero extends Model
{
    protected $table = 'cond_cajeros';

    protected $fillable = [
        'caja_id',
        'user_id',
        'fecha_asignacion',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function condCaja(): BelongsTo
    {
        return $this->belongsTo(CondCaja::class, 'caja_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
