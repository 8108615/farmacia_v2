<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Mostrar el listado de productos.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));
        $filtro = $request->input('filtro', 'activos');

        if (!in_array($filtro, ['activos', 'eliminados'], true)) {
            $filtro = 'activos';
        }

        $productos = Producto::query()
            ->with('categoria')
            ->when($filtro === 'eliminados', function ($query) {
                $query->onlyTrashed();
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($consulta) use ($buscar) {
                    $consulta
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('codigo_barras', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('nombre_generico', 'like', "%{$buscar}%")
                        ->orWhere('marca', 'like', "%{$buscar}%")
                        ->orWhere('laboratorio', 'like', "%{$buscar}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.productos.index', compact(
            'productos',
            'buscar',
            'filtro'
        ));
    }

    /**
     * Mostrar el formulario de registro.
     */
    public function create()
    {
        $categorias = Categoria::query()
            ->where('estado', 'Activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.productos.create', compact('categorias'));
    }

    /**
     * Registrar un nuevo producto.
     */
    public function store(Request $request)
    {
        $request->merge([
            'nombre' => mb_strtoupper(
                trim((string) $request->input('nombre', '')),
                'UTF-8'
            ),
            'codigo_barras' => $request->filled('codigo_barras')
                ? trim((string) $request->input('codigo_barras'))
                : null,
            'requiere_receta' => $request->boolean('requiere_receta'),
        ]);

        $datos = $request->validate(
            $this->reglasValidacion(),
            $this->mensajesValidacion()
        );

        $datos['requiere_receta'] = $request->boolean('requiere_receta');

        // El código interno se genera automáticamente.
        unset($datos['codigo']);

        // La transacción protege el registro del producto.
        $producto = DB::transaction(function () use ($datos) {

            // Código temporal único que no supera los 20 caracteres.
            $codigoTemporal = 'T-' . bin2hex(random_bytes(8));

            // Registrar inicialmente el producto.
            $producto = Producto::create(array_merge(
                $datos,
                ['codigo' => $codigoTemporal]
            ));

            // Generar el código definitivo utilizando el ID.
            $codigoDefinitivo = 'PROD-' . str_pad(
                (string) $producto->id,
                6,
                '0',
                STR_PAD_LEFT
            );

            // Actualizar el código interno.
            $producto->update([
                'codigo' => $codigoDefinitivo,
            ]);

            return $producto;
        });

        return redirect()
            ->route('admin.productos.index')
            ->with(
                'success',
                '¡Producto registrado correctamente!'
            );
    }

    /**
     * Mostrar detalles de un producto.
     */
    public function show($id)
    {
        $producto = Producto::with('categoria')
            ->findOrFail($id);

        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);

        $categorias = Categoria::query()
            ->where(function ($query) use ($producto) {
                $query->where('estado', 'Activo')
                    ->orWhere('id', $producto->categoria_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('admin.productos.edit', compact(
            'producto',
            'categorias'
        ));
    }

    /**
     * Actualizar un producto.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->merge([
            'nombre' => mb_strtoupper(
                trim((string) $request->input('nombre', '')),
                'UTF-8'
            ),
            'codigo_barras' => $request->filled('codigo_barras')
                ? trim((string) $request->input('codigo_barras'))
                : null,
            'requiere_receta' => $request->boolean('requiere_receta'),
        ]);

        $datos = $request->validate(
            $this->reglasValidacion($producto),
            $this->mensajesValidacion()
        );

        $datos['requiere_receta'] = $request->boolean('requiere_receta');

        // El código interno no se modifica.
        unset($datos['codigo']);

        $producto->update($datos);

        return redirect()
            ->route('admin.productos.index')
            ->with(
                'success',
                '¡Producto actualizado correctamente!'
            );
    }

    /**
     * Eliminar lógicamente un producto.
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        $producto->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with(
                'success',
                '¡Producto eliminado correctamente!'
            );
    }

    /**
     * Restaurar un producto eliminado.
     */
    public function restaurar($id)
    {
        $producto = Producto::onlyTrashed()
            ->findOrFail($id);

        $producto->restore();

        return redirect()
            ->route('admin.productos.index', [
                'filtro' => 'eliminados',
            ])
            ->with(
                'success',
                '¡Producto restaurado correctamente!'
            );
    }

    /**
     * Reglas de validación.
     */
    private function reglasValidacion(?Producto $producto = null): array
    {
        $codigoBarras = Rule::unique(
            'productos',
            'codigo_barras'
        );

        if ($producto) {
            $codigoBarras->ignore($producto->id);
        }

        return [
            'categoria_id' => [
                'required',
                'integer',
                Rule::exists('categorias', 'id')
                    ->where('estado', 'Activo')
                    ->whereNull('deleted_at'),
            ],
            'codigo_barras' => [
                'nullable',
                'string',
                'max:100',
                $codigoBarras,
            ],
            'nombre' => [
                'required',
                'string',
                'max:200',
            ],
            'nombre_generico' => [
                'nullable',
                'string',
                'max:200',
            ],
            'marca' => [
                'nullable',
                'string',
                'max:150',
            ],
            'laboratorio' => [
                'nullable',
                'string',
                'max:150',
            ],
            'presentacion' => [
                'nullable',
                'string',
                'max:100',
            ],
            'concentracion' => [
                'nullable',
                'string',
                'max:100',
            ],

            'precio_venta' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
                'max:9999999999.99',
            ],
            'registro_sanitario' => [
                'nullable',
                'string',
                'max:100',
            ],
            'requiere_receta' => [
                'required',
                'boolean',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
            'estado' => [
                'required',
                Rule::in(['Activo', 'Inactivo']),
            ],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     */
    private function mensajesValidacion(): array
    {
        return [
            'categoria_id.required' =>
                'Debe seleccionar una categoría.',
            'categoria_id.exists' =>
                'La categoría seleccionada no está disponible.',
            'nombre.required' =>
                'El nombre del producto es obligatorio.',
            'nombre.max' =>
                'El nombre no puede superar los 200 caracteres.',
            'codigo_barras.unique' =>
                'Ya existe un producto con este código de barras.',
            'estado.required' =>
                'Debe seleccionar un estado.',
            'estado.in' =>
                'El estado seleccionado no es válido.',

            'precio_venta.required' =>
                'Debe ingresar el precio de venta del medicamento.',

            'precio_venta.numeric' =>
                'El precio de venta debe ser un número válido.',

            'precio_venta.gt' =>
                'El precio de venta debe ser mayor a cero.',

            'precio_venta.decimal' =>
                'El precio de venta debe tener como máximo dos decimales.',

            'precio_venta.max' =>
                'El precio de venta supera el monto máximo permitido.',
        ];
    }
}
