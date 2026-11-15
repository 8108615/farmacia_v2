<x-layouts::app title="Detalle de Producto">

    <div class="flex flex-col gap-6">

        {{-- ========================================
             ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalle de Producto
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información completa del producto registrado en el Sistema Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.productos.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>

        </div>

        {{-- ========================================
             INFORMACIÓN DEL PRODUCTO
        ======================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            {{-- INFORMACIÓN GENERAL --}}
            <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-5">
                <h2 class="text-base font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Datos principales para identificar el producto.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                {{-- Código interno --}}
                <div>
                    <p class="text-xs text-zinc-400">Código Interno</p>

                    <p class="mt-2 text-lg font-bold text-blue-400">
                        {{ $producto->codigo }}
                    </p>
                </div>

                {{-- Categoría --}}
                <div>
                    <p class="text-xs text-zinc-400">Categoría</p>

                    <p class="mt-2 text-sm font-semibold text-white">
                        {{ $producto->categoria?->nombre ?? 'Sin categoría disponible' }}
                    </p>
                </div>

                {{-- Nombre --}}
                <div>
                    <p class="text-xs text-zinc-400">Nombre del Producto</p>

                    <p class="mt-2 text-base font-semibold text-white">
                        {{ $producto->nombre }}
                    </p>
                </div>

                {{-- Nombre genérico --}}
                <div>
                    <p class="text-xs text-zinc-400">Nombre Genérico</p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $producto->nombre_generico ?: 'No registrado' }}
                    </p>
                </div>

                {{-- Marca --}}
                <div>
                    <p class="text-xs text-zinc-400">Marca Comercial</p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $producto->marca ?: 'No registrada' }}
                    </p>
                </div>

                {{-- Laboratorio --}}
                <div>
                    <p class="text-xs text-zinc-400">Laboratorio</p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $producto->laboratorio ?: 'No registrado' }}
                    </p>
                </div>

            </div>

            {{-- ========================================
                 INFORMACIÓN FARMACÉUTICA
            ======================================== --}}
            <div class="border-y border-zinc-700 bg-zinc-950 px-6 py-5">

                <h2 class="text-base font-semibold text-white">
                    Información Farmacéutica
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Características y datos sanitarios del medicamento.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                {{-- Presentación --}}
                <div>
                    <p class="text-xs text-zinc-400">Presentación</p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $producto->presentacion ?: 'No registrada' }}
                    </p>
                </div>

                {{-- Concentración --}}
                <div>
                    <p class="text-xs text-zinc-400">Concentración</p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $producto->concentracion ?: 'No registrada' }}
                    </p>
                </div>

                {{-- Registro sanitario --}}
                <div>
                    <p class="text-xs text-zinc-400">Registro Sanitario</p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $producto->registro_sanitario ?: 'No registrado' }}
                    </p>
                </div>

                {{-- Receta médica --}}
                <div>
                    <p class="text-xs text-zinc-400">
                        ¿Requiere Receta Médica?
                    </p>

                    <div class="mt-2">
                        @if($producto->requiere_receta)
                            <span class="inline-flex rounded-full border border-amber-800 bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-400">
                                Sí requiere receta
                            </span>
                        @else
                            <span class="inline-flex rounded-full border border-green-800 bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                No requiere receta
                            </span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ========================================
                 INFORMACIÓN ADICIONAL
            ======================================== --}}
            <div class="border-y border-zinc-700 bg-zinc-950 px-6 py-5">

                <h2 class="text-base font-semibold text-white">
                    Información Adicional
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Datos complementarios del producto.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-6 p-6">

                {{-- Código de barras --}}
                <div>
                    <p class="text-xs text-zinc-400">Código de Barras</p>

                    <p class="mt-2 text-sm font-semibold text-white">
                        {{ $producto->codigo_barras ?: 'No registrado' }}
                    </p>
                </div>

                {{-- Descripción --}}
                <div>
                    <p class="text-xs text-zinc-400">Descripción</p>

                    <div class="mt-2 rounded-lg border border-zinc-700 bg-zinc-800 p-4">
                        <p class="whitespace-pre-line break-words text-sm leading-6 text-zinc-200">{{ $producto->descripcion ?: 'Sin descripción registrada.' }}</p>
                    </div>
                </div>

            </div>

            {{-- ========================================
                 ESTADO DEL PRODUCTO
            ======================================== --}}
            <div class="border-y border-zinc-700 bg-zinc-950 px-6 py-5">

                <h2 class="text-base font-semibold text-white">
                    Estado del Producto
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Disponibilidad administrativa del producto.
                </p>

            </div>

            <div class="p-6">

                <p class="text-xs text-zinc-400">
                    Estado
                </p>

                <div class="mt-2">
                    @if($producto->estado === 'Activo')
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
                            {{ $producto->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">
                            Última Actualización
                        </p>

                        <p class="mt-2 text-sm font-medium text-white">
                            {{ $producto->updated_at?->format('d/m/Y H:i') ?? 'No disponible' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- ========================================
                 BOTONES DE ACCIÓN
            ======================================== --}}
            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-zinc-700 bg-zinc-950 px-6 py-5">

                <a href="{{ route('admin.productos.index') }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                    Volver
                </a>

                @can('productos.editar')
                    <a href="{{ route('admin.productos.edit', $producto->id) }}"
                       class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700">
                        Editar Producto
                    </a>
                @endcan

            </div>

        </div>

    </div>

</x-layouts::app>
