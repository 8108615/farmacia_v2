<x-layouts::app :title="'Productos Más Vendidos'">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Productos Más Vendidos</h1>
                <p class="mt-1 text-sm text-zinc-400">Ranking por unidades de ventas confirmadas.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reportes.index') }}"
                   class="rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                    Volver a Reportes
                </a>
                <a href="{{ route('admin.reportes.productos-vendidos.pdf', $filtros) }}"
                   class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Exportar PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.reportes.productos-vendidos') }}"
              class="rounded-xl border border-zinc-700 bg-zinc-950 p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label for="categoria_id" class="mb-2 block text-xs font-semibold text-zinc-300">Categoría</label>
                    <select id="categoria_id" name="categoria_id" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <option value="">Todas</option>
                        @foreach ($categorias as $id => $nombre)
                            <option value="{{ $id }}" @selected((string) ($filtros['categoria_id'] ?? '') === (string) $id)>{{ $nombre }}</option>
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
                <a href="{{ route('admin.reportes.productos-vendidos') }}"
                   class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Limpiar</a>
            </div>
        </form>

        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($indicadores as $etiqueta => $valor)
                <div class="rounded-xl border border-zinc-700 bg-zinc-950 p-4">
                    <dt class="text-sm text-zinc-400">{{ $etiqueta }}</dt>
                    <dd class="mt-1 text-2xl font-bold text-white">{{ number_format($valor, 0, ',', '.') }}</dd>
                </div>
            @endforeach
        </dl>

        <section class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-950">
            <h2 class="border-b border-zinc-700 px-5 py-4 font-semibold text-white">Ranking por producto</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-300">
                    @include('admin.reportes.partials.productos-vendidos')
                </table>
            </div>
            @if ($productos->hasPages())
                <div class="border-t border-zinc-700 px-5 py-4">{{ $productos->links() }}</div>
            @endif
        </section>
    </div>
</x-layouts::app>
