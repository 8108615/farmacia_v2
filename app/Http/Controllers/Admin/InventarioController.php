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

class InventarioController extends Controller
{
    /**
     * Mostrar el listado de inventario.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $lotes = Lote::query()
            ->with(['producto.categoria'])
            ->whereHas('producto', function ($query) {
                $query->where('estado', 'Activo');
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($consulta) use ($buscar) {
                    $consulta
                        ->where('numero_lote', 'like', "%{$buscar}%")
                        ->orWhereHas('producto', function ($producto) use ($buscar) {
                            $producto
                                ->where('codigo', 'like', "%{$buscar}%")
                                ->orWhere('nombre', 'like', "%{$buscar}%")
                                ->orWhere('codigo_barras', 'like', "%{$buscar}%");
                        });
                });
            })
            ->orderBy('fecha_vencimiento')
            ->paginate(10)
            ->withQueryString();

        return view('admin.inventario.index', compact(
            'lotes',
            'buscar'
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
                'after:today',
                'after:fecha_fabricacion',
            ],

            'stock_actual' => [
                'required',
                'integer',
                'min:0',
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
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha actual y a la de fabricación.',
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
            'lote_id.required' => 'Seleccione un lote.',
            'lote_id.exists' => 'El lote seleccionado no está disponible.',
            'tipo.required' => 'Seleccione el tipo de movimiento.',
            'tipo.in' => 'El tipo de movimiento no es válido.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor que cero.',
        ]);

        // ========================================
        // TRANSACCIÓN DE INVENTARIO
        // ========================================

        DB::transaction(function () use ($datos, $request) {

            // Bloquear el lote mientras se actualiza
            // para evitar movimientos simultáneos.

            $lote = Lote::query()
                ->whereKey($datos['lote_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Verificar que el lote siga disponible.

            if (
                $lote->estado !== 'Activo' ||
                $lote->producto === null ||
                $lote->producto->estado !== 'Activo'
            ) {
                throw ValidationException::withMessages([
                    'lote_id' => 'El lote o su producto no están activos.',
                ]);
            }

            $stockAnterior = (int) $lote->stock_actual;
            $cantidad = (int) $datos['cantidad'];
            $tipo = $datos['tipo'];

            // ====================================
            // CALCULAR NUEVO STOCK
            // ====================================

            if (in_array($tipo, ['Entrada', 'Ajuste Entrada'], true)) {
                $stockPosterior = $stockAnterior + $cantidad;
            } else {

                if ($cantidad > $stockAnterior) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'La cantidad supera el stock disponible de ' .
                            $stockAnterior . ' unidades.',
                    ]);
                }

                $stockPosterior = $stockAnterior - $cantidad;
            }

            // Evitar desbordamiento del campo
            // unsignedInteger de la base de datos.

            if ($stockPosterior > 4294967295) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad supera el límite permitido.',
                ]);
            }

            // ====================================
            // ACTUALIZAR EXISTENCIAS
            // ====================================

            $lote->update([
                'stock_actual' => $stockPosterior,
            ]);

            // ====================================
            // REGISTRAR MOVIMIENTO
            // ====================================

            MovimientoInventario::create([
                'lote_id' => $lote->id,
                'usuario_id' => $request->user()->id,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $stockPosterior,
                'referencia' => $datos['referencia'] ?? null,
                'observacion' => $datos['observacion'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.inventario.index')
            ->with('success', '¡Movimiento de inventario registrado correctamente!');
    }
}
