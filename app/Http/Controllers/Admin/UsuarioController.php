<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    /**
     * ============================================
     * LISTADO DE USUARIOS
     * ============================================
     */
    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('usuarios.ver'),
            403,
            'No tienes permisos para consultar usuarios.'
        );

        $buscar = trim((string) $request->input('buscar', ''));

        $filtro = $request->input('filtro', 'activos');

        if (!in_array($filtro, ['activos', 'eliminados'], true)) {
            $filtro = 'activos';
        }

        // Proteger el listado de usuarios eliminados.
        if ($filtro === 'eliminados') {
            abort_unless(
                $request->user()->can('usuarios.restaurar'),
                403,
                'No tienes permisos para consultar usuarios eliminados.'
            );
        }

        $usuarios = User::query()
            ->with('roles')

            ->when($filtro === 'eliminados', function ($query) {
                $query->onlyTrashed();
            })

            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('name', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%")
                        ->orWhere('numero_documento', 'like', "%{$buscar}%");
                });
            })

            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.usuarios.index',
            compact('usuarios', 'buscar', 'filtro')
        );
    }

    /**
     * ============================================
     * FORMULARIO DE CREACIÓN
     * ============================================
     */
    public function create(Request $request)
    {
        abort_unless(
            $request->user()->can('usuarios.crear'),
            403,
            'No tienes permisos para crear usuarios.'
        );

        $roles = Role::query()
            ->where('guard_name', 'web')

            // Solo SUPER ADMIN puede asignar su propio rol.
            ->when(
                ! $request->user()->hasRole('SUPER ADMIN'),
                fn ($query) => $query->where('name', '!=', 'SUPER ADMIN')
            )

            ->orderBy('name')
            ->get();

        return view('admin.usuarios.create', compact('roles'));
    }

    /**
     * ============================================
     * GUARDAR NUEVO USUARIO
     * ============================================
     */
    public function store(Request $request)
    {
        abort_unless(
            $request->user()->can('usuarios.crear'),
            403,
            'No tienes permisos para registrar usuarios.'
        );

        // Validar los datos del formulario.
        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'tipo_documento' => [
                'required',
                Rule::in(['cedula de identidad', 'Pasaporte']),
            ],

            'numero_documento' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'numero_documento'),
            ],

            'celular' => [
                'required',
                'string',
                'max:30',
            ],

            'direccion' => [
                'required',
                'string',
                'max:255',
            ],

            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'genero' => [
                'required',
                Rule::in(['Masculino', 'Femenino']),
            ],

            'estado' => [
                'required',
                Rule::in(['Activo', 'Inactivo']),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'rol' => [
                'required',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web'),
            ],

            'foto_perfil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $rol = $datos['rol'];

        // ============================================
        // PROTECCIÓN DEL ROL SUPER ADMIN
        // ============================================

        if (
            $rol === 'SUPER ADMIN' &&
            ! $request->user()->hasRole('SUPER ADMIN')
        ) {
            abort(
                403,
                'No tienes autorización para asignar el rol SUPER ADMIN.'
            );
        }

        unset($datos['rol'], $datos['foto_perfil']);

        // ============================================
        // GUARDAR FOTOGRAFÍA
        // ============================================

        $rutaNueva = null;

        if ($request->hasFile('foto_perfil')) {
            $rutaNueva = $request->file('foto_perfil')
                ->store('usuarios', 'public');

            $datos['foto_perfil'] = $rutaNueva;
        }

        // ============================================
        // REGISTRAR USUARIO Y ASIGNAR ROL
        // ============================================

        try {
            DB::transaction(function () use ($datos, $rol) {
                $usuario = User::create($datos);

                $usuario->assignRole($rol);
            });
        } catch (\Throwable $e) {

            // Si falla el registro, eliminar la foto nueva.
            if ($rutaNueva !== null) {
                Storage::disk('public')->delete($rutaNueva);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with(
                'success',
                '¡Usuario registrado correctamente!'
            );
    }

    /**
     * ============================================
     * MOSTRAR DETALLES DEL USUARIO
     * ============================================
     */
    public function show(Request $request, string $id)
    {
        abort_unless(
            $request->user()->can('usuarios.ver'),
            403,
            'No tienes permisos para consultar este usuario.'
        );

        $usuario = User::with('roles')->findOrFail($id);

        return view(
            'admin.usuarios.show',
            compact('usuario')
        );
    }

    /**
     * ============================================
     * FORMULARIO DE EDICIÓN
     * ============================================
     */
    public function edit(Request $request, string $id)
    {
        abort_unless(
            $request->user()->can('usuarios.editar'),
            403,
            'No tienes permisos para editar usuarios.'
        );

        $usuario = User::with('roles')->findOrFail($id);

        // Solo un SUPER ADMIN puede editar una cuenta
        // que tenga asignado el rol SUPER ADMIN.
        if (
            $usuario->hasRole('SUPER ADMIN') &&
            ! $request->user()->hasRole('SUPER ADMIN')
        ) {
            abort(
                403,
                'No tienes autorización para modificar una cuenta SUPER ADMIN.'
            );
        }

        $roles = Role::query()
            ->where('guard_name', 'web')

            ->when(
                ! $request->user()->hasRole('SUPER ADMIN'),
                fn ($query) => $query->where('name', '!=', 'SUPER ADMIN')
            )

            ->orderBy('name')
            ->get();

        return view(
            'admin.usuarios.edit',
            compact('usuario', 'roles')
        );
    }

    /**
     * ============================================
     * ACTUALIZAR USUARIO
     * ============================================
     */
    public function update(Request $request, string $id)
    {
        abort_unless(
            $request->user()->can('usuarios.editar'),
            403,
            'No tienes permisos para actualizar usuarios.'
        );

        $usuario = User::with('roles')->findOrFail($id);

        // ============================================
        // PROTEGER CUENTAS SUPER ADMIN
        // ============================================

        if (
            $usuario->hasRole('SUPER ADMIN') &&
            ! $request->user()->hasRole('SUPER ADMIN')
        ) {
            abort(
                403,
                'No tienes autorización para modificar una cuenta SUPER ADMIN.'
            );
        }

        // ============================================
        // VALIDAR DATOS
        // ============================================

        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],

            'tipo_documento' => [
                'required',
                Rule::in(['cedula de identidad', 'Pasaporte']),
            ],

            'numero_documento' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'numero_documento')
                    ->ignore($usuario->id),
            ],

            'celular' => [
                'required',
                'string',
                'max:30',
            ],

            'direccion' => [
                'required',
                'string',
                'max:255',
            ],

            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'genero' => [
                'required',
                Rule::in(['Masculino', 'Femenino']),
            ],

            'estado' => [
                'required',
                Rule::in(['Activo', 'Inactivo']),
            ],

            'rol' => [
                'required',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web'),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'foto_perfil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $rol = $datos['rol'];

        // ============================================
        // CONTROL DE ASIGNACIÓN SUPER ADMIN
        // ============================================

        if (
            $rol === 'SUPER ADMIN' &&
            ! $request->user()->hasRole('SUPER ADMIN')
        ) {
            abort(
                403,
                'No tienes autorización para asignar el rol SUPER ADMIN.'
            );
        }

        // Impedir quitar el rol SUPER ADMIN a una
        // cuenta que ya lo tiene asignado.
        if (
            $usuario->hasRole('SUPER ADMIN') &&
            $rol !== 'SUPER ADMIN'
        ) {
            return back()
                ->withInput($request->except(
                    'password',
                    'password_confirmation',
                    'foto_perfil'
                ))
                ->with(
                    'error',
                    'No se puede cambiar el rol de una cuenta SUPER ADMIN.'
                );
        }

        unset($datos['rol'], $datos['foto_perfil']);

        // ============================================
        // CONTRASEÑA OPCIONAL
        // ============================================

        if (blank($datos['password'] ?? null)) {
            unset($datos['password']);
        }

        // ============================================
        // ACTUALIZAR FOTOGRAFÍA
        // ============================================

        $rutaNueva = null;
        $rutaAnterior = $usuario->foto_perfil;

        if ($request->hasFile('foto_perfil')) {
            $rutaNueva = $request->file('foto_perfil')
                ->store('usuarios', 'public');

            $datos['foto_perfil'] = $rutaNueva;
        }

        // ============================================
        // ACTUALIZAR USUARIO Y ROL
        // ============================================

        try {
            DB::transaction(function () use ($usuario, $datos, $rol) {

                $usuario->update($datos);

                $usuario->syncRoles([$rol]);
            });
        } catch (\Throwable $e) {

            // Eliminar la nueva foto si falla la actualización.
            if ($rutaNueva !== null) {
                Storage::disk('public')->delete($rutaNueva);
            }

            throw $e;
        }

        // Eliminar la foto anterior después de actualizar.
        if ($rutaNueva !== null && $rutaAnterior) {
            Storage::disk('public')->delete($rutaAnterior);
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with(
                'success',
                '¡Usuario actualizado correctamente!'
            );
    }

    /**
     * ============================================
     * ELIMINAR USUARIO (SOFT DELETE)
     * ============================================
     */
    public function destroy(Request $request, string $id)
    {
        abort_unless(
            $request->user()->can('usuarios.eliminar'),
            403,
            'No tienes permisos para eliminar usuarios.'
        );

        $usuario = User::findOrFail($id);

        // Impedir eliminar la propia cuenta.
        if ($usuario->id === $request->user()->id) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with(
                    'error',
                    'No puedes eliminar tu propia cuenta.'
                );
        }

        // Proteger cuentas SUPER ADMIN.
        if ($usuario->hasRole('SUPER ADMIN')) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with(
                    'error',
                    'No se puede eliminar una cuenta SUPER ADMIN.'
                );
        }

        // Eliminación lógica.
        $usuario->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with(
                'success',
                '¡Usuario eliminado correctamente!'
            );
    }

    /**
     * ============================================
     * RESTAURAR USUARIO ELIMINADO
     * ============================================
     */
    public function restaurar(Request $request, string $id)
    {
        abort_unless(
            $request->user()->can('usuarios.restaurar'),
            403,
            'No tienes permisos para restaurar usuarios.'
        );

        $usuario = User::onlyTrashed()->findOrFail($id);

        $usuario->restore();

        return redirect()
            ->route('admin.usuarios.index', [
                'filtro' => 'eliminados',
            ])
            ->with(
                'success',
                '¡Usuario restaurado correctamente!'
            );
    }
}
