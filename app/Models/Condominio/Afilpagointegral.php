<?php

namespace App\Models\Condominio;

use App\Models\Catalogo\Estado;
use App\Models\Financiero\Banco;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Afilpagointegral extends Model
{
    protected $table = 'afilpagointegral';

    protected $fillable = [
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
        'fecha'                     => 'date',
        'fecha_estatus'             => 'date',
        'mercantil_fecha_envio'     => 'date',
        'mercantil_fecha_respuesta' => 'date',
    ];

    // ── Relaciones ──

    /** Un afiliado puede tener múltiples afilapto (apartamentos afiliados) */
    public function afilaptos(): HasMany
    {
        return $this->hasMany(Afilapto::class);
    }

    /** Primer afilapto activo — compatibilidad con código legado que usa ->afilapto */
    public function afilapto(): HasMany
    {
        return $this->hasMany(Afilapto::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

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

    /** Primer afilapto con apartamento vinculado (para contextos que necesitan un único inmueble) */
    public function primerAfilapto(): ?Afilapto
    {
        return $this->afilaptos()->whereNotNull('apartamento_id')->first();
    }
}
