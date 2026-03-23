<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondDescuentoApto extends Model
{
    protected $table = 'cond_descuentos_apto';

    protected $fillable = [
        'compania_id', 'edificio_id', 'apartamento_id', 'periodo',
        'descuento', 'descuento_num', 'monto_honorario', 'monto_honorario_num',
        'motivo', 'observaciones',
        'cod_edif_legacy', 'compania_legacy', 'num_apto_legacy',
        'legacy_created_by', 'legacy_created_at',
        'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'descuento' => 'decimal:2',
        'descuento_num' => 'decimal:2',
        'monto_honorario' => 'decimal:2',
        'monto_honorario_num' => 'decimal:2',
        'legacy_created_at' => 'datetime',
        'legacy_updated_at' => 'datetime',
    ];

    public function compania(): BelongsTo { return $this->belongsTo(Compania::class); }
    public function edificio(): BelongsTo { return $this->belongsTo(Edificio::class); }
    public function apartamento(): BelongsTo { return $this->belongsTo(Apartamento::class); }
}
