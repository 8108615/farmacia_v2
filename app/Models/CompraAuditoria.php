<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraAuditoria extends Model
{
    // ========================================
    // TABLA
    // ========================================

    protected $table = 'compra_auditorias';

    // ========================================
    // CAMPOS PERMITIDOS
    // ========================================

    protected $fillable = [
        'compra_id',
        'usuario_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'descripcion',
    ];

    // ========================================
    // CONVERSIÓN DE TIPOS
    // ========================================

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
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
    // RELACIÓN CON USUARIO
    // ========================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
