
<x-layouts::app :title="'Crear Usuario'">

    <div class="flex flex-col gap-6">

        <!-- ENCABEZADO -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white">
                    Registrar Nuevo Usuario
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Complete los datos para registrar un usuario en el Sistema Farmacia.
                </p>
            </div>

            <a href="{{ route('admin.usuarios.index') }}"
               class="rounded-lg border border-zinc-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-700">
                ← Volver al listado
            </a>
        </div>

        <!-- MENSAJES DE VALIDACIÓN -->
        @if ($errors->any())
            <div class="rounded-xl border border-red-800 bg-red-950/40 p-4 text-sm text-red-300">
                <p class="mb-2 font-semibold">
                    Por favor, revise los siguientes errores:
                </p>

                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORMULARIO -->
        <form
            action="{{ route('admin.usuarios.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="rounded-xl border border-zinc-700 bg-zinc-900 p-6"
        >
            @csrf

            <div class="mb-6 border-b border-zinc-700 pb-4">
                <h2 class="text-xl font-semibold text-white">
                    Información del Usuario
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Datos personales, información de contacto y acceso al sistema.
                </p>
            </div>

            <!-- FOTOGRAFÍA DE PERFIL -->
            <div class="mb-8">
                <label class="mb-3 block text-sm font-semibold text-white">
                    Fotografía de Perfil
                </label>

                <div class="flex flex-wrap items-center gap-5">

                    <!-- VISTA PREVIA -->
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-zinc-600 bg-zinc-800">

                        <img
                            id="fotoPreview"
                            alt="Vista previa de fotografía"
                            class="hidden h-full w-full object-cover"
                        >

                        <div id="fotoPlaceholder"
                             class="flex flex-col items-center text-zinc-500">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="32"
                                 height="32"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.5"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">

                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="9" cy="9" r="2"/>
                                <path d="m21 15-5-5L5 21"/>

                            </svg>
                        </div>
                    </div>

                    <!-- SELECCIONAR FOTOGRAFÍA -->
                    <div>
                        <label
                            for="foto_perfil"
                            class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            Seleccionar Foto
                        </label>

                        <input
                            type="file"
                            name="foto_perfil"
                            id="foto_perfil"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="hidden"
                        >

                        <p id="nombreFoto"
                           class="mt-2 text-xs italic text-zinc-400">
                            Ningún archivo seleccionado
                        </p>

                        <p class="mt-1 text-xs text-zinc-500">
                            JPG, PNG o WEBP. Máximo 2 MB.
                        </p>

                        @error('foto_perfil')
                            <p class="mt-1 text-xs text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- CAMPOS DEL FORMULARIO -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                <!-- ROL -->
                <div>
                    <label for="rol" class="mb-2 block text-sm font-medium text-white">
                        Rol del Usuario <span class="text-red-400">*</span>
                    </label>

                    <select
                        name="rol"
                        id="rol"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Seleccione un rol</option>

                        @foreach ($roles as $rol)
                            <option
                                value="{{ $rol->name }}"
                                @selected(old('rol') === $rol->name)
                            >
                                {{ $rol->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('rol')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NOMBRE -->
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-white">
                        Nombres y Apellidos <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="Ingrese nombres y apellidos"
                        maxlength="255"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- EMAIL -->
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-white">
                        Correo Electrónico <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="usuario@correo.com"
                        maxlength="255"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- TIPO DOCUMENTO -->
                <div>
                    <label for="tipo_documento" class="mb-2 block text-sm font-medium text-white">
                        Tipo de Documento <span class="text-red-400">*</span>
                    </label>

                    <select
                        name="tipo_documento"
                        id="tipo_documento"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Seleccione un documento</option>

                        <option value="cedula de identidad"
                            @selected(old('tipo_documento') === 'cedula de identidad')>
                            Cédula de Identidad
                        </option>

                        <option value="Pasaporte"
                            @selected(old('tipo_documento') === 'Pasaporte')>
                            Pasaporte
                        </option>
                    </select>

                    @error('tipo_documento')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NÚMERO DOCUMENTO -->
                <div>
                    <label for="numero_documento" class="mb-2 block text-sm font-medium text-white">
                        Número de Documento <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="numero_documento"
                        id="numero_documento"
                        value="{{ old('numero_documento') }}"
                        placeholder="Ingrese el número de documento"
                        maxlength="50"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('numero_documento')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CELULAR -->
                <div>
                    <label for="celular" class="mb-2 block text-sm font-medium text-white">
                        Número de Celular <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="celular"
                        id="celular"
                        value="{{ old('celular') }}"
                        placeholder="Ingrese el número de celular"
                        maxlength="30"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('celular')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DIRECCIÓN -->
                <div class="md:col-span-2 xl:col-span-3">
                    <label for="direccion" class="mb-2 block text-sm font-medium text-white">
                        Dirección <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        id="direccion"
                        value="{{ old('direccion') }}"
                        placeholder="Ingrese la dirección del usuario"
                        maxlength="255"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('direccion')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- FECHA NACIMIENTO -->
                <div>
                    <label for="fecha_nacimiento" class="mb-2 block text-sm font-medium text-white">
                        Fecha de Nacimiento <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="date"
                        name="fecha_nacimiento"
                        id="fecha_nacimiento"
                        value="{{ old('fecha_nacimiento') }}"
                        max="{{ now()->toDateString() }}"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('fecha_nacimiento')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- GÉNERO -->
                <div>
                    <label for="genero" class="mb-2 block text-sm font-medium text-white">
                        Género <span class="text-red-400">*</span>
                    </label>

                    <select
                        name="genero"
                        id="genero"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Seleccione el género</option>

                        <option value="Masculino"
                            @selected(old('genero') === 'Masculino')>
                            Masculino
                        </option>

                        <option value="Femenino"
                            @selected(old('genero') === 'Femenino')>
                            Femenino
                        </option>
                    </select>

                    @error('genero')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ESTADO -->
                <div>
                    <label for="estado" class="mb-2 block text-sm font-medium text-white">
                        Estado del Usuario <span class="text-red-400">*</span>
                    </label>

                    <select
                        name="estado"
                        id="estado"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:ring-blue-500"
                    >
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
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CONTRASEÑA -->
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-white">
                        Contraseña <span class="text-red-400">*</span>
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Mínimo 8 caracteres"
                            minlength="8"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 pr-16 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                        >

                        <button
                            type="button"
                            data-toggle-password="password"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-3 text-xs font-semibold text-blue-400 hover:text-blue-300"
                        >
                            Mostrar
                        </button>
                    </div>

                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CONFIRMAR CONTRASEÑA -->
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-white">
                        Confirmar Contraseña <span class="text-red-400">*</span>
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Repita la contraseña"
                            minlength="8"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 pr-16 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                        >

                        <button
                            type="button"
                            data-toggle-password="password_confirmation"
                            aria-label="Mostrar confirmación de contraseña"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-3 text-xs font-semibold text-blue-400 hover:text-blue-300"
                        >
                            Mostrar
                        </button>
                    </div>
                </div>


            </div>

            <!-- BOTONES -->
            <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-zinc-700 pt-6">

                <a
                    href="{{ route('admin.usuarios.index') }}"
                    class="rounded-lg border border-zinc-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    id="btnRegistrarUsuario"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span id="textoRegistrarUsuario">Registrar Usuario</span>
                </button>

            </div>

        </form>

    </div>

</x-layouts::app>

<!-- VISTA PREVIA DE FOTOGRAFÍA -->
<script>
(function () {
    function inicializarFormularioUsuario() {
        const inputFoto = document.getElementById('foto_perfil');
        const preview = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('fotoPlaceholder');
        const nombreFoto = document.getElementById('nombreFoto');

        // ==========================================
        // VISTA PREVIA DE FOTOGRAFÍA
        // ==========================================

        if (
            inputFoto &&
            preview &&
            placeholder &&
            nombreFoto &&
            !inputFoto.dataset.inicializado
        ) {
            inputFoto.dataset.inicializado = 'true';

            let fotoUrl = null;

            inputFoto.addEventListener('change', function () {
                const archivo = this.files[0];

                if (fotoUrl) {
                    URL.revokeObjectURL(fotoUrl);
                    fotoUrl = null;
                }

                const limpiarVistaPrevia = () => {
                    preview.removeAttribute('src');
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                    nombreFoto.textContent = 'Ningún archivo seleccionado';
                };

                if (!archivo) {
                    limpiarVistaPrevia();
                    return;
                }

                const tiposPermitidos = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                const tamanoMaximo = 2 * 1024 * 1024;

                if (
                    !tiposPermitidos.includes(archivo.type) ||
                    archivo.size > tamanoMaximo
                ) {
                    alert(
                        'Seleccione una imagen JPG, PNG o WEBP de máximo 2 MB.'
                    );

                    inputFoto.value = '';
                    limpiarVistaPrevia();
                    return;
                }

                fotoUrl = URL.createObjectURL(archivo);

                preview.onload = function () {
                    if (fotoUrl) {
                        URL.revokeObjectURL(fotoUrl);
                        fotoUrl = null;
                    }
                };

                preview.src = fotoUrl;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');

                nombreFoto.textContent = archivo.name;
            });
        }

        // ==========================================
        // MOSTRAR / OCULTAR CONTRASEÑAS
        // ==========================================

        document.querySelectorAll('[data-toggle-password]').forEach(
            function (boton) {
                if (boton.dataset.inicializado) return;

                boton.dataset.inicializado = 'true';

                boton.addEventListener('click', function () {
                    const campoId = this.dataset.togglePassword;
                    const campo = document.getElementById(campoId);

                    if (!campo) return;

                    const mostrar = campo.type === 'password';

                    campo.type = mostrar ? 'text' : 'password';
                    this.textContent = mostrar ? 'Ocultar' : 'Mostrar';

                    this.setAttribute(
                        'aria-pressed',
                        mostrar ? 'true' : 'false'
                    );

                    this.setAttribute(
                        'aria-label',
                        mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
                    );
                });
            }
        );

        // ==========================================
        // PREVENIR ENVÍOS DUPLICADOS
        // ==========================================

        const botonRegistrar = document.getElementById(
            'btnRegistrarUsuario'
        );

        const textoRegistrar = document.getElementById(
            'textoRegistrarUsuario'
        );

        const formulario = botonRegistrar?.closest('form');

        if (
            formulario &&
            botonRegistrar &&
            textoRegistrar &&
            !formulario.dataset.inicializado
        ) {
            formulario.dataset.inicializado = 'true';

            formulario.addEventListener('submit', function (evento) {
                if (formulario.dataset.enviando === 'true') {
                    evento.preventDefault();
                    return;
                }

                // El navegador ya comprobó los campos required,
                // formatos y demás validaciones HTML.
                formulario.dataset.enviando = 'true';

                botonRegistrar.disabled = true;
                textoRegistrar.textContent = 'Registrando usuario...';
            });
        }
    }

    // Primera carga y navegación con Livewire.
    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            inicializarFormularioUsuario,
            { once: true }
        );
    } else {
        inicializarFormularioUsuario();
    }

    // Evitar registrar el mismo listener varias veces.
    if (!window.__farmaciaCrearUsuarioNavegacion) {
        window.__farmaciaCrearUsuarioNavegacion = true;

        document.addEventListener(
            'livewire:navigated',
            inicializarFormularioUsuario
        );
    }

    // Restaurar el botón si se regresa a la página
    // mediante el historial del navegador.
    if (!window.__farmaciaCrearUsuarioPageshow) {
        window.__farmaciaCrearUsuarioPageshow = true;

        window.addEventListener('pageshow', function () {
            const boton = document.getElementById(
                'btnRegistrarUsuario'
            );

            const texto = document.getElementById(
                'textoRegistrarUsuario'
            );

            const formulario = boton?.closest('form');

            if (boton && texto && formulario) {
                boton.disabled = false;
                texto.textContent = 'Registrar Usuario';
                formulario.dataset.enviando = 'false';
            }
        });
    }
})();
</script>

