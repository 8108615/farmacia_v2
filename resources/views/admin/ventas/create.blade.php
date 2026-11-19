<x-layouts::app :title="'Nueva Venta'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Registrar Nueva Venta
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Registra los medicamentos y sus lotes disponibles.
                </p>
            </div>

            <a href="{{ route('admin.ventas.index') }}"
               class="rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form id="formVenta"
              method="POST"
              action="{{ route('admin.ventas.store') }}"
              class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            @csrf

            {{-- INFORMACIÓN GENERAL --}}
            <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-4">
                <h2 class="font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-xs text-zinc-400">
                    Datos principales de la operación.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 border-b border-zinc-700 p-6 md:grid-cols-2">

                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-200">
                        Fecha de Venta
                    </label>

                    <input type="text"
                           value="{{ now()->format('d/m/Y H:i') }}"
                           readonly
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-zinc-300">
                </div>

                <div>
                    <label for="observacion"
                           class="mb-2 block text-sm font-medium text-zinc-200">
                        Observaciones
                    </label>

                    <textarea id="observacion"
                              name="observacion"
                              rows="2"
                              placeholder="Observaciones adicionales (opcional)"
                              class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none focus:border-blue-500">{{ old('observacion') }}</textarea>

                    @error('observacion')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- DETALLE DE PRODUCTOS --}}
            <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-3">

                    <div>
                        <h2 class="font-semibold text-white">
                            Detalle de Productos
                        </h2>

                        <p class="mt-1 text-xs text-zinc-400">
                            Selecciona los medicamentos, lotes y cantidades.
                        </p>
                    </div>

                    <button type="button"
                            id="btnAgregarProducto"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        + Agregar Producto
                    </button>

                </div>
            </div>

            <div class="p-6">

                <div id="contenedorDetalles" class="space-y-4"></div>

                <div id="mensajeSinProductos"
                     class="rounded-lg border border-dashed border-zinc-700 p-8 text-center text-sm text-zinc-400">
                    Todavía no agregaste medicamentos a la venta.
                </div>

                @error('detalles')
                    <p class="mt-3 text-sm text-red-400">{{ $message }}</p>
                @enderror

                @if ($errors->has('detalles.*'))
                    <div class="mt-3 space-y-1">
                        @foreach ($errors->get('detalles.*') as $mensajes)
                            @foreach ($mensajes as $mensaje)
                                <p class="text-sm text-red-400">
                                    {{ $mensaje }}
                                </p>
                            @endforeach
                        @endforeach
                    </div>
                @endif

            </div>

            {{-- RESUMEN DE LA VENTA --}}
            <div class="border-t border-zinc-700 bg-zinc-950 px-6 py-5">

                <h2 class="mb-4 font-semibold text-white">
                    Resumen de la Venta
                </h2>

                <div class="ml-auto max-w-sm space-y-3">

                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-400">Subtotal</span>

                        <span id="resumenSubtotal"
                              class="font-semibold text-white">
                            Bs 0.00
                        </span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-400">Descuentos</span>

                        <span id="resumenDescuento"
                              class="font-semibold text-red-400">
                            Bs 0.00
                        </span>
                    </div>

                    <div class="border-t border-zinc-700 pt-3">
                        <div class="flex items-center justify-between">

                            <span class="font-semibold text-white">
                                Total a Pagar
                            </span>

                            <span id="resumenTotal"
                                  class="text-2xl font-bold text-blue-400">
                                Bs 0.00
                            </span>

                        </div>
                    </div>

                </div>
            </div>

            {{-- ACCIONES --}}
            <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-700 p-6">

                <a href="{{ route('admin.ventas.index') }}"
                   class="rounded-lg border border-zinc-700 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-800">
                    Cancelar
                </a>

               <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Guardar Venta como Borrador
                </button>

            </div>

        </form>

    </div>

    {{-- DATOS DE PRODUCTOS Y LOTES --}}
    @php
        $datosProductosVenta = $productos->map(function ($producto) {
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
                        'fecha_vencimiento' => $lote->fecha_vencimiento
                            ?->format('Y-m-d'),
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    @endphp

    <script type="application/json" id="datosProductosVenta">
        @json($datosProductosVenta)
    </script>

    {{-- JAVASCRIPT --}}
    <script>
        (() => {

            const init = () => {

                const contenedor = document.getElementById('contenedorDetalles');
                const botonAgregar = document.getElementById('btnAgregarProducto');
                const datos = document.getElementById('datosProductosVenta');
                const mensajeVacio = document.getElementById('mensajeSinProductos');

                if (!contenedor || !botonAgregar || !datos || !mensajeVacio) {
                    return;
                }

                if (contenedor.dataset.inicializado === '1') {
                    return;
                }

                contenedor.dataset.inicializado = '1';

                const productos = JSON.parse(datos.textContent);
                let siguienteId = 0;

                const dinero = (valor) => {
                    return 'Bs ' + (Number(valor) || 0).toFixed(2);
                };

                const crearElemento = (tag, clases = '') => {
                    const elemento = document.createElement(tag);
                    elemento.className = clases;
                    return elemento;
                };

                const claseInput =
                    'w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-3 text-sm text-white outline-none focus:border-blue-500';

                // ========================================
                // ACTUALIZAR RESUMEN
                // ========================================

                function actualizarResumen() {

                    let subtotal = 0;
                    let descuento = 0;

                    contenedor.querySelectorAll('[data-detalle]').forEach((fila) => {

                        const cantidad = Math.max(
                            0,
                            Number(fila.querySelector('[data-cantidad]').value) || 0
                        );

                        const precio = Math.max(
                            0,
                            Number(fila.querySelector('[data-precio]').value) || 0
                        );

                        const descuentoFila = Math.max(
                            0,
                            Number(fila.querySelector('[data-descuento]').value) || 0
                        );

                        const bruto = cantidad * precio;
                        const descuentoAplicado = Math.min(descuentoFila, bruto);

                        subtotal += bruto;
                        descuento += descuentoAplicado;

                        fila.querySelector('[data-subtotal]').textContent =
                            dinero(bruto - descuentoAplicado);

                    });

                    document.getElementById('resumenSubtotal').textContent =
                        dinero(subtotal);

                    document.getElementById('resumenDescuento').textContent =
                        dinero(descuento);

                    document.getElementById('resumenTotal').textContent =
                        dinero(Math.max(0, subtotal - descuento));

                    mensajeVacio.classList.toggle(
                        'hidden',
                        contenedor.children.length > 0
                    );
                }

                // ========================================
                // AGREGAR PRODUCTO
                // ========================================

                function agregarProducto() {

                    const id = siguienteId++;

                    const fila = crearElemento(
                        'div',
                        'rounded-xl border border-zinc-700 bg-zinc-950/50 p-4'
                    );

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

                            <!-- PRODUCTO -->
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

                            <!-- LOTE -->
                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Lote disponible
                                </label>

                                <select data-lote
                                        name="detalles[${id}][lote_id]"
                                        required
                                        class="${claseInput}">
                                </select>
                            </div>

                            <!-- CANTIDAD -->
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

                            <!-- PRECIO -->
                            <div>
                                <label class="mb-2 block text-xs text-zinc-300">
                                    Precio Unitario (Bs)
                                </label>

                                <input data-precio
                                       type="number"
                                       readonly
                                       class="${claseInput} bg-zinc-900">
                            </div>

                            <!-- DESCUENTO -->
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

                            <!-- SUBTOTAL -->
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
                    const textoStock = fila.querySelector('[data-stock]');

                    selectProducto.add(
                        new Option('Seleccione un medicamento', '')
                    );

                    productos.forEach((producto) => {

                        selectProducto.add(
                            new Option(
                                `${producto.codigo} - ${producto.nombre}`,
                                producto.id
                            )
                        );

                    });

                    // ========================================
                    // CAMBIAR PRODUCTO
                    // ========================================

                    function cambiarProducto() {

                        const producto = productos.find(
                            (p) => String(p.id) === selectProducto.value
                        );

                        selectLote.replaceChildren(
                            new Option('Seleccione un lote', '')
                        );

                        inputPrecio.value = producto
                            ? Number(producto.precio_venta).toFixed(2)
                            : '';

                        inputCantidad.max = '';
                        textoStock.textContent = '';

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

                        actualizarResumen();
                    }

                    // ========================================
                    // CAMBIAR LOTE
                    // ========================================

                    function cambiarLote() {

                        const producto = productos.find(
                            (p) => String(p.id) === selectProducto.value
                        );

                        const lote = producto?.lotes.find(
                            (l) => String(l.id) === selectLote.value
                        );

                        inputCantidad.max = lote
                            ? lote.stock_actual
                            : '';

                        textoStock.textContent = lote
                            ? `Stock disponible: ${lote.stock_actual} unidades. Vencimiento: ${lote.fecha_vencimiento}.`
                            : '';

                        actualizarResumen();
                    }

                    // ========================================
                    // EVENTOS
                    // ========================================

                    selectProducto.addEventListener(
                        'change',
                        cambiarProducto
                    );

                    selectLote.addEventListener(
                        'change',
                        cambiarLote
                    );

                    fila.querySelectorAll('input').forEach((input) => {

                        input.addEventListener(
                            'input',
                            actualizarResumen
                        );

                    });

                    fila.querySelector('[data-eliminar]').addEventListener(
                        'click',
                        () => {

                            fila.remove();
                            actualizarResumen();

                        }
                    );

                    contenedor.appendChild(fila);
                    actualizarResumen();
                }

                botonAgregar.addEventListener(
                    'click',
                    agregarProducto
                );

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
