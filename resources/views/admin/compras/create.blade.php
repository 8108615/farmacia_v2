<x-layouts::app :title="'Nueva Compra'">

    @php
        $detallesIniciales = old('detalles', [[
            'producto_id' => '',
            'numero_lote' => '',
            'fecha_fabricacion' => '',
            'fecha_vencimiento' => '',
            'cantidad' => 1,
            'precio_compra' => '',
        ]]);
    @endphp

    <div class="w-full max-w-none space-y-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100">
                    Registrar Nueva Compra
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Registra productos adquiridos a proveedores.
                </p>
            </div>

            <a href="{{ route('admin.compras.index') }}"
               class="rounded-lg border border-zinc-700 px-5 py-2.5 text-center text-sm font-semibold text-zinc-300 transition hover:bg-zinc-800">
                Volver al listado
            </a>
        </div>

        {{-- ERRORES DE VALIDACIÓN --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-800 bg-red-950/30 p-5 text-sm text-red-300">
                <p class="mb-2 font-semibold">
                    Revisa los siguientes errores:
                </p>

                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            id="form-compra"
            method="POST"
            action="{{ route('admin.compras.store') }}"
            class="space-y-6"
        >
            @csrf

            {{-- INFORMACIÓN GENERAL --}}
            <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-6">

                <h2 class="mb-6 text-lg font-semibold text-zinc-100">
                    Información de la Compra
                </h2>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="proveedor_id"
                               class="mb-2 block text-sm font-medium text-zinc-300">
                            Proveedor *
                        </label>

                        <select
                            name="proveedor_id"
                            id="proveedor_id"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">Seleccione un proveedor</option>

                            @foreach ($proveedores as $proveedor)
                                <option
                                    value="{{ $proveedor->id }}"
                                    @selected(old('proveedor_id') == $proveedor->id)
                                >
                                    {{ $proveedor->razon_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha_compra"
                               class="mb-2 block text-sm font-medium text-zinc-300">
                            Fecha de compra *
                        </label>

                        <input
                            type="date"
                            name="fecha_compra"
                            id="fecha_compra"
                            required
                            value="{{ old('fecha_compra', now()->toDateString()) }}"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="numero_factura"
                               class="mb-2 block text-sm font-medium text-zinc-300">
                            Número de factura
                        </label>

                        <input
                            type="text"
                            name="numero_factura"
                            id="numero_factura"
                            maxlength="100"
                            value="{{ old('numero_factura') }}"
                            placeholder="Ej.: FAC-000123"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-zinc-300">
                            Estado inicial
                        </label>

                        <div class="rounded-lg border border-amber-800/40 bg-amber-500/10 px-4 py-3 text-sm font-semibold text-amber-400">
                            Borrador
                        </div>
                    </div>

                </div>
            </div>

            {{-- DETALLE DE PRODUCTOS --}}
            <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-6">

                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-100">
                            Detalle de Productos
                        </h2>

                        <p class="mt-1 text-sm text-zinc-400">
                            Registra los productos, lotes y cantidades adquiridas.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="agregar-producto"
                        class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                    >
                        + Agregar Producto
                    </button>
                </div>

                <div id="contenedor-detalles" class="space-y-4">

                    @foreach ($detallesIniciales as $indice => $detalle)
                        <div class="fila-producto rounded-xl border border-zinc-800 bg-zinc-900/60 p-5">

                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="titulo-producto font-semibold text-zinc-200">
                                    Producto {{ $loop->iteration }}
                                </h3>

                                <button
                                    type="button"
                                    class="eliminar-producto rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700"
                                >
                                    Eliminar
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-12">

                                <div class="md:col-span-6">
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Producto *
                                    </label>

                                    <select
                                        name="detalles[{{ $indice }}][producto_id]"
                                        data-campo="producto_id"
                                        required
                                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                        <option value="">Seleccione un producto</option>

                                        @foreach ($productos as $producto)
                                            <option
                                                value="{{ $producto->id }}"
                                                @selected(($detalle['producto_id'] ?? '') == $producto->id)
                                            >
                                                {{ $producto->codigo }} - {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Número de lote *
                                    </label>

                                    <input
                                        type="text"
                                        name="detalles[{{ $indice }}][numero_lote]"
                                        data-campo="numero_lote"
                                        maxlength="100"
                                        required
                                        value="{{ $detalle['numero_lote'] ?? '' }}"
                                        placeholder="Ej.: LOTE-001"
                                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div class="md:col-span-3">
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Fecha de fabricación
                                    </label>

                                    <input
                                        type="date"
                                        name="detalles[{{ $indice }}][fecha_fabricacion]"
                                        data-campo="fecha_fabricacion"
                                        value="{{ $detalle['fecha_fabricacion'] ?? '' }}"
                                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div class="md:col-span-3">
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Fecha de vencimiento *
                                    </label>

                                    <input
                                        type="date"
                                        name="detalles[{{ $indice }}][fecha_vencimiento]"
                                        data-campo="fecha_vencimiento"
                                        required
                                        value="{{ $detalle['fecha_vencimiento'] ?? '' }}"
                                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div class="md:col-span-3">
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Cantidad *
                                    </label>

                                    <input
                                        type="number"
                                        name="detalles[{{ $indice }}][cantidad]"
                                        data-campo="cantidad"
                                        min="1"
                                        step="1"
                                        required
                                        value="{{ $detalle['cantidad'] ?? 1 }}"
                                        class="campo-cantidad w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div class="md:col-span-3">
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Precio de compra (Bs) *
                                    </label>

                                    <input
                                        type="number"
                                        name="detalles[{{ $indice }}][precio_compra]"
                                        data-campo="precio_compra"
                                        min="0"
                                        step="0.01"
                                        required
                                        value="{{ $detalle['precio_compra'] ?? '' }}"
                                        placeholder="0.00"
                                        class="campo-precio w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div class="md:col-span-3">
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Subtotal (Bs)
                                    </label>

                                    <input
                                        type="text"
                                        readonly
                                        value="0.00"
                                        class="campo-subtotal w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm font-semibold text-emerald-400"
                                    >
                                </div>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            {{-- RESUMEN ECONÓMICO --}}
            <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-6">

                <h2 class="mb-6 text-lg font-semibold text-zinc-100">
                    Resumen de la Compra
                </h2>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <div>
                        <label for="observaciones"
                               class="mb-2 block text-sm font-medium text-zinc-300">
                            Observaciones
                        </label>

                        <textarea
                            name="observaciones"
                            id="observaciones"
                            rows="5"
                            placeholder="Información adicional sobre la compra..."
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                        >{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="space-y-4 rounded-xl border border-zinc-800 bg-zinc-900/60 p-5">

                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm text-zinc-400">Subtotal</span>
                            <span id="resumen-subtotal" class="font-semibold text-zinc-100">
                                Bs 0,00
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label for="descuento" class="text-sm text-zinc-400">
                                Descuento (Bs)
                            </label>

                            <input
                                type="number"
                                name="descuento"
                                id="descuento"
                                min="0"
                                step="0.01"
                                value="{{ old('descuento', '0') }}"
                                class="w-40 rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-right text-sm text-white"
                            >
                        </div>

                        <div class="border-t border-zinc-700 pt-4">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-lg font-bold text-zinc-100">
                                    Total a pagar
                                </span>

                                <span id="resumen-total" class="text-2xl font-bold text-blue-400">
                                    Bs 0,00
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ACCIONES --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('admin.compras.index') }}"
                    class="rounded-lg border border-zinc-700 px-6 py-3 text-center text-sm font-semibold text-zinc-300 transition hover:bg-zinc-800"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    id="guardar-compra"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Guardar Compra como Borrador
                </button>

            </div>

        </form>
    </div>

    {{-- PLANTILLA PARA NUEVAS FILAS --}}
    <template id="plantilla-producto">
        <div class="fila-producto rounded-xl border border-zinc-800 bg-zinc-900/60 p-5">

            <div class="mb-4 flex items-center justify-between">
                <h3 class="titulo-producto font-semibold text-zinc-200">
                    Producto
                </h3>

                <button
                    type="button"
                    class="eliminar-producto rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700"
                >
                    Eliminar
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-12">

                <div class="md:col-span-6">
                    <label class="mb-2 block text-sm text-zinc-400">Producto *</label>

                    <select data-campo="producto_id" required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white">
                        <option value="">Seleccione un producto</option>

                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}">
                                {{ $producto->codigo }} - {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm text-zinc-400">Número de lote *</label>
                    <input type="text" data-campo="numero_lote" maxlength="100" required
                           placeholder="Ej.: LOTE-001"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm text-zinc-400">Fecha de fabricación</label>
                    <input type="date" data-campo="fecha_fabricacion"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm text-zinc-400">Fecha de vencimiento *</label>
                    <input type="date" data-campo="fecha_vencimiento" required
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm text-zinc-400">Cantidad *</label>
                    <input type="number" data-campo="cantidad" min="1" step="1" value="1" required
                           class="campo-cantidad w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm text-zinc-400">Precio de compra (Bs) *</label>
                    <input type="number" data-campo="precio_compra" min="0" step="0.01" required
                           placeholder="0.00"
                           class="campo-precio w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-2 block text-sm text-zinc-400">Subtotal (Bs)</label>
                    <input type="text" readonly value="0.00"
                           class="campo-subtotal w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm font-semibold text-emerald-400">
                </div>

            </div>
        </div>
    </template>

    <script>
        function inicializarFormularioCompras() {
            const formulario = document.getElementById('form-compra');

            if (!formulario || formulario.dataset.inicializado === 'si') {
                return;
            }

            formulario.dataset.inicializado = 'si';

            const contenedor = document.getElementById('contenedor-detalles');
            const plantilla = document.getElementById('plantilla-producto');
            const botonAgregar = document.getElementById('agregar-producto');
            const descuento = document.getElementById('descuento');
            const botonGuardar = document.getElementById('guardar-compra');

            const formatoMoneda = new Intl.NumberFormat('es-BO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            function convertirCentavos(valor) {
                const numero = Number(valor);

                if (!Number.isFinite(numero) || numero < 0) {
                    return 0;
                }

                return Math.round((numero + Number.EPSILON) * 100);
            }

            function actualizarTotales() {
                let subtotalCentavos = 0;

                contenedor.querySelectorAll('.fila-producto').forEach(fila => {
                    const cantidad = Number(
                        fila.querySelector('.campo-cantidad').value
                    ) || 0;

                    const precioCentavos = convertirCentavos(
                        fila.querySelector('.campo-precio').value
                    );

                    const importe = Math.max(0, cantidad) * precioCentavos;

                    fila.querySelector('.campo-subtotal').value =
                        (importe / 100).toFixed(2);

                    subtotalCentavos += importe;
                });

                const descuentoCentavos = convertirCentavos(descuento.value);
                const totalCentavos = Math.max(
                    0,
                    subtotalCentavos - descuentoCentavos
                );

                document.getElementById('resumen-subtotal').textContent =
                    'Bs ' + formatoMoneda.format(subtotalCentavos / 100);

                document.getElementById('resumen-total').textContent =
                    'Bs ' + formatoMoneda.format(totalCentavos / 100);

                descuento.setCustomValidity(
                    descuentoCentavos > subtotalCentavos
                        ? 'El descuento no puede superar el subtotal.'
                        : ''
                );
            }

            function renumerarFilas() {
                const filas = contenedor.querySelectorAll('.fila-producto');

                filas.forEach((fila, indice) => {
                    fila.querySelector('.titulo-producto').textContent =
                        'Producto ' + (indice + 1);

                    fila.querySelectorAll('[data-campo]').forEach(campo => {
                        const nombreCampo = campo.dataset.campo;

                        campo.name =
                            `detalles[${indice}][${nombreCampo}]`;
                    });

                    fila.querySelector('.eliminar-producto').disabled =
                        filas.length === 1;

                    fila.querySelector('.eliminar-producto').classList.toggle(
                        'opacity-40',
                        filas.length === 1
                    );
                });

                actualizarTotales();
            }

            botonAgregar.addEventListener('click', () => {
                const nuevaFila = plantilla.content.firstElementChild.cloneNode(true);

                contenedor.appendChild(nuevaFila);
                renumerarFilas();
            });

            contenedor.addEventListener('click', evento => {
                const boton = evento.target.closest('.eliminar-producto');

                if (!boton) {
                    return;
                }

                if (contenedor.querySelectorAll('.fila-producto').length <= 1) {
                    return;
                }

                boton.closest('.fila-producto').remove();
                renumerarFilas();
            });

            contenedor.addEventListener('input', actualizarTotales);
            contenedor.addEventListener('change', actualizarTotales);
            descuento.addEventListener('input', actualizarTotales);

            formulario.addEventListener('submit', evento => {
                actualizarTotales();

                if (!formulario.checkValidity()) {
                    evento.preventDefault();
                    formulario.reportValidity();
                    return;
                }

                botonGuardar.disabled = true;
                botonGuardar.textContent = 'Guardando compra...';
            });

            renumerarFilas();
        }

        document.addEventListener(
            'DOMContentLoaded',
            inicializarFormularioCompras
        );

        document.addEventListener(
            'livewire:navigated',
            inicializarFormularioCompras
        );

        if (document.readyState !== 'loading') {
            inicializarFormularioCompras();
        }
    </script>

</x-layouts::app>
