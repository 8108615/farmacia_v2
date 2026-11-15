<x-layouts::app :title="'Editar Producto'">

    @php
        $claseInput = 'w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

        $camposGenerales = [
            ['nombre', 'Nombre del Producto', 200, 'Ej. PARACETAMOL 500 MG', true],
            ['nombre_generico', 'Nombre Genérico', 200, 'Ej. PARACETAMOL', false],
            ['marca', 'Marca Comercial', 150, 'Ingrese la marca comercial', false],
            ['laboratorio', 'Laboratorio', 150, 'Ingrese el laboratorio fabricante', false],
        ];

        $camposFarmaceuticos = [
            ['presentacion', 'Presentación', 'Ej. Tabletas, jarabe, cápsulas'],
            ['concentracion', 'Concentración', 'Ej. 500 mg, 250 mg/5 mL'],
            ['registro_sanitario', 'Registro Sanitario', 'Ingrese el registro sanitario'],
        ];
    @endphp

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Producto
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Actualice la información del producto registrado
                    en el Sistema Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.productos.index') }}"
               class="rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver al listado
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('admin.productos.update', $producto->id) }}"
              method="POST"
              class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            @csrf
            @method('PUT')

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
                        <label for="codigo"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Código Interno
                        </label>

                        <input type="text"
                               id="codigo"
                               value="{{ $producto->codigo }}"
                               readonly
                               class="w-full cursor-not-allowed rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm font-semibold text-blue-400">

                        <p class="mt-2 text-xs text-zinc-500">
                            El código interno no puede modificarse.
                        </p>
                    </div>

                    {{-- CATEGORÍA --}}
                    <div>
                        <label for="categoria_id"
                               class="mb-2 block text-sm font-medium text-zinc-200">
                            Categoría <span class="text-red-400">*</span>
                        </label>

                        <select id="categoria_id"
                                name="categoria_id"
                                required
                                class="{{ $claseInput }}">

                            <option value="">Seleccione una categoría</option>

                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    @selected(old('categoria_id', $producto->categoria_id) == $categoria->id)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('categoria_id')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NOMBRE, GENÉRICO, MARCA Y LABORATORIO --}}
                    @foreach ($camposGenerales as [$campo, $etiqueta, $maximo, $ejemplo, $obligatorio])
                        <div>
                            <label for="{{ $campo }}"
                                   class="mb-2 block text-sm font-medium text-zinc-200">
                                {{ $etiqueta }}

                                @if ($obligatorio)
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>

                            <input type="text"
                                   id="{{ $campo }}"
                                   name="{{ $campo }}"
                                   value="{{ old($campo, $producto->{$campo}) }}"
                                   maxlength="{{ $maximo }}"
                                   placeholder="{{ $ejemplo }}"
                                   @if ($obligatorio) required @endif
                                   @if ($campo === 'nombre')
                                       oninput="this.value = this.value.toLocaleUpperCase('es')"
                                   @endif
                                   class="{{ $claseInput }}">

                            @error($campo)
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

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

                    @foreach ($camposFarmaceuticos as [$campo, $etiqueta, $ejemplo])
                        <div>
                            <label for="{{ $campo }}"
                                   class="mb-2 block text-sm font-medium text-zinc-200">
                                {{ $etiqueta }}
                            </label>

                            <input type="text"
                                   id="{{ $campo }}"
                                   name="{{ $campo }}"
                                   value="{{ old($campo, $producto->{$campo}) }}"
                                   maxlength="100"
                                   placeholder="{{ $ejemplo }}"
                                   class="{{ $claseInput }}">

                            @error($campo)
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

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
                                class="{{ $claseInput }}">

                            <option value="0"
                                @selected((string) old('requiere_receta', (int) $producto->requiere_receta) === '0')>
                                No requiere receta
                            </option>

                            <option value="1"
                                @selected((string) old('requiere_receta', (int) $producto->requiere_receta) === '1')>
                                Sí requiere receta
                            </option>
                        </select>

                        @error('requiere_receta')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
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
                               value="{{ old('codigo_barras', $producto->codigo_barras) }}"
                               maxlength="100"
                               placeholder="Ingrese o escanee el código de barras"
                               class="{{ $claseInput }}">

                        @error('codigo_barras')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
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
                                  class="{{ $claseInput }}">{{ old('descripcion', $producto->descripcion) }}</textarea>

                        @error('descripcion')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- SECCIÓN 4: ESTADO --}}
            <div class="border-b border-zinc-700">

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
                        Estado <span class="text-red-400">*</span>
                    </label>

                    <select id="estado"
                            name="estado"
                            required
                            class="{{ $claseInput }}">

                        <option value="Activo"
                            @selected(old('estado', $producto->estado) === 'Activo')>
                            Activo
                        </option>

                        <option value="Inactivo"
                            @selected(old('estado', $producto->estado) === 'Inactivo')>
                            Inactivo
                        </option>
                    </select>

                    @error('estado')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500">
                        Los productos inactivos permanecerán registrados
                        en el sistema.
                    </p>
                </div>
            </div>

            {{-- INFORMACIÓN DEL REGISTRO --}}
            <div class="border-b border-zinc-700">

                <div class="bg-zinc-950 px-6 py-5">
                    <h2 class="font-semibold text-white">
                        Información del Registro
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                    <div>
                        <p class="text-xs text-zinc-400">
                            Fecha de Registro
                        </p>
                        <p class="mt-1 text-sm font-semibold text-white">
                            {{ $producto->created_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400">
                            Última Actualización
                        </p>
                        <p class="mt-1 text-sm font-semibold text-white">
                            {{ $producto->updated_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- BOTONES --}}
            <div class="flex flex-wrap justify-end gap-3 bg-zinc-950 px-6 py-5">

                <a href="{{ route('admin.productos.index') }}"
                   class="rounded-lg border border-zinc-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-green-700 focus:ring-2 focus:ring-green-500/30">
                    Actualizar Producto
                </button>

            </div>

        </form>
    </div>

</x-layouts::app>
