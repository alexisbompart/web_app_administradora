<?php

namespace App\Models\Condominio;

use App\Models\Catalogo\Estado;
use App\Models\Financiero\Banco;
use App\Models\Financiero\CondGasto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edificio extends Model
{
    use SoftDeletes;

    protected $table = 'cond_edificios';

    protected $fillable = [
        'cod_edif',
        'compania_id',
        'nombre',
        'direccion',
        'ciudad',
        'estado_id',
        'telefono',
        'email',
        'total_aptos',
        'rif',
        'nit',
        'alicuota_base',
        'fondo_reserva_porcentaje',
        'dia_corte',
        'dia_vencimiento',
        'mora_porcentaje',
        'interes_mora_porcentaje',
        'cuenta_bancaria',
        'banco_id',
        'activo',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'total_aptos' => 'integer',
        'alicuota_base' => 'decimal:4',
        'fondo_reserva_porcentaje' => 'decimal:2',
        'mora_porcentaje' => 'decimal:2',
        'interes_mora_porcentaje' => 'decimal:2',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function apartamentos(): HasMany
    {
        return $this->hasMany(Apartamento::class);
    }

    public function condGastos(): HasMany
    {
        return $this->hasMany(CondGasto::class, 'edificio_id');
    }
}
