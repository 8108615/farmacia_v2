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

    </div>

</x-layouts::app>
