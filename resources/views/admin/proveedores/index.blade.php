<x-layouts::app :title="__('Gestión de Proveedores')">

    <div class="flex flex-col gap-6 p-4 md:p-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Gestión de Proveedores
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Administra los proveedores registrados en Farmacia.
                </p>
            </div>

            @can('proveedores.crear')
                <a href="{{ route('admin.proveedores.create') }}"
                   class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    + Nuevo Proveedor
                </a>
            @endcan
        </div>

        {{-- FILTROS --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

            <h2 class="mb-4 font-semibold text-white">
                Buscar Proveedores
            </h2>

            <form method="GET"
                  action="{{ route('admin.proveedores.index') }}"
                  class="grid grid-cols-1 gap-4 md:grid-cols-4">

                <div>
                    <label class="mb-2 block text-sm text-zinc-300">
                        Buscar
                    </label>

                    <input type="text"
                           name="buscar"
                           value="{{ $buscar }}"
                           placeholder="Código, razón social o NIT"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm text-white focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-300">
                        Estado
                    </label>

                    <select name="estado"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm text-white">
                        <option value="todos" @selected($estado === 'todos')>
                            Todos los estados
                        </option>
                        <option value="Activo" @selected($estado === 'Activo')>
                            Activo
                        </option>
                        <option value="Inactivo" @selected($estado === 'Inactivo')>
                            Inactivo
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm text-zinc-300">
                        Registros
                    </label>

                    <select name="eliminados"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2.5 text-sm text-white">
                        <option value="no" @selected($eliminados === 'no')>
                            Proveedores vigentes
                        </option>
                        <option value="si" @selected($eliminados === 'si')>
                            Proveedores eliminados
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Buscar
                    </button>

                    <a href="{{ route('admin.proveedores.index') }}"
                       class="rounded-lg border border-zinc-600 px-4 py-2.5 text-sm text-white hover:bg-zinc-800">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- TABLA --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-700 px-5 py-4">
                <h2 class="font-semibold text-white">
                    Proveedores Registrados
                </h2>

                <span class="text-sm text-zinc-400">
                    Total: {{ $proveedores->total() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left text-sm">

                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">
                        <tr>
                            <th class="px-4 py-4">Código</th>
                            <th class="px-4 py-4">Razón Social</th>
                            <th class="px-4 py-4">NIT</th>
                            <th class="px-4 py-4">Contacto</th>
                            <th class="px-4 py-4">Celular</th>
                            <th class="px-4 py-4">Ciudad</th>
                            <th class="px-4 py-4">Estado</th>
                            <th class="px-4 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-800">

                        @forelse($proveedores as $proveedor)

                            <tr class="transition hover:bg-zinc-800/60">

                                <td class="px-4 py-4 font-semibold text-blue-400">
                                    {{ $proveedor->codigo }}
                                </td>

                                <td class="px-4 py-4">
                                    <div class="font-semibold text-white">
                                        {{ $proveedor->razon_social }}
                                    </div>

                                    @if($proveedor->nombre_comercial)
                                        <div class="mt-1 text-xs text-zinc-400">
                                            {{ $proveedor->nombre_comercial }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $proveedor->nit ?: '—' }}
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $proveedor->contacto ?: '—' }}
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $proveedor->celular ?: '—' }}
                                </td>

                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $proveedor->ciudad ?: '—' }}
                                </td>

                                <td class="px-4 py-4">

                                    @if($proveedor->trashed())
                                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-400">
                                            Eliminado
                                        </span>
                                    @elseif($proveedor->estado === 'Activo')
                                        <span class="rounded-full bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                            Activo
                                        </span>
                                    @else
                                        <span class="rounded-full bg-yellow-950 px-3 py-1 text-xs font-semibold text-yellow-400">
                                            Inactivo
                                        </span>
                                    @endif

                                </td>

                                <td class="px-4 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        @if($proveedor->trashed())

                                            @can('proveedores.restaurar')
                                                <form method="POST"
                                                    action="{{ route('admin.proveedores.restaurar', $proveedor->id) }}"
                                                    class="form-restaurar-proveedor">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                        Restaurar
                                                    </button>
                                                </form>
                                            @endcan

                                        @else

                                            @can('proveedores.ver')
                                                <a href="{{ route('admin.proveedores.show', $proveedor) }}"
                                                   class="rounded-lg bg-zinc-700 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-600">
                                                    Ver
                                                </a>
                                            @endcan

                                            @can('proveedores.editar')
                                                <a href="{{ route('admin.proveedores.edit', $proveedor) }}"
                                                   class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-green-700">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('proveedores.eliminar')
                                                <form method="POST"
                                                    action="{{ route('admin.proveedores.destroy', $proveedor) }}"
                                                    class="form-eliminar-proveedor">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="rounded-lg bg-red-700 px-3 py-2 text-xs font-semibold text-white hover:bg-red-600">
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
                                    class="px-4 py-12 text-center text-zinc-400">
                                    No se encontraron proveedores registrados
                                    con los filtros seleccionados.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            @if($proveedores->hasPages())
                <div class="border-t border-zinc-700 px-5 py-4">
                    {{ $proveedores->links() }}
                </div>
            @endif

        </div>

    </div>

    @once
        <script>
            function inicializarSweetAlertProveedores() {

                if (typeof Swal === 'undefined') {
                    console.error('SweetAlert2 no está cargado.');
                    return;
                }

                // ========================================
                // ELIMINAR PROVEEDOR
                // ========================================

                document.querySelectorAll('.form-eliminar-proveedor')
                    .forEach(form => {

                        if (form.dataset.swalInicializado === '1') return;

                        form.dataset.swalInicializado = '1';

                        form.addEventListener('submit', async function (event) {

                            event.preventDefault();

                            const resultado = await Swal.fire({
                                title: '¿Eliminar proveedor?',
                                text: 'El proveedor será enviado a los registros eliminados y podrá restaurarse posteriormente.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, eliminar',
                                cancelButtonText: 'Cancelar',
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#52525b',
                                background: '#18181b',
                                color: '#ffffff',
                                reverseButtons: true
                            });

                            if (resultado.isConfirmed) {
                                HTMLFormElement.prototype.submit.call(form);
                            }
                        });
                    });

                // ========================================
                // RESTAURAR PROVEEDOR
                // ========================================

                document.querySelectorAll('.form-restaurar-proveedor')
                    .forEach(form => {

                        if (form.dataset.swalInicializado === '1') return;

                        form.dataset.swalInicializado = '1';

                        form.addEventListener('submit', async function (event) {

                            event.preventDefault();

                            const resultado = await Swal.fire({
                                title: '¿Restaurar proveedor?',
                                text: 'El proveedor volverá a estar disponible en los registros vigentes.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, restaurar',
                                cancelButtonText: 'Cancelar',
                                confirmButtonColor: '#2563eb',
                                cancelButtonColor: '#52525b',
                                background: '#18181b',
                                color: '#ffffff',
                                reverseButtons: true
                            });

                            if (resultado.isConfirmed) {
                                HTMLFormElement.prototype.submit.call(form);
                            }
                        });
                    });
            }

            document.addEventListener(
                'DOMContentLoaded',
                inicializarSweetAlertProveedores
            );

            document.addEventListener(
                'livewire:navigated',
                inicializarSweetAlertProveedores
            );
        </script>
    @endonce

</x-layouts::app>
