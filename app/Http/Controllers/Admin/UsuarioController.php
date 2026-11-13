<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));

        // Determinar qué usuarios queremos mostrar.
        $filtro = $request->input('filtro', 'activos');

        if (!in_array($filtro, ['activos', 'eliminados'], true)) {
            $filtro = 'activos';
        }

        $usuarios = User::query()
            ->with('roles')

            // Mostrar usuarios eliminados o usuarios activos.
            ->when($filtro === 'eliminados', function ($query) {
                $query->onlyTrashed();
            })

            // Buscar por nombre, correo o documento.
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('name', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%")
                    ->orWhere('numero_documento', 'like', "%{$buscar}%");
                });
            })

            // Ordenar y paginar.
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.usuarios.index',
            compact('usuarios', 'buscar', 'filtro')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('admin.usuarios.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],

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

            'celular' => ['required', 'string', 'max:30'],

            'direccion' => ['required', 'string', 'max:255'],

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

        unset($datos['rol'], $datos['foto_perfil']);

        $usuario = DB::transaction(function () use ($datos, $rol) {
            $usuario = User::create($datos);

            $usuario->assignRole($rol);

            return $usuario;
        });

        if ($request->hasFile('foto_perfil')) {
            $ruta = $request->file('foto_perfil')
                ->store('usuarios', 'public');

            $usuario->update([
                'foto_perfil' => $ruta,
            ]);
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuario = User::with('roles')->findOrFail($id);

        return view('admin.usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $usuario = User::with('roles')->findOrFail($id);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        // Validar la información del usuario.
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],

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
                Rule::unique('users', 'numero_documento')->ignore($usuario->id),
            ],

            'celular' => ['required', 'string', 'max:30'],
            'direccion' => ['required', 'string', 'max:255'],

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

            // La contraseña es opcional al editar.
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

        unset($datos['rol'], $datos['foto_perfil']);

        // Conservar la contraseña si no se modifica.
        if (blank($datos['password'] ?? null)) {
            unset($datos['password']);
        }

        // Evitar quitar accidentalmente el rol SUPER ADMIN.
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
                ->with('error', 'No se puede cambiar el rol del SUPER ADMIN.');
        }

        // Guardar la nueva fotografía si se seleccionó.
        $rutaNueva = null;

        if ($request->hasFile('foto_perfil')) {
            $rutaNueva = $request->file('foto_perfil')
                ->store('usuarios', 'public');

            $datos['foto_perfil'] = $rutaNueva;
        }

        $rutaAnterior = $usuario->foto_perfil;

        try {
            DB::transaction(function () use ($usuario, $datos, $rol) {
                $usuario->update($datos);

                $usuario->syncRoles([$rol]);
            });
        } catch (\Throwable $e) {
            // Eliminar la fotografía nueva si falla el guardado.
            if ($rutaNueva !== null) {
                \Illuminate\Support\Facades\Storage::disk('public')
                    ->delete($rutaNueva);
            }

            throw $e;
        }

        // Eliminar la fotografía anterior solo después
        // de actualizar correctamente la base de datos.
        if ($rutaNueva !== null && $rutaAnterior) {
            \Illuminate\Support\Facades\Storage::disk('public')
                ->delete($rutaAnterior);
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', '¡Usuario actualizado correctamente!');
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        // 1. Buscar el usuario activo.
        $usuario = User::findOrFail($id);

        // 2. Impedir eliminar al usuario que inició sesión.
        if ($usuario->id === auth()->id()) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // 3. Proteger a los usuarios SUPER ADMIN.
        if ($usuario->hasRole('SUPER ADMIN')) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No se puede eliminar un usuario con el rol SUPER ADMIN.');
        }

        // 4. Eliminar lógicamente al usuario.
        $usuario->delete();

        // 5. Regresar al listado con SweetAlert2.
        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', '¡Usuario eliminado correctamente!');
    }

    /**
     * Restaurar un usuario eliminado lógicamente.
     */
    public function restaurar(string $id)
    {
        // Buscar únicamente entre usuarios eliminados.
        $usuario = User::onlyTrashed()->findOrFail($id);

        // Restaurar el usuario.
        $usuario->restore();

        // Regresar al listado de usuarios eliminados.
        return redirect()
            ->route('admin.usuarios.index', [
                'filtro' => 'eliminados'
            ])
            ->with(
                'success',
                '¡Usuario restaurado correctamente!'
            );
    }


}
