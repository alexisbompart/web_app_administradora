<?php

namespace App\Models\Proveedor;

use App\Models\Condominio\Compania;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaProveedor extends Model
{
    use SoftDeletes;

    protected $table = 'facturas_proveedores';

    protected $fillable = [
        'proveedor_id',
        'compania_id',
        'numero_factura',
        'numero_control',
        'fecha_factura',
        'fecha_recepcion',
        'fecha_vencimiento',
        'subtotal',
        'base_imponible',
        'monto_exento',
        'iva_porcentaje',
        'iva_monto',
        'total',
        'estatus',
        'observaciones',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_factura' => 'date',
        'fecha_recepcion' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'base_imponible' => 'decimal:2',
        'monto_exento' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_monto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function retenciones(): HasMany
    {
        return $this->hasMany(Retencion::class);
    }

    public function cronogramaPagos(): HasMany
    {
        return $this->hasMany(CronogramaPago::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
