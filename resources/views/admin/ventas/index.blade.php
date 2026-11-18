
<x-layouts::app :title="'Gestión de Ventas'">

    <div class="space-y-6">

        {{-- ========================================
            ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100">
                    Gestión de Ventas
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Administra las ventas de medicamentos y productos de la farmacia.
                </p>
            </div>

            {{-- Se habilitará cuando implementemos store() y create.blade.php --}}
        </div>

        {{-- ========================================
            FILTROS DE BÚSQUEDA
        ======================================== --}}
        <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-5">

            <h2 class="mb-5 text-base font-semibold text-zinc-200">
                Buscar Ventas
            </h2>

            <form
                method="GET"
                action="{{ route('admin.ventas.index') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end"
            >

                <div class="md:col-span-5">
                    <label for="buscar" class="mb-2 block text-sm text-zinc-400">
                        Buscar
                    </label>

                    <input
                        type="text"
                        name="buscar"
                        id="buscar"
                        value="{{ $buscar }}"
                        placeholder="Código de venta"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                </div>

                <div class="md:col-span-3">
                    <label for="estado" class="mb-2 block text-sm text-zinc-400">
                        Estado
                    </label>

                    <select
                        name="estado"
                        id="estado"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-zinc-100 outline-none focus:border-blue-500"
                    >
                        <option value="todos" @selected($estado === 'todos')>
                            Todos los estados
                        </option>

                        <option value="Borrador" @selected($estado === 'Borrador')>
                            Borrador
                        </option>

                        <option value="Confirmada" @selected($estado === 'Confirmada')>
                            Confirmada
                        </option>

                        <option value="Anulada" @selected($estado === 'Anulada')>
                            Anulada
                        </option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Buscar
                    </button>
                </div>

                <div class="md:col-span-1">
                    <a
                        href="{{ route('admin.ventas.index') }}"
                        class="flex w-full items-center justify-center rounded-lg border border-zinc-700 px-3 py-2.5 text-sm text-zinc-300 transition hover:bg-zinc-800"
                    >
                        Limpiar
                    </a>
                </div>

            </form>
        </div>

        {{-- ========================================
            TABLA DE VENTAS
        ======================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-950">

            <div class="flex items-center justify-between border-b border-zinc-800 px-5 py-4">
                <h2 class="font-semibold text-zinc-200">
                    Ventas Registradas
                </h2>

                <span class="text-sm text-zinc-400">
                    Total: {{ $ventas->total() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-900 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-5 py-4">Código</th>
                            <th class="px-5 py-4">Fecha</th>
                            <th class="px-5 py-4">Usuario</th>
                            <th class="px-5 py-4 text-center">Productos</th>
                            <th class="px-5 py-4 text-right">Subtotal (Bs)</th>
                            <th class="px-5 py-4 text-right">Descuento (Bs)</th>
                            <th class="px-5 py-4 text-right">Total (Bs)</th>
                            <th class="px-5 py-4 text-center">Estado</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($ventas as $venta)

                            <tr class="transition hover:bg-zinc-900/70">

                                {{-- CÓDIGO --}}
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-blue-400">
                                    {{ $venta->codigo }}
                                </td>

                                {{-- FECHA --}}
                                <td class="whitespace-nowrap px-5 py-4 text-zinc-300">
                                    {{ $venta->fecha_venta?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                {{-- USUARIO --}}
                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $venta->usuario?->name ?? 'Usuario no disponible' }}
                                </td>

                                {{-- PRODUCTOS --}}
                                <td class="px-5 py-4 text-center text-zinc-300">
                                    {{ $venta->detalles_count }}
                                </td>

                                {{-- SUBTOTAL --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right text-zinc-300">
                                    {{ number_format((float) $venta->subtotal, 2, ',', '.') }}
                                </td>

                                {{-- DESCUENTO --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right text-zinc-300">
                                    {{ number_format((float) $venta->descuento, 2, ',', '.') }}
                                </td>

                                {{-- TOTAL --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-zinc-100">
                                    {{ number_format((float) $venta->total, 2, ',', '.') }}
                                </td>

                                {{-- ESTADO --}}
                                <td class="px-5 py-4 text-center">

                                    @if ($venta->estado === 'Borrador')

                                        <span class="inline-flex rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-400">
                                            Borrador
                                        </span>

                                    @elseif ($venta->estado === 'Confirmada')

                                        <span class="inline-flex rounded-full bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">
                                            Confirmada
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">
                                            Anulada
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center">

                                    <div class="text-base font-medium text-zinc-300">
                                        No se encontraron ventas registradas.
                                    </div>

                                    <p class="mt-2 text-sm text-zinc-500">
                                        Cuando registremos ventas, aparecerán en este listado.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            {{-- ========================================
                PAGINACIÓN
            ======================================== --}}
            @if ($ventas->hasPages())
                <div class="border-t border-zinc-800 px-5 py-4">
                    {{ $ventas->links() }}
                </div>
            @endif

        </div>

    </div>

</x-layouts::app>
