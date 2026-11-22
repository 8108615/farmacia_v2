<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();
        $hoy = today();
        $manana = $hoy->copy()->addDay();
        $limiteVencimiento = $hoy->copy()->addDays(30);
        $stockMinimo = 10;

        $datos = [
            'puedeVerProductos' => $usuario->can('productos.ver'),
            'puedeVerVentas' => $usuario->can('ventas.ver'),
            'puedeVerInventario' => $usuario->can('inventario.ver'),
            'totalProductos' => 0,
            'ventasDelDia' => 0,
            'ultimasVentas' => collect(),
            'lotesStockBajo' => 0,
            'lotesPorVencer' => 0,
            'alertasInventario' => collect(),
            'hoy' => $hoy,
            'limiteVencimiento' => $limiteVencimiento,
            'stockMinimo' => $stockMinimo,
        ];

        if ($datos['puedeVerProductos']) {
            $datos['totalProductos'] = Producto::query()->count();
        }

        if ($datos['puedeVerVentas']) {
            $ventasConfirmadas = Venta::query()->where('estado', 'Confirmada');

            $datos['ventasDelDia'] = (clone $ventasConfirmadas)
                ->where('fecha_venta', '>=', $hoy)
                ->where('fecha_venta', '<', $manana)
                ->sum('total');

            $datos['ultimasVentas'] = $ventasConfirmadas
                ->orderByDesc('fecha_venta')
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        if ($datos['puedeVerInventario']) {
            $lotesActivos = Lote::query()
                ->where('estado', 'Activo')
                ->whereHas('producto', fn ($query) => $query->where('estado', 'Activo'));

            $datos['lotesStockBajo'] = (clone $lotesActivos)
                ->whereBetween('stock_actual', [1, $stockMinimo])
                ->count();

            $datos['lotesPorVencer'] = (clone $lotesActivos)
                ->whereBetween('fecha_vencimiento', [
                    $hoy->toDateString(),
                    $limiteVencimiento->toDateString(),
                ])
                ->count();

            $datos['alertasInventario'] = $lotesActivos
                ->with('producto')
                ->where(function ($query) use ($stockMinimo, $hoy, $limiteVencimiento) {
                    $query->whereBetween('stock_actual', [1, $stockMinimo])
                        ->orWhereBetween('fecha_vencimiento', [
                            $hoy->toDateString(),
                            $limiteVencimiento->toDateString(),
                        ]);
                })
                ->orderBy('fecha_vencimiento')
                ->orderBy('id')
                ->limit(5)
                ->get();
        }

        return view('dashboard', $datos);
    }
}
