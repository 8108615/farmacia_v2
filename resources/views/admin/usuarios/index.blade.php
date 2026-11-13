<x-layouts::app :title="'Usuarios'">

    <div class="flex flex-col gap-6">

        <!-- ENCABEZADO -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-3xl font-bold text-white">
                    Gestión de Usuarios
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Administración de usuarios del Sistema Farmacia
                </p>
            </div>

            <a href="{{ route('admin.usuarios.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                <span class="text-lg leading-none">+</span>
                Crear nuevo
            </a>

        </div>

        <!-- PESTAÑAS DE USUARIOS -->
        <div class="flex flex-wrap gap-3">

            <!-- Usuarios activos -->
            <a href="{{ route('admin.usuarios.index', ['filtro' => 'activos']) }}"
               class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition
               {{ $filtro === 'activos'
                   ? 'bg-blue-600 text-white'
                   : 'border border-zinc-700 bg-zinc-900 text-zinc-300 hover:bg-zinc-800' }}">

                <flux:icon.users class="size-4" />

                Usuarios activos
            </a>

            <!-- Usuarios eliminados -->
            <a href="{{ route('admin.usuarios.index', ['filtro' => 'eliminados']) }}"
               class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition
               {{ $filtro === 'eliminados'
                   ? 'bg-red-600 text-white'
                   : 'border border-zinc-700 bg-zinc-900 text-zinc-300 hover:bg-zinc-800' }}">

                <flux:icon.trash class="size-4" />

                Usuarios eliminados
            </a>

        </div>

        <!-- BUSCADOR -->
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

            <form method="GET"
                  action="{{ route('admin.usuarios.index') }}"
                  class="flex flex-col gap-3 sm:flex-row">

                <!-- Mantener filtro seleccionado -->
                <input type="hidden"
                       name="filtro"
                       value="{{ $filtro }}">

                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    placeholder="Buscar por nombre, correo o documento..."
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                >

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Buscar
                </button>

                <a href="{{ route('admin.usuarios.index', ['filtro' => $filtro]) }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2.5 text-center text-sm text-zinc-300 transition hover:bg-zinc-800">
                    Limpiar
                </a>

            </form>

        </div>

        <!-- TABLA DE USUARIOS -->
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <!-- Encabezado de la tabla -->
            <div class="flex items-center justify-between border-b border-zinc-700 px-5 py-4">

                <h2 class="text-lg font-semibold text-white">

                    {{ $filtro === 'eliminados'
                        ? 'Usuarios Eliminados'
                        : 'Listado de Usuarios' }}

                </h2>

                <span class="text-sm text-zinc-400">
                    Total: {{ $usuarios->total() }}
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">

                        <tr>
                            <th class="px-4 py-4 text-center">NRO</th>
                            <th class="px-4 py-4 text-center">FOTO</th>
                            <th class="px-4 py-4">ROL DE USUARIO</th>
                            <th class="px-4 py-4">NOMBRES Y APELLIDOS</th>
                            <th class="px-4 py-4">EMAIL</th>
                            <th class="px-4 py-4">DOCUMENTO</th>
                            <th class="px-4 py-4">GÉNERO</th>
                            <th class="px-4 py-4 text-center">ESTADO</th>
                            <th class="px-4 py-4 text-center">ACCIONES</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($usuarios as $usuario)

                            <tr class="transition hover:bg-zinc-800/60">

                                <!-- NÚMERO -->
                                <td class="px-4 py-4 text-center text-zinc-300">

                                    {{ $usuarios->firstItem() + $loop->index }}

                                </td>

                                <!-- FOTO -->
                                <td class="px-4 py-4">

                                    <div class="flex justify-center">

                                        @if ($usuario->foto_perfil)

                                            <img
                                                src="{{ asset('storage/'.$usuario->foto_perfil) }}"
                                                alt="Foto de {{ $usuario->name }}"
                                                class="h-10 w-10 rounded-full object-cover ring-2 ring-zinc-700"
                                            >

                                        @else

                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 font-semibold text-white">

                                                {{ strtoupper(substr($usuario->name, 0, 1)) }}

                                            </div>

                                        @endif

                                    </div>

                                </td>

                                <!-- ROL -->
                                <td class="px-4 py-4">

                                    @forelse ($usuario->roles as $rol)

                                        <span class="inline-flex rounded-md bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-300">

                                            {{ $rol->name }}

                                        </span>

                                    @empty

                                        <span class="text-xs text-zinc-500">
                                            Sin rol
                                        </span>

                                    @endforelse

                                </td>

                                <!-- NOMBRE -->
                                <td class="px-4 py-4 font-medium text-white">

                                    {{ $usuario->name }}

                                </td>

                                <!-- CORREO -->
                                <td class="px-4 py-4 text-zinc-300">

                                    {{ $usuario->email }}

                                </td>

                                <!-- DOCUMENTO -->
                                <td class="px-4 py-4 text-zinc-300">

                                    {{ $usuario->numero_documento }}

                                </td>

                                <!-- GÉNERO -->
                                <td class="px-4 py-4 text-zinc-300">

                                    {{ $usuario->genero }}

                                </td>

                                <!-- ESTADO -->
                                <td class="px-4 py-4 text-center">

                                    @if ($filtro === 'eliminados')

                                        <span class="inline-flex rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">

                                            Eliminado

                                        </span>

                                    @elseif ($usuario->estado === 'Activo')

                                        <span class="inline-flex rounded-full border border-green-500/30 bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">

                                            Activo

                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">

                                            Inactivo

                                        </span>

                                    @endif

                                </td>

                                <!-- ACCIONES -->
                                <td class="px-4 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        @if ($filtro === 'eliminados')

                                            <!-- RESTAURAR -->
                                            <form
                                                id="form-restaurar-{{ $usuario->id }}"
                                                action="{{ route('admin.usuarios.restaurar', $usuario->id) }}"
                                                method="POST"
                                                class="inline"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="button"
                                                    onclick="confirmarRestaurarUsuario({{ $usuario->id }}, @js($usuario->name))"
                                                    class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-emerald-700"
                                                >

                                                    <flux:icon.arrow-path class="size-4" />

                                                    Restaurar

                                                </button>

                                            </form>

                                        @else

                                            <!-- VER -->
                                            <a href="{{ route('admin.usuarios.show', $usuario->id) }}"
                                               class="inline-flex items-center gap-1.5 rounded-md bg-zinc-700 px-3 py-2 text-xs font-medium text-white transition hover:bg-zinc-600">

                                                <flux:icon.eye class="size-4" />

                                                Ver

                                            </a>

                                            <!-- EDITAR -->
                                            <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                                               class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-emerald-700">

                                                <flux:icon.pencil-square class="size-4" />

                                                Editar

                                            </a>

                                            <!-- ELIMINAR -->
                                            @if ($usuario->hasRole('SUPER ADMIN') || $usuario->id === auth()->id())

                                                <span
                                                    title="Este usuario está protegido"
                                                    class="inline-flex items-center gap-1.5 rounded-md bg-zinc-600 px-3 py-2 text-xs font-semibold text-zinc-300"
                                                >

                                                    <flux:icon.shield-check class="size-4" />

                                                    Protegido

                                                </span>

                                            @else

                                                <form
                                                    id="form-eliminar-{{ $usuario->id }}"
                                                    action="{{ route('admin.usuarios.destroy', $usuario->id) }}"
                                                    method="POST"
                                                    class="inline"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="button"
                                                        onclick="confirmarEliminarUsuario({{ $usuario->id }}, @js($usuario->name))"
                                                        class="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-red-700"
                                                    >

                                                        <flux:icon.trash class="size-4" />

                                                        Eliminar

                                                    </button>

                                                </form>

                                            @endif

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="px-5 py-12 text-center text-zinc-400">

                                    {{ $filtro === 'eliminados'
                                        ? 'No se encontraron usuarios eliminados.'
                                        : 'No se encontraron usuarios registrados.' }}

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <!-- PAGINACIÓN -->
            <div class="border-t border-zinc-700 px-5 py-4">

                <div class="mb-3 text-sm text-zinc-400">

                    Mostrando

                    {{ $usuarios->firstItem() ?? 0 }}

                    a

                    {{ $usuarios->lastItem() ?? 0 }}

                    de

                    {{ $usuarios->total() }}

                    usuarios

                </div>

                {{ $usuarios->links() }}

            </div>

        </div>

    </div>

</x-layouts::app>
