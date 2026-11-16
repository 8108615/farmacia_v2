<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo',
        'razon_social',
        'nombre_comercial',
        'nit',
        'contacto',
        'telefono',
        'celular',
        'email',
        'direccion',
        'ciudad',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
