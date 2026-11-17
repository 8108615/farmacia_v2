<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    // ========================================
    // TABLA
    // ========================================

    protected $table = 'compras';

    // ========================================
    // CAMPOS PERMITIDOS
    // ========================================

    protected $fillable = [
        'codigo',
        'proveedor_id',
        'usuario_id',
        'fecha_compra',
        'numero_factura',
        'subtotal',
        'descuento',
        'total',
        'estado',
        'fecha_confirmacion',
        'observaciones',
    ];

    // ========================================
    // CONVERSIÓN DE TIPOS
    // ========================================

    protected function casts(): array
    {
        return [
            'fecha_compra' => 'date',
            'fecha_confirmacion' => 'datetime',
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // ========================================
    // RELACIÓN CON PROVEEDOR
    // ========================================

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    // ========================================
    // RELACIÓN CON USUARIO
    // ========================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ========================================
    // RELACIÓN CON DETALLES
    // ========================================

    public function detalles(): HasMany
    {
        return $this->hasMany(CompraDetalle::class);
    }

    // ========================================
    // RELACIÓN CON AUDITORÍA DE COMPRAS
    // ========================================

    public function auditorias(): HasMany
    {
        return $this->hasMany(CompraAuditoria::class, 'compra_id');
    }
}
