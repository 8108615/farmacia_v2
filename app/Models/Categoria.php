<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'categorias';

    /**
     * Campos permitidos para asignación masiva.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    /**
     * Conversión de atributos.
     */
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }
}
