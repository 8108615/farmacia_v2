<x-layouts::app :title="'Editar Usuario'">

    <div class="flex flex-col gap-6">

        {{-- ============================================
             ENCABEZADO
        ============================================ --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white">
                    Editar Usuario
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Actualice la información del usuario en el Sistema Farmacia.
                </p>
            </div>

            <a
                href="{{ route('admin.usuarios.index') }}"
                class="rounded-lg border border-zinc-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-700"
            >
                ← Volver al listado
            </a>
        </div>

        {{-- ============================================
             MENSAJES DE VALIDACIÓN
        ============================================ --}}
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

        {{-- ============================================
             FORMULARIO
        ============================================ --}}
        <form
            id="formEditarUsuario"
            action="{{ route('admin.usuarios.update', $usuario->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="rounded-xl border border-zinc-700 bg-zinc-900 p-6"
        >
            @csrf
            @method('PUT')

            <div class="mb-6 border-b border-zinc-700 pb-4">
                <h2 class="text-xl font-semibold text-white">
                    Información del Usuario
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Modifique los datos personales, de contacto y acceso al sistema.
                </p>
            </div>

            {{-- ============================================
                 FOTOGRAFÍA DE PERFIL
            ============================================ --}}
            <div class="mb-8">

                <label class="mb-3 block text-sm font-semibold text-white">
                    Fotografía de Perfil
                </label>

                <div class="flex flex-wrap items-center gap-5">

                    {{-- VISTA PREVIA --}}
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-zinc-600 bg-zinc-800">

                        <img
                            id="fotoPreview"
                            src="{{ $usuario->foto_perfil ? asset('storage/' . $usuario->foto_perfil) : '' }}"
                            alt="Fotografía del usuario"
                            class="{{ $usuario->foto_perfil ? '' : 'hidden' }} h-full w-full object-cover"
                        >

                        <div
                            id="fotoPlaceholder"
                            class="{{ $usuario->foto_perfil ? 'hidden' : 'flex' }} flex-col items-center text-zinc-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="32"
                                height="32"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="9" cy="9" r="2"/>
                                <path d="m21 15-5-5L5 21"/>
                            </svg>
                        </div>

                    </div>

                    {{-- SELECCIONAR FOTOGRAFÍA --}}
                    <div>

                        <label
                            for="foto_perfil"
                            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/>
                                <circle cx="12" cy="13" r="3"/>
                            </svg>

                            Cambiar Fotografía
                        </label>

                        <input
                            type="file"
                            name="foto_perfil"
                            id="foto_perfil"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="hidden"
                        >

                        <p
                            id="nombreFoto"
                            class="mt-2 text-xs italic text-zinc-400"
                        >
                            {{ $usuario->foto_perfil ? 'Fotografía actual registrada' : 'Ningún archivo seleccionado' }}
                        </p>

                        <p class="mt-1 text-xs text-zinc-500">
                            JPG, PNG o WEBP. Máximo 2 MB.
                        </p>

                        <p class="mt-1 text-xs text-zinc-500">
                            Si no selecciona una nueva fotografía, se conservará la actual.
                        </p>

                        @error('foto_perfil')
                            <p class="mt-1 text-xs text-red-400">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>
            </div>

            {{-- ============================================
                 CAMPOS DEL FORMULARIO
            ============================================ --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                {{-- ROL --}}
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
                                @selected(old('rol', $usuario->getRoleNames()->first()) === $rol->name)
                            >
                                {{ $rol->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('rol')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NOMBRES Y APELLIDOS --}}
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-white">
                        Nombres y Apellidos <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $usuario->name) }}"
                        placeholder="Ingrese nombres y apellidos"
                        maxlength="255"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CORREO ELECTRÓNICO --}}
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-white">
                        Correo Electrónico <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $usuario->email) }}"
                        placeholder="usuario@correo.com"
                        maxlength="255"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TIPO DE DOCUMENTO --}}
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

                        <option
                            value="cedula de identidad"
                            @selected(old('tipo_documento', $usuario->tipo_documento) === 'cedula de identidad')
                        >
                            Cédula de Identidad
                        </option>

                        <option
                            value="Pasaporte"
                            @selected(old('tipo_documento', $usuario->tipo_documento) === 'Pasaporte')
                        >
                            Pasaporte
                        </option>
                    </select>

                    @error('tipo_documento')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NÚMERO DE DOCUMENTO --}}
                <div>
                    <label for="numero_documento" class="mb-2 block text-sm font-medium text-white">
                        Número de Documento <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="numero_documento"
                        id="numero_documento"
                        value="{{ old('numero_documento', $usuario->numero_documento) }}"
                        placeholder="Ingrese el número de documento"
                        maxlength="50"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('numero_documento')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NÚMERO DE CELULAR --}}
                <div>
                    <label for="celular" class="mb-2 block text-sm font-medium text-white">
                        Número de Celular <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="celular"
                        id="celular"
                        value="{{ old('celular', $usuario->celular) }}"
                        placeholder="Ingrese el número de celular"
                        maxlength="30"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('celular')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DIRECCIÓN --}}
                <div class="md:col-span-2 xl:col-span-3">
                    <label for="direccion" class="mb-2 block text-sm font-medium text-white">
                        Dirección <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        id="direccion"
                        value="{{ old('direccion', $usuario->direccion) }}"
                        placeholder="Ingrese la dirección del usuario"
                        maxlength="255"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('direccion')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- FECHA DE NACIMIENTO --}}
                <div>
                    <label for="fecha_nacimiento" class="mb-2 block text-sm font-medium text-white">
                        Fecha de Nacimiento <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="date"
                        name="fecha_nacimiento"
                        id="fecha_nacimiento"
                        value="{{ old('fecha_nacimiento', $usuario->fecha_nacimiento ? \Illuminate\Support\Carbon::parse($usuario->fecha_nacimiento)->format('Y-m-d') : '') }}"
                        max="{{ now()->toDateString() }}"
                        required
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('fecha_nacimiento')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- GÉNERO --}}
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

                        <option
                            value="Masculino"
                            @selected(old('genero', $usuario->genero) === 'Masculino')
                        >
                            Masculino
                        </option>

                        <option
                            value="Femenino"
                            @selected(old('genero', $usuario->genero) === 'Femenino')
                        >
                            Femenino
                        </option>
                    </select>

                    @error('genero')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ESTADO DEL USUARIO --}}
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
                        <option
                            value="Activo"
                            @selected(old('estado', $usuario->estado) === 'Activo')
                        >
                            Activo
                        </option>

                        <option
                            value="Inactivo"
                            @selected(old('estado', $usuario->estado) === 'Inactivo')
                        >
                            Inactivo
                        </option>
                    </select>

                    @error('estado')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ============================================
                     NUEVA CONTRASEÑA
                ============================================ --}}
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-white">
                        Nueva Contraseña
                        <span class="text-zinc-400">(Opcional)</span>
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Dejar vacío para conservar"
                            autocomplete="new-password"
                            minlength="8"
                            class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 pr-20 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                        >

                        <button
                            type="button"
                            data-toggle-password="password"
                            aria-label="Mostrar nueva contraseña"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-3 text-xs font-semibold text-blue-400 transition hover:text-blue-300"
                        >
                            Mostrar
                        </button>
                    </div>

                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONFIRMAR NUEVA CONTRASEÑA --}}
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-white">
                        Confirmar Nueva Contraseña
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Repita la nueva contraseña"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-3 pr-20 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                        >

                        <button
                            type="button"
                            data-toggle-password="password_confirmation"
                            aria-label="Mostrar confirmación de contraseña"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-3 text-xs font-semibold text-blue-400 transition hover:text-blue-300"
                        >
                            Mostrar
                        </button>
                    </div>

                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- INFORMACIÓN DE CONTRASEÑA --}}
                <div class="md:col-span-2 xl:col-span-3">
                    <div class="rounded-lg border border-blue-900/50 bg-blue-950/20 px-4 py-3">
                        <p class="text-sm text-blue-300">
                            Por seguridad, la contraseña actual no se muestra.
                            Si deja ambos campos vacíos, se conservará la contraseña existente.
                            Si desea cambiarla, ingrese una nueva contraseña
                            de mínimo 8 caracteres y confírmela.
                        </p>
                    </div>
                </div>

            </div>

            {{-- ============================================
                 BOTONES
            ============================================ --}}
            <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-zinc-700 pt-6">

                <a
                    href="{{ route('admin.usuarios.index') }}"
                    class="rounded-lg border border-zinc-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    id="btnActualizarUsuario"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{-- ÍCONO GUARDAR --}}
                    <svg
                        id="iconoActualizar"
                        xmlns="http://www.w3.org/2000/svg"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>

                    {{-- ÍCONO DE CARGA --}}
                    <svg
                        id="iconoCargando"
                        class="hidden h-5 w-5 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        />
                    </svg>

                    <span id="textoActualizarUsuario">
                        Actualizar Usuario
                    </span>
                </button>

            </div>

        </form>

    </div>

