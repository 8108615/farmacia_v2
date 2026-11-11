<x-layouts::app :title="'Dashboard'">

    <div class="flex flex-col gap-6">

        {{-- ENCABEZADO --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Panel de Administración
            </h1>

            <p class="mt-2 text-gray-500 dark:text-gray-400">
                Bienvenido al Sistema de Gestión de Farmacia
            </p>
        </div>

        {{-- TARJETAS ESTADÍSTICAS --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Total de Productos
                </p>
                <h2 class="mt-3 text-3xl font-bold text-blue-500">
                    0
                </h2>
                <p class="mt-2 text-xs text-gray-400">
                    Productos registrados
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Ventas del Día
                </p>
                <h2 class="mt-3 text-3xl font-bold text-green-500">
                    Bs 0.00
                </h2>
                <p class="mt-2 text-xs text-gray-400">
                    Total vendido hoy
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Stock Bajo
                </p>
                <h2 class="mt-3 text-3xl font-bold text-orange-500">
                    0
                </h2>
                <p class="mt-2 text-xs text-gray-400">
                    Productos por reponer
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Productos por Vencer
                </p>
                <h2 class="mt-3 text-3xl font-bold text-red-500">
                    0
                </h2>
                <p class="mt-2 text-xs text-gray-400">
                    Próximos a su vencimiento
                </p>
            </div>

        </div>

        {{-- PANEL INFERIOR --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Últimas Ventas
                </h3>

                <div class="mt-6 rounded-lg border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">
                        Todavía no existen ventas registradas.
                    </p>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Alertas de Inventario
                </h3>

                <div class="mt-6 rounded-lg border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">
                        Las alertas aparecerán cuando configuremos el inventario.
                    </p>
                </div>
            </div>

        </div>

    </div>

</x-layouts::app>
