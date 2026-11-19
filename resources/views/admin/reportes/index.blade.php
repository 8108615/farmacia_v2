
<x-layouts::app :title="'Gestión de Reportes'">

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div>
            <h1 class="text-2xl font-bold text-white">
                Gestión de Reportes
            </h1>

            <p class="mt-1 text-sm text-zinc-400">
                Consulta, analiza y genera reportes de las operaciones
                del Sistema Farmacia.
            </p>
        </div>

        {{-- INFORMACIÓN GENERAL --}}
        <div class="rounded-xl border border-blue-800 bg-blue-950/30 p-4">
            <div class="flex items-start gap-3">
                <flux:icon.information-circle
                    class="mt-0.5 size-5 shrink-0 text-blue-400"
                />

                <p class="text-sm text-blue-300">
                    Selecciona el tipo de reporte que deseas consultar.
                    Cada sección contará con filtros, información
                    detallada y opciones de exportación.
                </p>
            </div>
        </div>

        {{-- ENCABEZADO DE SECCIONES --}}
        <div>
            <h2 class="text-lg font-semibold text-white">
                Reportes Disponibles
            </h2>

            <p class="mt-1 text-sm text-zinc-400">
                Selecciona una categoría para consultar sus estadísticas.
            </p>
        </div>

        {{-- TARJETAS DE REPORTES --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

            @php
                $reportes = [
                    [
                        'titulo' => 'Reporte de Ventas',
                        'descripcion' => 'Consulta ventas por fechas, estados, descuentos y montos registrados.',
                        'icono' => 'banknotes',
                        'color' => 'blue',
                        'ruta' => 'admin.reportes.ventas',
                    ],
                    [
                        'titulo' => 'Reporte de Compras',
                        'descripcion' => 'Analiza las compras realizadas, proveedores y montos de adquisición.',
                        'icono' => 'shopping-cart',
                        'color' => 'emerald',
                    ],
                    [
                        'titulo' => 'Reporte de Inventario',
                        'descripcion' => 'Consulta existencias disponibles por medicamento y lote.',
                        'icono' => 'cube',
                        'color' => 'violet',
                    ],
                    [
                        'titulo' => 'Reporte de Vencimientos',
                        'descripcion' => 'Identifica medicamentos vencidos y próximos a vencer.',
                        'icono' => 'calendar-days',
                        'color' => 'amber',
                    ],
                    [
                        'titulo' => 'Movimientos de Inventario',
                        'descripcion' => 'Consulta entradas, salidas y ajustes realizados en el inventario.',
                        'icono' => 'arrows-right-left',
                        'color' => 'cyan',
                    ],
                    [
                        'titulo' => 'Productos Más Vendidos',
                        'descripcion' => 'Analiza los medicamentos con mayor cantidad de ventas.',
                        'icono' => 'chart-bar',
                        'color' => 'rose',
                    ],
                ];
            @endphp

            @foreach ($reportes as $reporte)

                <div class="flex flex-col justify-between rounded-xl border border-zinc-700 bg-zinc-900 p-6 transition hover:border-zinc-500">

                    <div>
                        <div class="mb-5 flex size-12 items-center justify-center rounded-xl bg-zinc-800 text-blue-400">
                            @switch($reporte['icono'])
                                @case('banknotes')
                                    <flux:icon.banknotes class="size-6" />
                                    @break

                                @case('shopping-cart')
                                    <flux:icon.shopping-cart class="size-6" />
                                    @break

                                @case('cube')
                                    <flux:icon.cube class="size-6" />
                                    @break

                                @case('calendar-days')
                                    <flux:icon.calendar-days class="size-6" />
                                    @break

                                @case('arrows-right-left')
                                    <flux:icon.arrows-right-left class="size-6" />
                                    @break

                                @case('chart-bar')
                                    <flux:icon.chart-bar class="size-6" />
                                    @break
                            @endswitch
                        </div>

                        <h3 class="text-base font-bold text-white">
                            {{ $reporte['titulo'] }}
                        </h3>

                        <p class="mt-3 min-h-16 text-sm leading-6 text-zinc-400">
                            {{ $reporte['descripcion'] }}
                        </p>
                    </div>

                    <div class="mt-6 border-t border-zinc-700 pt-5">

                        <div class="flex items-center justify-between gap-3">

                            @if (isset($reporte['ruta']))

                                <span class="inline-flex items-center rounded-full bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                    Disponible
                                </span>

                                <a href="{{ route($reporte['ruta']) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                    Ver Reporte
                                </a>

                            @else

                                <span class="inline-flex items-center rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-400">
                                    Próximamente
                                </span>

                                <button type="button"
                                        disabled
                                        class="cursor-not-allowed rounded-lg bg-zinc-700 px-4 py-2 text-xs font-semibold text-zinc-400 opacity-70">
                                    Ver Reporte
                                </button>

                            @endif

                        </div>
                    </div>

                </div>

            @endforeach

        </div>

    </div>

</x-layouts::app>
