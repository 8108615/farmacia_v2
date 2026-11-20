<x-layouts::app :title="'Reporte de Vencimientos'">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Reporte de Vencimientos</h1>
                <p class="mt-1 text-sm text-zinc-400">Lotes vencidos, próximos a vencer y vigentes de productos activos.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reportes.index') }}"
                   class="rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                    Volver a Reportes
                </a>
                <a href="{{ route('admin.reportes.vencimientos.pdf', $filtros) }}"
                   class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                    Exportar PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.reportes.vencimientos') }}"
              class="rounded-xl border border-zinc-700 bg-zinc-950 p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="buscar" class="mb-2 block text-xs font-semibold text-zinc-300">Producto, código o lote</label>
                    <input type="search" id="buscar" name="buscar" maxlength="200" value="{{ $filtros['buscar'] ?? '' }}"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                </div>
                <div>
                    <label for="categoria_id" class="mb-2 block text-xs font-semibold text-zinc-300">Categoría</label>
                    <select id="categoria_id" name="categoria_id"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <option value="">Todas</option>
                        @foreach ($categorias as $id => $nombre)
                            <option value="{{ $id }}" @selected((string) ($filtros['categoria_id'] ?? '') === (string) $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="estado" class="mb-2 block text-xs font-semibold text-zinc-300">Estado del lote</label>
                    <select id="estado" name="estado"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <option value="">Todos</option>
                        @foreach (['Activo', 'Inactivo'] as $estado)
                            <option value="{{ $estado }}" @selected(($filtros['estado'] ?? null) === $estado)>{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="vencimiento" class="mb-2 block text-xs font-semibold text-zinc-300">Estado de vencimiento</label>
                    <select id="vencimiento" name="vencimiento"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white">
                        <option value="">Todos</option>
                        @foreach ($opciones as $valor => $opcion)
                            <option value="{{ $valor }}" @selected(($filtros['vencimiento'] ?? null) === $valor)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($errors->any())
                <ul class="mt-4 text-sm text-red-400" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4 flex flex-wrap gap-3">
                <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Filtrar</button>
                <a href="{{ route('admin.reportes.vencimientos') }}"
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
            <h2 class="border-b border-zinc-700 px-5 py-4 font-semibold text-white">Lotes por fecha de vencimiento</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-300">
                    @include('admin.reportes.partials.vencimientos')
                </table>
            </div>
            @if ($lotes->hasPages())
                <div class="border-t border-zinc-700 px-5 py-4">{{ $lotes->links() }}</div>
            @endif
        </section>
    </div>
</x-layouts::app>
