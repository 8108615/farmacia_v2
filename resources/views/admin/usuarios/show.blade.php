
<x-layouts::app :title="'Ver Usuario'">

    @php
        // Roles y permisos del usuario consultado
        $roles = $usuario->roles;

        $permisos = $usuario->getAllPermissions()
            ->sortBy('name')
            ->groupBy(function ($permiso) {
                return strtoupper(
                    explode('.', $permiso->name)[0]
                );
            });

        $totalPermisos = $usuario->getAllPermissions()->count();

        $eliminado = $usuario->trashed();
    @endphp

    <div class="flex flex-col gap-6">

        {{-- ========================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================= --}}

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalles del Usuario
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información y permisos del usuario en el Sistema Farmacia.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                @can('usuarios.editar')
                    @unless($eliminado)
                        <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                           class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Editar Usuario
                        </a>
                    @endunless
                @endcan

                <a href="{{ route('admin.usuarios.index') }}"
                   class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800">
                    ← Volver al listado
                </a>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN GENERAL --}}
        {{-- ========================================= --}}

        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 p-5">

                <h2 class="text-base font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-xs text-zinc-400">
                    Datos principales del usuario seleccionado.
                </p>

            </div>

            <div class="p-5">

                <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-start">

                    {{-- FOTOGRAFÍA --}}

                    @if ($usuario->foto_perfil)

                        <img
                            src="{{ asset('storage/'.$usuario->foto_perfil) }}"
                            alt="Fotografía de {{ $usuario->name }}"
                            class="h-28 w-28 rounded-xl border border-zinc-700 object-cover"
                        >

                    @else

                        <div class="flex h-28 w-28 items-center justify-center rounded-xl bg-blue-600 text-4xl font-bold text-white">
                            {{ mb_strtoupper(mb_substr($usuario->name, 0, 1)) }}
                        </div>

                    @endif

                    {{-- DATOS PRINCIPALES --}}

                    <div class="flex-1 text-center sm:text-left">

                        <h3 class="text-xl font-bold text-white">
                            {{ $usuario->name }}
                        </h3>

                        <p class="mt-1 break-all text-sm text-zinc-400">
                            {{ $usuario->email }}
                        </p>

                        <div class="mt-4 flex flex-wrap justify-center gap-2 sm:justify-start">

                            {{-- ROLES --}}

                            @forelse($roles as $rol)

                                <span class="rounded-md border border-blue-500/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-400">
                                    {{ $rol->name }}
                                </span>

                            @empty

                                <span class="rounded-md bg-zinc-800 px-3 py-1 text-xs text-zinc-400">
                                    Sin rol asignado
                                </span>

                            @endforelse

                            {{-- ESTADO --}}

                            @if($eliminado)

                                <span class="rounded-md border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">
                                    Eliminado
                                </span>

                            @elseif($usuario->estado === 'Activo')

                                <span class="rounded-md border border-green-500/30 bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">
                                    Activo
                                </span>

                            @else

                                <span class="rounded-md border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-400">
                                    Inactivo
                                </span>

                            @endif

                        </div>

                    </div>

                    {{-- RESUMEN --}}

                    <div class="grid w-full grid-cols-2 gap-3 sm:w-auto">

                        <div class="min-w-28 rounded-lg border border-zinc-700 bg-zinc-800 p-4 text-center">

                            <p class="text-xs text-zinc-400">
                                Roles
                            </p>

                            <p class="mt-2 text-2xl font-bold text-blue-400">
                                {{ $roles->count() }}
                            </p>

                        </div>

                        <div class="min-w-28 rounded-lg border border-zinc-700 bg-zinc-800 p-4 text-center">

                            <p class="text-xs text-zinc-400">
                                Permisos
                            </p>

                            <p class="mt-2 text-2xl font-bold text-emerald-400">
                                {{ $totalPermisos }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN PERSONAL --}}
        {{-- ========================================= --}}

        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 p-5">

                <h2 class="text-base font-semibold text-white">
                    Información Personal
                </h2>

                <p class="mt-1 text-xs text-zinc-400">
                    Datos personales registrados del usuario.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">

                <div>
                    <p class="text-xs text-zinc-400">
                        Nombres y Apellidos
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Tipo de Documento
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->tipo_documento ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Número de Documento
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->numero_documento ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Fecha de Nacimiento
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->fecha_nacimiento
                            ? \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y')
                            : 'No registrada' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Género
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->genero ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Estado del Usuario
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->estado ?: 'No registrado' }}
                    </p>
                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN DE CONTACTO --}}
        {{-- ========================================= --}}

        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 p-5">

                <h2 class="text-base font-semibold text-white">
                    Información de Contacto
                </h2>

                <p class="mt-1 text-xs text-zinc-400">
                    Medios de contacto registrados.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">

                <div>
                    <p class="text-xs text-zinc-400">
                        Correo Electrónico
                    </p>

                    <p class="mt-2 break-all text-sm font-medium text-white">
                        {{ $usuario->email }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Número de Celular
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->celular ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Dirección
                    </p>

                    <p class="mt-2 break-words text-sm font-medium text-white">
                        {{ $usuario->direccion ?: 'No registrada' }}
                    </p>
                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- PERMISOS DEL USUARIO --}}
        {{-- ========================================= --}}

        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-700 p-5">

                <div>
                    <h2 class="text-base font-semibold text-white">
                        Permisos del Usuario
                    </h2>

                    <p class="mt-1 text-xs text-zinc-400">
                        Permisos asignados mediante sus roles o directamente al usuario.
                    </p>
                </div>

                <span class="rounded-lg border border-blue-500/40 bg-blue-500/10 px-3 py-1.5 text-xs font-semibold text-blue-400">
                    {{ $totalPermisos }} permisos
                </span>

            </div>

            <div class="p-5">

                @if($permisos->isNotEmpty())

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">

                        @foreach($permisos as $modulo => $permisosModulo)

                            <div class="overflow-hidden rounded-lg border border-zinc-700 bg-zinc-950">

                                <div class="flex items-center justify-between border-b border-zinc-700 bg-zinc-800 px-4 py-3">

                                    <h3 class="text-xs font-bold text-white">
                                        {{ $modulo }}
                                    </h3>

                                    <span class="rounded bg-blue-500/10 px-2 py-1 text-xs font-semibold text-blue-400">
                                        {{ $permisosModulo->count() }}
                                    </span>

                                </div>

                                <div class="flex flex-wrap gap-2 p-3">

                                    @foreach($permisosModulo as $permiso)

                                        <span class="inline-flex items-center gap-2 rounded-md border border-zinc-700 bg-zinc-900 px-3 py-1.5 text-xs text-zinc-200">

                                            <span class="font-bold text-emerald-400">
                                                ✓
                                            </span>

                                            {{ ucfirst(
                                                explode('.', $permiso->name)[1] ?? $permiso->name
                                            ) }}

                                        </span>

                                    @endforeach

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-lg border border-dashed border-zinc-700 p-8 text-center">

                        <p class="text-sm text-zinc-400">
                            Este usuario no tiene permisos asignados.
                        </p>

                    </div>

                @endif

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN DEL SISTEMA --}}
        {{-- ========================================= --}}

        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 p-5">

                <h2 class="text-base font-semibold text-white">
                    Información del Sistema
                </h2>

                <p class="mt-1 text-xs text-zinc-400">
                    Información administrativa del registro.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">

                <div>
                    <p class="text-xs text-zinc-400">
                        Código de Usuario
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        #{{ $usuario->id }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Fecha de Registro
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->created_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">
                        Última Actualización
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $usuario->updated_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                    </p>
                </div>

                @if($eliminado)

                    <div>
                        <p class="text-xs text-zinc-400">
                            Fecha de Eliminación
                        </p>

                        <p class="mt-2 text-sm font-medium text-red-400">
                            {{ $usuario->deleted_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                        </p>
                    </div>

                @endif

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- BOTONES INFERIORES --}}
        {{-- ========================================= --}}

        <div class="flex flex-wrap justify-end gap-3">

            <a href="{{ route('admin.usuarios.index') }}"
               class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                Volver al listado
            </a>

            @can('usuarios.editar')
                @unless($eliminado)

                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                       class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        Editar Usuario
                    </a>

                @endunless
            @endcan

        </div>

    </div>

</x-layouts::app>
