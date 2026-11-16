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
        // Se implementará junto con la vista de edición.
        abort(501, 'La edición de compras todavía no está habilitada.');
    }

    // ========================================
    // ELIMINACIÓN
    // ========================================

    public function destroy(Compra $compra)
    {
        // No habilitamos eliminación hasta definir
        // las reglas de anulación y auditoría.
        abort(501, 'La eliminación de compras no está habilitada.');
    }
}
