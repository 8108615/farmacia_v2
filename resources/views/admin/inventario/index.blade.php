<x-layouts::app title="Gestión de Inventario">

    <div class="flex flex-col gap-6">

        {{-- ========================================
            ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Gestión de Inventario
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Control de existencias, lotes y fechas de vencimiento
                    del Sistema Farmacia.
                </p>
            </div>

            @can('inventario.gestionar')
                <a href="{{ route('admin.inventario.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    + Registrar Lote
                </a>
            @endcan

        </div>

        {{-- ========================================
            BUSCADOR
        ======================================== --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-4">

            <form method="GET"
                  action="{{ route('admin.inventario.index') }}"
                  class="flex flex-col gap-3 sm:flex-row">

                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    placeholder="Buscar por producto, código, código de barras o lote..."
                    class="w-full flex-1 rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                >

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    Buscar
                </button>

                <a href="{{ route('admin.inventario.index') }}"
                   class="rounded-lg border border-zinc-700 bg-zinc-950 px-6 py-3 text-center text-sm font-semibold text-white hover:bg-zinc-800">
                    Limpiar
                </a>

            </form>

        </div>

        {{-- LISTADO --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-700 bg-zinc-950 px-5 py-5">

                <h2 class="font-semibold text-white">
                    Inventario de Productos
                </h2>

                <span class="text-xs text-zinc-400">
                    Total: {{ $lotes->total() }}
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">

                        <tr>
                            <th class="px-4 py-4">Nro.</th>
                            <th class="px-4 py-4">Código</th>
                            <th class="px-4 py-4">Producto</th>
                            <th class="px-4 py-4">Lote</th>
                            <th class="px-4 py-4">Vencimiento</th>
                            <th class="px-4 py-4">Stock</th>
                            <th class="px-4 py-4">Estado</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-zinc-700">

                        @forelse($lotes as $lote)

                            @php
                                $vencido = $lote->fecha_vencimiento
                                    && $lote->fecha_vencimiento->isPast();

                                $porVencer = $lote->fecha_vencimiento
                                    && !$vencido
                                    && $lote->fecha_vencimiento->lte(
                                        now()->addDays(30)
                                    );
                            @endphp

                            <tr class="hover:bg-zinc-800/50">

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $lotes->firstItem() + $loop->index }}
                                </td>

                                <td class="px-4 py-4 font-semibold text-blue-400">
                                    {{ $lote->producto?->codigo ?? 'N/D' }}
                                </td>

                                <td class="px-4 py-4">

                                    <p class="font-semibold text-white">
                                        {{ $lote->producto?->nombre ?? 'Producto no disponible' }}
                                    </p>

                                    <p class="mt-1 text-xs text-zinc-400">
                                        {{ $lote->producto?->categoria?->nombre ?? 'Sin categoría' }}
                                    </p>

                                </td>

                                <td class="px-4 py-4 text-white">
                                    {{ $lote->numero_lote }}
                                </td>

                                <td class="px-4 py-4">

                                    <p class="text-white">
                                        {{ $lote->fecha_vencimiento?->format('d/m/Y') ?? 'Sin fecha' }}
                                    </p>

                                    @if($vencido)
                                        <span class="text-xs font-semibold text-red-400">
                                            Vencido
                                        </span>
                                    @elseif($porVencer)
                                        <span class="text-xs font-semibold text-yellow-400">
                                            Próximo a vencer
                                        </span>
                                    @else
                                        <span class="text-xs text-green-400">
                                            Vigente
                                        </span>
                                    @endif

                                </td>

                                <td class="px-4 py-4">

                                    <span class="font-bold {{ $lote->stock_actual == 0 ? 'text-red-400' : 'text-green-400' }}">
                                        {{ $lote->stock_actual }}
                                    </span>

                                </td>

                                <td class="px-4 py-4">

                                    @if($lote->estado === 'Activo')

                                        <span class="rounded-full bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                            Activo
                                        </span>

                                    @else

                                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-400">
                                            Inactivo
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7"
                                    class="px-6 py-12 text-center text-sm text-zinc-400">

                                    No se encontraron lotes registrados en el inventario.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($lotes->hasPages())

                <div class="border-t border-zinc-700 px-5 py-4">
                    {{ $lotes->links() }}
                </div>

            @endif

        </div>

    </div>

</x-layouts::app>
