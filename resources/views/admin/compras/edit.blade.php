<x-layouts::app :title="'Editar Compra - Farmacia'">

    @php
        $detallesIniciales = old(
            'detalles',
            $compra->detalles->map(fn ($detalle) => [
                'producto_id' => $detalle->producto_id,
                'numero_lote' => $detalle->numero_lote,
                'fecha_fabricacion' => $detalle->fecha_fabricacion?->format('Y-m-d'),
                'fecha_vencimiento' => $detalle->fecha_vencimiento?->format('Y-m-d'),
                'cantidad' => $detalle->cantidad,
                'precio_compra' => $detalle->precio_compra,
            ])->toArray()
        );
    @endphp

    <div class="w-full space-y-6 p-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Compra
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Modifica la información de la compra
                    {{ $compra->codigo }}.
                </p>
            </div>

            <a href="{{ route('admin.compras.index') }}"
               class="rounded-lg border border-zinc-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800">
                Volver al listado
            </a>
        </div>

        {{-- ERRORES --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-800 bg-red-950/40 p-5">
                <h3 class="mb-3 font-semibold text-red-400">
                    Corrige los siguientes errores:
                </h3>

                <ul class="list-inside list-disc space-y-1 text-sm text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            id="form-compra"
            action="{{ route('admin.compras.update', $compra) }}"
            method="POST"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- INFORMACIÓN GENERAL --}}
            <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-6">

                <h2 class="mb-6 text-lg font-semibold text-white">
                    Información de la Compra
                </h2>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm font-medium text-zinc-300">
                            Proveedor *
                        </label>

                        <select
                            name="proveedor_id"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">Seleccione un proveedor</option>

                            @foreach ($proveedores as $proveedor)
                                <option
                                    value="{{ $proveedor->id }}"
                                    @selected(old('proveedor_id', $compra->proveedor_id) == $proveedor->id)
                                >
                                    {{ $proveedor->razon_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-zinc-300">
                            Fecha de compra *
                        </label>

                        <input
                            type="date"
                            name="fecha_compra"
                            required
                            value="{{ old('fecha_compra', $compra->fecha_compra?->format('Y-m-d')) }}"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-zinc-300">
                            Número de factura
                        </label>

                        <input
                            type="text"
                            name="numero_factura"
                            maxlength="100"
                            value="{{ old('numero_factura', $compra->numero_factura) }}"
                            placeholder="Ej.: FAC-000123"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-zinc-300">
                            Estado actual
                        </label>

                        <div class="rounded-lg border border-amber-800/40 bg-amber-500/10 px-4 py-3 text-sm font-semibold text-amber-400">
                            {{ $compra->estado }}
                        </div>
                    </div>

                </div>
            </div>

            {{-- DETALLE DE PRODUCTOS --}}
            <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-6">

                <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">
                            Detalle de Productos
                        </h2>
                        <p class="mt-1 text-sm text-zinc-400">
                            Modifica los productos, lotes, cantidades y precios.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="agregar-producto"
                        class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700"
                    >
                        + Agregar Producto
                    </button>
                </div>

                <div id="contenedor-detalles" class="space-y-4">

                    @foreach ($detallesIniciales as $indice => $detalle)

                        <div class="fila-producto rounded-xl border border-zinc-800 bg-zinc-900/60 p-5">

                            <div class="mb-4 flex items-center justify-between gap-4">
                                <h3 class="titulo-producto font-semibold text-zinc-200">
                                    Producto {{ $loop->iteration }}
                                </h3>

                                <button
                                    type="button"
                                    class="eliminar-producto rounded-lg bg-red-900/70 px-3 py-2 text-xs font-semibold text-red-300 hover:bg-red-800"
                                >
                                    Eliminar
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                                <div class="md:col-span-2">
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
                                                {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Número de lote *
                                    </label>

                                    <input
                                        type="text"
                                        name="detalles[{{ $indice }}][numero_lote]"
                                        data-campo="numero_lote"
                                        value="{{ $detalle['numero_lote'] ?? '' }}"
                                        maxlength="100"
                                        required
                                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div>
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

                                <div>
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Fecha de vencimiento *
                                    </label>

                                    <input
                                        type="date"
                                        name="detalles[{{ $indice }}][fecha_vencimiento]"
                                        data-campo="fecha_vencimiento"
                                        value="{{ $detalle['fecha_vencimiento'] ?? '' }}"
                                        required
                                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Cantidad *
                                    </label>

                                    <input
                                        type="number"
                                        name="detalles[{{ $indice }}][cantidad]"
                                        data-campo="cantidad"
                                        value="{{ $detalle['cantidad'] ?? 1 }}"
                                        min="1"
                                        step="1"
                                        required
                                        class="cantidad w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Precio de compra (Bs) *
                                    </label>

                                    <input
                                        type="number"
                                        name="detalles[{{ $indice }}][precio_compra]"
                                        data-campo="precio_compra"
                                        value="{{ $detalle['precio_compra'] ?? 0 }}"
                                        min="0"
                                        step="0.01"
                                        required
                                        class="precio-compra w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm text-zinc-400">
                                        Subtotal (Bs)
                                    </label>

                                    <input
                                        type="text"
                                        readonly
                                        value="0.00"
                                        class="subtotal-producto w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm font-semibold text-emerald-400"
                                    >
                                </div>

                            </div>
                        </div>

                    @endforeach

                </div>
            </div>

            {{-- RESUMEN --}}
            <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-6">

                <h2 class="mb-6 text-lg font-semibold text-white">
                    Resumen de la Compra
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm text-zinc-300">
                            Observaciones
                        </label>

                        <textarea
                            name="observaciones"
                            rows="5"
                            placeholder="Información adicional sobre la compra..."
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                        >{{ old('observaciones', $compra->observaciones) }}</textarea>
                    </div>

                    <div class="rounded-xl border border-zinc-800 bg-zinc-900/60 p-5">

                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-zinc-400">
                                Subtotal
                            </span>
                            <span id="subtotal-general" class="font-semibold text-white">
                                Bs 0,00
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 py-3">
                            <label for="descuento" class="text-sm text-zinc-400">
                                Descuento (Bs)
                            </label>

                            <input
                                type="number"
                                name="descuento"
                                id="descuento"
                                value="{{ old('descuento', $compra->descuento) }}"
                                min="0"
                                step="0.01"
                                class="w-36 rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-right text-sm text-white"
                            >
                        </div>

                        <div class="mt-3 flex items-center justify-between border-t border-zinc-700 pt-4">
                            <span class="font-bold text-white">
                                Total a pagar
                            </span>

                            <span id="total-general" class="text-2xl font-bold text-blue-500">
                                Bs 0,00
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ACCIONES --}}
            <div class="flex flex-wrap justify-end gap-3">

                <a
                    href="{{ route('admin.compras.index') }}"
                    class="rounded-lg border border-zinc-700 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-800"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    form="form-compra"
                    class="inline-flex items-center justify-center
                        rounded-lg bg-emerald-600 px-5 py-3
                        text-sm font-semibold text-white
                        transition-colors duration-200
                        hover:bg-emerald-700
                        focus:outline-none focus:ring-2
                        focus:ring-emerald-500
                        focus:ring-offset-2
                        focus:ring-offset-zinc-900"
                >
                    Actualizar Compra
                </button>

            </div>

        </form>
    </div>

    {{-- PLANTILLA PARA NUEVOS PRODUCTOS --}}
    <template id="plantilla-producto">

        <div class="fila-producto rounded-xl border border-zinc-800 bg-zinc-900/60 p-5">

            <div class="mb-4 flex items-center justify-between gap-4">
                <h3 class="titulo-producto font-semibold text-zinc-200">
                    Nuevo Producto
                </h3>

                <button
                    type="button"
                    class="eliminar-producto rounded-lg bg-red-900/70 px-3 py-2 text-xs font-semibold text-red-300 hover:bg-red-800"
                >
                    Eliminar
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm text-zinc-400">
                        Producto *
                    </label>

                    <select
                        data-campo="producto_id"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                    >
                        <option value="">Seleccione un producto</option>

                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}">
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-400">
                        Número de lote *
                    </label>

                    <input
                        type="text"
                        data-campo="numero_lote"
                        maxlength="100"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-400">
                        Fecha de fabricación
                    </label>

                    <input
                        type="date"
                        data-campo="fecha_fabricacion"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-400">
                        Fecha de vencimiento *
                    </label>

                    <input
                        type="date"
                        data-campo="fecha_vencimiento"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-400">
                        Cantidad *
                    </label>

                    <input
                        type="number"
                        data-campo="cantidad"
                        value="1"
                        min="1"
                        step="1"
                        required
                        class="cantidad w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-400">
                        Precio de compra (Bs) *
                    </label>

                    <input
                        type="number"
                        data-campo="precio_compra"
                        value="0.00"
                        min="0"
                        step="0.01"
                        required
                        class="precio-compra w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-sm text-white"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-400">
                        Subtotal (Bs)
                    </label>

                    <input
                        type="text"
                        readonly
                        value="0.00"
                        class="subtotal-producto w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm font-semibold text-emerald-400"
                    >
                </div>

            </div>
        </div>

    </template>

    {{-- JAVASCRIPT --}}
    <script>
        function inicializarEdicionCompra() {
            const formulario = document.getElementById('form-compra');

            if (!formulario || formulario.dataset.inicializado === '1') {
                return;
            }

            formulario.dataset.inicializado = '1';

            const contenedor = document.getElementById('contenedor-detalles');
            const plantilla = document.getElementById('plantilla-producto');
            const agregar = document.getElementById('agregar-producto');
            const descuento = document.getElementById('descuento');
            const subtotalGeneral = document.getElementById('subtotal-general');
            const totalGeneral = document.getElementById('total-general');
            const guardar = document.getElementById('guardar-compra');

            const formatoBolivianos = (valor) => {
                return 'Bs ' + valor.toLocaleString('es-BO', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            };

            function actualizarNombres() {
                const filas = contenedor.querySelectorAll('.fila-producto');

                filas.forEach((fila, indice) => {
                    fila.querySelector('.titulo-producto').textContent =
                        `Producto ${indice + 1}`;

                    fila.querySelectorAll('[data-campo]').forEach((campo) => {
                        campo.name =
                            `detalles[${indice}][${campo.dataset.campo}]`;
                    });
                });
            }

            function calcularTotales() {
                let subtotalCentavos = 0;

                contenedor.querySelectorAll('.fila-producto').forEach((fila) => {
                    const cantidad = Number(
                        fila.querySelector('.cantidad').value
                    ) || 0;

                    const precio = Number(
                        fila.querySelector('.precio-compra').value
                    ) || 0;

                    const precioCentavos = Math.round(precio * 100);
                    const importeCentavos =
                        Math.max(0, cantidad) * Math.max(0, precioCentavos);

                    subtotalCentavos += importeCentavos;

                    fila.querySelector('.subtotal-producto').value =
                        (importeCentavos / 100).toFixed(2);
                });

                const descuentoCentavos = Math.round(
                    (Number(descuento.value) || 0) * 100
                );

                const totalCentavos = Math.max(
                    0,
                    subtotalCentavos - descuentoCentavos
                );

                subtotalGeneral.textContent =
                    formatoBolivianos(subtotalCentavos / 100);

                totalGeneral.textContent =
                    formatoBolivianos(totalCentavos / 100);
            }

            agregar.addEventListener('click', () => {
                const nuevaFila = plantilla.content.firstElementChild.cloneNode(true);

                contenedor.appendChild(nuevaFila);

                actualizarNombres();
                calcularTotales();
            });

            contenedor.addEventListener('click', (evento) => {
                const boton = evento.target.closest('.eliminar-producto');

                if (!boton) {
                    return;
                }

                const filas = contenedor.querySelectorAll('.fila-producto');

                if (filas.length <= 1) {
                    alert('La compra debe contener al menos un producto.');
                    return;
                }

                boton.closest('.fila-producto').remove();

                actualizarNombres();
                calcularTotales();
            });

            contenedor.addEventListener('input', calcularTotales);
            contenedor.addEventListener('change', calcularTotales);

            descuento.addEventListener('input', calcularTotales);

            formulario.addEventListener('submit', (evento) => {
                if (!formulario.checkValidity()) {
                    return;
                }

                const filas = contenedor.querySelectorAll('.fila-producto');

                if (filas.length === 0) {
                    evento.preventDefault();
                    alert('Agrega al menos un producto.');
                    return;
                }

                guardar.disabled = true;
                guardar.textContent = 'Guardando cambios...';
            });

            actualizarNombres();
            calcularTotales();
        }

        document.addEventListener(
            'DOMContentLoaded',
            inicializarEdicionCompra
        );

        document.addEventListener(
            'livewire:navigated',
            inicializarEdicionCompra
        );
    </script>

</x-layouts::app>
