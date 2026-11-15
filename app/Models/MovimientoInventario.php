<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'lote_id',
        'usuario_id',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_posterior',
        'referencia',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'stock_anterior' => 'integer',
            'stock_posterior' => 'integer',
        ];
    }

    // Relación con el lote
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    // Relación con el usuario responsable
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
