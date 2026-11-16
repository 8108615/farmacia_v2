<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class InventarioController extends Controller
{
    /**
     * Mostrar el listado de inventario.
     */
    public function index(Request $request)
    {
        // ========================================
        // BUSCADOR Y FILTROS
        // ========================================

        $buscar = trim((string) $request->input('buscar', ''));

        $filtrosPermitidos = [
            'todos',
            'vigentes',
            'por_vencer',
            'vencidos',
            'stock_bajo',
            'sin_stock',
        ];

        $filtro = (string) $request->input('filtro', 'todos');

        if (!in_array($filtro, $filtrosPermitidos, true)) {
            $filtro = 'todos';
        }

        // ========================================
        // FECHAS Y STOCK MÍNIMO
        // ========================================

        $hoy = today();
        $limiteVencimiento = today()->addDays(30);

        // Umbral provisional para stock bajo.
        $stockMinimo = 10;

        // ========================================
        // CONSULTA PRINCIPAL DEL INVENTARIO
        // ========================================

        $consulta = Lote::query()
            ->with(['producto.categoria'])
            ->whereHas('producto', function ($query) {
                $query->where('estado', 'Activo');
            });

        // ========================================
        // BUSCAR PRODUCTOS O LOTES
        // ========================================

        if ($buscar !== '') {

            $consulta->where(function ($query) use ($buscar) {

                $query
                    ->where('numero_lote', 'like', "%{$buscar}%")
                    ->orWhereHas('producto', function ($producto) use ($buscar) {

                        $producto
                            ->where('codigo', 'like', "%{$buscar}%")
                            ->orWhere('nombre', 'like', "%{$buscar}%")
                            ->orWhere('codigo_barras', 'like', "%{$buscar}%");

                    });

            });
        }

        // ========================================
        // FILTROS INTELIGENTES
        // ========================================

        switch ($filtro) {

            case 'vigentes':

                $consulta
                    ->where('estado', 'Activo')
                    ->whereDate('fecha_vencimiento', '>', $limiteVencimiento);

                break;

            case 'por_vencer':

                $consulta
                    ->where('estado', 'Activo')
                    ->whereDate('fecha_vencimiento', '>=', $hoy)
                    ->whereDate('fecha_vencimiento', '<=', $limiteVencimiento);

                break;

            case 'vencidos':

                $consulta
                    ->where('estado', 'Activo')
                    ->whereDate('fecha_vencimiento', '<', $hoy);

                break;

            case 'stock_bajo':

                $consulta
                    ->where('estado', 'Activo')
                    ->where('stock_actual', '>', 0)
                    ->where('stock_actual', '<=', $stockMinimo);

                break;

            case 'sin_stock':

                $consulta
                    ->where('estado', 'Activo')
                    ->where('stock_actual', 0);

                break;

            default:

                // Mostrar todos los lotes no eliminados
                // cuyos productos estén activos.

                break;
        }

        // ========================================
        // PAGINACIÓN
        // ========================================

        $lotes = $consulta
            ->orderBy('fecha_vencimiento')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        // ========================================
        // ESTADÍSTICAS DEL INVENTARIO
        // ========================================

        // Las estadísticas son generales.
        // No dependen del buscador ni del filtro.

        $consultaEstadisticas = Lote::query()
            ->where('estado', 'Activo')
            ->whereHas('producto', function ($query) {
                $query->where('estado', 'Activo');
            });

        // ========================================
        // LOTES VIGENTES
        // ========================================

        $lotesVigentes = (clone $consultaEstadisticas)
            ->whereDate('fecha_vencimiento', '>', $limiteVencimiento)
            ->count();

        // ========================================
        // PRÓXIMOS A VENCER
        // ========================================

        $lotesPorVencer = (clone $consultaEstadisticas)
            ->whereDate('fecha_vencimiento', '>=', $hoy)
            ->whereDate('fecha_vencimiento', '<=', $limiteVencimiento)
            ->count();

        // ========================================
        // LOTES VENCIDOS
        // ========================================

        $lotesVencidos = (clone $consultaEstadisticas)
            ->whereDate('fecha_vencimiento', '<', $hoy)
            ->count();

        // ========================================
        // STOCK BAJO
        // ========================================

        $lotesStockBajo = (clone $consultaEstadisticas)
            ->where('stock_actual', '>', 0)
            ->where('stock_actual', '<=', $stockMinimo)
            ->count();

        // ========================================
        // LOTES SIN STOCK
        // ========================================

        $lotesSinStock = (clone $consultaEstadisticas)
            ->where('stock_actual', 0)
            ->count();

        // ========================================
        // MOSTRAR VISTA
        // ========================================

        return view('admin.inventario.index', compact(
            'lotes',
            'buscar',
            'filtro',
            'lotesVigentes',
            'lotesPorVencer',
            'lotesVencidos',
            'lotesStockBajo',
            'lotesSinStock',
            'stockMinimo'
        ));
    }

    /**
     * Mostrar el formulario para registrar un lote.
     */
    public function create()
    {
        $productos = Producto::query()
            ->where('estado', 'Activo')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        return view('admin.inventario.create', compact('productos'));
    }

    /**
     * Guardar un nuevo lote.
     */
    public function store(Request $request)
    {
        // ========================================
        // VALIDACIÓN DE DATOS
        // ========================================

        $datos = $request->validate([
            'producto_id' => [
                'required',
                'integer',
                Rule::exists('productos', 'id')
                    ->where('estado', 'Activo')
                    ->whereNull('deleted_at'),
            ],

            'numero_lote' => [
                'required',
                'string',
                'max:100',
            ],

            'fecha_fabricacion' => [
                'nullable',
                'date',
            ],

            'fecha_vencimiento' => [
                'required',
                'date',
                'after_or_equal:fecha_fabricacion',
                'after_or_equal:today',
            ],

            'stock_actual' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],

            'estado' => [
                'required',
                Rule::in(['Activo', 'Inactivo']),
            ],
        ], [
            'producto_id.required' => 'Seleccione un producto.',
            'producto_id.exists' => 'El producto seleccionado no está disponible.',
            'numero_lote.required' => 'Ingrese el número de lote.',
            'fecha_vencimiento.required' => 'Ingrese la fecha de vencimiento.',
            'fecha_vencimiento.after_or_equal' =>
                    'La fecha de vencimiento debe ser igual o posterior a la fecha de fabricación y no puede estar vencida.',
                    
            'stock_actual.required' => 'Ingrese el stock inicial.',
            'stock_actual.min' => 'El stock no puede ser negativo.',
        ]);

        // ========================================
        // NORMALIZAR NÚMERO DE LOTE
        // ========================================

        $datos['numero_lote'] = mb_strtoupper(
            trim($datos['numero_lote'])
        );

        // ========================================
        // VERIFICAR LOTE DUPLICADO
        // ========================================

        $existe = Lote::withTrashed()
            ->where('producto_id', $datos['producto_id'])
            ->where('numero_lote', $datos['numero_lote'])
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'numero_lote' => 'Este número de lote ya está registrado para el producto seleccionado.',
            ]);
        }

        // ========================================
        // GUARDAR LOTE Y MOVIMIENTO
        // ========================================

        DB::transaction(function () use ($datos, $request) {

            $lote = Lote::create([
                'producto_id' => $datos['producto_id'],
                'numero_lote' => $datos['numero_lote'],
                'fecha_fabricacion' => $datos['fecha_fabricacion'] ?? null,
                'fecha_vencimiento' => $datos['fecha_vencimiento'],
                'stock_actual' => $datos['stock_actual'],
                'estado' => $datos['estado'],
            ]);

            // Registrar movimiento únicamente
            // cuando existen unidades iniciales.

            if ($lote->stock_actual > 0) {

                MovimientoInventario::create([
                    'lote_id' => $lote->id,
                    'usuario_id' => $request->user()->id,
                    'tipo' => 'Entrada',
                    'cantidad' => $lote->stock_actual,
                    'stock_anterior' => 0,
                    'stock_posterior' => $lote->stock_actual,
                    'referencia' => 'REGISTRO INICIAL',
                    'observacion' => 'Ingreso inicial de existencias al registrar el lote.',
                ]);
            }
        });

        // ========================================
        // REDIRECCIÓN
        // ========================================

        return redirect()
            ->route('admin.inventario.index')
            ->with('success', '¡Lote registrado correctamente!');
    }

    /**
     * Registrar una entrada, salida o ajuste de inventario.
     */
    public function registrarMovimiento(Request $request)
    {
        // ========================================
        // VALIDACIÓN DE DATOS
        // ========================================

        $datos = $request->validate([
            'lote_id' => [
                'required',
                'integer',
                Rule::exists('lotes', 'id')
                    ->where('estado', 'Activo')
                    ->whereNull('deleted_at'),
            ],

            'tipo' => [
                'required',
                Rule::in([
                    'Entrada',
                    'Salida',
                    'Ajuste Entrada',
                    'Ajuste Salida',
                ]),
            ],

            'cantidad' => [
                'required',
                'integer',
                'min:1',
            ],

            'referencia' => [
                'nullable',
                'string',
                'max:150',
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'lote_id.required' =>
                'Seleccione un lote.',

            'lote_id.exists' =>
                'El lote seleccionado no está disponible.',

            'tipo.required' =>
                'Seleccione el tipo de movimiento.',

            'tipo.in' =>
                'El tipo de movimiento no es válido.',

            'cantidad.required' =>
                'Ingrese la cantidad.',

            'cantidad.integer' =>
                'La cantidad debe ser un número entero.',

            'cantidad.min' =>
                'La cantidad debe ser mayor que cero.',
        ]);

        // ========================================
        // VALIDAR PERMISOS DEL USUARIO
        // ========================================

        $usuario = $request->user();

        $tipoMovimiento = $datos['tipo'];

        $esAjuste = in_array(
            $tipoMovimiento,
            ['Ajuste Entrada', 'Ajuste Salida'],
            true
        );

        if ($esAjuste) {

            // Los ajustes requieren un permiso especial.

            abort_unless(
                $usuario->can('inventario.ajustar'),
                403,
                'No tiene permisos para realizar ajustes de inventario.'
            );

        } else {

            // Entradas y salidas normales.

            abort_unless(
                $usuario->can('inventario.gestionar'),
                403,
                'No tiene permisos para gestionar movimientos de inventario.'
            );
        }

        // ========================================
        // JUSTIFICACIÓN OBLIGATORIA PARA AJUSTES
        // ========================================

        if (
            $esAjuste &&
            blank($datos['observacion'] ?? null)
        ) {

            throw ValidationException::withMessages([
                'observacion' =>
                    'Debe ingresar una justificación para realizar un ajuste de inventario.',
            ]);
        }

        // ========================================
        // TRANSACCIÓN DE INVENTARIO
        // ========================================

        DB::transaction(function () use ($datos, $usuario) {

            // Bloquear el lote para evitar
            // movimientos simultáneos.

            $lote = Lote::query()
                ->with('producto')
                ->whereKey($datos['lote_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // ====================================
            // VALIDAR ESTADO DEL LOTE Y PRODUCTO
            // ====================================

            if (
                $lote->estado !== 'Activo' ||
                $lote->producto === null ||
                $lote->producto->estado !== 'Activo' ||
                $lote->producto->trashed()
            ) {

                throw ValidationException::withMessages([
                    'lote_id' =>
                        'El lote o su producto no están disponibles o activos.',
                ]);
            }

            // ====================================
            // DATOS DEL MOVIMIENTO
            // ====================================

            $stockAnterior = (int) $lote->stock_actual;

            $cantidad = (int) $datos['cantidad'];

            $tipo = $datos['tipo'];

            // ====================================
            // VALIDAR FECHA DE VENCIMIENTO
            // ====================================

            $fechaVencimiento = \Illuminate\Support\Carbon::parse(
                $lote->fecha_vencimiento
            )->startOfDay();

            $hoy = today();

            $loteVencido = $fechaVencimiento->lt($hoy);

            // ====================================
            // BLOQUEAR SALIDAS NORMALES
            // DE MEDICAMENTOS VENCIDOS
            // ====================================

            if ($tipo === 'Salida' && $loteVencido) {

                throw ValidationException::withMessages([
                    'tipo' =>
                        'No se puede registrar una salida del lote ' .
                        $lote->numero_lote .
                        ' porque el medicamento está vencido. ' .
                        'Utilice Ajuste Salida con la autorización correspondiente.',
                ]);
            }

            // ====================================
            // BLOQUEAR INGRESOS A LOTES VENCIDOS
            // ====================================

            // No permitir nuevas entradas ni ajustes
            // de entrada cuando el lote ya venció.

            if (
                $loteVencido &&
                in_array(
                    $tipo,
                    ['Entrada', 'Ajuste Entrada'],
                    true
                )
            ) {

                throw ValidationException::withMessages([
                    'tipo' =>
                        'No se pueden ingresar existencias al lote ' .
                        $lote->numero_lote .
                        ' porque el medicamento está vencido.',
                ]);
            }

            // ====================================
            // CALCULAR NUEVO STOCK
            // ====================================

            if (in_array(
                $tipo,
                ['Entrada', 'Ajuste Entrada'],
                true
            )) {

                // Incrementar existencias.

                $stockPosterior = $stockAnterior + $cantidad;

            } else {

                // Verificar existencias suficientes.

                if ($cantidad > $stockAnterior) {

                    throw ValidationException::withMessages([
                        'cantidad' =>
                            'La cantidad supera el stock disponible de ' .
                            $stockAnterior . ' unidades.',
                    ]);
                }

                // Disminuir existencias.

                $stockPosterior = $stockAnterior - $cantidad;
            }

            // ====================================
            // VALIDAR LÍMITE DE STOCK
            // ====================================

            // Límite del campo unsignedInteger.

            if ($stockPosterior > 4294967295) {

                throw ValidationException::withMessages([
                    'cantidad' =>
                        'La cantidad supera el límite permitido.',
                ]);
            }

            // ====================================
            // ACTUALIZAR EXISTENCIAS
            // ====================================

            $lote->update([
                'stock_actual' => $stockPosterior,
            ]);

            // ====================================
            // REGISTRAR HISTORIAL
            // ====================================

            MovimientoInventario::create([
                'lote_id' => $lote->id,
                'usuario_id' => $usuario->id,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $stockPosterior,
                'referencia' => $datos['referencia'] ?? null,
                'observacion' => $datos['observacion'] ?? null,
            ]);
        });

        // ========================================
        // REDIRECCIÓN
        // ========================================

        return redirect()
            ->route('admin.inventario.index')
            ->with(
                'success',
                '¡Movimiento de inventario registrado correctamente!'
            );
    }

    /**
     * Mostrar el historial de movimientos de un lote.
     */
    public function historial(Request $request, Lote $lote)
    {
        // ========================================
        // VALIDACIÓN DE FILTROS
        // ========================================

        $filtros = $request->validate([
            'tipo' => [
                'nullable',
                Rule::in([
                    'Entrada',
                    'Salida',
                    'Ajuste Entrada',
                    'Ajuste Salida',
                ]),
            ],

            'fecha_desde' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'fecha_hasta' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_desde',
            ],
        ], [
            'tipo.in' =>
                'El tipo de movimiento seleccionado no es válido.',

            'fecha_desde.date_format' =>
                'La fecha inicial no es válida.',

            'fecha_hasta.date_format' =>
                'La fecha final no es válida.',

            'fecha_hasta.after_or_equal' =>
                'La fecha final no puede ser anterior a la fecha inicial.',
        ]);

        // ========================================
        // INFORMACIÓN DEL LOTE
        // ========================================

        $lote->load('producto.categoria');

        // ========================================
        // CONSULTA BASE DE MOVIMIENTOS
        // ========================================

        $consulta = MovimientoInventario::query()
            ->with('usuario')
            ->where('lote_id', $lote->id);

        // ========================================
        // FILTRAR POR TIPO DE MOVIMIENTO
        // ========================================

        if (!empty($filtros['tipo'])) {

            $consulta->where(
                'tipo',
                $filtros['tipo']
            );
        }

        // ========================================
        // FILTRAR POR FECHA INICIAL
        // ========================================

        if (!empty($filtros['fecha_desde'])) {

            $consulta->where(
                'created_at',
                '>=',
                $filtros['fecha_desde'] . ' 00:00:00'
            );
        }

        // ========================================
        // FILTRAR POR FECHA FINAL
        // ========================================

        if (!empty($filtros['fecha_hasta'])) {

            $consulta->where(
                'created_at',
                '<',
                \Illuminate\Support\Carbon::parse(
                    $filtros['fecha_hasta']
                )->addDay()->toDateString() . ' 00:00:00'
            );
        }

        // ========================================
        // ORDENAR Y PAGINAR RESULTADOS
        // ========================================

        $movimientos = $consulta
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // ========================================
        // ESTADÍSTICAS DEL HISTORIAL DEL LOTE
        // ========================================

        $estadisticas = MovimientoInventario::query()
            ->where('lote_id', $lote->id)
            ->selectRaw("
                tipo,
                COUNT(*) as total_movimientos,
                COALESCE(SUM(cantidad), 0) as total_unidades
            ")
            ->groupBy('tipo')
            ->get()
            ->keyBy('tipo');

        $resumen = [
            'entradas' => (int) (
                $estadisticas->get('Entrada')?->total_unidades ?? 0
            ),

            'salidas' => (int) (
                $estadisticas->get('Salida')?->total_unidades ?? 0
            ),

            'ajustes_entrada' => (int) (
                $estadisticas->get('Ajuste Entrada')?->total_unidades ?? 0
            ),

            'ajustes_salida' => (int) (
                $estadisticas->get('Ajuste Salida')?->total_unidades ?? 0
            ),
        ];

        // ========================================
        // MOSTRAR VISTA
        // ========================================

        return view('admin.inventario.historial', compact(
            'lote',
            'movimientos',
            'resumen'
        ));
    }

    /**
     * Exportar el historial de movimientos de un lote a PDF.
     */
    public function exportarPdf(Request $request, Lote $lote)
    {
        // ========================================
        // VALIDAR FILTROS
        // ========================================

        $filtros = $request->validate([
            'tipo' => [
                'nullable',
                Rule::in([
                    'Entrada',
                    'Salida',
                    'Ajuste Entrada',
                    'Ajuste Salida',
                ]),
            ],

            'fecha_desde' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'fecha_hasta' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_desde',
            ],
        ]);

        // ========================================
        // INFORMACIÓN DEL LOTE
        // ========================================

        $lote->load('producto.categoria');

        // ========================================
        // CONSULTAR MOVIMIENTOS
        // ========================================

        $consulta = MovimientoInventario::query()
            ->with('usuario')
            ->where('lote_id', $lote->id);

        // Filtrar por tipo de movimiento.
        if (!empty($filtros['tipo'])) {
            $consulta->where('tipo', $filtros['tipo']);
        }

        // Filtrar por fecha inicial.
        if (!empty($filtros['fecha_desde'])) {
            $consulta->where(
                'created_at',
                '>=',
                $filtros['fecha_desde'] . ' 00:00:00'
            );
        }

        // Filtrar por fecha final.
        if (!empty($filtros['fecha_hasta'])) {
            $fechaFinal = \Illuminate\Support\Carbon::parse(
                $filtros['fecha_hasta']
            )->addDay()->toDateString();

            $consulta->where(
                'created_at',
                '<',
                $fechaFinal . ' 00:00:00'
            );
        }

        // ========================================
        // OBTENER TODOS LOS RESULTADOS
        // ========================================

        // No se utiliza paginate(), porque el PDF
        // debe incluir todos los registros filtrados.

        $movimientos = $consulta
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        // ========================================
        // CALCULAR RESUMEN DE LOS RESULTADOS
        // ========================================

        $resumen = [
            'entradas' => (int) $movimientos
                ->where('tipo', 'Entrada')
                ->sum('cantidad'),

            'salidas' => (int) $movimientos
                ->where('tipo', 'Salida')
                ->sum('cantidad'),

            'ajustes_entrada' => (int) $movimientos
                ->where('tipo', 'Ajuste Entrada')
                ->sum('cantidad'),

            'ajustes_salida' => (int) $movimientos
                ->where('tipo', 'Ajuste Salida')
                ->sum('cantidad'),
        ];

        // ========================================
        // GENERAR DOCUMENTO PDF
        // ========================================

        $pdf = Pdf::loadView(
            'admin.inventario.pdf.historial',
            compact(
                'lote',
                'movimientos',
                'resumen',
                'filtros'
            )
        )->setPaper('a4', 'landscape');

        // ========================================
        // NOMBRE DEL ARCHIVO
        // ========================================

        $nombreLote = preg_replace(
            '/[^A-Za-z0-9_-]/',
            '_',
            $lote->numero_lote
        );

        $nombreArchivo = 'Historial_Inventario_' .
            $nombreLote . '_' .
            now()->format('Ymd_His') . '.pdf';

        // ========================================
        // DESCARGAR PDF
        // ========================================

        return $pdf->download($nombreArchivo);
    }
}
