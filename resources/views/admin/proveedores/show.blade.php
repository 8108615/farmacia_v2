<x-layouts::app :title="'Detalle del Proveedor'">

    <div class="space-y-6">

        {{-- ========================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================= --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalle del Proveedor
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información completa del proveedor registrado en Farmacia.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a href="{{ route('admin.proveedores.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                    ← Volver a Proveedores
                </a>

                @can('proveedores.editar')
                    <a href="{{ route('admin.proveedores.edit', $proveedor) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Editar Proveedor
                    </a>
                @endcan

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- IDENTIFICACIÓN DEL PROVEEDOR --}}
        {{-- ========================================= --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400">
                        Código del Proveedor
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-blue-400">
                        {{ $proveedor->codigo }}
                    </h2>

                    <p class="mt-2 text-lg font-semibold text-white">
                        {{ $proveedor->razon_social }}
                    </p>

                    @if($proveedor->nombre_comercial)
                        <p class="mt-1 text-sm text-zinc-400">
                            {{ $proveedor->nombre_comercial }}
                        </p>
                    @endif
                </div>

                <div>
                    @if($proveedor->estado === 'Activo')
                        <span class="inline-flex rounded-full bg-green-500/15 px-4 py-2 text-sm font-semibold text-green-400">
                            Activo
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-red-500/15 px-4 py-2 text-sm font-semibold text-red-400">
                            Inactivo
                        </span>
                    @endif
                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN EMPRESARIAL --}}
        {{-- ========================================= --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            <h3 class="text-lg font-bold text-white">
                Información Empresarial
            </h3>

            <p class="mt-1 text-sm text-zinc-400">
                Datos principales de identificación del proveedor.
            </p>

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <p class="text-sm text-zinc-400">Razón Social</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->razon_social }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Nombre Comercial</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->nombre_comercial ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">NIT</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->nit ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Estado</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->estado }}
                    </p>
                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN DE CONTACTO --}}
        {{-- ========================================= --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            <h3 class="text-lg font-bold text-white">
                Información de Contacto
            </h3>

            <p class="mt-1 text-sm text-zinc-400">
                Datos para comunicarse con el proveedor.
            </p>

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <p class="text-sm text-zinc-400">Persona de Contacto</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->contacto ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Correo Electrónico</p>
                    <p class="mt-1 break-all font-semibold text-white">
                        {{ $proveedor->email ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Teléfono Fijo</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->telefono ?: 'No registrado' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Celular</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->celular ?: 'No registrado' }}
                    </p>
                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- UBICACIÓN DEL PROVEEDOR --}}
        {{-- ========================================= --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            <h3 class="text-lg font-bold text-white">
                Ubicación del Proveedor
            </h3>

            <p class="mt-1 text-sm text-zinc-400">
                Dirección y ciudad donde opera el proveedor.
            </p>

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <p class="text-sm text-zinc-400">Ciudad</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->ciudad ?: 'No registrada' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Dirección</p>
                    <p class="mt-1 whitespace-pre-line font-semibold text-white">{{ $proveedor->direccion ?: 'No registrada' }}</p>
                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN ADICIONAL --}}
        {{-- ========================================= --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            <h3 class="text-lg font-bold text-white">
                Información Adicional
            </h3>

            <div class="mt-5">

                <p class="text-sm text-zinc-400">
                    Observaciones
                </p>

                <div class="mt-2 rounded-lg border border-zinc-700 bg-zinc-800 p-4 text-sm text-zinc-200">

                    <p class="whitespace-pre-line break-words">{{ $proveedor->observaciones ?: 'Sin observaciones registradas.' }}</p>

                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- INFORMACIÓN DEL REGISTRO --}}
        {{-- ========================================= --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            <h3 class="text-lg font-bold text-white">
                Información del Registro
            </h3>

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <p class="text-sm text-zinc-400">Fecha de Registro</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Última Actualización</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $proveedor->updated_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                    </p>
                </div>

            </div>

        </div>

        {{-- ========================================= --}}
        {{-- BOTONES INFERIORES --}}
        {{-- ========================================= --}}

        <div class="flex flex-wrap justify-end gap-3 pb-6">

            <a href="{{ route('admin.proveedores.index') }}"
               class="rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                Volver
            </a>

            @can('proveedores.editar')
                <a href="{{ route('admin.proveedores.edit', $proveedor) }}"
                   class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Editar Proveedor
                </a>
            @endcan

        </div>

    </div>

</x-layouts::app>
