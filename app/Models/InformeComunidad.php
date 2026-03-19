<?php

namespace App\Models;

use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformeComunidad extends Model
{
    use HasFactory;

    protected $table = 'informes_comunidad';

    protected $fillable = [
        'compania_id',
        'edificio_id',
        'generado_por',
        'tipo',
        'titulo',
        'contenido',
        'archivo_path',
        'periodo',
        'fecha_generacion',
        'enviado',
        'fecha_envio',
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
        'fecha_envio'      => 'datetime',
        'enviado'          => 'boolean',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generado_por');
    }
}
