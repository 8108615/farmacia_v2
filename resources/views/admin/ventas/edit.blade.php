<x-layouts::app :title="'Editar Venta'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Venta
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Modificando la venta
                    <span class="font-semibold text-blue-400">
                        {{ $venta->codigo }}
                    </span>
                </p>
            </div>

            <a href="{{ route('admin.ventas.show', $venta) }}"
               class="rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                ← Volver al detalle
            </a>
        </div>

        {{-- AVISO --}}
        <div class="rounded-lg border border-blue-800 bg-blue-950/30 p-4 text-sm text-blue-300">
            Esta venta se encuentra en estado Borrador.
            Puedes modificar sus medicamentos, lotes, cantidades y descuentos.
            El inventario no se modificará hasta confirmar la venta.
        </div>

        {{-- ERRORES --}}
        @if ($errors->any())
            <div class="rounded-lg border border-red-800 bg-red-950/30 p-4">
                <p class="mb-2 font-semibold text-red-400">
                    No se pudo actualizar la venta:
                </p>

                <ul class="list-inside list-disc space-y-1 text-sm text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORMULARIO --}}
        <form id="formEditarVenta"
              method="POST"
              action="{{ route('admin.ventas.update', $venta) }}"
              class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            @csrf
            @method('PUT')

            {{-- INFORMACIÓN GENERAL --}}
            <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-4">
                <h2 class="font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-xs text-zinc-400">
                    Datos principales de la venta.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 border-b border-zinc-700 p-6 md:grid-cols-3">

                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-200">
                        Código de Venta
                    </label>

                    <input type="text"
                           value="{{ $venta->codigo }}"
                           readonly
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm font-semibold text-blue-400">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-200">
                        Fecha de Venta
                    </label>

                    <input type="text"
                           value="{{ $venta->fecha_venta?->format('d/m/Y H:i') }}"
                           readonly
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-zinc-300">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-200">
                        Estado
                    </label>

                    <div class="flex h-[46px] items-center rounded-lg border border-yellow-800 bg-yellow-950/20 px-4">
                        <span class="rounded-full bg-yellow-900/50 px-3 py-1 text-xs font-semibold text-yellow-400">
                            {{ $venta->estado }}
                        </span>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label for="observacion"
                           class="mb-2 block text-sm font-medium text-zinc-200">
                        Observaciones
                    </label>

                    <textarea id="observacion"
                              name="observacion"
                              rows="3"
                              maxlength="1000"
                              placeholder="Observaciones adicionales (opcional)"
                              class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none focus:border-blue-500">{{ old('observacion', $venta->observacion) }}</textarea>
                </div>
            </div>

            {{-- DETALLES --}}
            <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-white">
                            Medicamentos de la Venta
                        </h2>

                        <p class="mt-1 text-xs text-zinc-400">
                            Modifica los medicamentos, lotes y cantidades registrados.
                        </p>
                    </div>

                    <button type="button"
                            id="btnAgregarProductoEditar"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        + Agregar Medicamento
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div id="contenedorDetallesEditar" class="space-y-4"></div>

                <div id="mensajeSinProductosEditar"
                     class="hidden rounded-lg border border-dashed border-zinc-700 p-8 text-center text-sm text-zinc-400">
                    No hay medicamentos registrados.
                </div>
            </div>

            {{-- RESUMEN --}}
            <div class="border-t border-zinc-700 bg-zinc-950 px-6 py-5">
                <h2 class="mb-4 font-semibold text-white">
                    Resumen de la Venta
                </h2>

                <div class="ml-auto max-w-sm space-y-3">

                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-400">Subtotal</span>
                        <span id="resumenSubtotalEditar"
                              class="font-semibold text-white">
                            Bs 0.00
                        </span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-400">Descuentos</span>
                        <span id="resumenDescuentoEditar"
                              class="font-semibold text-red-400">
                            Bs 0.00
                        </span>
                    </div>

                    <div class="border-t border-zinc-700 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-white">
                                Total de la Venta
                            </span>

                            <span id="resumenTotalEditar"
                                  class="text-2xl font-bold text-blue-400">
                                Bs 0.00
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACCIONES --}}
            <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-700 p-6">

                <a href="{{ route('admin.ventas.show', $venta) }}"
                   class="rounded-lg border border-zinc-700 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-800">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700">
                    ✓ Actualizar Venta
                </button>
            </div>

        </form>
    </div>

    {{-- PRODUCTOS Y LOTES DISPONIBLES --}}
    @php
        $datosProductosEditar = $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'precio_venta' => $producto->precio_venta,
                'lotes' => $producto->lotes->map(function ($lote) {
                    return [
                        'id' => $lote->id,
                        'numero_lote' => $lote->numero_lote,
                        'stock_actual' => $lote->stock_actual,
                        'fecha_vencimiento' => $lote->fecha_vencimiento?->format('Y-m-d'),
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        $detallesInicialesEditar = old(
            'detalles',
            $venta->detalles->map(function ($detalle) {
                return [
                    'producto_id' => $detalle->producto_id,
                    'lote_id' => $detalle->lote_id,
                    'cantidad' => $detalle->cantidad,
                    'descuento' => $detalle->descuento,
                ];
            })->values()->all()
        );

        $datosSeleccionadosEditar = $venta->detalles->map(function ($detalle) {
            return [
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto?->nombre,
                'producto_codigo' => $detalle->producto?->codigo,
                'lote_id' => $detalle->lote_id,
                'numero_lote' => $detalle->lote?->numero_lote,
                'stock_actual' => $detalle->lote?->stock_actual,
                'fecha_vencimiento' => $detalle->lote?->fecha_vencimiento?->format('Y-m-d'),
                'precio_unitario' => $detalle->precio_unitario,
            ];
        })->values()->all();
    @endphp

    <script type="application/json" id="datosProductosEditar">
        @json($datosProductosEditar)
    </script>

    <script type="application/json" id="detallesInicialesEditar">
        @json($detallesInicialesEditar)
    </script>

    <script type="application/json" id="datosSeleccionadosEditar">
        @json($datosSeleccionadosEditar)
    </script>

    {{-- JAVASCRIPT --}}
    <script>
        (() => {
            const init = () => {
                const contenedor = document.getElementById('contenedorDetallesEditar');
                const botonAgregar = document.getElementById('btnAgregarProductoEditar');
                const mensajeVacio = document.getElementById('mensajeSinProductosEditar');
                const form = document.getElementById('formEditarVenta');

                if (!contenedor || !botonAgregar || !mensajeVacio || !form) {
                    return;
                }

                if (contenedor.dataset.inicializado === '1') {
                    return;
                }

                contenedor.dataset.inicializado = '1';

                const leerJSON = (id) => {
                    try {
                        return JSON.parse(document.getElementById(id).textContent);
                    } catch {
                        return [];
                    }
                };

                const productos = leerJSON('datosProductosEditar');
                const detallesIniciales = leerJSON('detallesInicialesEditar');
                const seleccionados = leerJSON('datosSeleccionadosEditar');

                let siguienteId = 0;

                const claseInput =
                    'w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-3 text-sm text-white outline-none focus:border-blue-500';

                const dinero = (valor) =>
                    'Bs ' + (Number(valor) || 0).toFixed(2);

                function actualizarResumen() {
                    let subtotalCentavos = 0;
                    let descuentoCentavos = 0;

                    const filas = contenedor.querySelectorAll('[data-detalle]');

                    filas.forEach((fila) => {
                        const cantidad = Math.max(
                            0,
                            Math.trunc(Number(fila.querySelector('[data-cantidad]').value) || 0)
                        );

                        const precio = Math.round(
                            Math.max(0, Number(fila.querySelector('[data-precio]').value) || 0) * 100
                        );

                        const descuento = Math.round(
                            Math.max(0, Number(fila.querySelector('[data-descuento]').value) || 0) * 100
                        );

                        const bruto = cantidad * precio;
                        const descuentoAplicado = Math.min(descuento, bruto);

                        subtotalCentavos += bruto;
                        descuentoCentavos += descuentoAplicado;

                        fila.querySelector('[data-subtotal]').textContent =
                            dinero((bruto - descuentoAplicado) / 100);
                    });

                    document.getElementById('resumenSubtotalEditar').textContent =
                        dinero(subtotalCentavos / 100);

                    document.getElementById('resumenDescuentoEditar').textContent =
                        dinero(descuentoCentavos / 100);

                    document.getElementById('resumenTotalEditar').textContent =
                        dinero((subtotalCentavos - descuentoCentavos) / 100);

                    mensajeVacio.classList.toggle('hidden', filas.length > 0);
                }

                function agregarProducto(detalle = null) {
                    const id = siguienteId++;

                    const fila = document.createElement('div');
                    fila.className =
                        'rounded-xl border border-zinc-700 bg-zinc-950/50 p-4';

                    fila.dataset.detalle = id;

                    fila.innerHTML = `
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-white">
                                Medicamento
                            </h3>

                            <button type="button"
                                    data-eliminar
                                    class="rounded-lg bg-red-600/15 px-3 py-2 text-xs font-semibold text-red-400 hover:bg-red-600/25">
                                Eliminar
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Producto
                                </label>

                                <select data-producto
                                        name="detalles[${id}][producto_id]"
                                        required
                                        class="${claseInput}">
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Lote
                                </label>

                                <select data-lote
                                        name="detalles[${id}][lote_id]"
                                        required
                                        class="${claseInput}">
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Cantidad
                                </label>

                                <input data-cantidad
                                       name="detalles[${id}][cantidad]"
                                       type="number"
                                       min="1"
                                       step="1"
                                       value="1"
                                       required
                                       class="${claseInput}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Precio Unitario (Bs)
                                </label>

                                <input data-precio
                                       type="number"
                                       readonly
                                       class="${claseInput} bg-zinc-900">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Descuento (Bs)
                                </label>

                                <input data-descuento
                                       name="detalles[${id}][descuento]"
                                       type="number"
                                       min="0"
                                       step="0.01"
                                       value="0.00"
                                       required
                                       class="${claseInput}">
                            </div>

                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Subtotal
                                </label>

                                <div data-subtotal
                                     class="rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-3 text-sm font-bold text-blue-400">
                                    Bs 0.00
                                </div>
                            </div>
                        </div>

                        <p data-stock class="mt-3 text-xs text-zinc-400"></p>
                    `;

                    const selectProducto = fila.querySelector('[data-producto]');
                    const selectLote = fila.querySelector('[data-lote]');
                    const inputPrecio = fila.querySelector('[data-precio]');
                    const inputCantidad = fila.querySelector('[data-cantidad]');
                    const inputDescuento = fila.querySelector('[data-descuento]');
                    const textoStock = fila.querySelector('[data-stock]');

                    selectProducto.add(new Option('Seleccione un medicamento', ''));

                    productos.forEach((producto) => {
                        selectProducto.add(
                            new Option(
                                `${producto.codigo} - ${producto.nombre}`,
                                producto.id
                            )
                        );
                    });

                    // Mostrar el producto original aunque ya no esté disponible.
                    const productoOriginal = seleccionados.find(
                        (item) => String(item.producto_id) === String(detalle?.producto_id)
                    );

                    if (
                        detalle?.producto_id &&
                        !productos.some(
                            (p) => String(p.id) === String(detalle.producto_id)
                        )
                    ) {
                        selectProducto.add(
                            new Option(
                                `${productoOriginal?.producto_codigo || 'Producto'} - ${productoOriginal?.producto_nombre || 'No disponible'} (no disponible)`,
                                detalle.producto_id
                            )
                        );
                    }

                    function cambiarProducto(loteSeleccionado = null) {
                        const producto = productos.find(
                            (p) => String(p.id) === selectProducto.value
                        );

                        selectLote.replaceChildren(
                            new Option('Seleccione un lote', '')
                        );

                        inputPrecio.value = producto
                            ? Number(producto.precio_venta).toFixed(2)
                            : (productoOriginal?.precio_unitario ?? '');

                        if (producto) {
                            producto.lotes.forEach((lote) => {
                                selectLote.add(
                                    new Option(
                                        `${lote.numero_lote} | Stock: ${lote.stock_actual} | Vence: ${lote.fecha_vencimiento}`,
                                        lote.id
                                    )
                                );
                            });
                        }

                        // Mantener visible el lote original aunque esté agotado
                        // o vencido. El servidor decidirá si permite guardarlo.
                        if (loteSeleccionado) {
                            const existe = [...selectLote.options].some(
                                (opcion) => opcion.value === String(loteSeleccionado)
                            );

                            if (!existe) {
                                const original = seleccionados.find(
                                    (item) =>
                                        String(item.producto_id) === selectProducto.value &&
                                        String(item.lote_id) === String(loteSeleccionado)
                                );

                                selectLote.add(
                                    new Option(
                                        `${original?.numero_lote || 'Lote original'} (no disponible; seleccione otro lote)`,
                                        loteSeleccionado
                                    )
                                );
                            }

                            selectLote.value = String(loteSeleccionado);
                        }

                        cambiarLote();
                    }

                    function cambiarLote() {
                        const producto = productos.find(
                            (p) => String(p.id) === selectProducto.value
                        );

                        const lote = producto?.lotes.find(
                            (l) => String(l.id) === selectLote.value
                        );

                        inputCantidad.max = lote ? lote.stock_actual : '';

                        if (lote) {
                            textoStock.textContent =
                                `Stock disponible: ${lote.stock_actual} unidades. Vencimiento: ${lote.fecha_vencimiento}.`;

                            textoStock.className = 'mt-3 text-xs text-green-400';
                        } else if (selectLote.value) {
                            textoStock.textContent =
                                'El lote seleccionado ya no está disponible. Selecciona un lote válido antes de actualizar.';

                            textoStock.className = 'mt-3 text-xs text-yellow-400';
                        } else {
                            textoStock.textContent = '';
                        }

                        actualizarResumen();
                    }

                    selectProducto.addEventListener('change', () => {
                        cambiarProducto();
                    });

                    selectLote.addEventListener('change', cambiarLote);

                    fila.querySelectorAll('input').forEach((input) => {
                        input.addEventListener('input', actualizarResumen);
                    });

                    fila.querySelector('[data-eliminar]').addEventListener(
                        'click',
                        () => {
                            fila.remove();
                            actualizarResumen();
                        }
                    );

                    contenedor.appendChild(fila);

                    if (detalle) {
                        selectProducto.value = String(detalle.producto_id ?? '');

                        cambiarProducto(detalle.lote_id);

                        inputCantidad.value = detalle.cantidad ?? 1;
                        inputDescuento.value = detalle.descuento ?? '0.00';
                    } else {
                        cambiarProducto();
                    }

                    actualizarResumen();
                }

                botonAgregar.addEventListener('click', () => agregarProducto());

                form.addEventListener('submit', (evento) => {
                    if (!contenedor.querySelector('[data-detalle]')) {
                        evento.preventDefault();
                        alert('Debes registrar al menos un medicamento.');
                        return;
                    }

                    // La validación definitiva se realiza en el controlador.
                });

                const iniciales = Array.isArray(detallesIniciales)
                    ? detallesIniciales
                    : Object.values(detallesIniciales || {});

                iniciales.forEach((detalle) => agregarProducto(detalle));

                actualizarResumen();
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            document.addEventListener('livewire:navigated', init);
        })();
    </script>

</x-layouts::app>
