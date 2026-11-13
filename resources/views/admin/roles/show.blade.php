
<x-layouts::app :title="'Detalle del Rol'">

    <div class="mx-auto w-full max-w-5xl space-y-6">

        <!-- Encabezado -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalles del Rol
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información y usuarios asignados al rol
                </p>
            </div>

            <a href="{{ route('admin.roles.index') }}"
               class="rounded-lg border border-zinc-700 px-4 py-2 text-center text-sm font-medium text-white transition hover:bg-zinc-800">
                Volver al listado
            </a>

        </div>

        <!-- Información general -->
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 px-6 py-5">
                <h2 class="font-semibold text-white">
                    Información General
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-400">
                        Nombre del Rol
                    </p>

                    <p class="mt-2 text-lg font-semibold text-blue-400">
                        {{ $rol->name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-400">
                        Usuarios Asignados
                    </p>

                    <p class="mt-2 text-lg font-semibold text-white">
                        {{ $rol->users_count }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-400">
                        Fecha de Creación
                    </p>

                    <p class="mt-2 text-sm text-white">
                        {{ $rol->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-zinc-400">
                        Estado del Rol
                    </p>

                    <div class="mt-2">
                        @if ($rol->name === 'SUPER ADMIN')
                            <span class="rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-400">
                                Rol protegido
                            </span>
                        @else
                            <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-400">
                                Registrado
                            </span>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        <!-- Usuarios asignados -->
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 px-6 py-5">
                <h2 class="font-semibold text-white">
                    Usuarios Asignados
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Usuarios que tienen asignado este rol.
                </p>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-6 py-4">NRO</th>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Correo Electrónico</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($rol->users as $usuario)

                            <tr>
                                <td class="px-6 py-4 text-zinc-400">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-4 font-medium text-white">
                                    {{ $usuario->name }}
                                </td>

                                <td class="px-6 py-4 text-zinc-300">
                                    {{ $usuario->email }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="3"
                                    class="px-6 py-8 text-center text-zinc-400">
                                    Este rol todavía no tiene usuarios asignados.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Permisos -->
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 px-6 py-5">
                <h2 class="font-semibold text-white">
                    Permisos del Rol
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Permisos asignados directamente a este rol.
                </p>
            </div>

            <div class="flex flex-wrap gap-2 p-6">

                @forelse ($rol->permissions as $permiso)

                    <span class="rounded-lg border border-blue-800 bg-blue-950/30 px-3 py-2 text-xs font-medium text-blue-400">
                        {{ $permiso->name }}
                    </span>

                @empty

                    <p class="text-sm text-zinc-400">
                        Este rol todavía no tiene permisos asignados.
                    </p>

                @endforelse

            </div>

        </div>

    </div>

</x-layouts::app>
