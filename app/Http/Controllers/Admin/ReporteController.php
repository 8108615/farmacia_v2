<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Categoria;
use App\Models\Lote;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

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

    /**
    * Exportar el reporte de ventas a PDF.
    */
    public function ventasPdf(Request $request)
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

        // Consulta base con los mismos filtros del reporte.
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

        // Indicadores del reporte.
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

        // Obtener todas las ventas filtradas, sin paginación.
        $ventas = (clone $consulta)
            ->with('usuario')
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id')
            ->get();

        // Generar el documento PDF.
        $pdf = Pdf::loadView('admin.reportes.pdf.ventas', compact(
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
        ))->setPaper('a4', 'landscape');

        // Descargar el archivo.
        return $pdf->download(
            'Reporte_Ventas_' . now()->format('Ymd_His') . '.pdf'
        );
    }

        /**
     * Reporte de compras con filtros y estadísticas.
     */
    public function compras(Request $request)
    {
        // ========================================
        // VALIDAR FILTROS
        // ========================================

        $filtros = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],
            'estado' => [
                'nullable',
                'in:Borrador,Confirmada,Anulada',
            ],
            'proveedor_id' => [
                'nullable',
                'integer',
                'exists:proveedores,id',
            ],
        ]);

        $fechaInicio = $filtros['fecha_inicio'] ?? null;
        $fechaFin = $filtros['fecha_fin'] ?? null;
        $estado = $filtros['estado'] ?? null;
        $proveedorId = $filtros['proveedor_id'] ?? null;

        // ========================================
        // CONSULTA BASE
        // ========================================

        $consulta = Compra::query()
            ->when($fechaInicio, function ($query) use ($fechaInicio) {
                $query->whereDate(
                    'fecha_compra',
                    '>=',
                    $fechaInicio
                );
            })
            ->when($fechaFin, function ($query) use ($fechaFin) {
                $query->whereDate(
                    'fecha_compra',
                    '<=',
                    $fechaFin
                );
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($proveedorId, function ($query) use ($proveedorId) {
                $query->where('proveedor_id', $proveedorId);
            });

        // ========================================
        // INDICADORES
        // ========================================

        $totalCompras = (clone $consulta)->count();

        $comprasBorrador = (clone $consulta)
            ->where('estado', 'Borrador')
            ->count();

        $comprasConfirmadas = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->count();

        $comprasAnuladas = (clone $consulta)
            ->where('estado', 'Anulada')
            ->count();

        // Solo las compras confirmadas representan
        // adquisiciones efectivamente ingresadas al inventario.

        $totalConfirmado = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->sum('total');

        $descuentosConfirmados = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->sum('descuento');

        // ========================================
        // LISTADO PAGINADO
        // ========================================

        $compras = (clone $consulta)
            ->with(['proveedor', 'usuario'])
            ->orderByDesc('fecha_compra')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // ========================================
        // LISTADO DE PROVEEDORES
        // ========================================

        $proveedores = Proveedor::query()
            ->orderBy('razon_social')
            ->get();

        // ========================================
        // RETORNAR VISTA
        // ========================================

        return view('admin.reportes.compras', compact(
            'compras',
            'proveedores',
            'fechaInicio',
            'fechaFin',
            'estado',
            'proveedorId',
            'totalCompras',
            'comprasBorrador',
            'comprasConfirmadas',
            'comprasAnuladas',
            'totalConfirmado',
            'descuentosConfirmados'
        ));
    }


    /**
     * Exportar el reporte de compras a PDF.
     */
    public function comprasPdf(Request $request)
    {
        // ========================================
        // VALIDAR FILTROS
        // ========================================

        $filtros = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],
            'estado' => [
                'nullable',
                'in:Borrador,Confirmada,Anulada',
            ],
            'proveedor_id' => [
                'nullable',
                'integer',
                'exists:proveedores,id',
            ],
        ]);

        $fechaInicio = $filtros['fecha_inicio'] ?? null;
        $fechaFin = $filtros['fecha_fin'] ?? null;
        $estado = $filtros['estado'] ?? null;
        $proveedorId = $filtros['proveedor_id'] ?? null;

        // ========================================
        // CONSULTA BASE CON FILTROS
        // ========================================

        $consulta = Compra::query()
            ->when($fechaInicio, function ($query) use ($fechaInicio) {
                $query->whereDate(
                    'fecha_compra',
                    '>=',
                    $fechaInicio
                );
            })
            ->when($fechaFin, function ($query) use ($fechaFin) {
                $query->whereDate(
                    'fecha_compra',
                    '<=',
                    $fechaFin
                );
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($proveedorId, function ($query) use ($proveedorId) {
                $query->where('proveedor_id', $proveedorId);
            });

        // ========================================
        // INDICADORES
        // ========================================

        $totalCompras = (clone $consulta)->count();

        $comprasBorrador = (clone $consulta)
            ->where('estado', 'Borrador')
            ->count();

        $comprasConfirmadas = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->count();

        $comprasAnuladas = (clone $consulta)
            ->where('estado', 'Anulada')
            ->count();

        // Solo las compras confirmadas se consideran
        // adquisiciones efectivamente registradas.

        $totalConfirmado = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->sum('total');

        $descuentosConfirmados = (clone $consulta)
            ->where('estado', 'Confirmada')
            ->sum('descuento');

        // ========================================
        // OBTENER COMPRAS SIN PAGINACIÓN
        // ========================================

        $compras = (clone $consulta)
            ->with(['proveedor', 'usuario'])
            ->orderByDesc('fecha_compra')
            ->orderByDesc('id')
            ->get();

        // ========================================
        // PROVEEDOR SELECCIONADO
        // ========================================

        $proveedorSeleccionado = $proveedorId
            ? Proveedor::find($proveedorId)
            : null;

        // ========================================
        // GENERAR PDF
        // ========================================

        $pdf = Pdf::loadView(
            'admin.reportes.pdf.compras',
            compact(
                'compras',
                'fechaInicio',
                'fechaFin',
                'estado',
                'proveedorId',
                'proveedorSeleccionado',
                'totalCompras',
                'comprasBorrador',
                'comprasConfirmadas',
                'comprasAnuladas',
                'totalConfirmado',
                'descuentosConfirmados'
            )
        )->setPaper('a4', 'landscape');

        // ========================================
        // DESCARGAR PDF
        // ========================================

        return $pdf->download(
            'Reporte_Compras_' .
            now()->format('Ymd_His') .
            '.pdf'
        );
    }

    public function inventario(Request $request)
    {
        $datos = $this->datosInventario($request);
        $datos['lotes'] = $datos['consulta']->paginate(15)->appends($datos['filtros']);

        return view('admin.reportes.inventario', $datos);
    }

    public function inventarioPdf(Request $request)
    {
        $datos = $this->datosInventario($request);
        $datos['lotes'] = $datos['consulta']->get();

        return Pdf::loadView('admin.reportes.pdf.inventario', $datos)
            ->setPaper('a4', 'landscape')
            ->download('Reporte_Inventario_' . now()->format('Ymd_His') . '.pdf');
    }

    public function vencimientos(Request $request)
    {
        $datos = $this->datosVencimientos($request);
        $datos['lotes'] = $datos['consulta']->paginate(15)->appends($datos['filtros']);

        return view('admin.reportes.vencimientos', $datos);
    }

    public function vencimientosPdf(Request $request)
    {
        $datos = $this->datosVencimientos($request);
        $datos['lotes'] = $datos['consulta']->get();

        return Pdf::loadView('admin.reportes.pdf.vencimientos', $datos)
            ->setPaper('a4', 'landscape')
            ->download('Reporte_Vencimientos_' . now()->format('Ymd_His') . '.pdf');
    }

    private function datosVencimientos(Request $request): array
    {
        $opciones = [
            'vencidos' => 'Vencidos',
            'por_vencer' => 'Por vencer (hasta 30 días)',
            'vigentes' => 'Vigentes (más de 30 días)',
        ];

        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:200'],
            'categoria_id' => ['nullable', 'integer', Rule::exists('categorias', 'id')->whereNull('deleted_at')],
            'estado' => ['nullable', Rule::in(['Activo', 'Inactivo'])],
            'vencimiento' => ['nullable', Rule::in(array_keys($opciones))],
        ]);

        $buscar = $filtros['buscar'] ?? '';
        $categoriaId = $filtros['categoria_id'] ?? null;
        $hoy = today();
        $limite = $hoy->copy()->addDays(30);

        $consulta = Lote::query()
            ->with('producto.categoria')
            ->whereHas('producto', function ($producto) use ($categoriaId) {
                $producto->where('estado', 'Activo')
                    ->when($categoriaId, fn ($query) => $query->where('categoria_id', $categoriaId));
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $patron = "%{$buscar}%";
                $query->where(function ($coincidencias) use ($patron) {
                    $coincidencias->where('numero_lote', 'like', $patron)
                        ->orWhereHas('producto', function ($producto) use ($patron) {
                            $producto->where('nombre', 'like', $patron)
                                ->orWhere('codigo', 'like', $patron)
                                ->orWhere('codigo_barras', 'like', $patron);
                        });
                });
            })
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado));

        match ($filtros['vencimiento'] ?? null) {
            'vencidos' => $consulta->whereDate('fecha_vencimiento', '<', $hoy->toDateString()),
            'por_vencer' => $consulta->whereBetween('fecha_vencimiento', [$hoy->toDateString(), $limite->toDateString()]),
            'vigentes' => $consulta->whereDate('fecha_vencimiento', '>', $limite->toDateString()),
            default => null,
        };

        $totales = (clone $consulta)->toBase()->selectRaw(
            'COUNT(DISTINCT producto_id) AS productos, COUNT(*) AS lotes,
             COALESCE(SUM(stock_actual), 0) AS unidades,
             COUNT(CASE WHEN fecha_vencimiento < ? THEN 1 END) AS vencidos,
             COUNT(CASE WHEN fecha_vencimiento BETWEEN ? AND ? THEN 1 END) AS por_vencer,
             COUNT(CASE WHEN fecha_vencimiento > ? THEN 1 END) AS vigentes',
            [$hoy->toDateString(), $hoy->toDateString(), $limite->toDateString(), $limite->toDateString()]
        )->first();

        $indicadores = [
            'Productos' => $totales->productos,
            'Lotes' => $totales->lotes,
            'Unidades registradas' => $totales->unidades,
            'Lotes vencidos' => $totales->vencidos,
            'Lotes por vencer' => $totales->por_vencer,
            'Lotes vigentes' => $totales->vigentes,
        ];

        $categorias = Categoria::query()->orderBy('nombre')->pluck('nombre', 'id');
        $consulta->orderBy('fecha_vencimiento')->orderBy('producto_id')->orderBy('id');

        return compact('consulta', 'filtros', 'categorias', 'opciones', 'indicadores', 'hoy', 'limite');
    }

    private function datosInventario(Request $request): array
    {
        $opciones = [
            'vigentes' => 'Vigentes (más de 30 días)',
            'por_vencer' => 'Por vencer (hasta 30 días)',
            'vencidos' => 'Vencidos',
            'stock_bajo' => 'Stock bajo (1 a 10 unidades)',
            'sin_stock' => 'Sin stock',
        ];

        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:200'],
            'categoria_id' => ['nullable', 'integer', Rule::exists('categorias', 'id')->whereNull('deleted_at')],
            'estado' => ['nullable', Rule::in(['Activo', 'Inactivo'])],
            'filtro' => ['nullable', Rule::in(array_keys($opciones))],
        ]);

        $buscar = $filtros['buscar'] ?? '';
        $categoriaId = $filtros['categoria_id'] ?? null;
        $hoy = today();
        $limite = $hoy->copy()->addDays(30);
        $stockMinimo = 10;

        $consulta = Lote::query()
            ->with('producto.categoria')
            ->whereHas('producto', function ($producto) use ($categoriaId) {
                $producto->where('estado', 'Activo')
                    ->when($categoriaId, fn ($query) => $query->where('categoria_id', $categoriaId));
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $patron = "%{$buscar}%";
                $query->where(function ($coincidencias) use ($patron) {
                    $coincidencias->where('numero_lote', 'like', $patron)
                        ->orWhereHas('producto', function ($producto) use ($patron) {
                            $producto->where('nombre', 'like', $patron)
                                ->orWhere('codigo', 'like', $patron)
                                ->orWhere('codigo_barras', 'like', $patron);
                        });
                });
            })
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado));

        match ($filtros['filtro'] ?? null) {
            'vigentes' => $consulta->where('fecha_vencimiento', '>', $limite->toDateString()),
            'por_vencer' => $consulta->whereBetween('fecha_vencimiento', [$hoy->toDateString(), $limite->toDateString()]),
            'vencidos' => $consulta->where('fecha_vencimiento', '<', $hoy->toDateString()),
            'stock_bajo' => $consulta->whereBetween('stock_actual', [1, $stockMinimo]),
            'sin_stock' => $consulta->where('stock_actual', 0),
            default => null,
        };

        $totales = (clone $consulta)->toBase()->selectRaw(
            'COUNT(DISTINCT producto_id) AS productos, COUNT(*) AS lotes,
             COALESCE(SUM(stock_actual), 0) AS unidades,
             COUNT(CASE WHEN stock_actual BETWEEN 1 AND ? THEN 1 END) AS stock_bajo,
             COUNT(CASE WHEN stock_actual = 0 THEN 1 END) AS sin_stock,
             COUNT(CASE WHEN fecha_vencimiento BETWEEN ? AND ? THEN 1 END) AS por_vencer,
             COUNT(CASE WHEN fecha_vencimiento < ? THEN 1 END) AS vencidos',
            [$stockMinimo, $hoy->toDateString(), $limite->toDateString(), $hoy->toDateString()]
        )->first();

        $indicadores = [
            'Productos con lotes' => $totales->productos,
            'Lotes' => $totales->lotes,
            'Unidades registradas' => $totales->unidades,
            'Lotes con stock bajo' => $totales->stock_bajo,
            'Lotes sin stock' => $totales->sin_stock,
            'Lotes por vencer' => $totales->por_vencer,
            'Lotes vencidos' => $totales->vencidos,
        ];

        $categorias = Categoria::query()->orderBy('nombre')->pluck('nombre', 'id');
        $consulta->orderBy('producto_id')->orderBy('fecha_vencimiento')->orderBy('id');

        return compact('consulta', 'filtros', 'categorias', 'opciones', 'indicadores', 'hoy', 'limite');
    }
}
