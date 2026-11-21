<x-layouts::app :title="'Movimientos de Inventario'">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Movimientos de Inventario</h1>
                <p class="mt-1 text-sm text-zinc-400">Consulta entradas, salidas y ajustes por producto y lote.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reportes.index') }}"
                   class="rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                    Volver a Reportes
                </a>
                <a href="{{ route('admin.reportes.movimientos.pdf', $filtros) }}"
                   class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Exportar PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.reportes.movimientos') }}"
              class="rounded-xl border border-zinc-700 bg-zinc-950 p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="buscar" class="mb-2 block text-xs font-semibold text-zinc-300">Producto, código o lote</label>
                    <input type="search" id="buscar" name="buscar" maxlength="200" value="{{ $filtros['buscar'] ?? '' }}"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                </div>
                <div>
                    <label for="tipo" class="mb-2 block text-xs font-semibold text-zinc-300">Tipo de movimiento</label>
                    <select id="tipo" name="tipo" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <option value="">Todos</option>
                        @foreach (array_keys($tipos) as $tipo)
                            <option value="{{ $tipo }}" @selected(($filtros['tipo'] ?? null) === $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                @foreach (['fecha_desde' => 'Desde', 'fecha_hasta' => 'Hasta'] as $campo => $etiqueta)
                    <div>
                        <label for="{{ $campo }}" class="mb-2 block text-xs font-semibold text-zinc-300">{{ $etiqueta }}</label>
                        <input type="date" id="{{ $campo }}" name="{{ $campo }}" value="{{ $filtros[$campo] ?? '' }}"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                    </div>
                @endforeach
            </div>

            @if ($errors->any())
                <ul class="mt-4 text-sm text-red-400" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4 flex flex-wrap gap-3">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Filtrar</button>
                <a href="{{ route('admin.reportes.movimientos') }}"
                   class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Limpiar</a>
            </div>
        </form>

        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($indicadores as $etiqueta => $valor)
                <div class="rounded-xl border border-zinc-700 bg-zinc-950 p-4">
                    <dt class="text-sm text-zinc-400">{{ $etiqueta }}</dt>
                    <dd class="mt-1 text-2xl font-bold text-white">{{ number_format($valor, 0, ',', '.') }}</dd>
                </div>
            @endforeach
        </dl>

        <section class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-950">
            <h2 class="border-b border-zinc-700 px-5 py-4 font-semibold text-white">Historial de movimientos</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-300">
                    @include('admin.reportes.partials.movimientos')
                </table>
            </div>
            @if ($movimientos->hasPages())
                <div class="border-t border-zinc-700 px-5 py-4">{{ $movimientos->links() }}</div>
            @endif
        </section>
    </div>
</x-layouts::app>
