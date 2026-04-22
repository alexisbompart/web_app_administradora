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
        // Proceso Mercantil
        'tipo_operacion',
        'mercantil_archivo_enviado',
        'mercantil_fecha_envio',
        'mercantil_estatus_proceso',
        'mercantil_fecha_respuesta',
        'mercantil_cod_respuesta',
        'mercantil_mensaje',
    ];

    protected $casts = [
        'fecha'                   => 'date',
        'fecha_estatus'           => 'date',
        'mercantil_fecha_envio'   => 'date',
        'mercantil_fecha_respuesta' => 'date',
    ];

    // ── Helpers ──
    public function esMercantil(): bool
    {
        return $this->banco?->iniciales === 'BM';
    }

    public function esAfiliacion(): bool
    {
        return ($this->tipo_operacion ?? 'A') === 'A';
    }

    public function labelTipoOperacion(): string
    {
        return ($this->tipo_operacion ?? 'A') === 'A' ? 'Afiliación' : 'Desafiliación';
    }

    public function labelMercantilEstatus(): string
    {
        return match($this->mercantil_estatus_proceso) {
            'P'     => 'Pendiente respuesta',
            'A'     => 'Aprobado',
            'R'     => 'Rechazado',
            default => 'No aplica',
        };
    }

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
