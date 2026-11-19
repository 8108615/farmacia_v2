<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;

class ReporteController extends Controller
{
    /**
     * Panel principal del módulo de Reportes.
     */
    public function index()
    {
        return view('admin.reportes.index');
    }


    /**
     * Reporte de ventas con filtros y estadísticas.
     */
    public function ventas(Request $request)
    {
        // Validar los filtros recibidos.
        $filtros = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['nullable', 'in:Borrador,Confirmada,Anulada'],
        ]);

        $fechaInicio = $filtros['fecha_inicio'] ?? null;
        $fechaFin = $filtros['fecha_fin'] ?? null;
        $estado = $filtros['estado'] ?? null;

        // Consulta base con filtros.
        $consulta = Venta::query()
            ->when($fechaInicio, function ($query) use ($fechaInicio) {
                $query->whereDate('fecha_venta', '>=', $fechaInicio);
            })
            ->when($fechaFin, function ($query) use ($fechaFin) {
                $query->whereDate('fecha_venta', '<=', $fechaFin);
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('estado', $estado);
            });

        // Indicadores de las ventas filtradas.
        $totalVentas = (clone $consulta)->count();

        $ventasBorrador = (clone $consulta)
            ->where('estado', 'Borrador')
            ->count();

        $ventasConfirmadas = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->count();

        $ventasAnuladas = (clone $consulta)
            ->where('estado', 'Anulada')
            ->count();

        // Solo las ventas confirmadas representan ingresos.
        $ingresosConfirmados = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->sum('total');

        $descuentosConfirmados = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->sum('descuento');

        // Listado paginado.
        $ventas = (clone $consulta)
            ->with('usuario')
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reportes.ventas', compact(
            'ventas',
            'fechaInicio',
            'fechaFin',
            'estado',
            'totalVentas',
            'ventasBorrador',
            'ventasConfirmadas',
            'ventasAnuladas',
            'ingresosConfirmados',
            'descuentosConfirmados'
        ));
    }

}