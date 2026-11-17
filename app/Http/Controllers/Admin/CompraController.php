<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Lote;
use App\Models\MovimientoInventario;

class CompraController extends Controller
{
    // ========================================
    // LISTADO DE COMPRAS
    // ========================================

    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));
        $estado = $request->input('estado', 'todos');

        $query = Compra::with(['proveedor', 'usuario'])
            ->withCount('detalles');

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                    ->orWhere('numero_factura', 'like', "%{$buscar}%")
                    ->orWhereHas('proveedor', function ($p) use ($buscar) {
                        $p->where('razon_social', 'like', "%{$buscar}%");
                    });
            });
        }

        if (in_array($estado, [
            'Borrador',
            'Confirmada',
            'Anulada',
        ], true)) {
            $query->where('estado', $estado);
        }

        $compras = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.compras.index', compact(
            'compras',
            'buscar',
            'estado'
        ));
    }

    // ========================================
    // FORMULARIO DE REGISTRO
    // ========================================

    public function create()
    {
        $proveedores = Proveedor::where('estado', 'Activo')
            ->orderBy('razon_social')
            ->get();

        $productos = Producto::where('estado', 'Activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.compras.create', compact(
            'proveedores',
            'productos'
        ));
    }

    // ========================================
    // GUARDAR COMPRA COMO BORRADOR
    // ========================================

    public function store(Request $request)
    {
        $datos = $request->validate([
            'proveedor_id' => [
                'required',
                Rule::exists('proveedores', 'id')
                    ->whereNull('deleted_at')
                    ->where('estado', 'Activo'),
            ],
            'fecha_compra' => ['required', 'date'],
            'numero_factura' => ['nullable', 'string', 'max:100'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],

            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => [
                'required',
                Rule::exists('productos', 'id')
                    ->whereNull('deleted_at')
                    ->where('estado', 'Activo'),
            ],
            'detalles.*.numero_lote' => [
                'required', 'string', 'max:100',
            ],
            'detalles.*.fecha_fabricacion' => [
                'nullable', 'date',
            ],
            'detalles.*.fecha_vencimiento' => [
                'required', 'date',
            ],
            'detalles.*.cantidad' => [
                'required', 'integer', 'min:1',
            ],
            'detalles.*.precio_compra' => [
                'required', 'numeric', 'min:0',
            ],
        ]);

        // ========================================
        // NORMALIZAR NÚMEROS DE LOTE
        // ========================================

        foreach ($datos['detalles'] as $indice => $detalle) {

            $numeroLote = mb_strtoupper(
                trim($detalle['numero_lote'])
            );

            // No permitir números de lote vacíos
            // después de eliminar espacios.
            if ($numeroLote === '') {
                throw ValidationException::withMessages([
                    "detalles.$indice.numero_lote" =>
                        'Ingrese un número de lote válido.',
                ]);
            }

            // Verificar longitud después de normalizar.
            if (mb_strlen($numeroLote) > 100) {
                throw ValidationException::withMessages([
                    "detalles.$indice.numero_lote" =>
                        'El número de lote no puede superar los 100 caracteres.',
                ]);
            }

            $datos['detalles'][$indice]['numero_lote'] = $numeroLote;
        }

        // Validar fechas de fabricación y vencimiento.
        foreach ($datos['detalles'] as $indice => $detalle) {
            if (
                !empty($detalle['fecha_fabricacion']) &&
                $detalle['fecha_fabricacion'] >
                    $detalle['fecha_vencimiento']
            ) {
                throw ValidationException::withMessages([
                    "detalles.$indice.fecha_vencimiento" =>
                        'El vencimiento debe ser posterior o igual a la fabricación.',
                ]);
            }
        }

        // El servidor calcula los importes.
        $subtotalCentavos = 0;
        $detallesCalculados = [];

        foreach ($datos['detalles'] as $detalle) {
            $precioCentavos = (int) round(
                ((float) $detalle['precio_compra']) * 100
            );

            $importeCentavos =
                $precioCentavos * (int) $detalle['cantidad'];

            $subtotalCentavos += $importeCentavos;

            $detalle['precio_compra'] = number_format(
                $precioCentavos / 100, 2, '.', ''
            );

            $detalle['subtotal'] = number_format(
                $importeCentavos / 100, 2, '.', ''
            );

            $detallesCalculados[] = $detalle;
        }

        $descuentoCentavos = (int) round(
            ((float) ($datos['descuento'] ?? 0)) * 100
        );

        if ($descuentoCentavos > $subtotalCentavos) {
            throw ValidationException::withMessages([
                'descuento' =>
                    'El descuento no puede superar el subtotal.',
            ]);
        }

        DB::transaction(function () use (
            $datos,
            $detallesCalculados,
            $subtotalCentavos,
            $descuentoCentavos
        ) {
            // Bloquear secuencia para evitar códigos duplicados.
            $secuencia = DB::table('compra_secuencias')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            if (!$secuencia) {
                throw new \RuntimeException(
                    'No se encontró la secuencia de compras.'
                );
            }

            $numero = $secuencia->ultimo_numero + 1;

            DB::table('compra_secuencias')
                ->where('id', 1)
                ->update(['ultimo_numero' => $numero]);

            $compra = Compra::create([
                'codigo' => 'COMP-' .
                    str_pad((string) $numero, 6, '0', STR_PAD_LEFT),
                'proveedor_id' => $datos['proveedor_id'],
                'usuario_id' => auth()->id(),
                'fecha_compra' => $datos['fecha_compra'],
                'numero_factura' =>
                    $datos['numero_factura'] ?? null,
                'subtotal' => number_format(
                    $subtotalCentavos / 100, 2, '.', ''
                ),
                'descuento' => number_format(
                    $descuentoCentavos / 100, 2, '.', ''
                ),
                'total' => number_format(
                    ($subtotalCentavos - $descuentoCentavos) / 100,
                    2, '.', ''
                ),
                'estado' => 'Borrador',
                'observaciones' =>
                    $datos['observaciones'] ?? null,
            ]);

            $compra->detalles()->createMany(
                $detallesCalculados
            );
        });

        return redirect()
            ->route('admin.compras.index')
            ->with('success', '¡Compra registrada como borrador correctamente!');
    }

    // ========================================
    // DETALLE DE LA COMPRA
    // ========================================

    public function show(Compra $compra)
    {
        $compra->load([
            'proveedor',
            'usuario',
            'detalles.producto',
        ]);

        return view('admin.compras.show', compact('compra'));
    }

    // ========================================
    // EDICIÓN
    // ========================================

    public function edit(Compra $compra)
    {
        abort_unless($compra->estado === 'Borrador', 403);

        $compra->load('detalles');

        $proveedores = Proveedor::where('estado', 'Activo')
            ->orderBy('razon_social')
            ->get();

        $productos = Producto::where('estado', 'Activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.compras.edit', compact(
            'compra',
            'proveedores',
            'productos'
        ));
    }

    // ========================================
    // ACTUALIZACIÓN
    // ========================================

    public function update(Request $request, Compra $compra)
    {
        // ========================================
        // VERIFICAR ESTADO DE LA COMPRA
        // ========================================

        abort_unless($compra->estado === 'Borrador', 403,
            'Solamente se pueden editar compras en estado Borrador.'
        );

        // ========================================
        // VALIDAR INFORMACIÓN
        // ========================================

        $datos = $request->validate([
            'proveedor_id' => [
                'required',
                Rule::exists('proveedores', 'id')
                    ->whereNull('deleted_at')
                    ->where('estado', 'Activo'),
            ],

            'fecha_compra' => ['required', 'date'],

            'numero_factura' => [
                'nullable', 'string', 'max:100',
            ],

            'descuento' => [
                'nullable', 'numeric', 'min:0',
            ],

            'observaciones' => [
                'nullable', 'string',
            ],

            'detalles' => [
                'required', 'array', 'min:1',
            ],

            'detalles.*.producto_id' => [
                'required',
                Rule::exists('productos', 'id')
                    ->whereNull('deleted_at')
                    ->where('estado', 'Activo'),
            ],

            'detalles.*.numero_lote' => [
                'required', 'string', 'max:100',
            ],

            'detalles.*.fecha_fabricacion' => [
                'nullable', 'date',
            ],

            'detalles.*.fecha_vencimiento' => [
                'required', 'date',
            ],

            'detalles.*.cantidad' => [
                'required', 'integer', 'min:1',
            ],

            'detalles.*.precio_compra' => [
                'required', 'numeric', 'min:0',
            ],
        ]);

        // ========================================
        // NORMALIZAR NÚMEROS DE LOTE
        // ========================================

        foreach ($datos['detalles'] as $indice => $detalle) {

            $numeroLote = mb_strtoupper(
                trim($detalle['numero_lote'])
            );

            if ($numeroLote === '') {
                throw ValidationException::withMessages([
                    "detalles.$indice.numero_lote" =>
                        'Ingrese un número de lote válido.',
                ]);
            }

            if (mb_strlen($numeroLote) > 100) {
                throw ValidationException::withMessages([
                    "detalles.$indice.numero_lote" =>
                        'El número de lote no puede superar los 100 caracteres.',
                ]);
            }

            $datos['detalles'][$indice]['numero_lote'] = $numeroLote;
        }

        // ========================================
        // VALIDAR FECHAS DE LOS LOTES
        // ========================================

        foreach ($datos['detalles'] as $indice => $detalle) {

            if (
                !empty($detalle['fecha_fabricacion']) &&
                $detalle['fecha_fabricacion'] >
                    $detalle['fecha_vencimiento']
            ) {
                throw ValidationException::withMessages([
                    "detalles.$indice.fecha_vencimiento" =>
                        'El vencimiento debe ser posterior o igual a la fabricación.',
                ]);
            }
        }

        // ========================================
        // CALCULAR IMPORTES EN EL SERVIDOR
        // ========================================

        $subtotalCentavos = 0;
        $detallesCalculados = [];

        foreach ($datos['detalles'] as $detalle) {

            $precioCentavos = (int) round(
                ((float) $detalle['precio_compra']) * 100
            );

            $importeCentavos =
                $precioCentavos * (int) $detalle['cantidad'];

            $subtotalCentavos += $importeCentavos;

            $detallesCalculados[] = [
                'producto_id' => $detalle['producto_id'],
                'numero_lote' => $detalle['numero_lote'],
                'fecha_fabricacion' =>
                    $detalle['fecha_fabricacion'] ?? null,
                'fecha_vencimiento' =>
                    $detalle['fecha_vencimiento'],
                'cantidad' => $detalle['cantidad'],
                'precio_compra' => number_format(
                    $precioCentavos / 100, 2, '.', ''
                ),
                'subtotal' => number_format(
                    $importeCentavos / 100, 2, '.', ''
                ),
            ];
        }

        $descuentoCentavos = (int) round(
            ((float) ($datos['descuento'] ?? 0)) * 100
        );

        if ($descuentoCentavos > $subtotalCentavos) {
            throw ValidationException::withMessages([
                'descuento' =>
                    'El descuento no puede superar el subtotal.',
            ]);
        }

        // ========================================
        // ACTUALIZAR COMPRA Y DETALLES
        // ========================================

        DB::transaction(function () use (
            $compra,
            $datos,
            $detallesCalculados,
            $subtotalCentavos,
            $descuentoCentavos
        ) {

            // Bloquear la compra durante la actualización.
            $compraActual = Compra::query()
                ->whereKey($compra->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // Volver a comprobar el estado dentro
            // de la transacción.
            abort_unless(
                $compraActual->estado === 'Borrador',
                403,
                'Esta compra ya no puede modificarse.'
            );

            // Actualizar información general.
            $compraActual->update([
                'proveedor_id' => $datos['proveedor_id'],
                'fecha_compra' => $datos['fecha_compra'],
                'numero_factura' =>
                    $datos['numero_factura'] ?? null,

                'subtotal' => number_format(
                    $subtotalCentavos / 100, 2, '.', ''
                ),

                'descuento' => number_format(
                    $descuentoCentavos / 100, 2, '.', ''
                ),

                'total' => number_format(
                    ($subtotalCentavos - $descuentoCentavos) / 100,
                    2, '.', ''
                ),

                'observaciones' =>
                    $datos['observaciones'] ?? null,
            ]);

            // Reemplazar los detalles del borrador.
            // No se modifica el inventario.
            $compraActual->detalles()->delete();

            $compraActual->detalles()->createMany(
                $detallesCalculados
            );
        });

        // ========================================
        // RESPUESTA EXITOSA
        // ========================================

        return redirect()
            ->route('admin.compras.index')
            ->with(
                'success',
                '¡Compra actualizada correctamente!'
            );
    }

    // ========================================
    // CONFIRMAR COMPRA
    // ========================================

    public function confirmar(Compra $compra)
    {
        DB::transaction(function () use ($compra) {

            // ========================================
            // BLOQUEAR COMPRA
            // ========================================

            $compraActual = Compra::query()
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Solo confirmar compras en Borrador.
            abort_unless(
                $compraActual->estado === 'Borrador',
                403,
                'Esta compra ya fue procesada o no puede confirmarse.'
            );

            // ========================================
            // OBTENER DETALLES DE LA COMPRA
            // ========================================

            $detalles = $compraActual->detalles()
                ->orderBy('id')
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'compra' => 'La compra no contiene productos.',
                ]);
            }

            // ========================================
            // BLOQUEAR PRODUCTOS EN ORDEN FIJO
            // ========================================

            $productoIds = $detalles
                ->pluck('producto_id')
                ->unique()
                ->sort()
                ->values();

            $productos = Producto::query()
                ->whereIn('id', $productoIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // ========================================
            // VALIDAR DETALLES DE LA COMPRA
            // ========================================

            foreach ($detalles as $detalle) {

                $producto = $productos->get($detalle->producto_id);

                // Verificar producto activo.
                if (!$producto || $producto->estado !== 'Activo') {
                    throw ValidationException::withMessages([
                        'compra' =>
                            'Uno de los productos no está disponible.',
                    ]);
                }

                // Verificar cantidad.
                if ((int) $detalle->cantidad <= 0) {
                    throw ValidationException::withMessages([
                        'compra' =>
                            'La compra contiene cantidades inválidas.',
                    ]);
                }

                // Verificar número de lote.
                if (trim((string) $detalle->numero_lote) === '') {
                    throw ValidationException::withMessages([
                        'compra' =>
                            'Uno de los productos no tiene número de lote.',
                    ]);
                }

                // Verificar fecha de vencimiento.
                if (!$detalle->fecha_vencimiento) {
                    throw ValidationException::withMessages([
                        'compra' =>
                            'Uno de los lotes no tiene fecha de vencimiento.',
                    ]);
                }

                // Verificar fabricación y vencimiento.
                if (
                    $detalle->fecha_fabricacion &&
                    $detalle->fecha_fabricacion->gt(
                        $detalle->fecha_vencimiento
                    )
                ) {
                    throw ValidationException::withMessages([
                        'compra' =>
                            'La fecha de fabricación no puede ser posterior al vencimiento.',
                    ]);
                }

                // No permitir medicamentos vencidos.
                if (
                    $detalle->fecha_vencimiento
                        ->startOfDay()
                        ->lt(today())
                ) {
                    throw ValidationException::withMessages([
                        'compra' =>
                            'No se puede confirmar la compra porque el lote ' .
                            $detalle->numero_lote .
                            ' está vencido.',
                    ]);
                }
            }

            // ========================================
            // PROCESAR LOTES
            // ========================================

            foreach ($detalles as $detalle) {

                // ========================================
                // NORMALIZAR NÚMERO DE LOTE
                // ========================================

                $numeroLote = mb_strtoupper(
                    trim($detalle->numero_lote),
                    'UTF-8'
                );

                // ========================================
                // BUSCAR Y BLOQUEAR LOTE
                // ========================================

                $lote = Lote::withTrashed()
                    ->where('producto_id', $detalle->producto_id)
                    ->where('numero_lote', $numeroLote)
                    ->lockForUpdate()
                    ->first();

                if ($lote) {

                    // ========================================
                    // VALIDAR LOTE EXISTENTE
                    // ========================================

                    // No permitir lotes eliminados.
                    if ($lote->trashed()) {
                        throw ValidationException::withMessages([
                            'compra' =>
                                'El lote ' . $numeroLote .
                                ' está eliminado. Debe regularizarse antes de confirmar.',
                        ]);
                    }

                    // No permitir lotes inactivos.
                    if ($lote->estado !== 'Activo') {
                        throw ValidationException::withMessages([
                            'compra' =>
                                'El lote ' . $numeroLote .
                                ' está inactivo.',
                        ]);
                    }

                    // Validar fecha de vencimiento.
                    if (
                        !$lote->fecha_vencimiento->isSameDay(
                            $detalle->fecha_vencimiento
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'compra' =>
                                'El vencimiento del lote ' .
                                $numeroLote .
                                ' no coincide con el registrado.',
                        ]);
                    }

                    // Validar fecha de fabricación.
                    $fabricacionLote = $lote->fecha_fabricacion
                        ? $lote->fecha_fabricacion->format('Y-m-d')
                        : null;

                    $fabricacionDetalle = $detalle->fecha_fabricacion
                        ? $detalle->fecha_fabricacion->format('Y-m-d')
                        : null;

                    if ($fabricacionLote !== $fabricacionDetalle) {
                        throw ValidationException::withMessages([
                            'compra' =>
                                'La fecha de fabricación del lote ' .
                                $numeroLote .
                                ' no coincide con la registrada.',
                        ]);
                    }

                    $stockAnterior = (int) $lote->stock_actual;

                } else {

                    // ========================================
                    // CREAR LOTE NUEVO
                    // ========================================

                    $lote = Lote::create([
                        'producto_id' => $detalle->producto_id,
                        'numero_lote' => $numeroLote,
                        'fecha_fabricacion' =>
                            $detalle->fecha_fabricacion,
                        'fecha_vencimiento' =>
                            $detalle->fecha_vencimiento,
                        'stock_actual' => 0,
                        'estado' => 'Activo',
                    ]);

                    $stockAnterior = 0;
                }

                // ========================================
                // ACTUALIZAR EXISTENCIAS
                // ========================================

                $cantidad = (int) $detalle->cantidad;

                $stockPosterior = $stockAnterior + $cantidad;

                // Evitar superar el límite INT UNSIGNED.
                if ($stockPosterior > 4294967295) {
                    throw ValidationException::withMessages([
                        'compra' =>
                            'La cantidad supera el límite permitido para el lote.',
                    ]);
                }

                // Actualizar stock del lote.
                $lote->update([
                    'stock_actual' => $stockPosterior,
                ]);

                // ========================================
                // REGISTRAR MOVIMIENTO DE INVENTARIO
                // ========================================

                MovimientoInventario::create([
                    'lote_id' => $lote->id,
                    'usuario_id' => auth()->id(),
                    'tipo' => 'Entrada',
                    'cantidad' => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stockPosterior,
                    'referencia' => $compraActual->codigo,
                    'observacion' =>
                        'Entrada por confirmación de compra.',
                ]);
            }

            // ========================================
            // CONFIRMAR COMPRA
            // ========================================

            $compraActual->update([
                'estado' => 'Confirmada',
                'fecha_confirmacion' => now(),
            ]);

        }, 3);

        // ========================================
        // REDIRECCIONAR CON MENSAJE
        // ========================================

        return redirect()
            ->route('admin.compras.show', $compra)
            ->with(
                'success',
                '¡Compra confirmada e inventario actualizado correctamente!'
            );
    }

    // ========================================
    // ELIMINACIÓN
    // ========================================

    public function destroy(Compra $compra)
    {
        // ========================================
        // ANULAR COMPRA EN ESTADO BORRADOR
        // ========================================

        DB::transaction(function () use ($compra) {

            // Bloquear la compra para evitar que
            // se confirme o anule simultáneamente.

            $compraActual = Compra::query()
                ->whereKey($compra->id)
                ->lockForUpdate()
                ->firstOrFail();

            // ========================================
            // VALIDAR ESTADO DE LA COMPRA
            // ========================================

            if ($compraActual->estado !== 'Borrador') {
                throw ValidationException::withMessages([
                    'compra' =>
                        'Solamente se pueden anular compras en estado Borrador.',
                ]);
            }

            // ========================================
            // ACTUALIZAR ESTADO
            // ========================================

            $compraActual->update([
                'estado' => 'Anulada',
            ]);

            // No eliminamos la compra ni sus detalles.
            // No modificamos lotes ni existencias.
            // No generamos movimientos de inventario.

        }, 3);

        // ========================================
        // REDIRECCIÓN
        // ========================================

        return redirect()
            ->route('admin.compras.index')
            ->with(
                'success',
                '¡Compra anulada correctamente!'
            );
    }
}
