<x-layouts::app :title="'Gestión de Productos'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Gestión de Productos
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Administración de productos del Sistema Farmacia.
                </p>
            </div>

            @can('productos.crear')
                <a href="{{ route('admin.productos.create') }}"
                   class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    + Crear nuevo
                </a>
            @endcan
        </div>

        {{-- FILTROS --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.productos.index', ['filtro' => 'activos']) }}"
               class="rounded-lg px-5 py-3 text-sm font-semibold transition
               {{ $filtro === 'activos'
                    ? 'bg-blue-600 text-white'
                    : 'border border-zinc-700 bg-zinc-900 text-white hover:bg-zinc-800' }}">
                Productos activos
            </a>

            <a href="{{ route('admin.productos.index', ['filtro' => 'eliminados']) }}"
               class="rounded-lg px-5 py-3 text-sm font-semibold transition
               {{ $filtro === 'eliminados'
                    ? 'bg-red-600 text-white'
                    : 'border border-zinc-700 bg-zinc-900 text-white hover:bg-zinc-800' }}">
                Productos eliminados
            </a>
        </div>

        {{-- BUSCADOR --}}
        <form method="GET"
              action="{{ route('admin.productos.index') }}"
              class="flex flex-wrap gap-3 rounded-xl border border-zinc-700 bg-zinc-900 p-4">

            <input type="hidden" name="filtro" value="{{ $filtro }}">

            <input type="text"
                   name="buscar"
                   value="{{ $buscar }}"
                   placeholder="Buscar por código, nombre, marca o laboratorio..."
                   class="min-w-60 flex-1 rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none focus:border-blue-500">

            <button type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                Buscar
            </button>

            <a href="{{ route('admin.productos.index', ['filtro' => $filtro]) }}"
               class="rounded-lg border border-zinc-700 bg-zinc-950 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-800">
                Limpiar
            </a>
        </form>

        {{-- TABLA --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-700 px-5 py-4">
                <h2 class="font-semibold text-white">
                    {{ $filtro === 'eliminados'
                        ? 'Productos Eliminados'
                        : 'Listado de Productos' }}
                </h2>

                <span class="text-xs text-zinc-400">
                    Total: {{ $productos->total() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1150px] text-left text-sm">
                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">
                        <tr>
                            <th class="px-4 py-4">Nro.</th>
                            <th class="px-4 py-4">Código</th>
                            <th class="px-4 py-4">Producto</th>
                            <th class="px-4 py-4">Categoría</th>
                            <th class="px-4 py-4">Laboratorio</th>
                            <th class="px-4 py-4">Receta</th>
                            <th class="px-4 py-4">Estado</th>
                            <th class="px-4 py-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">
                        @forelse ($productos as $producto)
                            <tr class="hover:bg-zinc-800/60">
                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $productos->firstItem() + $loop->index }}
                                </td>

                                <td class="px-4 py-4 font-semibold text-blue-400">
                                    {{ $producto->codigo }}
                                </td>

                                <td class="px-4 py-4">
                                    <div class="font-semibold text-white">
                                        {{ $producto->nombre }}
                                    </div>

                                    @if ($producto->nombre_generico)
                                        <div class="mt-1 text-xs text-zinc-400">
                                            {{ $producto->nombre_generico }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $producto->categoria?->nombre ?? 'Sin categoría' }}
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $producto->laboratorio ?: '—' }}
                                </td>

                                <td class="px-4 py-4">
                                    @if ($producto->requiere_receta)
                                        <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-400">
                                            Sí
                                        </span>
                                    @else
                                        <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs text-zinc-300">
                                            No
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @if ($producto->trashed())
                                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-400">
                                            Eliminado
                                        </span>
                                    @elseif ($producto->estado === 'Activo')
                                        <span class="rounded-full bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                            Activo
                                        </span>
                                    @else
                                        <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs text-zinc-300">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">

                                        @if ($filtro === 'eliminados')
                                            @can('productos.restaurar')
                                                <form method="POST"
                                                      action="{{ route('admin.productos.restaurar', $producto->id) }}"
                                                      class="form-restaurar">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="button"
                                                            data-nombre="{{ $producto->nombre }}"
                                                            class="btn-restaurar rounded-md bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">
                                                        Restaurar
                                                    </button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('productos.ver')
                                                <a href="{{ route('admin.productos.show', $producto->id) }}"
                                                   class="rounded-md bg-zinc-700 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-600">
                                                    Ver
                                                </a>
                                            @endcan

                                            @can('productos.editar')
                                                <a href="{{ route('admin.productos.edit', $producto->id) }}"
                                                   class="rounded-md bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('productos.eliminar')
                                                <form method="POST"
                                                      action="{{ route('admin.productos.destroy', $producto->id) }}"
                                                      class="form-eliminar">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="button"
                                                            data-nombre="{{ $producto->nombre }}"
                                                            class="btn-eliminar rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    class="px-5 py-12 text-center text-zinc-400">
                                    No se encontraron productos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-700 px-5 py-4">
                <div class="mb-3 text-xs text-zinc-400">
                    Mostrando {{ $productos->firstItem() ?? 0 }}
                    a {{ $productos->lastItem() ?? 0 }}
                    de {{ $productos->total() }} productos
                </div>

                {{ $productos->links() }}
            </div>
        </div>
    </div>

    {{-- CONFIRMACIONES SWEETALERT2 --}}
    <script>
        function inicializarAccionesProductos() {
            if (typeof Swal === 'undefined') return;

            document.querySelectorAll('.btn-eliminar').forEach(boton => {
                if (boton.dataset.inicializado) return;
                boton.dataset.inicializado = '1';

                boton.addEventListener('click', function () {
                    const formulario = this.closest('form');
                    const nombre = this.dataset.nombre;

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: `¿Deseas eliminar el producto "${nombre}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#52525b',
                        background: '#18181b',
                        color: '#ffffff'
                    }).then(resultado => {
                        if (resultado.isConfirmed) {
                            formulario.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('.btn-restaurar').forEach(boton => {
                if (boton.dataset.inicializado) return;
                boton.dataset.inicializado = '1';

                boton.addEventListener('click', function () {
                    const formulario = this.closest('form');
                    const nombre = this.dataset.nombre;

                    Swal.fire({
                        title: '¿Restaurar producto?',
                        text: `¿Deseas restaurar el producto "${nombre}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#059669',
                        cancelButtonColor: '#52525b',
                        background: '#18181b',
                        color: '#ffffff'
                    }).then(resultado => {
                        if (resultado.isConfirmed) {
                            formulario.submit();
                        }
                    });
                });
            });
        }

        document.addEventListener(
            'DOMContentLoaded',
            inicializarAccionesProductos
        );

        document.addEventListener(
            'livewire:navigated',
            inicializarAccionesProductos
        );

        inicializarAccionesProductos();
    </script>

</x-layouts::app>
