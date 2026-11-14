<x-layouts::app :title="'Roles y Permisos'">

    <div class="flex flex-col gap-6">

        {{-- Encabezado --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white">
                    Gestión de Roles y Permisos
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Administración de roles del Sistema Farmacia
                </p>
            </div>

            <a href="{{ route('admin.roles.create') }}"
            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                + Crear nuevo
            </a>
        </div>

        {{-- Buscador --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-4">

            <form method="GET"
                  action="{{ route('admin.roles.index') }}"
                  class="flex flex-wrap gap-3">

                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    placeholder="Buscar rol por nombre..."
                    class="min-w-0 flex-1 rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                >

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                    Buscar
                </button>

                <a href="{{ route('admin.roles.index') }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2 text-sm text-white transition hover:bg-zinc-800">
                    Limpiar
                </a>

            </form>
        </div>

        {{-- Listado --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-700 p-4">

                <h2 class="font-semibold text-white">
                    Listado de Roles
                </h2>

                <span class="text-xs text-zinc-400">
                    Total: {{ $roles->total() }}
                </span>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">
                        <tr>
                            <th class="px-5 py-4">NRO</th>
                            <th class="px-5 py-4">Nombre del Rol</th>
                            <th class="px-5 py-4">Usuarios Asignados</th>
                            <th class="px-5 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($roles as $rol)
                            <tr class="transition hover:bg-zinc-800/50">

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $roles->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-400">
                                        {{ $rol->name }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $rol->users_count }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex justify-center gap-2">

                                        <a href="{{ route('admin.roles.show', $rol->id) }}"
                                            class="inline-flex items-center rounded-md bg-zinc-700 px-3 py-2 text-xs font-medium text-white transition hover:bg-zinc-600">
                                            Ver
                                        </a>

                                        @if ($rol->name === 'SUPER ADMIN')
                                            <span
                                                class="cursor-not-allowed rounded-md bg-zinc-700 px-3 py-2 text-xs text-zinc-400"
                                                title="El rol SUPER ADMIN no puede modificarse">
                                                Protegido
                                            </span>
                                        @else
                                            <a href="{{ route('admin.roles.edit', $rol->id) }}"
                                            class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-emerald-700">
                                                Editar
                                            </a>
                                        @endif

                                        @if ($rol->name === 'SUPER ADMIN')
                                            <span class="rounded-md bg-zinc-700 px-3 py-2 text-xs text-zinc-300">
                                                Protegido
                                            </span>
                                        @else
                                            <form
                                                id="form-eliminar-{{ $rol->id }}"
                                                action="{{ route('admin.roles.destroy', $rol->id) }}"
                                                method="POST"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="button"
                                                    onclick="confirmarEliminarRol(@js($rol->id), @js($rol->name))"
                                                    class="rounded-md bg-red-700 px-3 py-2 text-xs font-medium text-white transition hover:bg-red-600"
                                                >
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-5 py-10 text-center text-zinc-400">
                                    No se encontraron roles registrados.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="border-t border-zinc-700 p-4">

                <div class="mb-3 text-xs text-zinc-400">
                    Mostrando {{ $roles->firstItem() ?? 0 }}
                    a {{ $roles->lastItem() ?? 0 }}
                    de {{ $roles->total() }} roles
                </div>

                {{ $roles->links() }}

            </div>

        </div>

    </div>


    <script>
        function confirmarEliminarRol(id, nombre) {
            Swal.fire({
                title: '¿Eliminar este rol?',
                text: 'Se eliminará permanentemente el rol: ' + nombre,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#52525b',
                background: '#18181b',
                color: '#ffffff',
                reverseButtons: true
            }).then((resultado) => {
                if (resultado.isConfirmed) {
                    document.getElementById(
                        'form-eliminar-' + id
                    ).submit();
                }
            });
        }
    </script>


</x-layouts::app>