</x-layouts::app>

{{-- ============================================
     JAVASCRIPT DEL FORMULARIO
============================================ --}}
<script>
(function () {

    function inicializarEditarUsuario() {

        const formulario = document.getElementById('formEditarUsuario');

        if (!formulario || formulario.dataset.inicializado === 'true') {
            return;
        }

        formulario.dataset.inicializado = 'true';

        // ============================================
        // FOTOGRAFÍA DE PERFIL
        // ============================================

        const inputFoto = document.getElementById('foto_perfil');
        const preview = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('fotoPlaceholder');
        const nombreFoto = document.getElementById('nombreFoto');

        const fotoActual = @json(
            $usuario->foto_perfil
                ? asset('storage/' . $usuario->foto_perfil)
                : null
        );

        let fotoUrl = null;

        function restaurarFoto() {

            if (fotoUrl) {
                URL.revokeObjectURL(fotoUrl);
                fotoUrl = null;
            }

            if (fotoActual) {
                preview.src = fotoActual;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                nombreFoto.textContent = 'Fotografía actual registrada';
            } else {
                preview.removeAttribute('src');
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                nombreFoto.textContent = 'Ningún archivo seleccionado';
            }
        }

        if (inputFoto && preview && placeholder && nombreFoto) {

            inputFoto.addEventListener('change', function () {

                const archivo = this.files[0];

                if (!archivo) {
                    restaurarFoto();
                    return;
                }

                const tiposPermitidos = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                const tamañoMaximo = 2 * 1024 * 1024;

                if (
                    !tiposPermitidos.includes(archivo.type) ||
                    archivo.size > tamañoMaximo
                ) {

                    this.value = '';
                    restaurarFoto();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fotografía no válida',
                            text: 'Seleccione una imagen JPG, PNG o WEBP de máximo 2 MB.',
                            confirmButtonColor: '#2563eb',
                            background: '#18181b',
                            color: '#ffffff'
                        });
                    } else {
                        alert(
                            'Seleccione una imagen JPG, PNG o WEBP de máximo 2 MB.'
                        );
                    }

                    return;
                }

                if (fotoUrl) {
                    URL.revokeObjectURL(fotoUrl);
                }

                fotoUrl = URL.createObjectURL(archivo);

                preview.src = fotoUrl;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');

                nombreFoto.textContent = archivo.name;
            });
        }

        // ============================================
        // MOSTRAR / OCULTAR CONTRASEÑAS
        // ============================================

        formulario
            .querySelectorAll('[data-toggle-password]')
            .forEach(function (boton) {

                boton.addEventListener('click', function () {

                    const campo = document.getElementById(
                        boton.dataset.togglePassword
                    );

                    if (!campo) {
                        return;
                    }

                    const mostrar = campo.type === 'password';

                    campo.type = mostrar ? 'text' : 'password';

                    boton.textContent = mostrar
                        ? 'Ocultar'
                        : 'Mostrar';

                    boton.setAttribute(
                        'aria-pressed',
                        String(mostrar)
                    );

                    boton.setAttribute(
                        'aria-label',
                        mostrar
                            ? 'Ocultar contraseña'
                            : 'Mostrar contraseña'
                    );
                });
            });

        // ============================================
        // VALIDAR CONTRASEÑAS
        // ============================================

        const password = document.getElementById('password');
        const confirmacion = document.getElementById(
            'password_confirmation'
        );

        function limpiarErrorPassword() {
            if (confirmacion) {
                confirmacion.setCustomValidity('');
            }
        }

        if (password) {
            password.addEventListener('input', limpiarErrorPassword);
        }

        if (confirmacion) {
            confirmacion.addEventListener('input', limpiarErrorPassword);
        }

        // ============================================
        // ENVÍO DEL FORMULARIO
        // ============================================

        formulario.addEventListener('submit', function (evento) {

            if (formulario.dataset.enviando === 'true') {
                evento.preventDefault();
                return;
            }

            // Validación de contraseñas
            if (password && confirmacion) {

                limpiarErrorPassword();

                if (password.value !== confirmacion.value) {

                    evento.preventDefault();

                    confirmacion.setCustomValidity(
                        'Las contraseñas no coinciden.'
                    );

                    confirmacion.reportValidity();
                    return;
                }
            }

            // Validación HTML del formulario
            if (!formulario.checkValidity()) {
                return;
            }

            formulario.dataset.enviando = 'true';

            const boton = document.getElementById(
                'btnActualizarUsuario'
            );

            const texto = document.getElementById(
                'textoActualizarUsuario'
            );

            const icono = document.getElementById(
                'iconoActualizar'
            );

            const cargando = document.getElementById(
                'iconoCargando'
            );

            if (boton) {
                boton.disabled = true;
            }

            if (texto) {
                texto.textContent = 'Actualizando usuario...';
            }

            if (icono) {
                icono.classList.add('hidden');
            }

            if (cargando) {
                cargando.classList.remove('hidden');
            }
        });

        // Restaurar el botón al volver a la página
        // mediante el historial del navegador.
        window.addEventListener('pageshow', function () {

            formulario.dataset.enviando = 'false';

            const boton = document.getElementById(
                'btnActualizarUsuario'
            );

            const texto = document.getElementById(
                'textoActualizarUsuario'
            );

            const icono = document.getElementById(
                'iconoActualizar'
            );

            const cargando = document.getElementById(
                'iconoCargando'
            );

            if (boton) boton.disabled = false;

            if (texto) {
                texto.textContent = 'Actualizar Usuario';
            }

            if (icono) {
                icono.classList.remove('hidden');
            }

            if (cargando) {
                cargando.classList.add('hidden');
            }
        });

        // Liberar recursos de la vista previa
        document.addEventListener('livewire:navigating', function () {
            if (fotoUrl) {
                URL.revokeObjectURL(fotoUrl);
                fotoUrl = null;
            }
        }, { once: true });

    }

    // Carga normal
    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            inicializarEditarUsuario
        );
    } else {
        inicializarEditarUsuario();
    }

    // Navegación con Livewire
    document.addEventListener(
        'livewire:navigated',
        inicializarEditarUsuario
    );

})();
</script>
