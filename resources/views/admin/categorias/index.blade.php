<x-layouts::app :title="'Gestión de Categorías'">

    <div class="flex flex-col gap-6">

        {{-- ========================================
             ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Gestión de Categorías
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Administración de categorías del Sistema Farmacia.
                </p>
            </div>

            @can('categorias.crear')
                <a href="{{ route('admin.categorias.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <span class="text-lg">+</span>
                    Crear nueva
                </a>
            @endcan

        </div>

        {{-- ========================================
             FILTROS DE CATEGORÍAS
        ======================================== --}}
        <div class="flex flex-wrap gap-3">

            <a href="{{ route('admin.categorias.index', ['filtro' => 'activos']) }}"
               class="rounded-lg border px-5 py-2.5 text-sm font-semibold transition
               {{ $filtro === 'activos'
                    ? 'border-blue-600 bg-blue-600 text-white'
                    : 'border-zinc-700 bg-zinc-900 text-zinc-300 hover:bg-zinc-800' }}">
                Categorías activas
            </a>

            @can('categorias.restaurar')
                <a href="{{ route('admin.categorias.index', ['filtro' => 'eliminados']) }}"
                   class="rounded-lg border px-5 py-2.5 text-sm font-semibold transition
                   {{ $filtro === 'eliminados'
                        ? 'border-red-600 bg-red-600 text-white'
                        : 'border-zinc-700 bg-zinc-900 text-zinc-300 hover:bg-zinc-800' }}">
                    Categorías eliminadas
                </a>
            @endcan

        </div>

        {{-- ========================================
             BUSCADOR
        ======================================== --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-4">

            <form method="GET"
                  action="{{ route('admin.categorias.index') }}"
                  class="flex flex-col gap-3 sm:flex-row">

                <input type="hidden"
                       name="filtro"
                       value="{{ $filtro }}">

                <input type="text"
                       name="buscar"
                       value="{{ $buscar }}"
                       placeholder="Buscar por nombre o descripción..."
                       class="w-full flex-1 rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-zinc-500 focus:border-blue-500">

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Buscar
                </button>

                <a href="{{ route('admin.categorias.index', ['filtro' => $filtro]) }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2.5 text-center text-sm font-medium text-white transition hover:bg-zinc-800">
                    Limpiar
                </a>

            </form>

        </div>

        {{-- ========================================
             TABLA DE CATEGORÍAS
        ======================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-700 px-5 py-4">

                <h2 class="text-base font-semibold text-white">
                    {{ $filtro === 'eliminados'
                        ? 'Categorías Eliminadas'
                        : 'Listado de Categorías' }}
                </h2>

                <span class="text-xs text-zinc-400">
                    Total: {{ $categorias->total() }}
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[950px] text-left text-sm">

                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">

                        <tr>
                            <th class="px-5 py-4">Nro.</th>
                            <th class="px-5 py-4">Código</th>
                            <th class="px-5 py-4">Nombre</th>
                            <th class="px-5 py-4">Descripción</th>
                            <th class="px-5 py-4">Estado</th>
                            <th class="px-5 py-4">Fecha de Registro</th>
                            <th class="px-5 py-4 text-center">Acciones</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($categorias as $categoria)

                            <tr class="transition hover:bg-zinc-800/50">

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $categorias->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4 font-medium text-blue-400">
                                    {{ str_pad($categoria->id, 4, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-5 py-4 font-semibold text-white">
                                    {{ $categoria->nombre }}
                                </td>

                                <td class="max-w-xs px-5 py-4 text-zinc-300">
                                    <span class="block truncate"
                                          title="{{ $categoria->descripcion }}">
                                        {{ $categoria->descripcion ?: 'Sin descripción' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">

                                    @if ($filtro === 'eliminados')
                                        <span class="rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">
                                            Eliminada
                                        </span>
                                    @elseif ($categoria->estado === 'Activo')
                                        <span class="rounded-full border border-green-500/30 bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">
                                            Activo
                                        </span>
                                    @else
                                        <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-400">
                                            Inactivo
                                        </span>
                                    @endif

                                </td>

                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $categoria->created_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                                </td>

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        @if ($filtro === 'eliminados')

                                            @can('categorias.restaurar')
                                                <form method="POST"
                                                      action="{{ route('admin.categorias.restaurar', $categoria->id) }}"
                                                      class="form-restaurar">

                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden"
                                                           name="nombre_categoria"
                                                           value="{{ $categoria->nombre }}">

                                                    <button type="submit"
                                                            class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                                        Restaurar
                                                    </button>

                                                </form>
                                            @endcan

                                        @else

                                            @can('categorias.ver')
                                                <a href="{{ route('admin.categorias.show', $categoria->id) }}"
                                                   class="rounded-md bg-zinc-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-zinc-600">
                                                    Ver
                                                </a>
                                            @endcan

                                            @can('categorias.editar')
                                                <a href="{{ route('admin.categorias.edit', $categoria->id) }}"
                                                   class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('categorias.eliminar')
                                                <form method="POST"
                                                      action="{{ route('admin.categorias.destroy', $categoria->id) }}"
                                                      class="form-eliminar">

                                                    @csrf
                                                    @method('DELETE')

                                                    <input type="hidden"
                                                           name="nombre_categoria"
                                                           value="{{ $categoria->nombre }}">

                                                    <button type="submit"
                                                            class="rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
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
                                <td colspan="7"
                                    class="px-5 py-14 text-center text-sm text-zinc-400">

                                    {{ $filtro === 'eliminados'
                                        ? 'No se encontraron categorías eliminadas.'
                                        : 'No se encontraron categorías registradas.' }}

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- ====================================
                 PAGINACIÓN
            ==================================== --}}
            <div class="border-t border-zinc-700 px-5 py-4">

                <div class="flex flex-col gap-4">

                    <p class="text-xs text-zinc-400">
                        Mostrando {{ $categorias->firstItem() ?? 0 }}
                        a {{ $categorias->lastItem() ?? 0 }}
                        de {{ $categorias->total() }} categorías
                    </p>

                    @if ($categorias->hasPages())
                        {{ $categorias->links() }}
                    @endif

                </div>

            </div>

        </div>

    </div>

    {{-- ========================================
         SWEETALERT2
    ======================================== --}}
    @once
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endonce

    <script>
        function inicializarCategorias() {

            if (typeof Swal === 'undefined') {
                return;
            }

            // Evitar registrar eventos duplicados
            if (document.body.dataset.categoriasEventos === '1') {
                return;
            }

            document.body.dataset.categoriasEventos = '1';

            document.addEventListener('submit', function (evento) {

                const formulario = evento.target;

                if (!formulario.matches('.form-eliminar, .form-restaurar')) {
                    return;
                }

                if (formulario.dataset.confirmado === '1') {
                    return;
                }

                evento.preventDefault();

                const eliminar = formulario.classList.contains('form-eliminar');

                const nombre = formulario.querySelector(
                    '[name="nombre_categoria"]'
                )?.value || 'esta categoría';

                Swal.fire({
                    title: eliminar
                        ? '¿Estás seguro?'
                        : '¿Restaurar categoría?',

                    text: eliminar
                        ? `¿Deseas eliminar la categoría "${nombre}"?`
                        : `¿Deseas restaurar la categoría "${nombre}"?`,

                    icon: eliminar ? 'warning' : 'question',

                    showCancelButton: true,

                    confirmButtonText: eliminar
                        ? 'Sí, eliminar'
                        : 'Sí, restaurar',

                    cancelButtonText: 'Cancelar',

                    confirmButtonColor: eliminar
                        ? '#dc2626'
                        : '#059669',

                    cancelButtonColor: '#52525b',

                    background: '#18181b',
                    color: '#ffffff',

                    reverseButtons: true

                }).then((resultado) => {

                    if (resultado.isConfirmed) {
                        formulario.dataset.confirmado = '1';
                        formulario.requestSubmit();
                    }

                });

            });

        }

        document.addEventListener(
            'DOMContentLoaded',
            inicializarCategorias
        );

        document.addEventListener(
            'livewire:navigated',
            inicializarCategorias
        );

        inicializarCategorias();

        // Mensajes de sesión de Laravel
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¡Operación exitosa!',
                        text: @json(session('success')),
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#2563eb',
                        background: '#18181b',
                        color: '#ffffff'
                    });
                }
            });
        @endif

        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'No se pudo completar la operación',
                        text: @json(session('error')),
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#dc2626',
                        background: '#18181b',
                        color: '#ffffff'
                    });
                }
            });
        @endif

    </script>

</x-layouts::app>
