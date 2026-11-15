<x-layouts::app title="Registrar Lote">

    <div class="flex flex-col gap-6">

        {{-- ========================================
             ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Registrar Lote
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Registre un nuevo lote de producto en el inventario
                    del Sistema Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.inventario.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver al inventario
            </a>

        </div>

        {{-- ERRORES DE VALIDACIÓN --}}
        @if($errors->any())
            <div class="rounded-xl border border-red-800 bg-red-950/40 p-5 text-sm text-red-300">
                <p class="mb-2 font-semibold">
                    Revise los siguientes errores:
                </p>

                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.inventario.store') }}">

            @csrf

            <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

                {{-- ========================================
                     INFORMACIÓN DEL PRODUCTO
                ======================================== --}}
                <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-5">

                    <h2 class="text-base font-semibold text-white">
                        Información del Producto
                    </h2>

                    <p class="mt-1 text-sm text-zinc-400">
                        Seleccione el producto al que pertenecerá el lote.
                    </p>

                </div>

                <div class="p-6">

                    <label for="producto_id"
                           class="mb-2 block text-sm font-semibold text-white">
                        Producto <span class="text-red-400">*</span>
                    </label>

                    <select
                        name="producto_id"
                        id="producto_id"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none">

                        <option value="">
                            Seleccione un producto
                        </option>

                        @foreach($productos as $producto)
                            <option
                                value="{{ $producto->id }}"
                                @selected(old('producto_id') == $producto->id)>

                                {{ $producto->codigo }} - {{ $producto->nombre }}

                            </option>
                        @endforeach

                    </select>

                    <p class="mt-2 text-xs text-zinc-400">
                        Solamente se muestran productos activos.
                    </p>

                </div>

                {{-- ========================================
                     IDENTIFICACIÓN DEL LOTE
                ======================================== --}}
                <div class="border-y border-zinc-700 bg-zinc-950 px-6 py-5">

                    <h2 class="text-base font-semibold text-white">
                        Identificación del Lote
                    </h2>

                    <p class="mt-1 text-sm text-zinc-400">
                        Datos de identificación y trazabilidad.
                    </p>

                </div>

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                    <div class="md:col-span-2">

                        <label for="numero_lote"
                               class="mb-2 block text-sm font-semibold text-white">
                            Número de Lote <span class="text-red-400">*</span>
                        </label>

                        <input
                            type="text"
                            name="numero_lote"
                            id="numero_lote"
                            value="{{ old('numero_lote') }}"
                            maxlength="100"
                            required
                            placeholder="Ejemplo: LOT-2026-001"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">

                        <p class="mt-2 text-xs text-zinc-400">
                            El número de lote debe ser único para cada producto.
                        </p>

                    </div>

                    <div>

                        <label for="fecha_fabricacion"
                               class="mb-2 block text-sm font-semibold text-white">
                            Fecha de Fabricación
                        </label>

                        <input
                            type="date"
                            name="fecha_fabricacion"
                            id="fecha_fabricacion"
                            value="{{ old('fecha_fabricacion') }}"
                            max="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none">

                    </div>

                    <div>

                        <label for="fecha_vencimiento"
                               class="mb-2 block text-sm font-semibold text-white">
                            Fecha de Vencimiento <span class="text-red-400">*</span>
                        </label>

                        <input
                            type="date"
                            name="fecha_vencimiento"
                            id="fecha_vencimiento"
                            value="{{ old('fecha_vencimiento') }}"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                        >
                    </div>

                </div>

                {{-- ========================================
                     EXISTENCIAS
                ======================================== --}}
                <div class="border-y border-zinc-700 bg-zinc-950 px-6 py-5">

                    <h2 class="text-base font-semibold text-white">
                        Existencias del Lote
                    </h2>

                    <p class="mt-1 text-sm text-zinc-400">
                        Registre la cantidad inicial disponible.
                    </p>

                </div>

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                    <div>

                        <label for="stock_actual"
                               class="mb-2 block text-sm font-semibold text-white">
                            Stock Inicial <span class="text-red-400">*</span>
                        </label>

                        <input
                            type="number"
                            name="stock_actual"
                            id="stock_actual"
                            value="{{ old('stock_actual', 0) }}"
                            min="0"
                            step="1"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none">

                        <p class="mt-2 text-xs text-zinc-400">
                            Ingrese la cantidad inicial en unidades.
                        </p>

                    </div>

                    <div>

                        <label for="estado"
                               class="mb-2 block text-sm font-semibold text-white">
                            Estado del Lote <span class="text-red-400">*</span>
                        </label>

                        <select
                            name="estado"
                            id="estado"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none">

                            <option value="Activo"
                                @selected(old('estado', 'Activo') === 'Activo')>
                                Activo
                            </option>

                            <option value="Inactivo"
                                @selected(old('estado') === 'Inactivo')>
                                Inactivo
                            </option>

                        </select>

                    </div>

                </div>

                {{-- ========================================
                     BOTONES
                ======================================== --}}
                <div class="flex flex-wrap items-center justify-end gap-3 bg-zinc-950 px-6 py-5">

                    <a href="{{ route('admin.inventario.index') }}"
                       class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Guardar Lote
                    </button>

                </div>

            </div>

        </form>

    </div>

</x-layouts::app>
