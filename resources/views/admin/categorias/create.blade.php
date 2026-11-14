<x-layouts::app :title="'Nueva Categoría'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Registrar Nueva Categoría
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Complete la información para registrar una categoría en el Sistema Farmacia.
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
        <form action="{{ route('admin.categorias.store') }}"
              method="POST"
              class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            @csrf

            {{-- INFORMACIÓN GENERAL --}}
            <div class="border-b border-zinc-700 p-6">

                <h2 class="text-base font-semibold text-white">
                    Información General
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Datos principales de la categoría.
                </p>

            </div>

            <div class="space-y-6 p-6">

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
                        value="{{ old('nombre') }}"
                        maxlength="150"
                        required
                        autofocus
                        oninput="this.value = this.value.toLocaleUpperCase('es')"
                        placeholder="Ej. ANALGÉSICOS"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('nombre') border-red-500 @enderror">

                    @error('nombre')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500">
                        Ingrese un nombre único para identificar la categoría.
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
                              class="w-full resize-y rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>

                    @error('descripcion')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500">
                        Este campo es opcional.
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
                            @selected(old('estado', 'Activo') === 'Activo')>
                            Activo
                        </option>

                        <option value="Inactivo"
                            @selected(old('estado') === 'Inactivo')>
                            Inactivo
                        </option>

                    </select>

                    @error('estado')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500">
                        Las categorías inactivas permanecerán registradas en el sistema.
                    </p>
                </div>

            </div>

            {{-- BOTONES --}}
            <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-700 bg-zinc-900/70 p-6">

                <a href="{{ route('admin.categorias.index') }}"
                   class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    Guardar Categoría
                </button>

            </div>

        </form>

    </div>

</x-layouts::app>
