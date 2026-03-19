<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'parametros';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'grupo',
    ];
}
