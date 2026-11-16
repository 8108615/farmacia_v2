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

            {{-- ========================================
                BOTONES DEL ENCABEZADO
            ======================================== --}}

            <div class="flex flex-wrap items-center gap-3">

                {{-- EXPORTAR HISTORIAL A PDF --}}
                <a
                    href="{{ route('admin.inventario.historial.pdf', array_merge(
                        ['lote' => $lote->id],
                        request()->only([
                            'tipo',
                            'fecha_desde',
                            'fecha_hasta'
                        ])
                    )) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <path d="M8 13h2"/>
                        <path d="M8 17h8"/>
                    </svg>

                    Exportar PDF
                </a>

                {{-- VOLVER AL INVENTARIO --}}
                <a
                    href="{{ route('admin.inventario.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800"
                >
                    ← Volver al Inventario
                </a>

            </div>

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

                {{-- ========================================
                    FECHA Y ESTADO DE VENCIMIENTO
                ======================================== --}}

                <div>
                    <p class="text-xs text-zinc-400">
                        Fecha de Vencimiento
                    </p>

                    @php
                        $fechaVencimiento = $lote->fecha_vencimiento
                            ? \Illuminate\Support\Carbon::parse(
                                $lote->fecha_vencimiento
                            )->startOfDay()
                            : null;

                        $hoy = today();

                        $diasRestantes = $fechaVencimiento
                            ? (int) $hoy->diffInDays($fechaVencimiento, false)
                            : null;
                    @endphp

                    <p class="mt-1 font-semibold text-white">
                        {{ $fechaVencimiento?->format('d/m/Y') ?? 'Sin fecha' }}
                    </p>

                    <div class="mt-2">

                        @if($diasRestantes === null)

                            <span class="inline-flex rounded-full bg-zinc-800 px-3 py-1 text-xs font-semibold text-zinc-300">
                                Sin fecha de vencimiento
                            </span>

                        @elseif($diasRestantes < 0)

                            <span class="inline-flex rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-400">
                                Vencido hace {{ abs($diasRestantes) }}
                                {{ abs($diasRestantes) === 1 ? 'día' : 'días' }}
                            </span>

                        @elseif($diasRestantes <= 30)

                            <span class="inline-flex rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-400">
                                {{ $diasRestantes === 0
                                    ? 'Vence hoy'
                                    : 'Vence en ' . $diasRestantes . ' días' }}
                            </span>

                        @else

                            <span class="inline-flex rounded-full bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                Vigente
                            </span>

                        @endif

                    </div>
                </div>

            </div>

        </div>

        {{-- ========================================
            RESUMEN DE MOVIMIENTOS
        ======================================== --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- ENTRADAS --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <p class="text-sm font-medium text-zinc-400">
                    Total Entradas
                </p>

                <p class="mt-3 text-3xl font-bold text-green-400">
                    +{{ number_format($resumen['entradas']) }}
                </p>

                <p class="mt-2 text-xs text-zinc-500">
                    Unidades ingresadas
                </p>

            </div>

            {{-- SALIDAS --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <p class="text-sm font-medium text-zinc-400">
                    Total Salidas
                </p>

                <p class="mt-3 text-3xl font-bold text-red-400">
                    {{ $resumen['salidas'] > 0 ? '-' : '' }}{{ number_format($resumen['salidas']) }}
                </p>

                <p class="mt-2 text-xs text-zinc-500">
                    Unidades retiradas
                </p>

            </div>

            {{-- AJUSTES DE ENTRADA --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <p class="text-sm font-medium text-zinc-400">
                    Ajustes de Entrada
                </p>

                <p class="mt-3 text-3xl font-bold text-blue-400">
                    +{{ number_format($resumen['ajustes_entrada']) }}
                </p>

                <p class="mt-2 text-xs text-zinc-500">
                    Unidades agregadas por ajuste
                </p>

            </div>

            {{-- AJUSTES DE SALIDA --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <p class="text-sm font-medium text-zinc-400">
                    Ajustes de Salida
                </p>

                <p class="mt-3 text-3xl font-bold text-orange-400">
                    -{{ number_format($resumen['ajustes_salida']) }}
                </p>

                <p class="mt-2 text-xs text-zinc-500">
                    Unidades retiradas por ajuste
                </p>

            </div>

        </div>

        {{-- ========================================
            FILTROS DEL HISTORIAL
        ======================================== --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

            <div class="mb-5">
                <h2 class="font-semibold text-white">
                    Filtrar Movimientos
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Consulte los movimientos por tipo y rango de fechas.
                </p>
            </div>

            <form
                method="GET"
                action="{{ route('admin.inventario.historial', $lote->id) }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4"
            >

                {{-- TIPO DE MOVIMIENTO --}}
                <div>
                    <label
                        for="filtroTipo"
                        class="mb-2 block text-sm font-medium text-white"
                    >
                        Tipo de Movimiento
                    </label>

                    <select
                        name="tipo"
                        id="filtroTipo"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                    >
                        <option value="">
                            Todos los movimientos
                        </option>

                        <option value="Entrada"
                            @selected(request('tipo') === 'Entrada')>
                            Entrada
                        </option>

                        <option value="Salida"
                            @selected(request('tipo') === 'Salida')>
                            Salida
                        </option>

                        <option value="Ajuste Entrada"
                            @selected(request('tipo') === 'Ajuste Entrada')>
                            Ajuste Entrada
                        </option>

                        <option value="Ajuste Salida"
                            @selected(request('tipo') === 'Ajuste Salida')>
                            Ajuste Salida
                        </option>
                    </select>

                    @error('tipo')
                        <p class="mt-1 text-xs text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- FECHA DESDE --}}
                <div>
                    <label
                        for="fechaDesde"
                        class="mb-2 block text-sm font-medium text-white"
                    >
                        Fecha Desde
                    </label>

                    <input
                        type="date"
                        name="fecha_desde"
                        id="fechaDesde"
                        value="{{ request('fecha_desde') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                    >

                    @error('fecha_desde')
                        <p class="mt-1 text-xs text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- FECHA HASTA --}}
                <div>
                    <label
                        for="fechaHasta"
                        class="mb-2 block text-sm font-medium text-white"
                    >
                        Fecha Hasta
                    </label>

                    <input
                        type="date"
                        name="fecha_hasta"
                        id="fechaHasta"
                        value="{{ request('fecha_hasta') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                    >

                    @error('fecha_hasta')
                        <p class="mt-1 text-xs text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- BOTONES --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-transparent">
                        Acciones
                    </label>

                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="flex-1 rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            Buscar
                        </button>

                        <a
                            href="{{ route('admin.inventario.historial', $lote->id) }}"
                            class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700"
                        >
                            Limpiar
                        </a>

                    </div>
                </div>

            </form>

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
                                // ========================================
                                // CONFIGURACIÓN VISUAL DE MOVIMIENTOS
                                // ========================================

                                $entrada = in_array(
                                    $movimiento->tipo,
                                    ['Entrada', 'Ajuste Entrada'],
                                    true
                                );

                                $coloresMovimiento = [
                                    'Entrada' => 'bg-green-950 text-green-400',
                                    'Salida' => 'bg-red-950 text-red-400',
                                    'Ajuste Entrada' => 'bg-blue-950 text-blue-400',
                                    'Ajuste Salida' => 'bg-orange-950 text-orange-400',
                                ];

                                $colorMovimiento = $coloresMovimiento[$movimiento->tipo]
                                    ?? 'bg-zinc-800 text-zinc-300';
                            @endphp

                            <tr class="hover:bg-zinc-800/50">

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $movimientos->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-zinc-300">
                                    {{ $movimiento->created_at?->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-4 py-4">

                                    <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $colorMovimiento }}">
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
