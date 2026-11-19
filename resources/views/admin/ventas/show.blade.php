<x-layouts::app :title="'Detalle de Venta'">

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-zinc-100">
                    Detalle de Venta
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Información completa de la venta {{ $venta->codigo }}.
                </p>
            </div>

            <a href="{{ route('admin.ventas.index') }}"
               class="rounded-lg border border-zinc-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>

        </div>

        {{-- INFORMACIÓN GENERAL --}}
        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-950">

            <div class="border-b border-zinc-800 px-5 py-4">
                <h2 class="font-semibold text-zinc-200">
                    Información General
                </h2>
                <p class="mt-1 text-xs text-zinc-500">
                    Datos principales de la operación.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-5 md:grid-cols-2 lg:grid-cols-3">

                <div>
                    <p class="text-sm text-zinc-500">Código de Venta</p>
                    <p class="mt-1 font-semibold text-blue-400">
                        {{ $venta->codigo }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-500">Fecha de Venta</p>
                    <p class="mt-1 text-zinc-200">
                        {{ $venta->fecha_venta?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-500">Usuario Responsable</p>
                    <p class="mt-1 text-zinc-200">
                        {{ $venta->usuario?->name ?? 'Usuario no disponible' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-500">Estado</p>

                    <div class="mt-2">
                        @if ($venta->estado === 'Borrador')
                            <span class="rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-400">
                                Borrador
                            </span>
                        @elseif ($venta->estado === 'Confirmada')
                            <span class="rounded-full bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">
                                Confirmada
                            </span>
                        @else
                            <span class="rounded-full bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">
                                Anulada
                            </span>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2">
                    <p class="text-sm text-zinc-500">Observaciones</p>
                    <p class="mt-1 whitespace-pre-line text-zinc-200">
                        {{ $venta->observacion ?: 'Sin observaciones registradas.' }}
                    </p>
                </div>

            </div>
        </div>

        {{-- DETALLE DE MEDICAMENTOS --}}
        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-950">

            <div class="border-b border-zinc-800 px-5 py-4">
                <h2 class="font-semibold text-zinc-200">
                    Medicamentos de la Venta
                </h2>
                <p class="mt-1 text-xs text-zinc-500">
                    Productos, lotes, cantidades y precios registrados.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-900 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-5 py-4">Nro.</th>
                            <th class="px-5 py-4">Medicamento</th>
                            <th class="px-5 py-4">Lote</th>
                            <th class="px-5 py-4 text-center">Cantidad</th>
                            <th class="px-5 py-4 text-right">Precio (Bs)</th>
                            <th class="px-5 py-4 text-right">Descuento (Bs)</th>
                            <th class="px-5 py-4 text-right">Subtotal (Bs)</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($venta->detalles as $detalle)
                            <tr class="hover:bg-zinc-900/70">

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-zinc-100">
                                        {{ $detalle->producto?->nombre ?? 'Producto no disponible' }}
                                    </p>

                                    <p class="mt-1 text-xs text-zinc-500">
                                        {{ $detalle->producto?->codigo ?? '—' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $detalle->lote?->numero_lote ?? '—' }}

                                    @if ($detalle->lote?->fecha_vencimiento)
                                        <p class="mt-1 text-xs text-zinc-500">
                                            Vence:
                                            {{ \Illuminate\Support\Carbon::parse($detalle->lote->fecha_vencimiento)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center text-zinc-300">
                                    {{ $detalle->cantidad }}
                                </td>

                                <td class="px-5 py-4 text-right text-zinc-300">
                                    {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right text-red-400">
                                    {{ number_format((float) $detalle->descuento, 2, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right font-semibold text-zinc-100">
                                    {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-zinc-400">
                                    No existen medicamentos registrados en esta venta.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        {{-- RESUMEN DE LA VENTA --}}
        <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-5">

            <h2 class="mb-5 font-semibold text-zinc-200">
                Resumen de la Venta
            </h2>

            <div class="ml-auto max-w-sm space-y-3">

                <div class="flex justify-between text-sm">
                    <span class="text-zinc-400">Subtotal</span>
                    <span class="text-zinc-200">
                        Bs {{ number_format((float) $venta->subtotal, 2, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-zinc-400">Descuentos</span>
                    <span class="text-red-400">
                        Bs {{ number_format((float) $venta->descuento, 2, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-center justify-between border-t border-zinc-700 pt-4">
                    <span class="font-semibold text-white">Total de la Venta</span>
                    <span class="text-2xl font-bold text-blue-400">
                        Bs {{ number_format((float) $venta->total, 2, ',', '.') }}
                    </span>
                </div>

            </div>
        </div>

        {{-- HISTORIAL DE AUDITORÍA --}}
        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-950">

            <div class="border-b border-zinc-800 px-5 py-4">
                <h2 class="font-semibold text-zinc-200">
                    Historial de Auditoría
                </h2>
                <p class="mt-1 text-xs text-zinc-500">
                    Registro de las operaciones realizadas sobre esta venta.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-900 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-5 py-4">Fecha</th>
                            <th class="px-5 py-4">Usuario</th>
                            <th class="px-5 py-4">Acción</th>
                            <th class="px-5 py-4">Descripción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($venta->auditorias->sortByDesc('id') as $auditoria)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4 text-zinc-300">
                                    {{ $auditoria->created_at?->format('d/m/Y H:i:s') ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $auditoria->usuario?->name ?? 'Usuario no disponible' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-lg bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-400">
                                        {{ $auditoria->accion }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $auditoria->descripcion ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-zinc-400">
                                    No existen registros de auditoría.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-layouts::app>
