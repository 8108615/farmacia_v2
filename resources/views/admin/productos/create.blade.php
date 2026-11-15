<x-layouts::app :title="'Registrar Nuevo Producto'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Registrar Nuevo Producto
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Complete la información para registrar un producto
                    en el Sistema Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.productos.index') }}"
               class="rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('admin.productos.store') }}"
              method="POST"
              class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            @csrf

            {{-- ERRORES DE VALIDACIÓN --}}
            @if ($errors->any())
                <div class="m-5 rounded-lg border border-red-700 bg-red-950/40 p-4">
                    <h3 class="font-semibold text-red-400">
                        Verifique los datos ingresados:
                    </h3>

                    <ul class="mt-2 list-inside list-disc text-sm text-red-300">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SECCIÓN 1: INFORMACIÓN GENERAL --}}
            <div class="border-b border-zinc-700">
                <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-5">
                    <h2 class="font-semibold text-white">
                        Información General
                    </h2>
                    <p class="mt-1 text-sm text-zinc-400">
                        Datos principales para identificar el producto.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                    {{-- CÓDIGO INTERNO --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-zinc-200">
                            Código Interno
                        </label>

                        <input type="text"
                               value="Se generará automáticamente"
                               disabled
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-blue-400">

                        <p class="mt-2 text-xs text-zinc-500">
                            Ejemplo: PROD-000001.
                        </p>
                    </div>

                    {{-- CATEGORÍA --}}
                    <div>
                        <label for="categoria_id"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Categoría
                            <span class="text-red-400">*</span>
                        </label>

                        <select id="categoria_id"
                                name="categoria_id"
                                required
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                            <option value="">Seleccione una categoría</option>

                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    @selected(old('categoria_id') == $categoria->id)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('categoria_id')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- NOMBRE COMERCIAL --}}
                    <div>
                        <label for="nombre"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Nombre del Producto
                            <span class="text-red-400">*</span>
                        </label>

                        <input type="text"
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre') }}"
                               maxlength="200"
                               required
                               autofocus
                               placeholder="Ej. PARACETAMOL 500 MG"
                               oninput="this.value = this.value.toLocaleUpperCase('es')"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('nombre')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- NOMBRE GENÉRICO --}}
                    <div>
                        <label for="nombre_generico"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Nombre Genérico
                        </label>

                        <input type="text"
                               id="nombre_generico"
                               name="nombre_generico"
                               value="{{ old('nombre_generico') }}"
                               maxlength="200"
                               placeholder="Ej. ACETAMINOFÉN"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('nombre_generico')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- MARCA --}}
                    <div>
                        <label for="marca"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Marca Comercial
                        </label>

                        <input type="text"
                               id="marca"
                               name="marca"
                               value="{{ old('marca') }}"
                               maxlength="150"
                               placeholder="Ingrese la marca comercial"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('marca')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- LABORATORIO --}}
                    <div>
                        <label for="laboratorio"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Laboratorio
                        </label>

                        <input type="text"
                               id="laboratorio"
                               name="laboratorio"
                               value="{{ old('laboratorio') }}"
                               maxlength="150"
                               placeholder="Ingrese el laboratorio fabricante"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('laboratorio')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: INFORMACIÓN FARMACÉUTICA --}}
            <div class="border-b border-zinc-700">
                <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-5">
                    <h2 class="font-semibold text-white">
                        Información Farmacéutica
                    </h2>
                    <p class="mt-1 text-sm text-zinc-400">
                        Características y datos sanitarios del medicamento.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                    {{-- PRESENTACIÓN --}}
                    <div>
                        <label for="presentacion"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Presentación
                        </label>

                        <input type="text"
                               id="presentacion"
                               name="presentacion"
                               value="{{ old('presentacion') }}"
                               maxlength="100"
                               placeholder="Ej. Tabletas, jarabe, cápsulas"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('presentacion')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- CONCENTRACIÓN --}}
                    <div>
                        <label for="concentracion"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Concentración
                        </label>

                        <input type="text"
                               id="concentracion"
                               name="concentracion"
                               value="{{ old('concentracion') }}"
                               maxlength="100"
                               placeholder="Ej. 500 mg, 250 mg/5 mL"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('concentracion')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- REGISTRO SANITARIO --}}
                    <div>
                        <label for="registro_sanitario"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Registro Sanitario
                        </label>

                        <input type="text"
                               id="registro_sanitario"
                               name="registro_sanitario"
                               value="{{ old('registro_sanitario') }}"
                               maxlength="100"
                               placeholder="Ingrese el registro sanitario"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('registro_sanitario')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- REQUIERE RECETA --}}
                    <div>
                        <label for="requiere_receta"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            ¿Requiere Receta Médica?
                            <span class="text-red-400">*</span>
                        </label>

                        <select id="requiere_receta"
                                name="requiere_receta"
                                required
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                            <option value="0"
                                @selected(old('requiere_receta', '0') == '0')>
                                No requiere receta
                            </option>

                            <option value="1"
                                @selected(old('requiere_receta') == '1')>
                                Sí requiere receta
                            </option>
                        </select>

                        @error('requiere_receta')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: INFORMACIÓN ADICIONAL --}}
            <div class="border-b border-zinc-700">
                <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-5">
                    <h2 class="font-semibold text-white">
                        Información Adicional
                    </h2>
                    <p class="mt-1 text-sm text-zinc-400">
                        Datos complementarios para identificar el producto.
                    </p>
                </div>

                <div class="space-y-6 p-6">

                    {{-- CÓDIGO DE BARRAS --}}
                    <div>
                        <label for="codigo_barras"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Código de Barras
                        </label>

                        <input type="text"
                               id="codigo_barras"
                               name="codigo_barras"
                               value="{{ old('codigo_barras') }}"
                               maxlength="100"
                               placeholder="Ingrese o escanee el código de barras"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('codigo_barras')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-2 text-xs text-zinc-500">
                            Este código debe ser único para cada producto.
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
                                  placeholder="Ingrese información adicional del producto..."
                                  class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">{{ old('descripcion') }}</textarea>

                        @error('descripcion')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 4: ESTADO --}}
            <div>
                <div class="border-b border-zinc-700 bg-zinc-950 px-6 py-5">
                    <h2 class="font-semibold text-white">
                        Estado del Producto
                    </h2>
                    <p class="mt-1 text-sm text-zinc-400">
                        Configure la disponibilidad administrativa del producto.
                    </p>
                </div>

                <div class="p-6">
                    <label for="estado"
                           class="mb-2 block text-sm font-medium text-zinc-200">
                        Estado
                        <span class="text-red-400">*</span>
                    </label>

                    <select id="estado"
                            name="estado"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

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
                        Los productos inactivos permanecerán registrados
                        en el sistema.
                    </p>
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-700 bg-zinc-950 px-6 py-5">

                <a href="{{ route('admin.productos.index') }}"
                   class="rounded-lg border border-zinc-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:ring-2 focus:ring-blue-500/30">
                    Guardar Producto
                </button>
            </div>

        </form>
    </div>

</x-layouts::app>
