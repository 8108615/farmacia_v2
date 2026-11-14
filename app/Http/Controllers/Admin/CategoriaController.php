<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Mostrar el listado de categorías.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $filtro = $request->input('filtro', 'activos');

        if (!in_array($filtro, ['activos', 'eliminados'], true)) {
            $filtro = 'activos';
        }

        $categorias = Categoria::query()
            ->when($filtro === 'eliminados', function ($query) {
                $query->onlyTrashed();
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($consulta) use ($buscar) {
                    $consulta->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categorias.index', compact(
            'categorias',
            'buscar',
            'filtro'
        ));
    }

    /**
     * Mostrar formulario para registrar una categoría.
     */
    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * Guardar una nueva categoría.
     */
    public function store(Request $request)
    {
        $request->merge([
            'nombre' => mb_strtoupper(
                trim((string) $request->input('nombre', '')),
                'UTF-8'
            ),
        ]);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categorias', 'nombre'),
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
            'estado' => [
                'required',
                Rule::in(['Activo', 'Inactivo']),
            ],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con este nombre.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'estado.required' => 'Debe seleccionar un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        Categoria::create($datos);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', '¡Categoría registrada correctamente!');
    }

    /**
     * Mostrar los detalles de una categoría.
     */
    public function show($id)
    {
        $categoria = Categoria::findOrFail($id);

        return view('admin.categorias.show', compact('categoria'));
    }

    /**
     * Mostrar formulario para editar una categoría.
     */
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);

        return view('admin.categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar los datos de una categoría.
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->merge([
            'nombre' => mb_strtoupper(
                trim((string) $request->input('nombre', '')),
                'UTF-8'
            ),
        ]);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categorias', 'nombre')
                    ->ignore($categoria->id),
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
            'estado' => [
                'required',
                Rule::in(['Activo', 'Inactivo']),
            ],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con este nombre.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'estado.required' => 'Debe seleccionar un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        $categoria->update($datos);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', '¡Categoría actualizada correctamente!');
    }

    /**
     * Eliminar lógicamente una categoría.
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', '¡Categoría eliminada correctamente!');
    }

    /**
     * Restaurar una categoría eliminada.
     */
    public function restaurar($id)
    {
        $categoria = Categoria::onlyTrashed()->findOrFail($id);

        $categoria->restore();

        return redirect()
            ->route('admin.categorias.index', [
                'filtro' => 'eliminados',
            ])
            ->with('success', '¡Categoría restaurada correctamente!');
    }
}
