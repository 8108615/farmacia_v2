<x-layouts::app :title="'Nuevo Proveedor'">

    <div class="flex flex-col gap-6 p-4 md:p-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Registrar Nuevo Proveedor
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Complete los datos para registrar un proveedor en Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.proveedores.index') }}"
               class="rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                ← Volver a Proveedores
            </a>
        </div>

        {{-- ERRORES DE VALIDACIÓN --}}
        @if($errors->any())
            <div class="rounded-xl border border-red-700 bg-red-950/30 p-4">
                <h3 class="font-semibold text-red-400">
                    Verifique los siguientes datos:
                </h3>

                <ul class="mt-2 list-inside list-disc text-sm text-red-300">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.proveedores.store') }}"
              class="flex flex-col gap-6">

            @csrf

            {{-- INFORMACIÓN EMPRESARIAL --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5 md:p-6">

                <h2 class="mb-1 text-lg font-semibold text-white">
                    Información Empresarial
                </h2>

                <p class="mb-6 text-sm text-zinc-400">
                    Datos principales de identificación del proveedor.
                </p>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="razon_social" class="mb-2 block text-sm font-medium text-zinc-300">
                            Razón Social <span class="text-red-400">*</span>
                        </label>

                        <input id="razon_social"
                               type="text"
                               name="razon_social"
                               value="{{ old('razon_social') }}"
                               required
                               maxlength="200"
                               placeholder="Ej. Distribuidora Farmacéutica Bolivia SRL"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="nombre_comercial" class="mb-2 block text-sm font-medium text-zinc-300">
                            Nombre Comercial
                        </label>

                        <input id="nombre_comercial"
                               type="text"
                               name="nombre_comercial"
                               value="{{ old('nombre_comercial') }}"
                               maxlength="200"
                               placeholder="Ej. Farma Bolivia"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="nit" class="mb-2 block text-sm font-medium text-zinc-300">
                            NIT
                        </label>

                        <input id="nit"
                               type="text"
                               name="nit"
                               value="{{ old('nit') }}"
                               maxlength="30"
                               placeholder="Número de Identificación Tributaria"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="estado" class="mb-2 block text-sm font-medium text-zinc-300">
                            Estado <span class="text-red-400">*</span>
                        </label>

                        <select id="estado"
                                name="estado"
                                required
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none">

                            <option value="Activo" @selected(old('estado', 'Activo') === 'Activo')>
                                Activo
                            </option>

                            <option value="Inactivo" @selected(old('estado') === 'Inactivo')>
                                Inactivo
                            </option>
                        </select>
                    </div>

                </div>
            </div>

            {{-- INFORMACIÓN DE CONTACTO --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5 md:p-6">

                <h2 class="mb-1 text-lg font-semibold text-white">
                    Información de Contacto
                </h2>

                <p class="mb-6 text-sm text-zinc-400">
                    Datos para comunicarse con el proveedor.
                </p>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="contacto" class="mb-2 block text-sm font-medium text-zinc-300">
                            Persona de Contacto
                        </label>

                        <input id="contacto"
                               type="text"
                               name="contacto"
                               value="{{ old('contacto') }}"
                               maxlength="150"
                               placeholder="Nombre del representante"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-zinc-300">
                            Correo Electrónico
                        </label>

                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               maxlength="150"
                               placeholder="contacto@empresa.com"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="telefono" class="mb-2 block text-sm font-medium text-zinc-300">
                            Teléfono Fijo
                        </label>

                        <input id="telefono"
                               type="text"
                               name="telefono"
                               value="{{ old('telefono') }}"
                               maxlength="30"
                               placeholder="Ej. 33445566"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="celular" class="mb-2 block text-sm font-medium text-zinc-300">
                            Celular
                        </label>

                        <input id="celular"
                               type="text"
                               name="celular"
                               value="{{ old('celular') }}"
                               maxlength="30"
                               placeholder="Ej. 70000000"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                </div>
            </div>

            {{-- UBICACIÓN --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5 md:p-6">

                <h2 class="mb-1 text-lg font-semibold text-white">
                    Ubicación del Proveedor
                </h2>

                <p class="mb-6 text-sm text-zinc-400">
                    Dirección y ciudad donde opera el proveedor.
                </p>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="ciudad" class="mb-2 block text-sm font-medium text-zinc-300">
                            Ciudad
                        </label>

                        <input id="ciudad"
                               type="text"
                               name="ciudad"
                               value="{{ old('ciudad') }}"
                               maxlength="100"
                               placeholder="Ej. Santa Cruz de la Sierra"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="direccion" class="mb-2 block text-sm font-medium text-zinc-300">
                            Dirección
                        </label>

                        <input id="direccion"
                               type="text"
                               name="direccion"
                               value="{{ old('direccion') }}"
                               maxlength="255"
                               placeholder="Av., calle, número y zona"
                               class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">
                    </div>

                </div>
            </div>

            {{-- OBSERVACIONES --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5 md:p-6">

                <h2 class="mb-4 text-lg font-semibold text-white">
                    Información Adicional
                </h2>

                <label for="observaciones" class="mb-2 block text-sm font-medium text-zinc-300">
                    Observaciones
                </label>

                <textarea id="observaciones"
                          name="observaciones"
                          rows="4"
                          maxlength="5000"
                          placeholder="Información adicional del proveedor..."
                          class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none">{{ old('observaciones') }}</textarea>

            </div>

            {{-- ACCIONES --}}
            <div class="flex flex-wrap justify-end gap-3">

                <a href="{{ route('admin.proveedores.index') }}"
                   class="rounded-lg border border-zinc-700 bg-zinc-800 px-6 py-3 text-sm font-semibold text-white hover:bg-zinc-700">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Guardar Proveedor
                </button>

            </div>

        </form>

    </div>

</x-layouts::app>
