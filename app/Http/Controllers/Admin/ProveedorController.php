<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    // ========================================
    // LISTADO DE PROVEEDORES
    // ========================================

    public function index(Request $request)
    {
        // ========================================
        // OBTENER FILTROS
        // ========================================

        $buscar = trim((string) $request->input('buscar', ''));

        $estado = $request->input('estado', 'todos');

        $eliminados = $request->input('eliminados', 'no');

        // ========================================
        // CONSULTAR PROVEEDORES
        // ========================================

        $query = Proveedor::query();

        // Mostrar únicamente eliminados cuando se solicite.
        if ($eliminados === 'si') {
            $query->onlyTrashed();
        }

        // ========================================
        // FILTRO DE BÚSQUEDA
        // ========================================

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                    ->orWhere('razon_social', 'like', "%{$buscar}%")
                    ->orWhere('nombre_comercial', 'like', "%{$buscar}%")
                    ->orWhere('nit', 'like', "%{$buscar}%");
            });
        }

        // ========================================
        // FILTRO POR ESTADO
        // ========================================

        if (in_array($estado, ['Activo', 'Inactivo'], true)) {
            $query->where('estado', $estado);
        }

        // ========================================
        // PAGINACIÓN
        // ========================================

        $proveedores = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // ========================================
        // MOSTRAR VISTA
        // ========================================

        return view('admin.proveedores.index', compact(
            'proveedores',
            'buscar',
            'estado',
            'eliminados'
        ));
    }

    // ========================================
    // FORMULARIO DE REGISTRO
    // ========================================

    public function create()
    {
        return view('admin.proveedores.create');
    }

    // ========================================
    // GUARDAR PROVEEDOR
    // ========================================

    public function store(Request $request)
    {
        $datos = $this->validarProveedor($request);

        DB::transaction(function () use ($datos) {

            // Serializar la generación del código.
            // Esta consulta bloquea la fila de la secuencia.
            $secuencia = DB::table('proveedor_secuencias')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            if (!$secuencia) {
                throw new \RuntimeException(
                    'No se encontró la secuencia de proveedores.'
                );
            }

            $numero = $secuencia->ultimo_numero + 1;

            DB::table('proveedor_secuencias')
                ->where('id', 1)
                ->update(['ultimo_numero' => $numero]);

            $datos['codigo'] = 'PROV-' .
                str_pad((string) $numero, 6, '0', STR_PAD_LEFT);

            Proveedor::create($datos);
        });

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', '¡Proveedor registrado correctamente!');
    }

    // ========================================
    // DETALLE DEL PROVEEDOR
    // ========================================

    public function show(Proveedor $proveedor)
    {
        return view('admin.proveedores.show', compact('proveedor'));
    }

    // ========================================
    // FORMULARIO DE EDICIÓN
    // ========================================

    public function edit(Proveedor $proveedor)
    {
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    // ========================================
    // ACTUALIZAR PROVEEDOR
    // ========================================

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $this->validarProveedor($request, $proveedor);

        $proveedor->update($datos);

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', '¡Proveedor actualizado correctamente!');
    }

    // ========================================
    // ELIMINACIÓN LÓGICA
    // ========================================

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', '¡Proveedor eliminado correctamente!');
    }

    // ========================================
    // RESTAURAR PROVEEDOR
    // ========================================

    public function restaurar(int $proveedor)
    {
        $registro = Proveedor::onlyTrashed()
            ->findOrFail($proveedor);

        $registro->restore();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', '¡Proveedor restaurado correctamente!');
    }

    // ========================================
    // VALIDACIONES
    // ========================================

    private function validarProveedor(
        Request $request,
        ?Proveedor $proveedor = null
    ): array {

        $nit = Rule::unique('proveedores', 'nit');

        if ($proveedor) {
            $nit->ignore($proveedor->id);
        }

        return $request->validate([
            'razon_social' => ['required', 'string', 'max:200'],
            'nombre_comercial' => ['nullable', 'string', 'max:200'],
            'nit' => ['nullable', 'string', 'max:30', $nit],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'celular' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);
    }
}
