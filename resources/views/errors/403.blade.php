<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>403 - Acceso Denegado | Farmacia</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-zinc-950 text-white antialiased">

    <main class="flex min-h-screen items-center justify-center px-4 py-10">

        <div class="w-full max-w-xl">

            {{-- Tarjeta principal --}}
            <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-2xl">

                {{-- Línea decorativa --}}
                <div class="h-1 bg-gradient-to-r from-blue-600 via-cyan-500 to-emerald-500"></div>

                <div class="px-6 py-10 text-center sm:px-10">

                    {{-- Identidad del sistema --}}
                    <div class="mb-8 flex items-center justify-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="25"
                                 height="25"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">

                                <path d="M12 2v20"/>
                                <path d="M2 12h20"/>

                            </svg>

                        </div>

                        <div class="text-left">
                            <h1 class="text-lg font-bold tracking-wide text-white">
                                FARMACIA
                            </h1>

                            <p class="text-xs text-zinc-400">
                                Sistema de Gestión
                            </p>
                        </div>

                    </div>

                    {{-- Ícono de acceso denegado --}}
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full border border-red-500/30 bg-red-500/10">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="48"
                             height="48"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.6"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             class="text-red-400">

                            <rect x="4" y="10" width="16" height="11" rx="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            <circle cx="12" cy="15" r="1"/>
                            <path d="M12 16v2"/>

                        </svg>

                    </div>

                    {{-- Código de error --}}
                    <p class="text-7xl font-extrabold tracking-tight text-red-400">
                        403
                    </p>

                    <h2 class="mt-4 text-2xl font-bold text-white">
                        Acceso Denegado
                    </h2>

                    <p class="mx-auto mt-4 max-w-md text-sm leading-7 text-zinc-400">
                        No tienes los permisos necesarios para acceder
                        a esta sección del Sistema Farmacia.
                    </p>

                    {{-- Mensaje informativo --}}
                    <div class="mt-7 rounded-xl border border-blue-800/50 bg-blue-950/20 p-4 text-left">

                        <div class="flex items-start gap-3">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="20"
                                 height="20"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 class="mt-0.5 shrink-0 text-blue-400">

                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>

                            </svg>

                            <div>
                                <h3 class="text-sm font-semibold text-blue-400">
                                    Información importante
                                </h3>

                                <p class="mt-1 text-sm leading-6 text-zinc-300">
                                    Si consideras que deberías tener acceso
                                    a esta función, comunícate con el
                                    administrador del sistema para solicitar
                                    los permisos correspondientes.
                                </p>
                            </div>

                        </div>

                    </div>

                    {{-- Botones --}}
                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">

                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">

                            Ir al Dashboard
                        </a>

                        <button type="button"
                                onclick="window.history.back()"
                                class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700">

                            Volver atrás
                        </button>

                    </div>

                </div>

                {{-- Pie de página --}}
                <div class="border-t border-zinc-800 bg-zinc-950/60 px-6 py-4 text-center">

                    <p class="text-xs text-zinc-500">
                        &copy; {{ date('Y') }} Farmacia |
                        Sistema de Gestión Farmacéutica
                    </p>

                </div>

            </div>

            <p class="mt-5 text-center text-xs text-zinc-600">
                Error HTTP 403 - No autorizado
            </p>

        </div>

    </main>

</body>
</html>
