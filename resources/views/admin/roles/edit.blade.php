
<x-layouts::app :title="'Editar Rol'">

    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6">

        {{-- Encabezado --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Rol
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Actualización de roles del Sistema Farmacia
                </p>
            </div>

            <a href="{{ route('admin.roles.index') }}"
               class="rounded-md border border-zinc-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800">
                Volver al listado
            </a>
        </div>

        {{-- Formulario --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="border-b border-zinc-700 p-5">
                <h2 class="font-semibold text-white">
                    Información del Rol
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Modifica la información del rol seleccionado.
                </p>
            </div>

            <form action="{{ route('admin.roles.update', $rol->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="space-y-6 p-5">

                    {{-- Nombre del rol --}}
                    <div>
                        <label for="name"
                               class="mb-2 block text-sm font-medium text-white">
                            Nombre del Rol
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $rol->name) }}"
                            maxlength="100"
                            required
                            autocomplete="off"
                            class="w-full rounded-md border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition placeholder:text-zinc-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Ejemplo: SUPERVISOR"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-2 text-xs text-zinc-500">
                            El nombre se guardará automáticamente en mayúsculas.
                        </p>
                    </div>

                    {{-- Información --}}
                    <div class="rounded-md border border-blue-700/60 bg-blue-950/30 p-4">

                        <h3 class="text-sm font-semibold text-blue-400">
                            Información importante
                        </h3>

                        <p class="mt-2 text-sm text-zinc-300">
                            La modificación del nombre del rol no eliminará
                            los usuarios ni los permisos que tenga asignados.
                        </p>

                        <p class="mt-2 text-xs text-zinc-400">
                            Si el rol se utiliza en reglas de acceso del sistema,
                            también deberán actualizarse esas referencias.
                        </p>
                    </div>

                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 border-t border-zinc-700 p-5">

                    <a href="{{ route('admin.roles.index') }}"
                       class="rounded-md border border-zinc-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="rounded-md bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        Actualizar
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-layouts::app>
