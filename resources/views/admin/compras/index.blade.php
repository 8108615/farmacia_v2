<x-layouts::app :title="'Gestión de Compras'">

    <div class="space-y-6">

        {{-- ========================================
            ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100">
                    Gestión de Compras
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Administra las compras de medicamentos y productos a proveedores.
                </p>
            </div>

            @can('compras.crear')
                <a
                    href="{{ route('admin.compras.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Nueva Compra
                </a>
            @endcan
        </div>

        {{-- ========================================
            FILTROS DE BÚSQUEDA
        ======================================== --}}
        <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-5">

            <h2 class="mb-5 text-base font-semibold text-zinc-200">
                Buscar Compras
            </h2>

            <form
                method="GET"
                action="{{ route('admin.compras.index') }}"
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
                        placeholder="Código, número de factura o proveedor"
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
                        href="{{ route('admin.compras.index') }}"
                        class="flex w-full items-center justify-center rounded-lg border border-zinc-700 px-3 py-2.5 text-sm text-zinc-300 transition hover:bg-zinc-800"
                    >
                        Limpiar
                    </a>
                </div>

            </form>
        </div>

        {{-- ========================================
            TABLA DE COMPRAS
        ======================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-950">

            <div class="flex items-center justify-between border-b border-zinc-800 px-5 py-4">
                <h2 class="font-semibold text-zinc-200">
                    Compras Registradas
                </h2>

                <span class="text-sm text-zinc-400">
                    Total: {{ $compras->total() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-zinc-900 text-xs uppercase text-zinc-400">
                        <tr>
                            <th class="px-5 py-4">Código</th>
                            <th class="px-5 py-4">Proveedor</th>
                            <th class="px-5 py-4">Fecha</th>
                            <th class="px-5 py-4">Factura</th>
                            <th class="px-5 py-4 text-center">Productos</th>
                            <th class="px-5 py-4 text-right">Total (Bs)</th>
                            <th class="px-5 py-4 text-center">Estado</th>
                            <th class="px-5 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse ($compras as $compra)

                            <tr class="transition hover:bg-zinc-900/70">

                                {{-- CÓDIGO --}}
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-blue-400">
                                    {{ $compra->codigo }}
                                </td>

                                {{-- PROVEEDOR --}}
                                <td class="px-5 py-4">
                                    <div class="font-medium text-zinc-200">
                                        {{ $compra->proveedor?->razon_social ?? 'Proveedor no disponible' }}
                                    </div>
                                </td>

                                {{-- FECHA --}}
                                <td class="whitespace-nowrap px-5 py-4 text-zinc-300">
                                    {{ $compra->fecha_compra?->format('d/m/Y') ?? '—' }}
                                </td>

                                {{-- FACTURA --}}
                                <td class="px-5 py-4 text-zinc-300">
                                    {{ $compra->numero_factura ?: 'Sin factura' }}
                                </td>

                                {{-- CANTIDAD DE PRODUCTOS --}}
                                <td class="px-5 py-4 text-center text-zinc-300">
                                    {{ $compra->detalles_count }}
                                </td>

                                {{-- TOTAL --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-zinc-100">
                                    {{ number_format((float) $compra->total, 2, ',', '.') }}
                                </td>

                                {{-- ESTADO --}}
                                <td class="px-5 py-4 text-center">

                                    @if ($compra->estado === 'Borrador')

                                        <span class="inline-flex rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-400">
                                            Borrador
                                        </span>

                                    @elseif ($compra->estado === 'Confirmada')

                                        <span class="inline-flex rounded-full bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">
                                            Confirmada
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">
                                            Anulada
                                        </span>

                                    @endif

                                </td>

                                {{-- ========================================
                                    ACCIONES
                                ======================================== --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-center gap-2 whitespace-nowrap">

                                        {{-- VER COMPRA --}}
                                        @can('compras.ver')
                                            <a
                                                href="{{ route('admin.compras.show', $compra) }}"
                                                class="rounded-lg bg-zinc-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-zinc-600"
                                            >
                                                Ver
                                            </a>
                                        @endcan

                                        {{-- ACCIONES EXCLUSIVAS PARA BORRADOR --}}
                                        @if ($compra->estado === 'Borrador')

                                            {{-- EDITAR COMPRA --}}
                                            @can('compras.editar')
                                                <a
                                                    href="{{ route('admin.compras.edit', $compra) }}"
                                                    class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700"
                                                >
                                                    Editar
                                                </a>

                                                {{-- CONFIRMAR COMPRA --}}
                                                <form
                                                    id="form-confirmar-compra-{{ $compra->id }}"
                                                    action="{{ route('admin.compras.confirmar', $compra) }}"
                                                    method="POST"
                                                    class="inline-flex"
                                                >
                                                    @csrf

                                                    <button
                                                        type="button"
                                                        onclick="confirmarCompra(
                                                            {{ $compra->id }},
                                                            @js($compra->codigo)
                                                        )"
                                                        class="rounded-lg bg-violet-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-violet-700"
                                                    >
                                                        Confirmar
                                                    </button>
                                                </form>
                                            @endcan

                                            {{-- ANULAR COMPRA --}}
                                            @can('compras.anular')
                                                <form
                                                    id="form-anular-compra-{{ $compra->id }}"
                                                    action="{{ route('admin.compras.destroy', $compra) }}"
                                                    method="POST"
                                                    class="inline-flex"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="button"
                                                        onclick="anularCompra(
                                                            {{ $compra->id }},
                                                            @js($compra->codigo)
                                                        )"
                                                        class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700"
                                                    >
                                                        Anular
                                                    </button>
                                                </form>
                                            @endcan

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center">

                                    <div class="text-base font-medium text-zinc-300">
                                        No se encontraron compras registradas.
                                    </div>

                                    <p class="mt-2 text-sm text-zinc-500">
                                        Puedes registrar una nueva compra o modificar los filtros de búsqueda.
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
            @if ($compras->hasPages())
                <div class="border-t border-zinc-800 px-5 py-4">
                    {{ $compras->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- ========================================
        SWEETALERT2: CONFIRMAR COMPRA
    ======================================== --}}
    <script>
        function confirmarCompra(id, codigo) {

            if (typeof Swal === 'undefined') {
                alert('No se pudo cargar la ventana de confirmación.');
                return;
            }

            Swal.fire({
                title: '¿Confirmar compra?',
                html: `
                    <p>
                        Estás por confirmar la compra
                        <strong id="codigo-compra-confirmar"></strong>.
                    </p>

                    <p style="margin-top: 12px;">
                        Los productos ingresarán al inventario
                        y se actualizarán las existencias
                        de sus respectivos lotes.
                    </p>

                    <p style="margin-top: 12px; color: #fbbf24;">
                        Una vez confirmada, esta compra
                        no podrá editarse.
                    </p>
                `,
                icon: 'warning',
                background: '#18181b',
                color: '#f4f4f5',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar compra',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#52525b',
                reverseButtons: true,

                didOpen: () => {
                    const elemento = document.getElementById('codigo-compra-confirmar');

                    if (elemento) {
                        elemento.textContent = codigo;
                    }
                }

            }).then((result) => {

                if (result.isConfirmed) {

                    const formulario = document.getElementById(
                        'form-confirmar-compra-' + id
                    );

                    if (formulario) {
                        formulario.submit();
                    }
                }

            });
        }

        // ========================================
        // SWEETALERT2: ANULAR COMPRA
        // ========================================
        function anularCompra(id, codigo) {

            if (typeof Swal === 'undefined') {
                alert('No se pudo cargar la ventana de confirmación.');
                return;
            }

            Swal.fire({
                title: '¿Anular compra?',
                html: `
                    <p>
                        Estás por anular la compra
                        <strong id="codigo-compra-anular"></strong>.
                    </p>

                    <p style="margin-top: 12px;">
                        La compra quedará registrada
                        con estado <strong>Anulada</strong>.
                    </p>

                    <p style="margin-top: 12px; color: #f87171;">
                        Esta operación no modificará el inventario.
                        La compra ya no podrá editarse ni confirmarse.
                    </p>
                `,
                icon: 'warning',
                background: '#18181b',
                color: '#f4f4f5',
                showCancelButton: true,
                confirmButtonText: 'Sí, anular compra',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#52525b',
                reverseButtons: true,

                didOpen: () => {
                    const elemento = document.getElementById('codigo-compra-anular');

                    if (elemento) {
                        elemento.textContent = codigo;
                    }
                }

            }).then((result) => {

                if (result.isConfirmed) {

                    const formulario = document.getElementById(
                        'form-anular-compra-' + id
                    );

                    if (formulario) {
                        formulario.submit();
                    }
                }

            });
        }
    </script>

</x-layouts::app>
