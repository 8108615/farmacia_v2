<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'codigo',
        'usuario_id',
        'cliente_id',
        'fecha_venta',
        'subtotal',
        'descuento',
        'total',
        'estado',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_venta' => 'datetime',
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Usuario responsable de registrar la venta
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Medicamentos registrados en la venta
    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    // Historial de auditorías de la venta
    public function auditorias(): HasMany
    {
        return $this->hasMany(VentaAuditoria::class, 'venta_id');
    }
}
