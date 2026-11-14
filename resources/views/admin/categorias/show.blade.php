<x-layouts::app title="Detalle de Categoría">

    <div class="flex flex-col gap-6">

        {{-- ========================================
             ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalle de Categoría
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información completa de la categoría registrada en el Sistema Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.categorias.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>

        </div>

        {{-- ========================================
             INFORMACIÓN DE LA CATEGORÍA
        ======================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-5">
                <h2 class="text-base font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Datos principales de la categoría seleccionada.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                {{-- Código --}}
                <div>
                    <p class="text-xs text-zinc-400">
                        Código de Categoría
                    </p>

                    <p class="mt-2 text-lg font-bold text-blue-400">
                        {{ str_pad($categoria->id, 4, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                {{-- Estado --}}
                <div>
                    <p class="text-xs text-zinc-400">
                        Estado de la Categoría
                    </p>

                    <div class="mt-2">
                        @if($categoria->estado === 'Activo')
                            <span class="inline-flex rounded-full border border-green-800 bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                Activo
                            </span>
                        @else
                            <span class="inline-flex rounded-full border border-red-800 bg-red-950 px-3 py-1 text-xs font-semibold text-red-400">
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Nombre --}}
                <div class="md:col-span-2">
                    <p class="text-xs text-zinc-400">
                        Nombre de la Categoría
                    </p>

                    <p class="mt-2 text-base font-semibold text-white">
                        {{ $categoria->nombre }}
                    </p>
                </div>

                {{-- Descripción --}}
                <div class="md:col-span-2">
                    <p class="text-xs text-zinc-400">
                        Descripción
                    </p>

                    <div class="mt-2 rounded-lg border border-zinc-700 bg-zinc-800 p-4">
                        <p class="whitespace-pre-line break-words text-sm leading-6 text-zinc-200">{{ $categoria->descripcion ?: 'Sin descripción registrada.' }}</p>
                    </div>
                </div>

            </div>

            {{-- ========================================
                 INFORMACIÓN DEL REGISTRO
            ======================================== --}}
            <div class="border-t border-zinc-700 px-6 py-5">

                <h3 class="mb-5 text-sm font-semibold text-white">
                    Información del Registro
                </h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <div>
                        <p class="text-xs text-zinc-400">
                            Fecha de Registro
                        </p>

                        <p class="mt-2 text-sm font-medium text-white">
                            {{ $categoria->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">
                            Última Actualización
                        </p>

                        <p class="mt-2 text-sm font-medium text-white">
                            {{ $categoria->updated_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- ========================================
                 BOTONES DE ACCIÓN
            ======================================== --}}
            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-zinc-700 bg-zinc-950 px-6 py-5">

                <a href="{{ route('admin.categorias.index') }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                    Volver
                </a>

                @can('categorias.editar')
                    <a href="{{ route('admin.categorias.edit', $categoria->id) }}"
                       class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700">
                        Editar Categoría
                    </a>
                @endcan

            </div>

        </div>

    </div>

</x-layouts::app>
