<?php

namespace App\Models\Financiero;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $table = 'bancos';

    protected $fillable = [
        'cod_banco',
        'nombre',
        'iniciales',
        'contacto',
        'telefono',
        'celular',
        'email',
        'direccion',
        'estatus_ftp',
        'host_ftp',
        'usuario_ftp',
        'password_ftp',
        'ruta_imagen',
        'ruta_documento',
        'ruta_arch_afil',
        'ruta_arch_pago',
    ];
}
