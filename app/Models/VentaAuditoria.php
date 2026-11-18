<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaAuditoria extends Model
{
    protected $table = 'venta_auditorias';

    protected $fillable = [
        'venta_id',
        'usuario_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
        ];
    }

    // Venta relacionada con la auditoría
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // Usuario responsable de la operación
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
