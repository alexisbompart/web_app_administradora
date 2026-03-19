<?php

namespace App\Models;

use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Propietario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtencionCliente extends Model
{
    use HasFactory;

    protected $table = 'atencion_clientes';

    protected $fillable = [
        'compania_id',
        'edificio_id',
        'propietario_id',
        'ejecutivo_id',
        'tipo',
        'asunto',
        'descripcion',
        'prioridad',
        'estatus',
        'fecha_apertura',
        'fecha_cierre',
        'respuesta',
    ];

    protected $casts = [
        'fecha_apertura' => 'date',
        'fecha_cierre'   => 'date',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class);
    }

    public function ejecutivo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ejecutivo_id');
    }
}
