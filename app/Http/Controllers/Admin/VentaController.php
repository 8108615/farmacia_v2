<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\MovimientoInventario;
use App\Models\VentaAuditoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

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

    // ========================================
    // REGISTRAR NUEVA VENTA COMO BORRADOR
    // ========================================

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'observacion' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'detalles' => [
                'required',
                'array',
                'min:1',
            ],
            'detalles.*.producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
            ],
            'detalles.*.lote_id' => [
                'required',
                'integer',
                'exists:lotes,id',
            ],
            'detalles.*.cantidad' => [
                'required',
                'integer',
                'min:1',
            ],
            'detalles.*.descuento' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ], [
            'detalles.required' =>
                'Debe agregar al menos un medicamento.',
            'detalles.min' =>
                'Debe agregar al menos un medicamento.',
            'detalles.*.producto_id.required' =>
                'Debe seleccionar un medicamento.',
            'detalles.*.lote_id.required' =>
                'Debe seleccionar un lote.',
            'detalles.*.cantidad.min' =>
                'La cantidad debe ser mayor a cero.',
            'detalles.*.descuento.min' =>
                'El descuento no puede ser negativo.',
        ]);

        $datos = $validator->validate();

        // El borrador registra cantidades solicitadas,
        // pero todavía no descuenta existencias.
        $venta = DB::transaction(function () use ($datos) {

            $detallesPreparados = [];
            $subtotalCentavos = 0;
            $descuentoCentavos = 0;

            // Acumular cantidades por lote para evitar
            // superar el stock al repetir un medicamento.
            $cantidadesPorLote = [];

            foreach ($datos['detalles'] as $indice => $detalle) {

                $producto = Producto::query()
                    ->whereKey($detalle['producto_id'])
                    ->where('estado', 'Activo')
                    ->first();

                if (!$producto || !$producto->precio_venta ||
                    $producto->precio_venta <= 0) {
                    throw ValidationException::withMessages([
                        "detalles.$indice.producto_id" =>
                            'El medicamento no está disponible para la venta.',
                    ]);
                }

                $lote = Lote::query()
                    ->whereKey($detalle['lote_id'])
                    ->where('producto_id', $producto->id)
                    ->where('estado', 'Activo')
                    ->whereDate('fecha_vencimiento', '>=', today())
                    ->first();

                if (!$lote) {
                    throw ValidationException::withMessages([
                        "detalles.$indice.lote_id" =>
                            'El lote seleccionado no está disponible.',
                    ]);
                }

                $cantidad = (int) $detalle['cantidad'];
                $loteId = $lote->id;

                $cantidadesPorLote[$loteId] =
                    ($cantidadesPorLote[$loteId] ?? 0) + $cantidad;

                if ($cantidadesPorLote[$loteId] > $lote->stock_actual) {
                    throw ValidationException::withMessages([
                        "detalles.$indice.cantidad" =>
                            'La cantidad solicitada supera el stock disponible del lote.',
                    ]);
                }

                // Convertir importes a centavos para
                // evitar errores de precisión con decimales.
                $precioCentavos = (int) round(
                    (float) $producto->precio_venta * 100
                );

                $descuentoDetalleCentavos = (int) round(
                    (float) $detalle['descuento'] * 100
                );

                $importeBrutoCentavos = $precioCentavos * $cantidad;

                if ($descuentoDetalleCentavos > $importeBrutoCentavos) {
                    throw ValidationException::withMessages([
                        "detalles.$indice.descuento" =>
                            'El descuento no puede superar el importe del medicamento.',
                    ]);
                }

                $subtotalDetalleCentavos =
                    $importeBrutoCentavos - $descuentoDetalleCentavos;

                $subtotalCentavos += $importeBrutoCentavos;
                $descuentoCentavos += $descuentoDetalleCentavos;

                $detallesPreparados[] = [
                    'producto_id' => $producto->id,
                    'lote_id' => $lote->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => number_format(
                        $precioCentavos / 100, 2, '.', ''
                    ),
                    'descuento' => number_format(
                        $descuentoDetalleCentavos / 100, 2, '.', ''
                    ),
                    'subtotal' => number_format(
                        $subtotalDetalleCentavos / 100, 2, '.', ''
                    ),
                ];
            }

            // Bloquear la secuencia para evitar
            // códigos duplicados en ventas simultáneas.
            $secuencia = DB::table('venta_secuencias')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            if (!$secuencia) {
                DB::table('venta_secuencias')->insert([
                    'id' => 1,
                    'ultimo_numero' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $secuencia = DB::table('venta_secuencias')
                    ->where('id', 1)
                    ->lockForUpdate()
                    ->first();
            }

            $numero = $secuencia->ultimo_numero + 1;

            DB::table('venta_secuencias')
                ->where('id', 1)
                ->update([
                    'ultimo_numero' => $numero,
                    'updated_at' => now(),
                ]);

            $codigo = 'VENT-' . str_pad(
                (string) $numero,
                6,
                '0',
                STR_PAD_LEFT
            );

            $totalCentavos =
                $subtotalCentavos - $descuentoCentavos;

            // Crear la venta como borrador.
            $venta = Venta::create([
                'codigo' => $codigo,
                'usuario_id' => Auth::id(),
                'cliente_id' => null,
                'fecha_venta' => now(),
                'subtotal' => number_format(
                    $subtotalCentavos / 100, 2, '.', ''
                ),
                'descuento' => number_format(
                    $descuentoCentavos / 100, 2, '.', ''
                ),
                'total' => number_format(
                    $totalCentavos / 100, 2, '.', ''
                ),
                'estado' => 'Borrador',
                'observacion' => $datos['observacion'] ?? null,
            ]);

            // Registrar los medicamentos.
            $venta->detalles()->createMany($detallesPreparados);

            // Registrar auditoría.
            VentaAuditoria::create([
                'venta_id' => $venta->id,
                'usuario_id' => Auth::id(),
                'accion' => 'CREACION',
                'datos_anteriores' => null,
                'datos_nuevos' => [
                    'codigo' => $venta->codigo,
                    'estado' => $venta->estado,
                    'subtotal' => $venta->subtotal,
                    'descuento' => $venta->descuento,
                    'total' => $venta->total,
                    'detalles' => $detallesPreparados,
                ],
                'descripcion' =>
                    'Venta registrada como borrador.',
            ]);

            return $venta;
        });

        return redirect()
            ->route('admin.ventas.index')
            ->with(
                'success',
                "¡Venta {$venta->codigo} registrada correctamente como borrador!"
            );
    }

    // ========================================
    // VER DETALLE DE VENTA
    // ========================================

    public function show(Venta $venta)
    {
        $venta->load([
            'usuario',
            'detalles.producto',
            'detalles.lote',
            'auditorias.usuario',
        ]);

        return view('admin.ventas.show', compact('venta'));
    }


    public function edit(Venta $venta)
    {
        // Solo se pueden editar ventas en borrador.
        if ($venta->estado !== 'Borrador') {
            return redirect()
                ->route('admin.ventas.show', $venta)
                ->with(
                    'error',
                    'Solo se pueden editar ventas en estado Borrador.'
                );
        }

        // Cargar los detalles de la venta.
        $venta->load([
            'detalles.producto',
            'detalles.lote',
        ]);

        // Obtener productos activos con sus lotes disponibles.
        $productos = Producto::query()
            ->where('estado', 'Activo')
            ->where('precio_venta', '>', 0)
            ->with([
                'lotes' => function ($query) {
                    $query
                        ->where('estado', 'Activo')
                        ->where('stock_actual', '>', 0)
                        ->whereDate(
                            'fecha_vencimiento',
                            '>=',
                            today()
                        )
                        ->orderBy('fecha_vencimiento')
                        ->orderBy('id');
                },
            ])
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.ventas.edit',
            compact('venta', 'productos')
        );
    }


    public function update(Request $request, Venta $venta)
    {
        $datos = $request->validate([
            'observacion' => ['nullable', 'string', 'max:1000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => [
                'required', 'integer', 'exists:productos,id',
            ],
            'detalles.*.lote_id' => [
                'required', 'integer', 'exists:lotes,id',
            ],
            'detalles.*.cantidad' => [
                'required', 'integer', 'min:1',
            ],
            'detalles.*.descuento' => [
                'required', 'numeric', 'min:0', 'decimal:0,2',
            ],
        ]);

        try {
            DB::transaction(function () use ($venta, $datos) {

                // 1. Bloquear la venta y verificar su estado.
                $ventaActual = Venta::whereKey($venta->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($ventaActual->estado !== 'Borrador') {
                    throw ValidationException::withMessages([
                        'venta' => 'Solo se pueden editar ventas en estado Borrador.',
                    ]);
                }

                // 2. Guardar los datos anteriores.
                $ventaActual->load('detalles');

                $datosAnteriores = [
                    'observacion' => $ventaActual->observacion,
                    'subtotal' => $ventaActual->subtotal,
                    'descuento' => $ventaActual->descuento,
                    'total' => $ventaActual->total,
                    'detalles' => $ventaActual->detalles
                        ->map(fn ($detalle) => [
                            'producto_id' => $detalle->producto_id,
                            'lote_id' => $detalle->lote_id,
                            'cantidad' => $detalle->cantidad,
                            'precio_unitario' => $detalle->precio_unitario,
                            'descuento' => $detalle->descuento,
                            'subtotal' => $detalle->subtotal,
                        ])->values()->all(),
                ];

                // 3. Preparar los nuevos detalles.
                $detallesPreparados = [];
                $cantidadesPorLote = [];
                $subtotalCentavos = 0;
                $descuentoCentavos = 0;

                foreach ($datos['detalles'] as $indice => $detalle) {

                    $producto = Producto::query()
                        ->whereKey($detalle['producto_id'])
                        ->where('estado', 'Activo')
                        ->first();

                    if (
                        !$producto ||
                        $producto->precio_venta === null ||
                        (float) $producto->precio_venta <= 0
                    ) {
                        throw ValidationException::withMessages([
                            "detalles.$indice.producto_id" =>
                                'El medicamento no está disponible para la venta.',
                        ]);
                    }

                    $lote = Lote::query()
                        ->whereKey($detalle['lote_id'])
                        ->where('producto_id', $producto->id)
                        ->where('estado', 'Activo')
                        ->whereDate('fecha_vencimiento', '>=', today())
                        ->first();

                    if (!$lote) {
                        throw ValidationException::withMessages([
                            "detalles.$indice.lote_id" =>
                                'El lote seleccionado no está disponible.',
                        ]);
                    }

                    $cantidad = (int) $detalle['cantidad'];
                    $loteId = $lote->id;

                    $cantidadesPorLote[$loteId] =
                        ($cantidadesPorLote[$loteId] ?? 0) + $cantidad;

                    if ($cantidadesPorLote[$loteId] > $lote->stock_actual) {
                        throw ValidationException::withMessages([
                            "detalles.$indice.cantidad" =>
                                'La cantidad solicitada supera el stock disponible del lote.',
                        ]);
                    }

                    // Calcular importes en centavos.
                    $precioCentavos = (int) round(
                        (float) $producto->precio_venta * 100
                    );

                    $descuentoDetalleCentavos = (int) round(
                        (float) $detalle['descuento'] * 100
                    );

                    $brutoCentavos = $precioCentavos * $cantidad;

                    if ($descuentoDetalleCentavos > $brutoCentavos) {
                        throw ValidationException::withMessages([
                            "detalles.$indice.descuento" =>
                                'El descuento no puede superar el importe del medicamento.',
                        ]);
                    }

                    $netoCentavos =
                        $brutoCentavos - $descuentoDetalleCentavos;

                    $subtotalCentavos += $brutoCentavos;
                    $descuentoCentavos += $descuentoDetalleCentavos;

                    $detallesPreparados[] = [
                        'producto_id' => $producto->id,
                        'lote_id' => $lote->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => number_format(
                            $precioCentavos / 100, 2, '.', ''
                        ),
                        'descuento' => number_format(
                            $descuentoDetalleCentavos / 100, 2, '.', ''
                        ),
                        'subtotal' => number_format(
                            $netoCentavos / 100, 2, '.', ''
                        ),
                    ];
                }

                // 4. Actualizar los importes de la venta.
                $totalCentavos =
                    $subtotalCentavos - $descuentoCentavos;

                $ventaActual->update([
                    'observacion' => $datos['observacion'] ?? null,
                    'subtotal' => number_format(
                        $subtotalCentavos / 100, 2, '.', ''
                    ),
                    'descuento' => number_format(
                        $descuentoCentavos / 100, 2, '.', ''
                    ),
                    'total' => number_format(
                        $totalCentavos / 100, 2, '.', ''
                    ),
                ]);

                // 5. Reemplazar los detalles del borrador.
                $ventaActual->detalles()->delete();

                $ventaActual->detalles()
                    ->createMany($detallesPreparados);

                // 6. Registrar auditoría de edición.
                VentaAuditoria::create([
                    'venta_id' => $ventaActual->id,
                    'usuario_id' => Auth::id(),
                    'accion' => 'EDICION',
                    'datos_anteriores' => $datosAnteriores,
                    'datos_nuevos' => [
                        'observacion' => $ventaActual->observacion,
                        'subtotal' => $ventaActual->subtotal,
                        'descuento' => $ventaActual->descuento,
                        'total' => $ventaActual->total,
                        'detalles' => $detallesPreparados,
                    ],
                    'descripcion' =>
                        'Venta en borrador actualizada sin modificar inventario.',
                ]);

            }, 3);

            return redirect()
                ->route('admin.ventas.index')
                ->with(
                    'success',
                    "¡Venta {$venta->codigo} actualizada correctamente!"
                );

        } catch (ValidationException $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    collect($e->errors())->flatten()->first()
                );
        }
    }



    // ========================================
    // CONFIRMAR VENTA
    // ========================================

    public function confirmar(Venta $venta)
    {
        try {
            DB::transaction(function () use ($venta) {

                // ========================================
                // 1. BLOQUEAR Y VALIDAR LA VENTA
                // ========================================

                $venta = Venta::whereKey($venta->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($venta->estado !== 'Borrador') {
                    throw ValidationException::withMessages([
                        'venta' => 'Solo se pueden confirmar ventas en estado Borrador.',
                    ]);
                }

                $detalles = $venta->detalles()
                    ->orderBy('id')
                    ->get();

                if ($detalles->isEmpty()) {
                    throw ValidationException::withMessages([
                        'venta' => 'La venta no tiene medicamentos registrados.',
                    ]);
                }

                // ========================================
                // 2. AGRUPAR CANTIDADES POR LOTE
                // ========================================

                $cantidades = $detalles
                    ->groupBy('lote_id')
                    ->map(fn ($items) => $items->sum('cantidad'));

                // ========================================
                // 3. BLOQUEAR LOTES DISPONIBLES
                // ========================================

                // Se bloquean todos los lotes de los productos
                // involucrados, no solamente los seleccionados.

                $productoIds = $detalles
                    ->pluck('producto_id')
                    ->unique()
                    ->values();

                $lotes = Lote::query()
                    ->whereIn('producto_id', $productoIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $lotesPorId = $lotes->keyBy('id');

                // ========================================
                // 4. VALIDAR PRODUCTOS Y LOTES
                // ========================================

                foreach ($detalles as $detalle) {

                    $lote = $lotesPorId->get($detalle->lote_id);

                    if (
                        !$lote ||
                        (int) $lote->producto_id !== (int) $detalle->producto_id ||
                        $lote->estado !== 'Activo' ||
                        !$lote->fecha_vencimiento ||
                        $lote->fecha_vencimiento->lt(today())
                    ) {
                        throw ValidationException::withMessages([
                            'venta' => 'Uno de los lotes no está disponible para la venta.',
                        ]);
                    }

                    if (
                        !$lote->producto ||
                        $lote->producto->estado !== 'Activo'
                    ) {
                        throw ValidationException::withMessages([
                            'venta' => 'Uno de los productos está inactivo o no disponible.',
                        ]);
                    }

                    if ($detalle->cantidad <= 0) {
                        throw ValidationException::withMessages([
                            'venta' => 'Se encontró una cantidad de venta inválida.',
                        ]);
                    }
                }

                // ========================================
                // 5. VALIDAR STOCK DISPONIBLE
                // ========================================

                foreach ($cantidades as $loteId => $cantidad) {

                    $lote = $lotesPorId->get($loteId);

                    if ($lote->stock_actual < $cantidad) {
                        throw ValidationException::withMessages([
                            'venta' => "Stock insuficiente en el lote {$lote->numero_lote}.",
                        ]);
                    }
                }

                // ========================================
                // 6. VALIDACIÓN FEFO
                // ========================================

                foreach ($productoIds as $productoId) {

                    // Lotes activos, no vencidos y con stock.
                    $lotesProducto = $lotes
                        ->filter(function ($lote) use ($productoId) {
                            return
                                (int) $lote->producto_id === (int) $productoId &&
                                $lote->estado === 'Activo' &&
                                $lote->stock_actual > 0 &&
                                $lote->fecha_vencimiento !== null &&
                                $lote->fecha_vencimiento->gte(today());
                        })
                        ->sort(function ($a, $b) {

                            // Ordenar por fecha de vencimiento (FEFO).
                            $comparacion = $a->fecha_vencimiento->timestamp
                                <=> $b->fecha_vencimiento->timestamp;

                            // Si tienen la misma fecha, ordenar por ID.
                            return $comparacion !== 0
                                ? $comparacion
                                : $a->id <=> $b->id;
                        });

                    // Cantidad total solicitada del producto.
                    $cantidadPendiente = $detalles
                        ->where('producto_id', $productoId)
                        ->sum('cantidad');

                    foreach ($lotesProducto as $lote) {

                        if ($cantidadPendiente <= 0) {
                            break;
                        }

                        // Cantidad que corresponde consumir
                        // según el orden FEFO.
                        $cantidadEsperada = min(
                            $cantidadPendiente,
                            $lote->stock_actual
                        );

                        // Cantidad registrada en esta venta.
                        $cantidadRegistrada = (int) (
                            $cantidades->get($lote->id, 0)
                        );

                        if ($cantidadRegistrada !== $cantidadEsperada) {
                            throw ValidationException::withMessages([
                                'venta' =>
                                    "La venta no respeta FEFO. Revise el lote {$lote->numero_lote}, que tiene prioridad por vencimiento.",
                            ]);
                        }

                        $cantidadPendiente -= $cantidadEsperada;
                    }

                    if ($cantidadPendiente > 0) {
                        throw ValidationException::withMessages([
                            'venta' => 'No existe stock suficiente para completar la venta.',
                        ]);
                    }
                }

                // ========================================
                // 7. DESCONTAR INVENTARIO
                // ========================================

                foreach ($detalles as $detalle) {

                    $lote = $lotesPorId->get($detalle->lote_id);

                    $stockAnterior = $lote->stock_actual;

                    $stockPosterior = $stockAnterior - $detalle->cantidad;

                    $lote->update([
                        'stock_actual' => $stockPosterior,
                    ]);

                    MovimientoInventario::create([
                        'lote_id' => $lote->id,
                        'usuario_id' => Auth::id(),
                        'tipo' => 'Salida',
                        'cantidad' => $detalle->cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_posterior' => $stockPosterior,
                        'referencia' => $venta->codigo,
                        'observacion' => 'Salida por confirmación de venta.',
                    ]);
                }

                // ========================================
                // 8. ACTUALIZAR ESTADO
                // ========================================

                $venta->update([
                    'estado' => 'Confirmada',
                ]);

                // ========================================
                // 9. REGISTRAR AUDITORÍA
                // ========================================

                VentaAuditoria::create([
                    'venta_id' => $venta->id,
                    'usuario_id' => Auth::id(),
                    'accion' => 'CONFIRMACION',
                    'datos_anteriores' => [
                        'estado' => 'Borrador',
                    ],
                    'datos_nuevos' => [
                        'estado' => 'Confirmada',
                    ],
                    'descripcion' =>
                        'Venta confirmada y stock descontado del inventario.',
                ]);

            }, 3);

            return redirect()
                ->route('admin.ventas.show', $venta)
                ->with(
                    'success',
                    "¡Venta {$venta->codigo} confirmada correctamente!"
                );

        } catch (ValidationException $e) {

            return back()->with(
                'error',
                collect($e->errors())->flatten()->first()
            );
        }
    }


    // ========================================
    // ANULAR VENTA
    // ========================================

    public function anular(Venta $venta)
    {
        try {
            DB::transaction(function () use ($venta) {

                // 1. Bloquear la venta.
                $venta = Venta::whereKey($venta->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($venta->estado === 'Anulada') {
                    throw ValidationException::withMessages([
                        'venta' => 'Esta venta ya se encuentra anulada.',
                    ]);
                }

                if (!in_array($venta->estado, [
                    'Borrador',
                    'Confirmada',
                ], true)) {
                    throw ValidationException::withMessages([
                        'venta' => 'El estado de la venta no permite su anulación.',
                    ]);
                }

                $estadoAnterior = $venta->estado;

                // 2. Si está confirmada, devolver existencias.
                if ($estadoAnterior === 'Confirmada') {

                    $detalles = $venta->detalles()
                        ->orderBy('id')
                        ->get();

                    if ($detalles->isEmpty()) {
                        throw ValidationException::withMessages([
                            'venta' => 'La venta no tiene detalles para devolver al inventario.',
                        ]);
                    }

                    if ($detalles->contains(fn ($detalle) =>
                        !$detalle->lote_id || $detalle->cantidad <= 0
                    )) {
                        throw ValidationException::withMessages([
                            'venta' => 'La venta contiene detalles inválidos.',
                        ]);
                    }

                    // Bloquear los lotes en orden consistente.
                    $lotes = Lote::withTrashed()
                        ->whereIn(
                            'id',
                            $detalles->pluck('lote_id')->unique()
                        )
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    // Verificar que todos los lotes existan.
                    foreach ($detalles as $detalle) {
                        $lote = $lotes->get($detalle->lote_id);

                        if (
                            !$lote ||
                            (int) $lote->producto_id !== (int) $detalle->producto_id
                        ) {
                            throw ValidationException::withMessages([
                                'venta' => 'No se encontró el lote original de uno de los medicamentos.',
                            ]);
                        }
                    }

                    // ========================================
                    // VALIDAR SALIDAS ORIGINALES DE LA VENTA
                    // ========================================

                    $cantidadesVendidas = $detalles
                        ->groupBy('lote_id')
                        ->map(fn ($items) => $items->sum('cantidad'));

                    foreach ($cantidadesVendidas as $loteId => $cantidadVendida) {

                        $cantidadSalida = MovimientoInventario::query()
                            ->where('lote_id', $loteId)
                            ->where('referencia', $venta->codigo)
                            ->where('tipo', 'Salida')
                            ->sum('cantidad');

                        $cantidadReintegrada = MovimientoInventario::query()
                            ->where('lote_id', $loteId)
                            ->where('referencia', $venta->codigo)
                            ->where('tipo', 'Entrada')
                            ->sum('cantidad');

                        if (
                            (int) $cantidadSalida !== (int) $cantidadVendida ||
                            (int) $cantidadReintegrada !== 0
                        ) {
                            throw ValidationException::withMessages([
                                'venta' =>
                                    'No se puede anular la venta porque los movimientos de inventario no coinciden con las cantidades vendidas.',
                            ]);
                        }
                    }

                    // 3. Devolver stock y registrar movimientos.
                    foreach ($detalles as $detalle) {

                        $lote = $lotes->get($detalle->lote_id);

                        $stockAnterior = $lote->stock_actual;
                        $stockPosterior = $stockAnterior + $detalle->cantidad;

                        $lote->update([
                            'stock_actual' => $stockPosterior,
                        ]);

                        MovimientoInventario::create([
                            'lote_id' => $lote->id,
                            'usuario_id' => Auth::id(),
                            'tipo' => 'Entrada',
                            'cantidad' => $detalle->cantidad,
                            'stock_anterior' => $stockAnterior,
                            'stock_posterior' => $stockPosterior,
                            'referencia' => $venta->codigo,
                            'observacion' => 'Devolución por anulación de venta confirmada.',
                        ]);
                    }
                }

                // 4. Actualizar estado.
                $venta->update([
                    'estado' => 'Anulada',
                ]);

                // 5. Registrar auditoría.
                VentaAuditoria::create([
                    'venta_id' => $venta->id,
                    'usuario_id' => Auth::id(),
                    'accion' => 'ANULACION',
                    'datos_anteriores' => [
                        'estado' => $estadoAnterior,
                    ],
                    'datos_nuevos' => [
                        'estado' => 'Anulada',
                        'stock_reintegrado' =>
                            $estadoAnterior === 'Confirmada',
                    ],
                    'descripcion' =>
                        $estadoAnterior === 'Confirmada'
                            ? 'Venta anulada y existencias reintegradas a sus lotes originales.'
                            : 'Venta en borrador anulada sin modificar inventario.',
                ]);

            }, 3);

            return redirect()
                ->route('admin.ventas.show', $venta)
                ->with(
                    'success',
                    "¡Venta {$venta->codigo} anulada correctamente!"
                );

        } catch (ValidationException $e) {

            return back()->with(
                'error',
                collect($e->errors())->flatten()->first()
            );
        }
    }

}
