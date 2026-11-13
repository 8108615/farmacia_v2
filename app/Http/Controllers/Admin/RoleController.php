<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Mostrar el listado de roles.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->withCount('users')
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where('name', 'like', "%{$buscar}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.roles.index', compact(
            'roles',
            'buscar'
        ));
    }

    /**
     * Mostrar el formulario para crear un nuevo rol.
     */
    public function create()
    {
        // Obtener todos los permisos del sistema.
        $permisos = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permiso) {
                return explode('.', $permiso->name)[0];
            });

        return view('admin.roles.create', compact('permisos'));
    }

    /**
     * Guardar un nuevo rol.
     */
    public function store(Request $request)
    {
        // 1. Normalizar el nombre del rol.
        $request->merge([
            'name' => mb_strtoupper(
                trim((string) $request->input('name', '')),
                'UTF-8'
            ),
        ]);

        // 2. Validar nombre y permisos.
        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web'),
            ],
            'permisos' => [
                'nullable',
                'array',
            ],
            'permisos.*' => [
                'required',
                'string',
                'distinct',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', 'web'),
            ],
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.string' => 'El nombre del rol debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Ya existe un rol registrado con ese nombre.',
            'permisos.array' => 'Los permisos deben enviarse como una lista.',
            'permisos.*.exists' => 'Uno de los permisos seleccionados no existe.',
            'permisos.*.distinct' => 'No se permiten permisos duplicados.',
        ]);

        // 3. Crear el rol y asignar permisos.
        \Illuminate\Support\Facades\DB::transaction(function () use ($datos) {

            $rol = Role::create([
                'name' => $datos['name'],
                'guard_name' => 'web',
            ]);

            $rol->syncPermissions($datos['permisos'] ?? []);
        });

        // 4. Limpiar caché de permisos.
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        // 5. Regresar al listado.
        return redirect()
            ->route('admin.roles.index')
            ->with('success', '¡Rol y permisos registrados correctamente!');
    }

    /**
     * Mostrar los detalles de un rol.
     */
    public function show(string $id)
    {
        // 1. Buscar el rol con sus usuarios y permisos.
        $rol = Role::query()
            ->where('guard_name', 'web')
            ->with(['users', 'permissions'])
            ->withCount('users')
            ->findOrFail($id);

        // 2. Mostrar la vista con la información del rol.
        return view('admin.roles.show', compact('rol'));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(string $id)
    {
        // Buscar el rol.
        $rol = Role::where('guard_name', 'web')
            ->findOrFail($id);

        // Proteger el rol principal.
        if ($rol->name === 'SUPER ADMIN') {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'El rol SUPER ADMIN está protegido y no puede modificarse.'
                );
        }

        // Obtener todos los permisos agrupados por módulo.
        $permisos = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permiso) {
                return explode('.', $permiso->name)[0];
            });

        // Obtener los permisos asignados al rol.
        $permisosAsignados = $rol->permissions()
            ->pluck('name')
            ->toArray();

        return view('admin.roles.edit', compact(
            'rol',
            'permisos',
            'permisosAsignados'
        ));
    }

    /**
     * Actualizar un rol.
     */
    public function update(Request $request, string $id)
    {
        // 1. Buscar el rol.
        $rol = Role::where('guard_name', 'web')
            ->findOrFail($id);

        // 2. Proteger el rol SUPER ADMIN.
        if ($rol->name === 'SUPER ADMIN') {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'El rol SUPER ADMIN está protegido y no puede modificarse.'
                );
        }

        // 3. Normalizar el nombre.
        $request->merge([
            'name' => mb_strtoupper(
                trim((string) $request->input('name', '')),
                'UTF-8'
            ),
        ]);

        // 4. Validar nombre y permisos.
        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($rol->id),
            ],
            'permisos' => [
                'nullable',
                'array',
            ],
            'permisos.*' => [
                'required',
                'string',
                'distinct',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', 'web'),
            ],
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.string' => 'El nombre del rol debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Ya existe un rol registrado con ese nombre.',
            'permisos.array' => 'Los permisos deben enviarse como una lista.',
            'permisos.*.exists' => 'Uno de los permisos seleccionados no existe.',
            'permisos.*.distinct' => 'No se permiten permisos duplicados.',
        ]);

        // 5. Actualizar el rol y sus permisos.
        \Illuminate\Support\Facades\DB::transaction(function () use ($rol, $datos) {

            $rol->update([
                'name' => $datos['name'],
            ]);

            // Reemplazar permisos anteriores por los seleccionados.
            $rol->syncPermissions($datos['permisos'] ?? []);
        });

        // 6. Limpiar caché de permisos.
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        // 7. Regresar al listado.
        return redirect()
            ->route('admin.roles.index')
            ->with('success', '¡Rol y permisos actualizados correctamente!');
    }

    /**
     * Eliminar un rol.
     */
    public function destroy(string $id)
    {
        // 1. Buscar el rol.
        $rol = Role::where('guard_name', 'web')
            ->withCount('users')
            ->findOrFail($id);

        // 2. Proteger el rol principal del sistema.
        if ($rol->name === 'SUPER ADMIN') {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'El rol SUPER ADMIN está protegido y no puede eliminarse.'
                );
        }

        // 3. Verificar si tiene usuarios asignados.
        if ($rol->users_count > 0) {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'No se puede eliminar este rol porque tiene usuarios asignados.'
                );
        }

        // 4. Eliminar el rol.
        $rol->delete();

        // 5. Regresar al listado.
        return redirect()
            ->route('admin.roles.index')
            ->with(
                'success',
                '¡Rol eliminado correctamente!'
            );
    }
}
