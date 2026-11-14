<x-layouts::app :title="'Detalle del Rol'">

    @php
        // Agrupar los permisos asignados por módulo.
        $permisosAgrupados = $rol->permissions
            ->sortBy('name')
            ->groupBy(function ($permiso) {
                return explode('.', $permiso->name, 2)[0];
            });

        $totalPermisos = $rol->permissions->count();
        $esSuperAdmin = $rol->name === 'SUPER ADMIN';
    @endphp

    <div class="w-full max-w-none space-y-5">

        {{-- ========================================== --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================== --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalles del Rol
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información, permisos y usuarios asignados en el Sistema Farmacia
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                @if (!$esSuperAdmin && auth()->user()->hasRole('SUPER ADMIN'))
                    <a href="{{ route('admin.roles.edit', $rol->id) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        Editar Rol
                    </a>
                @endif

                <a href="{{ route('admin.roles.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-zinc-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800">
                    Volver al listado
                </a>

            </div>

        </div>

        {{-- ========================================== --}}
        {{-- INFORMACIÓN GENERAL --}}
        {{-- ========================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 px-5 py-4">
                <h2 class="text-base font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-xs text-zinc-400">
                    Datos principales del rol seleccionado.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Nombre --}}
                <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-4">
                    <p class="text-xs font-medium text-zinc-400">
                        Nombre del Rol
                    </p>

                    <p class="mt-2 break-words text-base font-bold text-blue-400">
                        {{ $rol->name }}
                    </p>
                </div>

                {{-- Usuarios --}}
                <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-4">
                    <p class="text-xs font-medium text-zinc-400">
                        Usuarios Asignados
                    </p>

                    <p class="mt-2 text-xl font-bold text-white">
                        {{ $rol->users_count }}
                    </p>
                </div>

                {{-- Permisos --}}
                <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-4">
                    <p class="text-xs font-medium text-zinc-400">
                        Permisos Asignados
                    </p>

                    <p class="mt-2 text-xl font-bold text-emerald-400">
                        {{ $totalPermisos }}
                    </p>
                </div>

                {{-- Estado --}}
                <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-4">
                    <p class="text-xs font-medium text-zinc-400">
                        Estado del Rol
                    </p>

                    <div class="mt-3">
                        @if ($esSuperAdmin)
                            <span class="inline-flex rounded-full border border-amber-700/50 bg-amber-950/30 px-3 py-1 text-xs font-semibold text-amber-400">
                                Rol protegido
                            </span>
                        @else
                            <span class="inline-flex rounded-full border border-emerald-700/50 bg-emerald-950/30 px-3 py-1 text-xs font-semibold text-emerald-400">
                                Registrado
                            </span>
                        @endif
                    </div>
                </div>

            </div>

            <div class="flex flex-wrap gap-x-8 gap-y-2 border-t border-zinc-700 px-5 py-3 text-xs text-zinc-400">
                <p>
                    <span class="font-medium text-zinc-300">Fecha de creación:</span>
                    {{ $rol->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                </p>

                <p>
                    <span class="font-medium text-zinc-300">Última actualización:</span>
                    {{ $rol->updated_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                </p>
            </div>

        </div>

        {{-- ========================================== --}}
        {{-- PERMISOS AGRUPADOS --}}
        {{-- ========================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex flex-col gap-3 border-b border-zinc-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-base font-semibold text-white">
                        Permisos del Rol
                    </h2>

                    <p class="mt-1 text-xs text-zinc-400">
                        Funciones autorizadas directamente para este rol.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-lg border border-blue-800 bg-blue-950/30 px-3 py-2 text-xs font-semibold text-blue-400">
                    {{ $totalPermisos }} permisos asignados
                </span>

            </div>

            <div class="p-4 sm:p-5">

                @if ($permisosAgrupados->isNotEmpty())

                    <div class="grid grid-cols-1 items-start gap-3 md:grid-cols-2 xl:grid-cols-3">

                        @foreach ($permisosAgrupados as $modulo => $listaPermisos)

                            <div class="overflow-hidden rounded-lg border border-zinc-700 bg-zinc-900">

                                {{-- Encabezado del módulo --}}
                                <div class="flex items-center justify-between gap-2 border-b border-zinc-700 bg-zinc-800/80 px-3 py-2.5">

                                    <h3 class="text-xs font-bold tracking-wide text-white">
                                        {{ mb_strtoupper($modulo, 'UTF-8') }}
                                    </h3>

                                    <span class="rounded-md bg-blue-950/40 px-2 py-1 text-xs font-semibold text-blue-400">
                                        {{ $listaPermisos->count() }}
                                    </span>

                                </div>

                                {{-- Permisos --}}
                                <div class="grid grid-cols-2 gap-3 p-3 sm:grid-cols-3">

                                    @foreach ($listaPermisos as $permiso)

                                        @php
                                            $accion = explode('.', $permiso->name, 2)[1]
                                                ?? $permiso->name;
                                        @endphp

                                        <div class="flex items-center gap-2 text-xs text-zinc-300">

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 width="16"
                                                 height="16"
                                                 viewBox="0 0 24 24"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 stroke-width="2"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round"
                                                 class="h-4 w-4 shrink-0 text-emerald-400">
                                                <path d="M20 6 9 17l-5-5"/>
                                            </svg>

                                            <span>
                                                {{ ucfirst(str_replace('_', ' ', $accion)) }}
                                            </span>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/40 px-4 py-8 text-center">
                        <p class="text-sm text-zinc-400">
                            Este rol todavía no tiene permisos asignados directamente.
                        </p>
                    </div>

                @endif

            </div>

        </div>

        {{-- ========================================== --}}
        {{-- USUARIOS ASIGNADOS --}}
        {{-- ========================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-700 px-5 py-4">

                <div>
                    <h2 class="text-base font-semibold text-white">
                        Usuarios Asignados
                    </h2>

                    <p class="mt-1 text-xs text-zinc-400">
                        Usuarios que pertenecen a este rol.
                    </p>
                </div>

                <span class="rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-xs font-semibold text-white">
                    Total: {{ $rol->users_count }}
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-800/80 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-5 py-3">NRO</th>
                            <th class="px-5 py-3">Nombre</th>
                            <th class="px-5 py-3">Correo Electrónico</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($rol->users as $usuario)

                            <tr class="transition hover:bg-zinc-800/40">

                                <td class="px-5 py-3 text-zinc-400">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-3 font-medium text-white">
                                    {{ $usuario->name }}
                                </td>

                                <td class="px-5 py-3 text-zinc-300">
                                    {{ $usuario->email }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-zinc-400">
                                    Este rol todavía no tiene usuarios asignados.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-layouts::app>
