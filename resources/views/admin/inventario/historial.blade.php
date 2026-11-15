<x-layouts::app title="Historial de Inventario">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Historial de Movimientos
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Consulta de entradas, salidas y ajustes del lote.
                </p>
            </div>

            <a href="{{ route('admin.inventario.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800">
                ← Volver al Inventario
            </a>

        </div>

        {{-- INFORMACIÓN DEL LOTE --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

            <h2 class="mb-4 font-semibold text-white">
                Información del Lote
            </h2>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">

                <div>
                    <p class="text-xs text-zinc-400">Producto</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $lote->producto?->nombre ?? 'No disponible' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">Número de Lote</p>
                    <p class="mt-1 font-semibold text-blue-400">
                        {{ $lote->numero_lote }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">Stock Actual</p>
                    <p class="mt-1 text-xl font-bold text-green-400">
                        {{ $lote->stock_actual }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400">Fecha de Vencimiento</p>
                    <p class="mt-1 font-semibold text-white">
                        {{ $lote->fecha_vencimiento?->format('d/m/Y') ?? 'Sin fecha' }}
                    </p>
                </div>

            </div>

        </div>

        {{-- HISTORIAL --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-700 bg-zinc-950 px-5 py-5">

                <h2 class="font-semibold text-white">
                    Movimientos Registrados
                </h2>

                <span class="text-xs text-zinc-400">
                    Total: {{ $movimientos->total() }}
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">
                        <tr>
                            <th class="px-4 py-4">Nro.</th>
                            <th class="px-4 py-4">Fecha</th>
                            <th class="px-4 py-4">Tipo</th>
                            <th class="px-4 py-4">Cantidad</th>
                            <th class="px-4 py-4">Stock Anterior</th>
                            <th class="px-4 py-4">Stock Posterior</th>
                            <th class="px-4 py-4">Responsable</th>
                            <th class="px-4 py-4">Referencia</th>
                            <th class="px-4 py-4">Observación</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-700">

                        @forelse($movimientos as $movimiento)

                            @php
                                $entrada = in_array(
                                    $movimiento->tipo,
                                    ['Entrada', 'Ajuste Entrada'],
                                    true
                                );
                            @endphp

                            <tr class="hover:bg-zinc-800/50">

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $movimientos->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-zinc-300">
                                    {{ $movimiento->created_at?->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-4 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $entrada
                                            ? 'bg-green-950 text-green-400'
                                            : 'bg-red-950 text-red-400' }}">
                                        {{ $movimiento->tipo }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 font-bold {{ $entrada ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $entrada ? '+' : '-' }}{{ $movimiento->cantidad }}
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $movimiento->stock_anterior }}
                                </td>

                                <td class="px-4 py-4 font-semibold text-white">
                                    {{ $movimiento->stock_posterior }}
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $movimiento->usuario?->name ?? 'Usuario no disponible' }}
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $movimiento->referencia ?? 'Sin referencia' }}
                                </td>

                                <td class="min-w-56 px-4 py-4 text-zinc-400">
                                    {{ $movimiento->observacion ?? 'Sin observaciones' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="9"
                                    class="px-6 py-12 text-center text-zinc-400">
                                    No existen movimientos registrados para este lote.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($movimientos->hasPages())
                <div class="border-t border-zinc-700 px-5 py-4">
                    {{ $movimientos->links() }}
                </div>
            @endif

        </div>

    </div>

</x-layouts::app>
