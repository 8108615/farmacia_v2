<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    // ========================================
    // LISTADO DE VENTAS
    // ========================================

    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));
        $estado = $request->input('estado', 'todos');

        $query = Venta::with(['usuario'])
            ->withCount('detalles');

        // Buscar por código de venta
        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%");
            });
        }

        // Filtrar por estado
        if (in_array($estado, [
            'Borrador',
            'Confirmada',
            'Anulada',
        ], true)) {
            $query->where('estado', $estado);
        }

        // Ordenar de más reciente a más antigua
        $ventas = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.ventas.index', compact(
            'ventas',
            'buscar',
            'estado'
        ));
    }

    // ========================================
    // FORMULARIO DE NUEVA VENTA
    // ========================================

    public function create()
    {
        $productos = Producto::query()
            ->where('estado', 'Activo')
            ->whereNotNull('precio_venta')
            ->where('precio_venta', '>', 0)

            // Mostrar solo productos con lotes disponibles.
            ->whereHas('lotes', function ($query) {
                $query->where('estado', 'Activo')
                    ->where('stock_actual', '>', 0)
                    ->whereDate(
                        'fecha_vencimiento',
                        '>=',
                        today()
                    );
            })

            // Cargar lotes disponibles ordenados por FEFO.
            ->with(['lotes' => function ($query) {
                $query->where('estado', 'Activo')
                    ->where('stock_actual', '>', 0)
                    ->whereDate(
                        'fecha_vencimiento',
                        '>=',
                        today()
                    )
                    ->orderBy('fecha_vencimiento')
                    ->orderBy('id');
            }])

            ->orderBy('nombre')
            ->get();

        return view('admin.ventas.create', compact(
            'productos'
        ));
    }
}
