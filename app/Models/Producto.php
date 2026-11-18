<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'codigo',
        'codigo_barras',
        'nombre',
        'nombre_generico',
        'marca',
        'laboratorio',
        'presentacion',
        'concentracion',
        'precio_venta',
        'registro_sanitario',
        'requiere_receta',
        'descripcion',
        'imagen',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'requiere_receta' => 'boolean',
            'precio_venta' => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    // Relación: un producto pertenece a una categoría
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }
}
