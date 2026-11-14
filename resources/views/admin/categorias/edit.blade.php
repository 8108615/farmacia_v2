<x-layouts::app :title="'Editar Categoría'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Categoría
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Modifique la información de la categoría seleccionada.
                </p>
            </div>

            <a href="{{ route('admin.categorias.index') }}"
               class="rounded-lg border border-zinc-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>

        </div>

        {{-- ERRORES GENERALES --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-300">
                <p class="font-semibold">
                    Por favor, revise los datos ingresados.
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORMULARIO --}}
        <form action="{{ route('admin.categorias.update', $categoria->id) }}"
              method="POST"
              class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            @csrf
            @method('PUT')

            {{-- ENCABEZADO DEL FORMULARIO --}}
            <div class="border-b border-zinc-700 p-6">

                <h2 class="text-base font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Actualice los datos principales de la categoría.
                </p>

            </div>

            <div class="space-y-6 p-6">

                {{-- CÓDIGO DE CATEGORÍA --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-200">
                        Código de Categoría
                    </label>

                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 px-4 py-3 text-sm font-semibold text-blue-400">
                        {{ str_pad($categoria->id, 4, '0', STR_PAD_LEFT) }}
                    </div>

                    <p class="mt-2 text-xs text-zinc-500">
                        Código asignado automáticamente por el sistema.
                    </p>
                </div>

                {{-- NOMBRE --}}
                <div>
                    <label for="nombre"
                           class="mb-2 block text-sm font-medium text-zinc-200">
                        Nombre de la Categoría
                        <span class="text-red-400">*</span>
                    </label>

                    <input type="text"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre', $categoria->nombre) }}"
                           maxlength="150"
                           required
                           autofocus
                           oninput="this.value = this.value.toLocaleUpperCase('es')"
                           placeholder="Ej. MEDICAMENTOS PEDIÁTRICOS"
                           class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('nombre') border-red-500 @enderror">

                    @error('nombre')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500">
                        El nombre se convierte automáticamente a mayúsculas.
                    </p>
                </div>

                {{-- DESCRIPCIÓN --}}
                <div>
                    <label for="descripcion"
                           class="mb-2 block text-sm font-medium text-zinc-200">
                        Descripción
                    </label>

                    <textarea id="descripcion"
                              name="descripcion"
                              rows="4"
                              placeholder="Describa los productos que pertenecen a esta categoría..."
                              class="w-full resize-y rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $categoria->descripcion) }}</textarea>

                    @error('descripcion')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500">
                        Puede actualizar la descripción o dejarla vacía.
                    </p>
                </div>

                {{-- ESTADO --}}
                <div>
                    <label for="estado"
                           class="mb-2 block text-sm font-medium text-zinc-200">
                        Estado de la Categoría
                        <span class="text-red-400">*</span>
                    </label>

                    <select id="estado"
                            name="estado"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('estado') border-red-500 @enderror">

                        <option value="Activo"
                            @selected(old('estado', $categoria->estado) === 'Activo')>
                            Activo
                        </option>

                        <option value="Inactivo"
                            @selected(old('estado', $categoria->estado) === 'Inactivo')>
                            Inactivo
                        </option>

                    </select>

                    @error('estado')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500">
                        Las categorías inactivas permanecen registradas en el sistema.
                    </p>
                </div>

            </div>

            {{-- INFORMACIÓN DEL REGISTRO --}}
            <div class="border-t border-zinc-700 px-6 py-5">

                <h3 class="mb-4 text-sm font-semibold text-white">
                    Información del Registro
                </h3>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs text-zinc-400">
                            Fecha de Registro
                        </p>

                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $categoria->created_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">
                            Última Actualización
                        </p>

                        <p class="mt-1 text-sm font-medium text-white">
                            {{ $categoria->updated_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}
            <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-700 bg-zinc-900/70 p-6">

                <a href="{{ route('admin.categorias.index') }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-green-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/40">
                    Actualizar Categoría
                </button>

            </div>

        </form>

    </div>

</x-layouts::app>
