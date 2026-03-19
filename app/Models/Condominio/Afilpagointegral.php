<?php

namespace App\Models\Condominio;

use App\Models\Catalogo\Estado;
use App\Models\Financiero\Banco;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Afilpagointegral extends Model
{
    protected $table = 'afilpagointegral';

    protected $fillable = [
        'afilapto_id',
        'fecha',
        'letra',
        'cedula_rif',
        'nombres',
        'apellidos',
        'email',
        'email_alterno',
        'calle_avenida',
        'piso_apto',
        'edif_casa',
        'urbanizacion',
        'ciudad',
        'estado_id',
        'telefono',
        'fax',
        'celular',
        'otro',
        'banco_id',
        'cta_bancaria',
        'tipo_cta',
        'nom_usuario',
        'clave',
        'creado_por',
        'cod_sucursal',
        'estatus',
        'fecha_estatus',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_estatus' => 'date',
    ];

    public function afilapto(): BelongsTo
    {
        return $this->belongsTo(Afilapto::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }
}
