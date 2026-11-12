
<x-layouts::app :title="'Ver Usuario'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalles del Usuario
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información registrada en el Sistema Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.usuarios.index') }}"
               class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>

        </div>

        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            {{-- PERFIL --}}
            <div class="flex flex-col items-center gap-4 border-b border-zinc-700 pb-6 sm:flex-row">

                @if ($usuario->foto_perfil)
                    <img
                        src="{{ asset('storage/'.$usuario->foto_perfil) }}"
                        alt="Fotografía de perfil"
                        class="h-24 w-24 rounded-xl border border-zinc-700 object-cover"
                    >
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-xl bg-blue-600 text-3xl font-bold text-white">
                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                    </div>
                @endif

                <div class="text-center sm:text-left">

                    <h2 class="text-xl font-semibold text-white">
                        {{ $usuario->name }}
                    </h2>

                    <p class="mt-1 text-sm text-zinc-400">
                        {{ $usuario->email }}
                    </p>

                    <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">

                        @forelse ($usuario->roles as $rol)
                            <span class="rounded-md bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-400">
                                {{ $rol->name }}
                            </span>
                        @empty
                            <span class="text-xs text-zinc-400">
                                Sin rol asignado
                            </span>
                        @endforelse

                        <span class="rounded-md px-3 py-1 text-xs font-semibold
                            {{ $usuario->estado === 'Activo'
                                ? 'bg-green-500/10 text-green-400'
                                : 'bg-red-500/10 text-red-400' }}">
                            {{ $usuario->estado }}
                        </span>

                    </div>
                </div>
            </div>

            {{-- INFORMACIÓN PERSONAL --}}
            <div class="mt-6">

                <h3 class="mb-5 border-b border-zinc-700 pb-3 text-base font-semibold text-white">
                    Información Personal
                </h3>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                    <div>
                        <p class="text-xs text-zinc-400">Nombres y Apellidos</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Tipo de Documento</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->tipo_documento ?? 'No registrado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Número de Documento</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->numero_documento ?? 'No registrado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Fecha de Nacimiento</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->fecha_nacimiento
                                ? \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y')
                                : 'No registrada' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Género</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->genero ?? 'No registrado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Estado</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->estado ?? 'No registrado' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- INFORMACIÓN DE CONTACTO --}}
            <div class="mt-8">

                <h3 class="mb-5 border-b border-zinc-700 pb-3 text-base font-semibold text-white">
                    Información de Contacto
                </h3>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                    <div>
                        <p class="text-xs text-zinc-400">Correo Electrónico</p>
                        <p class="mt-1 break-all text-sm font-medium text-white">
                            {{ $usuario->email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Número de Celular</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->celular ?? 'No registrado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Dirección</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->direccion ?? 'No registrada' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- INFORMACIÓN DEL SISTEMA --}}
            <div class="mt-8">

                <h3 class="mb-5 border-b border-zinc-700 pb-3 text-base font-semibold text-white">
                    Información del Sistema
                </h3>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                    <div>
                        <p class="text-xs text-zinc-400">Código de Usuario</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->id }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Fecha de Registro</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->created_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">Última Actualización</p>
                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $usuario->updated_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- BOTONES --}}
            <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-zinc-700 pt-5">

                <a href="{{ route('admin.usuarios.index') }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                    Volver
                </a>

                {{-- Se activará al implementar Editar --}}
                <button type="button"
                        disabled
                        class="cursor-not-allowed rounded-lg bg-blue-600/50 px-5 py-2.5 text-sm font-medium text-white/60">
                    Editar Usuario
                </button>

            </div>

        </div>

    </div>

</x-layouts::app>
