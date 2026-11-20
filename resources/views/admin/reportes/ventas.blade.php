<x-layouts::app :title="'Reporte de Ventas'">

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Reporte de Ventas
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Consulta las ventas registradas, sus estados
                    y los ingresos de la farmacia.
                </p>
            </div>

            <a href="{{ route('admin.reportes.index') }}"
               class="rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                ← Volver a Reportes
            </a>

            <a href="{{ route('admin.reportes.ventas.pdf', request()->only(['fecha_inicio', 'fecha_fin', 'estado'])) }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                Exportar PDF
            </a>
        </div>

        {{-- FILTROS --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-950">

            <div class="border-b border-zinc-700 px-5 py-4">
                <h2 class="font-semibold text-white">
                    Buscar Ventas
                </h2>
                <p class="mt-1 text-xs text-zinc-400">
                    Filtra las operaciones por fechas y estado.
                </p>
            </div>

            <form method="GET"
                  action="{{ route('admin.reportes.ventas') }}"
                  class="grid grid-cols-1 items-end gap-4 p-5 md:grid-cols-2 xl:grid-cols-5">

                <div>
                    <label for="fecha_inicio"
                           class="mb-2 block text-xs font-medium text-zinc-300">
                        Fecha Inicial
                    </label>

                    <input type="date"
                           id="fecha_inicio"
                           name="fecha_inicio"
                           value="{{ $fechaInicio }}"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2.5 text-sm text-white">
                </div>

                <div>
                    <label for="fecha_fin"
                           class="mb-2 block text-xs font-medium text-zinc-300">
                        Fecha Final
                    </label>

                    <input type="date"
                           id="fecha_fin"
                           name="fecha_fin"
                           value="{{ $fechaFin }}"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2.5 text-sm text-white">
                </div>

                <div>
                    <label for="estado"
                           class="mb-2 block text-xs font-medium text-zinc-300">
                        Estado
                    </label>

                    <select id="estado"
                            name="estado"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2.5 text-sm text-white">

                        <option value="">Todos los estados</option>

                        <option value="Borrador"
                            @selected($estado === 'Borrador')>
                            Borrador
                        </option>

                        <option value="Confirmada"
                            @selected($estado === 'Confirmada')>
                            Confirmada
                        </option>

                        <option value="Anulada"
                            @selected($estado === 'Anulada')>
                            Anulada
                        </option>
                    </select>
                </div>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Buscar
                </button>

                <a href="{{ route('admin.reportes.ventas') }}"
                   class="rounded-lg border border-zinc-700 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-zinc-800">
                    Limpiar
                </a>

            </form>

            @error('fecha_inicio')
                <p class="px-5 pb-3 text-sm text-red-400">{{ $message }}</p>
            @enderror

            @error('fecha_fin')
                <p class="px-5 pb-3 text-sm text-red-400">{{ $message }}</p>
            @enderror

            @error('estado')
                <p class="px-5 pb-3 text-sm text-red-400">{{ $message }}</p>
            @enderror

        </div>

        {{-- INDICADORES --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">
                <p class="text-sm text-zinc-400">Ventas Registradas</p>
                <p class="mt-3 text-3xl font-bold text-white">
                    {{ number_format($totalVentas, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">
                <p class="text-sm text-zinc-400">Ventas Confirmadas</p>
                <p class="mt-3 text-3xl font-bold text-green-400">
                    {{ number_format($ventasConfirmadas, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">
                <p class="text-sm text-zinc-400">Ventas en Borrador</p>
                <p class="mt-3 text-3xl font-bold text-amber-400">
                    {{ number_format($ventasBorrador, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">
                <p class="text-sm text-zinc-400">Ventas Anuladas</p>
                <p class="mt-3 text-3xl font-bold text-red-400">
                    {{ number_format($ventasAnuladas, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">
                <p class="text-sm text-zinc-400">Ingresos Confirmados</p>
                <p class="mt-3 text-3xl font-bold text-blue-400">
                    Bs {{ number_format($ingresosConfirmados, 2, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">
                <p class="text-sm text-zinc-400">Descuentos Confirmados</p>
                <p class="mt-3 text-3xl font-bold text-orange-400">
                    Bs {{ number_format($descuentosConfirmados, 2, ',', '.') }}
                </p>
            </div>

        </div>

        {{-- TABLA DE VENTAS --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-950">

            <div class="flex items-center justify-between border-b border-zinc-700 px-5 py-4">
                <h2 class="font-semibold text-white">
                    Detalle de Ventas
                </h2>

                <span class="text-xs text-zinc-400">
                    Total: {{ $ventas->total() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-900 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-4 py-4">Código</th>
                            <th class="px-4 py-4">Fecha</th>
                            <th class="px-4 py-4">Usuario</th>
                            <th class="px-4 py-4 text-right">Subtotal (Bs)</th>
                            <th class="px-4 py-4 text-right">Descuento (Bs)</th>
                            <th class="px-4 py-4 text-right">Total (Bs)</th>
                            <th class="px-4 py-4 text-center">Estado</th>
                            <th class="px-4 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($ventas as $venta)
                            <tr class="text-zinc-200 hover:bg-zinc-900/70">

                                <td class="px-4 py-4 font-semibold text-blue-400">
                                    {{ $venta->codigo }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ $venta->usuario?->name ?? 'Sin usuario' }}
                                </td>

                                <td class="px-4 py-4 text-right">
                                    {{ number_format($venta->subtotal, 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-right text-red-400">
                                    {{ number_format($venta->descuento, 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-right font-semibold">
                                    {{ number_format($venta->total, 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @if ($venta->estado === 'Confirmada')
                                        <span class="rounded-full bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                            Confirmada
                                        </span>
                                    @elseif ($venta->estado === 'Borrador')
                                        <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-400">
                                            Borrador
                                        </span>
                                    @else
                                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-400">
                                            Anulada
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @can('ventas.ver')
                                        <a href="{{ route('admin.ventas.show', $venta) }}"
                                           class="inline-flex rounded-lg bg-zinc-700 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-600">
                                            Ver
                                        </a>
                                    @else
                                        <span class="text-xs text-zinc-500">—</span>
                                    @endcan
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    class="px-4 py-10 text-center text-zinc-400">
                                    No se encontraron ventas con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            @if ($ventas->hasPages())
                <div class="border-t border-zinc-700 px-5 py-4">
                    {{ $ventas->links() }}
                </div>
            @endif

        </div>

    </div>

</x-layouts::app>
