<x-layouts::app :title="'Editar Proveedor'">

    <div class="space-y-6">

        {{-- ====================================== --}}
        {{-- ENCABEZADO --}}
        {{-- ====================================== --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Editar Proveedor
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Actualiza la información del proveedor registrado en Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.proveedores.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                ← Volver a Proveedores
            </a>

        </div>

        {{-- ====================================== --}}
        {{-- INFORMACIÓN DEL PROVEEDOR --}}
        {{-- ====================================== --}}

        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400">
                        Código del Proveedor
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-blue-400">
                        {{ $proveedor->codigo }}
                    </h2>

                    <p class="mt-2 text-sm text-zinc-400">
                        Este código es generado automáticamente y no puede modificarse.
                    </p>
                </div>

                <div>
                    @if($proveedor->estado === 'Activo')
                        <span class="rounded-full bg-green-500/15 px-4 py-2 text-sm font-semibold text-green-400">
                            Activo
                        </span>
                    @else
                        <span class="rounded-full bg-red-500/15 px-4 py-2 text-sm font-semibold text-red-400">
                            Inactivo
                        </span>
                    @endif
                </div>

            </div>

        </div>

        {{-- ====================================== --}}
        {{-- ERRORES DE VALIDACIÓN --}}
        {{-- ====================================== --}}

        @if($errors->any())
            <div class="rounded-xl border border-red-700 bg-red-950/40 p-5">

                <h3 class="font-semibold text-red-400">
                    No se pudo actualizar el proveedor.
                </h3>

                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-red-300">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        {{-- ====================================== --}}
        {{-- FORMULARIO DE EDICIÓN --}}
        {{-- ====================================== --}}

        <form method="POST"
              action="{{ route('admin.proveedores.update', $proveedor) }}"
              class="space-y-6">

            @csrf
            @method('PUT')

            {{-- ================================== --}}
            {{-- INFORMACIÓN EMPRESARIAL --}}
            {{-- ================================== --}}

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

                <h3 class="text-lg font-bold text-white">
                    Información Empresarial
                </h3>

                <p class="mt-1 text-sm text-zinc-400">
                    Datos principales de identificación del proveedor.
                </p>

                <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="razon_social" class="mb-2 block text-sm font-medium text-white">
                            Razón Social <span class="text-red-400">*</span>
                        </label>

                        <input type="text"
                               id="razon_social"
                               name="razon_social"
                               value="{{ old('razon_social', $proveedor->razon_social) }}"
                               maxlength="200"
                               required
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('razon_social')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombre_comercial" class="mb-2 block text-sm font-medium text-white">
                            Nombre Comercial
                        </label>

                        <input type="text"
                               id="nombre_comercial"
                               name="nombre_comercial"
                               value="{{ old('nombre_comercial', $proveedor->nombre_comercial) }}"
                               maxlength="200"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('nombre_comercial')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nit" class="mb-2 block text-sm font-medium text-white">
                            NIT
                        </label>

                        <input type="text"
                               id="nit"
                               name="nit"
                               value="{{ old('nit', $proveedor->nit) }}"
                               maxlength="30"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('nit')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="estado" class="mb-2 block text-sm font-medium text-white">
                            Estado <span class="text-red-400">*</span>
                        </label>

                        <select id="estado"
                                name="estado"
                                required
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                            <option value="Activo"
                                @selected(old('estado', $proveedor->estado) === 'Activo')>
                                Activo
                            </option>

                            <option value="Inactivo"
                                @selected(old('estado', $proveedor->estado) === 'Inactivo')>
                                Inactivo
                            </option>

                        </select>

                        @error('estado')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- ================================== --}}
            {{-- INFORMACIÓN DE CONTACTO --}}
            {{-- ================================== --}}

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

                <h3 class="text-lg font-bold text-white">
                    Información de Contacto
                </h3>

                <p class="mt-1 text-sm text-zinc-400">
                    Datos para comunicarse con el proveedor.
                </p>

                <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="contacto" class="mb-2 block text-sm font-medium text-white">
                            Persona de Contacto
                        </label>

                        <input type="text"
                               id="contacto"
                               name="contacto"
                               value="{{ old('contacto', $proveedor->contacto) }}"
                               maxlength="150"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('contacto')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-white">
                            Correo Electrónico
                        </label>

                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $proveedor->email) }}"
                               maxlength="150"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="mb-2 block text-sm font-medium text-white">
                            Teléfono Fijo
                        </label>

                        <input type="text"
                               id="telefono"
                               name="telefono"
                               value="{{ old('telefono', $proveedor->telefono) }}"
                               maxlength="30"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('telefono')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="celular" class="mb-2 block text-sm font-medium text-white">
                            Celular
                        </label>

                        <input type="text"
                               id="celular"
                               name="celular"
                               value="{{ old('celular', $proveedor->celular) }}"
                               maxlength="30"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('celular')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- ================================== --}}
            {{-- UBICACIÓN DEL PROVEEDOR --}}
            {{-- ================================== --}}

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

                <h3 class="text-lg font-bold text-white">
                    Ubicación del Proveedor
                </h3>

                <p class="mt-1 text-sm text-zinc-400">
                    Dirección y ciudad donde opera el proveedor.
                </p>

                <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="ciudad" class="mb-2 block text-sm font-medium text-white">
                            Ciudad
                        </label>

                        <input type="text"
                               id="ciudad"
                               name="ciudad"
                               value="{{ old('ciudad', $proveedor->ciudad) }}"
                               maxlength="100"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('ciudad')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="direccion" class="mb-2 block text-sm font-medium text-white">
                            Dirección
                        </label>

                        <input type="text"
                               id="direccion"
                               name="direccion"
                               value="{{ old('direccion', $proveedor->direccion) }}"
                               maxlength="255"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                        @error('direccion')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- ================================== --}}
            {{-- INFORMACIÓN ADICIONAL --}}
            {{-- ================================== --}}

            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">

                <h3 class="text-lg font-bold text-white">
                    Información Adicional
                </h3>

                <div class="mt-5">

                    <label for="observaciones" class="mb-2 block text-sm font-medium text-white">
                        Observaciones
                    </label>

                    <textarea id="observaciones"
                              name="observaciones"
                              rows="4"
                              maxlength="5000"
                              class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('observaciones', $proveedor->observaciones) }}</textarea>

                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                </div>

            </div>

            {{-- ================================== --}}
            {{-- BOTONES --}}
            {{-- ================================== --}}

            <div class="flex flex-wrap justify-end gap-3 pb-6">

                <a href="{{ route('admin.proveedores.index') }}"
                   class="rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Actualizar Proveedor
                </button>

            </div>

        </form>

    </div>

</x-layouts::app>
