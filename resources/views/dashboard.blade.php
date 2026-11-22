<x-layouts::app :title="'Dashboard'">
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Panel de Administración
            </h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                Bienvenido al Sistema de Gestión de Farmacia
            </p>
        </div>

        @if (isset($puedeVerProductos) && isset($puedeVerVentas) && isset($puedeVerInventario))
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                @if ($puedeVerProductos)
                    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total de Productos</p>
                        <p class="mt-3 text-3xl font-bold text-blue-500">
                            {{ number_format($totalProductos, 0, ',', '.') }}
                        </p>
                    </div>
                @endif

                @if ($puedeVerVentas)
                    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ventas del Día</p>
                        <p class="mt-3 text-3xl font-bold text-green-500">
                            Bs {{ number_format($ventasDelDia, 2, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-gray-400">Ventas confirmadas de hoy</p>
                    </div>
                @endif

                @if ($puedeVerInventario)
                    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lotes con Stock Bajo</p>
                        <p class="mt-3 text-3xl font-bold text-orange-500">
                            {{ number_format($lotesStockBajo, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-gray-400">De 1 a {{ $stockMinimo }} unidades</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lotes por Vencer</p>
                        <p class="mt-3 text-3xl font-bold text-red-500">
                            {{ number_format($lotesPorVencer, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-gray-400">
                            Desde hoy hasta el {{ $limiteVencimiento->format('d/m/Y') }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                @if ($puedeVerVentas)
                    <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Últimas Ventas Confirmadas
                        </h2>

                        <ul class="mt-4 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($ultimasVentas as $venta)
                                <li class="flex flex-wrap items-center justify-between gap-3 py-3">
                                    <div>
                                        <a href="{{ route('admin.ventas.show', $venta) }}"
                                           class="font-medium text-blue-500 hover:underline">
                                            {{ $venta->codigo }}
                                        </a>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $venta->fecha_venta->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        Bs {{ number_format($venta->total, 2, ',', '.') }}
                                    </span>
                                </li>
                            @empty
                                <li class="py-3 text-sm text-gray-500 dark:text-gray-400">
                                    Aún no hay ventas confirmadas.
                                </li>
                            @endforelse
                        </ul>
                    </section>
                @endif

                @if ($puedeVerInventario)
                    <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Alertas de Inventario
                        </h2>

                        <ul class="mt-4 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($alertasInventario as $lote)
                                <li class="py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $lote->producto?->nombre ?? 'Producto sin identificar' }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $lote->numero_lote }} · {{ $lote->stock_actual }} unidades
                                        · Vence el {{ $lote->fecha_vencimiento->format('d/m/Y') }}
                                    </p>
                                    <div class="mt-1 flex flex-wrap gap-3 text-sm">
                                        @if ($lote->stock_actual >= 1 && $lote->stock_actual <= $stockMinimo)
                                            <span class="text-orange-500">Stock bajo</span>
                                        @endif
                                        @if ($lote->fecha_vencimiento->betweenIncluded($hoy, $limiteVencimiento))
                                            <span class="text-red-500">Por vencer</span>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="py-3 text-sm text-gray-500 dark:text-gray-400">
                                    No hay lotes con stock bajo o próximos a vencer.
                                </li>
                            @endforelse
                        </ul>
                    </section>
                @endif
            </div>
        @endisset
    </div>
</x-layouts::app>
