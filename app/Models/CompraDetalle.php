<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraDetalle extends Model
{
    // ========================================
    // TABLA
    // ========================================

    protected $table = 'compra_detalles';

    // ========================================
    // CAMPOS PERMITIDOS
    // ========================================

    protected $fillable = [
        'compra_id',
        'producto_id',
        'numero_lote',
        'fecha_fabricacion',
        'fecha_vencimiento',
        'cantidad',
        'precio_compra',
        'subtotal',
    ];

    // ========================================
    // CONVERSIÓN DE TIPOS
    // ========================================

    protected function casts(): array
    {
        return [
            'fecha_fabricacion' => 'date',
            'fecha_vencimiento' => 'date',
            'cantidad' => 'integer',
            'precio_compra' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // ========================================
    // RELACIÓN CON COMPRA
    // ========================================

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    // ========================================
    // RELACIÓN CON PRODUCTO
    // ========================================

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
