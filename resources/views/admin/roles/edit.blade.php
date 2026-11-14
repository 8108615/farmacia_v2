<x-layouts::app :title="'Editar Rol'">

    <div class="w-full max-w-none space-y-5">

        {{-- ========================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================= --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Rol
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Actualización de roles y permisos del Sistema Farmacia
                </p>
            </div>

            <a href="{{ route('admin.roles.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-zinc-700 px-4 py-2 text-sm text-white transition hover:bg-zinc-800">
                Volver al listado
            </a>

        </div>

        {{-- ========================================= --}}
        {{-- FORMULARIO --}}
        {{-- ========================================= --}}
        <form
            id="form-editar-rol"
            action="{{ route('admin.roles.update', $rol->id) }}"
            method="POST"
            class="overflow-visible rounded-xl border border-zinc-700 bg-zinc-900"
        >

            @csrf
            @method('PUT')

            <div class="space-y-5 p-4 sm:p-6">

                {{-- ========================================= --}}
                {{-- NOMBRE DEL ROL --}}
                {{-- ========================================= --}}
                <div>

                    <label for="name"
                           class="mb-2 block text-sm font-semibold text-white">
                        Nombre del Rol
                        <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $rol->name) }}"
                        maxlength="100"
                        required
                        autocomplete="off"
                        placeholder="Ejemplo: SUPERVISOR"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    >

                    @error('name')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-1.5 text-xs text-zinc-500">
                        El nombre se guardará automáticamente en mayúsculas.
                    </p>

                </div>

                <div class="border-t border-zinc-700"></div>

                {{-- ========================================= --}}
                {{-- ENCABEZADO DE PERMISOS --}}
                {{-- ========================================= --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h2 class="text-base font-semibold text-white">
                            Asignación de Permisos
                        </h2>

                        <p class="mt-1 text-xs text-zinc-400">
                            Modifica las funciones que podrá utilizar este rol.
                        </p>
                    </div>

                    <div class="inline-flex w-fit items-center gap-2 rounded-lg border border-emerald-800 bg-emerald-950/30 px-3 py-2">

                        <span class="text-xs text-zinc-300">
                            Seleccionados:
                        </span>

                        <span id="contador-permisos"
                              class="text-sm font-bold text-emerald-400">
                            0
                        </span>

                        <span class="text-xs text-zinc-500">/</span>

                        <span id="total-permisos"
                              class="text-xs text-zinc-400">
                            0
                        </span>

                    </div>

                </div>

                {{-- ERRORES DE VALIDACIÓN --}}
                @error('permisos')
                    <p class="text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                @error('permisos.*')
                    <p class="text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                {{-- ========================================= --}}
                {{-- PERMISOS AGRUPADOS EN TRES COLUMNAS --}}
                {{-- ========================================= --}}
                <div class="grid grid-cols-1 items-start gap-3 md:grid-cols-2 xl:grid-cols-3">

                    @forelse ($permisos as $modulo => $listaPermisos)

                        <div class="overflow-hidden rounded-lg border border-zinc-700 bg-zinc-900 transition-colors hover:border-zinc-600">

                            {{-- ENCABEZADO DEL MÓDULO --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-700 bg-zinc-800/80 px-3 py-2.5">

                                <h3 class="text-xs font-bold tracking-wide text-white">
                                    {{ mb_strtoupper($modulo, 'UTF-8') }}
                                </h3>

                                <label class="flex cursor-pointer items-center gap-2 text-xs text-zinc-300">

                                    <input
                                        type="checkbox"
                                        class="seleccionar-modulo h-4 w-4 cursor-pointer accent-emerald-600"
                                        data-modulo="{{ $modulo }}"
                                    >

                                    <span>Seleccionar todos</span>

                                </label>

                            </div>

                            {{-- LISTADO DE PERMISOS --}}
                            <div class="grid grid-cols-2 gap-x-3 gap-y-3 p-3 sm:grid-cols-3">

                                @foreach ($listaPermisos as $permiso)

                                    @php
                                        $accion = explode('.', $permiso->name, 2)[1]
                                            ?? $permiso->name;

                                        // Mantener permisos seleccionados
                                        // después de un error de validación.
                                        $seleccionados = old(
                                            'permisos',
                                            $permisosAsignados
                                        );
                                    @endphp

                                    <label class="flex cursor-pointer items-center gap-2 text-xs text-zinc-300 transition hover:text-white">

                                        <input
                                            type="checkbox"
                                            name="permisos[]"
                                            value="{{ $permiso->name }}"
                                            data-permiso-modulo="{{ $modulo }}"
                                            @checked(in_array($permiso->name, $seleccionados))
                                            class="permiso-checkbox h-4 w-4 shrink-0 cursor-pointer rounded border-zinc-600 accent-emerald-600"
                                        >

                                        <span>
                                            {{ ucfirst(str_replace('_', ' ', $accion)) }}
                                        </span>

                                    </label>

                                @endforeach

                            </div>

                        </div>

                    @empty

                        <div class="rounded-lg border border-zinc-700 bg-zinc-800 p-4 xl:col-span-3">

                            <p class="text-sm text-zinc-400">
                                No existen permisos registrados en el sistema.
                            </p>

                        </div>

                    @endforelse

                </div>

                {{-- ========================================= --}}
                {{-- INFORMACIÓN IMPORTANTE --}}
                {{-- ========================================= --}}
                <div class="rounded-lg border border-blue-900 bg-blue-950/20 px-4 py-3">

                    <p class="text-xs leading-relaxed text-zinc-300">

                        <span class="font-semibold text-blue-400">
                            Información:
                        </span>

                        Los permisos seleccionados reemplazarán los permisos
                        actuales de este rol al guardar los cambios.

                        Los usuarios que tengan asignado este rol
                        conservarán su asignación al mismo.

                    </p>

                </div>

            </div>

            {{-- ========================================= --}}
            {{-- BOTONES --}}
            {{-- ========================================= --}}
            <div class="sticky bottom-0 z-10 flex flex-wrap items-center justify-end gap-3 rounded-b-xl border-t border-zinc-700 bg-zinc-900/95 px-4 py-4 shadow-lg backdrop-blur sm:px-6">

                <a href="{{ route('admin.roles.index') }}"
                   class="rounded-lg border border-zinc-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    Actualizar Rol
                </button>

            </div>

        </form>

    </div>

    {{-- ========================================= --}}
    {{-- JAVASCRIPT: CONTROL DE PERMISOS --}}
    {{-- ========================================= --}}
    <script>
        function inicializarPermisosEditarRol() {

            const formulario = document.getElementById('form-editar-rol');

            if (!formulario) {
                return;
            }

            const permisos = Array.from(
                formulario.querySelectorAll('.permiso-checkbox')
            );

            const contador = formulario.querySelector('#contador-permisos');
            const total = formulario.querySelector('#total-permisos');

            // Actualizar contador de permisos.
            function actualizarContador() {

                const seleccionados = permisos.filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

                if (contador) {
                    contador.textContent = seleccionados;
                }

                if (total) {
                    total.textContent = permisos.length;
                }
            }

            // Seleccionar todos los permisos por módulo.
            formulario.querySelectorAll('.seleccionar-modulo').forEach(function (selector) {

                if (selector.dataset.inicializado === '1') {
                    return;
                }

                selector.dataset.inicializado = '1';

                const modulo = selector.dataset.modulo;

                const permisosModulo = permisos.filter(function (checkbox) {
                    return checkbox.dataset.permisoModulo === modulo;
                });

                function actualizarSelector() {

                    const seleccionados = permisosModulo.filter(function (checkbox) {
                        return checkbox.checked;
                    }).length;

                    selector.checked =
                        permisosModulo.length > 0 &&
                        seleccionados === permisosModulo.length;

                    selector.indeterminate =
                        seleccionados > 0 &&
                        seleccionados < permisosModulo.length;
                }

                selector.addEventListener('change', function () {

                    permisosModulo.forEach(function (checkbox) {
                        checkbox.checked = selector.checked;
                    });

                    actualizarSelector();
                    actualizarContador();
                });

                permisosModulo.forEach(function (checkbox) {

                    checkbox.addEventListener('change', function () {
                        actualizarSelector();
                        actualizarContador();
                    });

                });

                actualizarSelector();
            });

            actualizarContador();
        }

        document.addEventListener(
            'DOMContentLoaded',
            inicializarPermisosEditarRol
        );

        document.addEventListener(
            'livewire:navigated',
            inicializarPermisosEditarRol
        );

        if (document.readyState !== 'loading') {
            inicializarPermisosEditarRol();
        }
    </script>

</x-layouts::app>
