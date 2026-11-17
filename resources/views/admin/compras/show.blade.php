<x-layouts::app :title="'Detalle de Compra'">

    <div class="w-full space-y-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Detalle de Compra
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Información general y productos registrados.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.index') }}"
                   class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                    Volver al listado
                </a>

                @if ($compra->estado === 'Borrador')
                    @can('compras.editar')
                        <a href="{{ route('admin.compras.edit', $compra) }}"
                           class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Editar Compra
                        </a>
                    @endcan
                @endif
            </div>
        </div>

        {{-- INFORMACIÓN GENERAL --}}
        <div class="rounded-xl bg-zinc-950 p-5 shadow-sm">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">
                    Información de la Compra
                </h2>

                @php
                    $colorEstado = match ($compra->estado) {
                        'Confirmada' => 'bg-emerald-950 text-emerald-400',
                        'Anulada' => 'bg-red-950 text-red-400',
                        default => 'bg-amber-950 text-amber-400',
                    };
                @endphp

                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $colorEstado }}">
                    {{ $compra->estado }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                <div>
                    <p class="text-sm text-zinc-400">Código de compra</p>
                    <p class="mt-1 font-semibold text-blue-400">
                        {{ $compra->codigo }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Proveedor</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $compra->proveedor?->razon_social ?? 'No disponible' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Fecha de compra</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $compra->fecha_compra?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Número de factura</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $compra->numero_factura ?: 'Sin factura' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Registrado por</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $compra->usuario?->name ?? 'No disponible' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-400">Fecha de registro</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $compra->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                @if ($compra->fecha_confirmacion)
                    <div>
                        <p class="text-sm text-zinc-400">Fecha de confirmación</p>
                        <p class="mt-1 font-semibold text-white">
                            {{ $compra->fecha_confirmacion->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @endif

            </div>
        </div>

        {{-- DETALLE DE PRODUCTOS --}}
        <div class="overflow-hidden rounded-xl bg-zinc-950 shadow-sm">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-800 p-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">
                        Productos de la Compra
                    </h2>
                    <p class="mt-1 text-sm text-zinc-400">
                        Detalle de lotes, cantidades y precios.
                    </p>
                </div>

                <span class="text-sm text-zinc-400">
                    Total de productos: {{ $compra->detalles->count() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left text-sm">

                    <thead class="bg-zinc-900 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-5 py-4">#</th>
                            <th class="px-5 py-4">Producto</th>
                            <th class="px-5 py-4">Lote</th>
                            <th class="px-5 py-4">Fabricación</th>
                            <th class="px-5 py-4">Vencimiento</th>
                            <th class="px-5 py-4 text-center">Cantidad</th>
                            <th class="px-5 py-4 text-right">Precio (Bs)</th>
                            <th class="px-5 py-4 text-right">Subtotal (Bs)</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">
                        @forelse ($compra->detalles as $detalle)
                            <tr class="text-zinc-200 hover:bg-zinc-900/60">
                                <td class="px-5 py-4">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-white">
                                        {{ $detalle->producto?->nombre ?? 'Producto no disponible' }}
                                    </p>
                                    <p class="text-xs text-zinc-500">
                                        {{ $detalle->producto?->codigo ?? '—' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    {{ $detalle->numero_lote }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $detalle->fecha_fabricacion?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $detalle->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-center font-semibold">
                                    {{ number_format($detalle->cantidad, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right">
                                    {{ number_format((float) $detalle->precio_compra, 2, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right font-semibold text-emerald-400">
                                    {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-zinc-400">
                                    Esta compra no tiene productos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <div class="rounded-xl bg-zinc-950 p-5">
                <h2 class="mb-4 text-lg font-semibold text-white">
                    Observaciones
                </h2>

                <p class="whitespace-pre-line text-sm leading-6 text-zinc-300">
                    {{ $compra->observaciones ?: 'No se registraron observaciones para esta compra.' }}
                </p>
            </div>

            <div class="rounded-xl bg-zinc-950 p-5">
                <h2 class="mb-5 text-lg font-semibold text-white">
                    Resumen de la Compra
                </h2>

                <div class="space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-400">Subtotal</span>
                        <span class="font-semibold text-white">
                            Bs {{ number_format((float) $compra->subtotal, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-400">Descuento</span>
                        <span class="font-semibold text-white">
                            Bs {{ number_format((float) $compra->descuento, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-zinc-700 pt-4">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-lg font-bold text-white">
                                Total de la compra
                            </span>

                            <span class="text-2xl font-bold text-blue-400">
                                Bs {{ number_format((float) $compra->total, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- INFORMACIÓN DE INVENTARIO --}}
        @if ($compra->estado === 'Borrador')
            <div class="rounded-xl border border-amber-800/50 bg-amber-950/20 p-4">
                <p class="font-semibold text-amber-400">
                    Compra pendiente de confirmación
                </p>

                <p class="mt-1 text-sm text-zinc-300">
                    Esta compra se encuentra en estado Borrador.
                    Sus productos todavía no han sido ingresados al inventario.
                </p>
            </div>
        @elseif ($compra->estado === 'Confirmada')
            <div class="rounded-xl border border-emerald-800/50 bg-emerald-950/20 p-4">
                <p class="font-semibold text-emerald-400">
                    Compra confirmada
                </p>

                <p class="mt-1 text-sm text-zinc-300">
                    Esta compra figura como confirmada.
                    Consulta los movimientos de inventario para verificar
                    el ingreso de las existencias.
                </p>
            </div>
        @endif

        {{-- ======================================== --}}
        {{-- HISTORIAL DE AUDITORÍA DE LA COMPRA --}}
        {{-- ======================================== --}}

        <div class="overflow-hidden rounded-xl bg-zinc-950 shadow-sm">

            <div class="border-b border-zinc-800 p-5">
                <h2 class="text-lg font-semibold text-white">
                    Historial de Auditoría
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Registro de las operaciones realizadas
                    sobre esta compra.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">

                    <thead class="bg-zinc-900 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-5 py-4">Fecha y hora</th>
                            <th class="px-5 py-4">Acción</th>
                            <th class="px-5 py-4">Usuario</th>
                            <th class="px-5 py-4">Descripción</th>
                            <th class="px-5 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($compra->auditorias as $auditoria)

                            @php
                                $colorAccion = match ($auditoria->accion) {
                                    'CREACION' => 'bg-blue-950 text-blue-400',
                                    'EDICION' => 'bg-amber-950 text-amber-400',
                                    'CONFIRMACION' => 'bg-emerald-950 text-emerald-400',
                                    'ANULACION' => 'bg-red-950 text-red-400',
                                    default => 'bg-zinc-800 text-zinc-300',
                                };

                                $nombreAccion = match ($auditoria->accion) {
                                    'CREACION' => 'Creación',
                                    'EDICION' => 'Edición',
                                    'CONFIRMACION' => 'Confirmación',
                                    'ANULACION' => 'Anulación',
                                    default => $auditoria->accion,
                                };
                            @endphp

                            <tr class="text-zinc-200 hover:bg-zinc-900/60">

                                <td class="whitespace-nowrap px-5 py-4">
                                    {{ $auditoria->created_at?->format('d/m/Y H:i:s') ?? '—' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $colorAccion }}">
                                        {{ $nombreAccion }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 font-medium text-white">
                                    {{ $auditoria->usuario?->name ?? 'Usuario no disponible' }}
                                </td>

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $auditoria->descripcion ?: 'Sin descripción' }}
                                </td>

                                <td class="px-5 py-4 text-center">

                                    @if (
                                        $auditoria->accion === 'EDICION' &&
                                        is_array($auditoria->datos_anteriores) &&
                                        is_array($auditoria->datos_nuevos)
                                    )

                                        <button
                                            type="button"
                                            class="btn-ver-cambios rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                                            data-anteriores="{{ json_encode($auditoria->datos_anteriores) }}"
                                            data-nuevos="{{ json_encode($auditoria->datos_nuevos) }}"
                                        >
                                            Ver cambios
                                        </button>

                                    @else
                                        <span class="text-xs text-zinc-500">—</span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="px-5 py-10 text-center text-zinc-400">
                                    No existen registros de auditoría para esta compra.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>
        </div>

    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ========================================
        // CATÁLOGO DE PRODUCTOS
        // ========================================

        const nombresProductos = {{ \Illuminate\Support\Js::from($nombresProductos) }};

        // ========================================
        // FUNCIONES AUXILIARES
        // ========================================

        const escapar = (valor) => String(valor)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const mostrar = (valor) => {
            if (valor === null || valor === undefined || valor === '') {
                return '—';
            }

            return escapar(valor);
        };

        const moneda = (valor) => {
            if (valor === null || valor === undefined || valor === '') {
                return '—';
            }

            const numero = Number(valor);

            if (!Number.isFinite(numero)) {
                return mostrar(valor);
            }

            return 'Bs ' + numero.toLocaleString('es-BO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        // ========================================
        // IDENTIFICAR PRODUCTOS POR NOMBRE
        // ========================================

        const nombreProducto = (productoId) => {
            if (productoId === null || productoId === undefined) {
                return 'Producto desconocido';
            }

            return nombresProductos[productoId]
                ?? `Producto ID: ${productoId}`;
        };

        const identificarDetalle = (detalle) => {
            const nombre = nombreProducto(detalle.producto_id);
            const lote = detalle.numero_lote || 'Sin lote';

            return `${nombre} | Lote: ${lote}`;
        };

        // ========================================
        // CAMPOS A COMPARAR
        // ========================================

        const camposCompra = {
            proveedor_id: 'Proveedor (ID)',
            fecha_compra: 'Fecha de compra',
            numero_factura: 'Número de factura',
            subtotal: 'Subtotal',
            descuento: 'Descuento',
            total: 'Total',
            observaciones: 'Observaciones'
        };

        const camposDetalle = {
            producto_id: 'Producto',
            numero_lote: 'Número de lote',
            fecha_fabricacion: 'Fecha de fabricación',
            fecha_vencimiento: 'Fecha de vencimiento',
            cantidad: 'Cantidad',
            precio_compra: 'Precio de compra',
            subtotal: 'Subtotal del producto'
        };

        const camposMonetarios = [
            'subtotal',
            'descuento',
            'total',
            'precio_compra'
        ];

        const formatear = (campo, valor) => {

            if (campo === 'producto_id') {
                if (valor === null || valor === undefined || valor === '') {
                    return '—';
                }

                return mostrar(nombreProducto(valor));
            }

            return camposMonetarios.includes(campo)
                ? moneda(valor)
                : mostrar(valor);
        };

        const sonIguales = (a, b) => {
            if (a === null || a === undefined || a === '') {
                a = '';
            }

            if (b === null || b === undefined || b === '') {
                b = '';
            }

            return String(a) === String(b);
        };

        // ========================================
        // CREAR FILAS Y ENCABEZADOS
        // ========================================

        const crearFila = (etiqueta, antes, despues) => `
            <tr class="border-b border-zinc-700">
                <td class="p-3 text-zinc-300">
                    ${escapar(etiqueta)}
                </td>

                <td class="p-3 text-red-400 break-words">
                    ${antes}
                </td>

                <td class="p-3 text-emerald-400 break-words">
                    ${despues}
                </td>
            </tr>
        `;

        const crearTitulo = (titulo) => `
            <tr class="bg-zinc-800">
                <td colspan="3" class="p-3 font-bold text-blue-400">
                    ${escapar(titulo)}
                </td>
            </tr>
        `;

        // ========================================
        // AGRUPAR DETALLES POR PRODUCTO Y LOTE
        // ========================================

        const claveDetalle = (detalle) => JSON.stringify([
            String(detalle.producto_id ?? ''),
            String(detalle.numero_lote ?? '').trim().toUpperCase()
        ]);

        const agruparDetalles = (detalles) => {
            const grupos = new Map();

            detalles.forEach((detalle) => {

                const clave = claveDetalle(detalle);

                if (!grupos.has(clave)) {
                    grupos.set(clave, []);
                }

                grupos.get(clave).push(detalle);
            });

            return grupos;
        };

        // ========================================
        // EVENTO VER CAMBIOS
        // ========================================

        document.querySelectorAll('.btn-ver-cambios').forEach(function (boton) {

            boton.addEventListener('click', function () {

                let anteriores;
                let nuevos;

                // ========================================
                // LEER DATOS DE AUDITORÍA
                // ========================================

                try {
                    anteriores = JSON.parse(this.dataset.anteriores);
                    nuevos = JSON.parse(this.dataset.nuevos);

                    if (
                        !anteriores ||
                        !nuevos ||
                        typeof anteriores !== 'object' ||
                        typeof nuevos !== 'object' ||
                        Array.isArray(anteriores) ||
                        Array.isArray(nuevos)
                    ) {
                        throw new Error('Formato de auditoría inválido');
                    }

                } catch (error) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error de auditoría',
                        text: 'No se pudieron leer los datos registrados.',
                        background: '#18181b',
                        color: '#ffffff',
                        confirmButtonColor: '#2563eb'
                    });

                    return;
                }

                let filas = '';
                let cantidadCambios = 0;

                // ========================================
                // 1. COMPARAR INFORMACIÓN GENERAL
                // ========================================

                const compraAnterior = anteriores.compra ?? {};
                const compraNueva = nuevos.compra ?? {};

                let filasGenerales = '';

                Object.entries(camposCompra).forEach(([campo, etiqueta]) => {

                    const antes = compraAnterior[campo];
                    const despues = compraNueva[campo];

                    if (!sonIguales(antes, despues)) {

                        filasGenerales += crearFila(
                            etiqueta,
                            formatear(campo, antes),
                            formatear(campo, despues)
                        );

                        cantidadCambios++;
                    }
                });

                if (filasGenerales) {
                    filas += crearTitulo('Información general de la compra');
                    filas += filasGenerales;
                }

                // ========================================
                // 2. COMPARAR PRODUCTOS Y LOTES
                // ========================================

                const detallesAnteriores = Array.isArray(anteriores.detalles)
                    ? anteriores.detalles
                    : [];

                const detallesNuevos = Array.isArray(nuevos.detalles)
                    ? nuevos.detalles
                    : [];

                const gruposAnteriores = agruparDetalles(detallesAnteriores);
                const gruposNuevos = agruparDetalles(detallesNuevos);

                const claves = new Set([
                    ...gruposAnteriores.keys(),
                    ...gruposNuevos.keys()
                ]);

                let filasProductos = '';

                claves.forEach((clave) => {

                    const anterioresGrupo = gruposAnteriores.get(clave) ?? [];
                    const nuevosGrupo = gruposNuevos.get(clave) ?? [];

                    const cantidad = Math.max(
                        anterioresGrupo.length,
                        nuevosGrupo.length
                    );

                    for (let indice = 0; indice < cantidad; indice++) {

                        const anterior = anterioresGrupo[indice] ?? null;
                        const nuevo = nuevosGrupo[indice] ?? null;

                        const referencia = nuevo ?? anterior;
                        const identificador = identificarDetalle(referencia);

                        // ========================================
                        // PRODUCTO O LOTE AGREGADO
                        // ========================================

                        if (!anterior) {

                            filasProductos += crearTitulo(
                                `Producto o lote agregado: ${identificador}`
                            );

                            Object.entries(camposDetalle).forEach(([campo, etiqueta]) => {

                                filasProductos += crearFila(
                                    etiqueta,
                                    '—',
                                    formatear(campo, nuevo[campo])
                                );
                            });

                            cantidadCambios++;
                            continue;
                        }

                        // ========================================
                        // PRODUCTO O LOTE RETIRADO
                        // ========================================

                        if (!nuevo) {

                            filasProductos += crearTitulo(
                                `Producto o lote retirado: ${identificador}`
                            );

                            Object.entries(camposDetalle).forEach(([campo, etiqueta]) => {

                                filasProductos += crearFila(
                                    etiqueta,
                                    formatear(campo, anterior[campo]),
                                    '—'
                                );
                            });

                            cantidadCambios++;
                            continue;
                        }

                        // ========================================
                        // PRODUCTO EXISTENTE MODIFICADO
                        // ========================================

                        let diferencias = '';

                        Object.entries(camposDetalle).forEach(([campo, etiqueta]) => {

                            const antes = anterior[campo];
                            const despues = nuevo[campo];

                            if (!sonIguales(antes, despues)) {

                                diferencias += crearFila(
                                    etiqueta,
                                    formatear(campo, antes),
                                    formatear(campo, despues)
                                );

                                cantidadCambios++;
                            }
                        });

                        if (diferencias) {

                            filasProductos += crearTitulo(identificador);
                            filasProductos += diferencias;
                        }
                    }
                });

                if (filasProductos) {
                    filas += crearTitulo('Cambios en productos y lotes');
                    filas += filasProductos;
                }

                // ========================================
                // 3. MENSAJE SIN DIFERENCIAS
                // ========================================

                if (!filas) {

                    filas = `
                        <tr>
                            <td colspan="3"
                                class="p-5 text-center text-zinc-400">
                                No se encontraron diferencias en los campos comparados.
                            </td>
                        </tr>
                    `;
                }

                // ========================================
                // 4. MOSTRAR HISTORIAL CON SWEETALERT2
                // ========================================

                Swal.fire({

                    title: 'Historial de modificaciones',

                    width: 950,

                    background: '#18181b',

                    color: '#ffffff',

                    confirmButtonText: 'Cerrar',

                    confirmButtonColor: '#2563eb',

                    html: `
                        <div class="mb-4 text-left text-sm text-zinc-400">
                            Diferencias detectadas:
                            <strong class="text-blue-400">
                                ${cantidadCambios}
                            </strong>
                        </div>

                        <div class="max-h-[60vh] overflow-auto rounded-lg border border-zinc-700 text-left">

                            <table class="w-full min-w-[650px] text-sm">

                                <thead class="sticky top-0 bg-zinc-800 text-white">
                                    <tr>
                                        <th class="p-3 text-left">Campo</th>
                                        <th class="p-3 text-left">Antes</th>
                                        <th class="p-3 text-left">Después</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    ${filas}
                                </tbody>

                            </table>

                        </div>
                    `
                });

            });

        });

    });
    </script>


</x-layouts::app>
