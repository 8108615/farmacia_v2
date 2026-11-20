
<x-layouts::app :title="'Reporte de Compras'">

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Reporte de Compras
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Consulta las compras registradas, proveedores y montos de adquisición.
                </p>
            </div>

            <a href="{{ route('admin.reportes.index') }}"
               class="inline-flex items-center rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                ← Volver a Reportes
            </a>

            <a href="{{ route('admin.reportes.compras.pdf', request()->only(['fecha_inicio', 'fecha_fin', 'estado', 'proveedor_id'])) }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                Exportar PDF
            </a>
        </div>

        {{-- FILTROS --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-950">
            <div class="border-b border-zinc-700 px-5 py-4">
                <h2 class="font-semibold text-white">Buscar Compras</h2>
                <p class="mt-1 text-xs text-zinc-400">
                    Filtra las operaciones por fechas, estado y proveedor.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reportes.compras') }}"
                  class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-4">

                <div>
                    <label for="fecha_inicio" class="mb-2 block text-xs font-semibold text-zinc-300">
                        Fecha Inicial
                    </label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio"
                           value="{{ $fechaInicio }}"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label for="fecha_fin" class="mb-2 block text-xs font-semibold text-zinc-300">
                        Fecha Final
                    </label>
                    <input type="date" id="fecha_fin" name="fecha_fin"
                           value="{{ $fechaFin }}"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label for="estado" class="mb-2 block text-xs font-semibold text-zinc-300">
                        Estado
                    </label>
                    <select id="estado" name="estado"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <option value="">Todos los estados</option>
                        <option value="Borrador" @selected($estado === 'Borrador')>Borrador</option>
                        <option value="Confirmada" @selected($estado === 'Confirmada')>Confirmada</option>
                        <option value="Anulada" @selected($estado === 'Anulada')>Anulada</option>
                    </select>
                </div>

                <div>
                    <label for="proveedor_id" class="mb-2 block text-xs font-semibold text-zinc-300">
                        Proveedor
                    </label>
                    <select id="proveedor_id" name="proveedor_id"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <option value="">Todos los proveedores</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}"
                                @selected((string) $proveedorId === (string) $proveedor->id)>
                                {{ $proveedor->nombre_comercial ?: $proveedor->razon_social }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Buscar
                </button>

                <a href="{{ route('admin.reportes.compras') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-zinc-700 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                    Limpiar
                </a>
            </form>
        </div>

        {{-- INDICADORES --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ([
                ['Compras Registradas', $totalCompras, 'text-white'],
                ['Compras Confirmadas', $comprasConfirmadas, 'text-green-500'],
                ['Compras en Borrador', $comprasBorrador, 'text-amber-400'],
                ['Compras Anuladas', $comprasAnuladas, 'text-red-500'],
                ['Total Confirmado', 'Bs ' . number_format($totalConfirmado, 2, ',', '.'), 'text-blue-400'],
                ['Descuentos Confirmados', 'Bs ' . number_format($descuentosConfirmados, 2, ',', '.'), 'text-orange-400'],
            ] as [$titulo, $valor, $color])
                <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">
                    <p class="text-sm text-zinc-400">{{ $titulo }}</p>
                    <p class="mt-3 text-2xl font-bold {{ $color }}">{{ $valor }}</p>
                </div>
            @endforeach
        </div>

        {{-- DETALLE DE COMPRAS --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-950">
            <div class="flex items-center justify-between border-b border-zinc-700 px-5 py-4">
                <h2 class="font-semibold text-white">Detalle de Compras</h2>
                <span class="text-xs text-zinc-400">Total: {{ $compras->total() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead class="bg-zinc-900 text-zinc-400">
                        <tr>
                            <th class="px-4 py-4">CÓDIGO</th>
                            <th class="px-4 py-4">FECHA</th>
                            <th class="px-4 py-4">PROVEEDOR</th>
                            <th class="px-4 py-4">USUARIO</th>
                            <th class="px-4 py-4 text-right">SUBTOTAL (BS)</th>
                            <th class="px-4 py-4 text-right">DESCUENTO (BS)</th>
                            <th class="px-4 py-4 text-right">TOTAL (BS)</th>
                            <th class="px-4 py-4 text-center">ESTADO</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">
                        @forelse ($compras as $compra)
                            <tr class="hover:bg-zinc-900/60">
                                <td class="px-4 py-4 font-semibold text-blue-400">
                                    {{ $compra->codigo }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ $compra->fecha_compra?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ $compra->proveedor?->nombre_comercial
                                       ?: ($compra->proveedor?->razon_social ?: 'Sin proveedor') }}
                                </td>

                                <td class="px-4 py-4">
                                    {{ $compra->usuario?->name ?? '—' }}
                                </td>

                                <td class="px-4 py-4 text-right">
                                    {{ number_format($compra->subtotal, 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-right text-red-400">
                                    {{ number_format($compra->descuento, 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-right font-bold text-white">
                                    {{ number_format($compra->total, 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @php
                                        $colorEstado = match ($compra->estado) {
                                            'Confirmada' => 'bg-green-950 text-green-400',
                                            'Anulada' => 'bg-red-950 text-red-400',
                                            default => 'bg-amber-950 text-amber-400',
                                        };
                                    @endphp

                                    <span class="rounded-full px-3 py-1 font-semibold {{ $colorEstado }}">
                                        {{ $compra->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-zinc-400">
                                    No se encontraron compras con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($compras->hasPages())
                <div class="border-t border-zinc-700 px-5 py-4">
                    {{ $compras->links() }}
                </div>
            @endif
        </div>

    </div>

</x-layouts::app>
