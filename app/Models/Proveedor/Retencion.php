<?php

namespace App\Models\Proveedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Retencion extends Model
{
    protected $table = 'retenciones';

    protected $fillable = [
        'factura_proveedor_id',
        'tipo',
        'porcentaje',
        'base_imponible',
        'monto_retenido',
        'numero_comprobante',
        'fecha_retencion',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'base_imponible' => 'decimal:2',
        'monto_retenido' => 'decimal:2',
        'fecha_retencion' => 'date',
    ];

    public function facturaProveedor(): BelongsTo
    {
        return $this->belongsTo(FacturaProveedor::class);
    }
}
