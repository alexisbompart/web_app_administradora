<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondMovFactEdif extends Model
{
    protected $table = 'cond_movs_fact_edif';
    protected $guarded = ['id'];

    protected $casts = [
        'monto_total' => 'decimal:2', 'fecha_calculo' => 'date', 'fecha_fact' => 'date',
        'legacy_created_at' => 'datetime', 'legacy_updated_at' => 'datetime',
    ];

    public function compania(): BelongsTo { return $this->belongsTo(Compania::class); }
    public function edificio(): BelongsTo { return $this->belongsTo(Edificio::class); }
}
