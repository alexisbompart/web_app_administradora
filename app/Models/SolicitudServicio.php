<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudServicio extends Model
{
    protected $table = 'solicitudes_servicio';

    protected $fillable = [
        'nombres_apellidos',
        'email',
        'telefono',
        'asunto',
        'descripcion',
        'estatus',
        'notas_internas',
        'atendido_por',
        'fecha_respuesta',
    ];

    protected $casts = [
        'fecha_respuesta' => 'datetime',
    ];

    public function atendidoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'atendido_por');
    }

    public function estatusLabel(): string
    {
        return match($this->estatus) {
            'pendiente'   => 'Pendiente',
            'en_revision' => 'En Revisión',
            'respondida'  => 'Respondida',
            'cerrada'     => 'Cerrada',
            default       => $this->estatus,
        };
    }

    public function estatusBadgeClass(): string
    {
        return match($this->estatus) {
            'pendiente'   => 'badge-warning',
            'en_revision' => 'badge-info',
            'respondida'  => 'badge-success',
            'cerrada'     => 'bg-slate_custom-200 text-slate_custom-600 px-3 py-1 rounded-full text-xs font-semibold',
            default       => 'badge-warning',
        };
    }
}
