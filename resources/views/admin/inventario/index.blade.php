<x-layouts::app title="Gestión de Inventario">

    @php
        // ========================================
        // PERMISOS DEL MÓDULO
        // ========================================

        $puedeVer = auth()->user()->can('inventario.ver');
        $puedeGestionar = auth()->user()->can('inventario.gestionar');

        $mostrarAcciones = $puedeVer || $puedeGestionar;
    @endphp

    <div class="flex flex-col gap-6">

        {{-- ========================================
            ENCABEZADO
        ======================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Gestión de Inventario
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Control de existencias, lotes y fechas de vencimiento
                    del Sistema Farmacia.
                </p>
            </div>

            @can('inventario.gestionar')
                <a href="{{ route('admin.inventario.create') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    + Registrar Lote
                </a>
            @endcan

        </div>

        {{-- ========================================
            INDICADORES DEL INVENTARIO
        ======================================== --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- LOTES VIGENTES --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-400">
                        Lotes Vigentes
                    </p>

                    <div class="rounded-lg bg-green-950 px-3 py-2 text-green-400">
                        ✓
                    </div>
                </div>

                <h3 class="mt-4 text-3xl font-bold text-green-400">
                    {{ number_format($lotesVigentes) }}
                </h3>

                <p class="mt-2 text-xs text-zinc-500">
                    Vencimiento superior a 30 días
                </p>

            </div>

            {{-- PRÓXIMOS A VENCER --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-400">
                        Próximos a Vencer
                    </p>

                    <div class="rounded-lg bg-yellow-950 px-3 py-2 text-yellow-400">
                        !
                    </div>
                </div>

                <h3 class="mt-4 text-3xl font-bold text-yellow-400">
                    {{ number_format($lotesPorVencer) }}
                </h3>

                <p class="mt-2 text-xs text-zinc-500">
                    Vencen dentro de los próximos 30 días
                </p>

            </div>

            {{-- LOTES VENCIDOS --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-400">
                        Lotes Vencidos
                    </p>

                    <div class="rounded-lg bg-red-950 px-3 py-2 text-red-400">
                        ✕
                    </div>
                </div>

                <h3 class="mt-4 text-3xl font-bold text-red-400">
                    {{ number_format($lotesVencidos) }}
                </h3>

                <p class="mt-2 text-xs text-zinc-500">
                    Productos con fecha vencida
                </p>

            </div>

            {{-- STOCK BAJO --}}
            <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-5">

                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-zinc-400">
                        Stock Bajo
                    </p>

                    <div class="rounded-lg bg-orange-950 px-3 py-2 text-orange-400">
                        !
                    </div>
                </div>

                <h3 class="mt-4 text-3xl font-bold text-orange-400">
                    {{ number_format($lotesStockBajo) }}
                </h3>

                <p class="mt-2 text-xs text-zinc-500">
                    Lotes con 1 a {{ $stockMinimo }} unidades
                </p>

            </div>

        </div>

        {{-- ========================================
            BUSCADOR Y FILTROS DEL INVENTARIO
        ======================================== --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-900 p-4">

            <form
                method="GET"
                action="{{ route('admin.inventario.index') }}"
                class="flex flex-col gap-3 lg:flex-row lg:items-center"
            >

                {{-- BUSCAR PRODUCTO O LOTE --}}
                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    placeholder="Buscar por producto, código, código de barras o lote..."
                    class="w-full min-w-0 flex-1 rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                >

                {{-- FILTRAR INVENTARIO --}}
                <select
                    name="filtro"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none lg:w-56"
                >
                    <option value="todos" @selected($filtro === 'todos')>
                        Todos los lotes
                    </option>

                    <option value="vigentes" @selected($filtro === 'vigentes')>
                        Lotes vigentes
                    </option>

                    <option value="por_vencer" @selected($filtro === 'por_vencer')>
                        Próximos a vencer
                    </option>

                    <option value="vencidos" @selected($filtro === 'vencidos')>
                        Lotes vencidos
                    </option>

                    <option value="stock_bajo" @selected($filtro === 'stock_bajo')>
                        Stock bajo
                    </option>

                    <option value="sin_stock" @selected($filtro === 'sin_stock')>
                        Sin stock
                    </option>
                </select>

                {{-- BOTÓN BUSCAR --}}
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Buscar
                </button>

                {{-- BOTÓN LIMPIAR --}}
                <a
                    href="{{ route('admin.inventario.index') }}"
                    class="rounded-lg border border-zinc-700 bg-zinc-950 px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-zinc-800"
                >
                    Limpiar
                </a>

            </form>

        </div>

        {{-- ========================================
            LISTADO DE INVENTARIO
        ======================================== --}}
        <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-700 bg-zinc-950 px-5 py-5">

                <h2 class="font-semibold text-white">
                    Inventario de Productos
                </h2>

                <span class="text-xs text-zinc-400">
                    Total: {{ $lotes->total() }}
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    {{-- ========================================
                        ENCABEZADO DE TABLA
                    ======================================== --}}
                    <thead class="bg-zinc-800 text-xs uppercase text-zinc-300">

                        <tr>
                            <th class="px-4 py-4">Nro.</th>
                            <th class="px-4 py-4">Código</th>
                            <th class="px-4 py-4">Producto</th>
                            <th class="px-4 py-4">Lote</th>
                            <th class="px-4 py-4">Vencimiento</th>
                            <th class="px-4 py-4">Stock</th>
                            <th class="px-4 py-4">Estado</th>

                            @if($mostrarAcciones)
                                <th class="px-4 py-4 text-center">
                                    Acciones
                                </th>
                            @endif
                        </tr>

                    </thead>

                    {{-- ========================================
                        REGISTROS
                    ======================================== --}}
                    <tbody class="divide-y divide-zinc-700">

                        @forelse($lotes as $lote)

                            @php

                                $loteDisponible =
                                    $lote->estado === 'Activo'
                                    && $lote->producto?->estado === 'Activo';
                            @endphp

                            <tr class="hover:bg-zinc-800/50">

                                {{-- NÚMERO --}}
                                <td class="px-4 py-4 text-zinc-300">
                                    {{ $lotes->firstItem() + $loop->index }}
                                </td>

                                {{-- CÓDIGO --}}
                                <td class="px-4 py-4 font-semibold text-blue-400">
                                    {{ $lote->producto?->codigo ?? 'N/D' }}
                                </td>

                                {{-- PRODUCTO --}}
                                <td class="px-4 py-4">

                                    <p class="font-semibold text-white">
                                        {{ $lote->producto?->nombre ?? 'Producto no disponible' }}
                                    </p>

                                    <p class="mt-1 text-xs text-zinc-400">
                                        {{ $lote->producto?->categoria?->nombre ?? 'Sin categoría' }}
                                    </p>

                                </td>

                                {{-- LOTE --}}
                                <td class="px-4 py-4 font-medium text-white">
                                    {{ $lote->numero_lote }}
                                </td>

                                {{-- VENCIMIENTO --}}
                                <td class="px-4 py-4">
                                    @php
                                        $fecha = $lote->fecha_vencimiento;
                                        $diasRestantes = $fecha
                                            ? (int) today()->diffInDays($fecha->copy()->startOfDay(), false)
                                            : null;
                                    @endphp

                                    <p class="whitespace-nowrap font-medium text-white">
                                        {{ $fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                                    </p>

                                    @if($diasRestantes === null)
                                        <span class="text-xs text-zinc-400">Sin fecha de vencimiento</span>
                                    @elseif($diasRestantes < 0)
                                        <span class="text-xs font-semibold text-red-400">
                                            Vencido hace {{ abs($diasRestantes) }}
                                            {{ abs($diasRestantes) === 1 ? 'día' : 'días' }}
                                        </span>
                                    @elseif($diasRestantes === 0)
                                        <span class="text-xs font-semibold text-yellow-400">Vence hoy</span>
                                    @elseif($diasRestantes <= 30)
                                        <span class="text-xs font-semibold text-yellow-400">
                                            Vence en {{ $diasRestantes }}
                                            {{ $diasRestantes === 1 ? 'día' : 'días' }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-green-400">Vigente</span>
                                    @endif
                                </td>

                                {{-- STOCK --}}
                                <td class="px-4 py-4">
                                    @php
                                        $stockActual = (int) $lote->stock_actual;
                                    @endphp

                                    @if($stockActual === 0)
                                        <div class="flex flex-col gap-1">
                                            <span class="font-bold text-red-400">{{ $stockActual }}</span>
                                            <span class="text-xs font-semibold text-red-400">Sin stock</span>
                                        </div>
                                    @elseif($stockActual <= $stockMinimo)
                                        <div class="flex flex-col gap-1">
                                            <span class="font-bold text-orange-400">{{ $stockActual }}</span>
                                            <span class="text-xs font-semibold text-orange-400">Stock bajo</span>
                                        </div>
                                    @else
                                        <span class="font-bold text-green-400">{{ $stockActual }}</span>
                                    @endif
                                </td>

                                {{-- ESTADO --}}
                                <td class="px-4 py-4">

                                    @if($lote->estado === 'Activo')

                                        <span class="inline-flex rounded-full bg-green-950 px-3 py-1 text-xs font-semibold text-green-400">
                                            Activo
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-400">
                                            Inactivo
                                        </span>

                                    @endif

                                </td>

                                {{-- ========================================
                                    ACCIONES
                                ======================================== --}}
                                @if($mostrarAcciones)

                                    <td class="px-4 py-4 text-center">

                                        <div class="flex items-center justify-center gap-2">

                                            {{-- REGISTRAR MOVIMIENTO --}}
                                            @can('inventario.gestionar')

                                                @if($loteDisponible)

                                                    <button
                                                        type="button"
                                                        data-movimiento
                                                        data-lote-id="{{ $lote->id }}"
                                                        data-producto="{{ $lote->producto?->nombre }}"
                                                        data-lote="{{ $lote->numero_lote }}"
                                                        data-stock="{{ $lote->stock_actual }}"
                                                        data-vencido="{{ $diasRestantes !== null && $diasRestantes < 0 ? '1' : '0' }}"
                                                        class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                                                    >
                                                        Movimiento
                                                    </button>

                                                @else

                                                    <span class="text-xs text-zinc-500">
                                                        No disponible
                                                    </span>

                                                @endif

                                            @endcan

                                            {{-- CONSULTAR HISTORIAL --}}
                                            @can('inventario.ver')

                                                <a href="{{ route('admin.inventario.historial', $lote->id) }}"
                                                   class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-zinc-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-zinc-600">
                                                    Historial
                                                </a>

                                            @endcan

                                        </div>

                                    </td>

                                @endif

                            </tr>

                        @empty

                            <tr>
                                <td colspan="{{ 7 + (int) $mostrarAcciones }}"
                                    class="px-6 py-12 text-center">

                                    <p class="font-semibold text-zinc-300">
                                        No se encontraron resultados
                                    </p>

                                    <p class="mt-2 text-sm text-zinc-500">
                                        @if($buscar !== '')
                                            No existen lotes que coincidan con "{{ $buscar }}" y el filtro seleccionado.
                                        @elseif($filtro === 'por_vencer')
                                            No existen lotes próximos a vencer.
                                        @elseif($filtro === 'vencidos')
                                            No existen lotes vencidos.
                                        @elseif($filtro === 'stock_bajo')
                                            No existen lotes con stock bajo.
                                        @elseif($filtro === 'sin_stock')
                                            No existen lotes sin existencias.
                                        @elseif($filtro === 'vigentes')
                                            No existen lotes vigentes.
                                        @else
                                            No hay lotes registrados en el inventario.
                                        @endif
                                    </p>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- ========================================
                PAGINACIÓN
            ======================================== --}}
            @if($lotes->hasPages())

                <div class="border-t border-zinc-700 px-5 py-4">
                    {{ $lotes->links() }}
                </div>

            @endif

        </div>

    </div>

    {{-- ========================================
        MODAL REGISTRAR MOVIMIENTO
    ======================================== --}}
    @can('inventario.gestionar')

        <div
            id="modalMovimiento"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="tituloModalMovimiento"
        >

            <div class="w-full max-w-lg overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900 shadow-2xl">

                {{-- ENCABEZADO DEL MODAL --}}
                <div class="flex items-center justify-between border-b border-zinc-700 bg-zinc-950 px-5 py-4">

                    <div>
                        <h2 id="tituloModalMovimiento" class="text-lg font-bold text-white">
                            Registrar Movimiento
                        </h2>

                        <p class="mt-1 text-xs text-zinc-400">
                            Control de entradas, salidas y ajustes.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="cerrarModalMovimiento"
                        class="rounded-lg px-2 py-1 text-xl text-zinc-400 hover:bg-zinc-800 hover:text-white"
                        aria-label="Cerrar modal">
                        &times;
                    </button>

                </div>

                {{-- FORMULARIO --}}
                <form
                    method="POST"
                    action="{{ route('admin.inventario.movimientos.store') }}"
                    id="formMovimiento"
                    class="p-5"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="lote_id"
                        id="movimientoLoteId"
                        value="{{ old('lote_id') }}"
                    >

                    {{-- INFORMACIÓN DEL PRODUCTO --}}
                    <div class="mb-5 rounded-lg border border-zinc-700 bg-zinc-800 p-4">

                        <p class="text-xs text-zinc-400">
                            Producto
                        </p>

                        <p id="movimientoProducto" class="mt-1 font-semibold text-white">
                            -
                        </p>

                        <div class="mt-3 flex items-center justify-between gap-3 text-xs">

                            <p class="text-zinc-400">
                                Lote:
                                <span id="movimientoLote" class="font-semibold text-white">
                                    -
                                </span>
                            </p>

                            <p class="text-zinc-400">
                                Stock disponible:
                                <span id="movimientoStock" class="font-bold text-green-400">
                                    0
                                </span>
                            </p>

                        </div>

                    </div>

                    {{-- ADVERTENCIA DE LOTE VENCIDO --}}
                    <div
                        id="advertenciaLoteVencido"
                        class="mb-4 hidden rounded-lg border border-red-800 bg-red-950/40 p-4"
                        role="alert"
                    >
                        <p class="text-sm font-semibold text-red-400">
                            ⚠ Medicamento vencido
                        </p>

                        <p class="mt-2 text-xs leading-relaxed text-red-300">
                            Este lote se encuentra vencido.

                            No está permitido registrar movimientos de tipo
                            <strong>Entrada</strong>,
                            <strong>Salida</strong> ni
                            <strong>Ajuste Entrada</strong>.

                            Para retirar existencias vencidas del inventario,
                            utilice <strong>Ajuste Salida</strong>, siempre que
                            cuente con la autorización correspondiente.
                        </p>
                    </div>

                    {{-- ========================================
                        TIPO DE MOVIMIENTO
                    ======================================== --}}

                    <div class="mb-4">

                        <label for="movimientoTipo"
                            class="mb-2 block text-sm font-semibold text-white">
                            Tipo de Movimiento
                            <span class="text-red-400">*</span>
                        </label>

                        <select
                            name="tipo"
                            id="movimientoTipo"
                            required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">Seleccione una opción</option>

                            @can('inventario.gestionar')
                                <option
                                    id="opcionEntrada"
                                    value="Entrada"
                                    @selected(old('tipo') === 'Entrada')
                                >
                                    Entrada
                                </option>

                                <option
                                    id="opcionSalida"
                                    value="Salida"
                                    @selected(old('tipo') === 'Salida')
                                >
                                    Salida
                                </option>
                            @endcan

                            @can('inventario.ajustar')
                                <option
                                    id="opcionAjusteEntrada"
                                    value="Ajuste Entrada"
                                    @selected(old('tipo') === 'Ajuste Entrada')
                                >
                                    Ajuste Entrada
                                </option>

                                <option
                                    id="opcionAjusteSalida"
                                    value="Ajuste Salida"
                                    @selected(old('tipo') === 'Ajuste Salida')
                                >
                                    Ajuste Salida
                                </option>
                            @endcan
                        </select>

                        @error('tipo')
                            <p class="mt-1 text-xs text-red-400">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    {{-- CANTIDAD --}}
                    <div class="mb-4">

                        <label for="movimientoCantidad" class="mb-2 block text-sm font-semibold text-white">
                            Cantidad <span class="text-red-400">*</span>
                        </label>

                        <input
                            type="number"
                            name="cantidad"
                            id="movimientoCantidad"
                            min="1"
                            step="1"
                            value="{{ old('cantidad') }}"
                            required
                            placeholder="Ingrese la cantidad"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                        >

                        @error('cantidad')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- REFERENCIA --}}
                    <div class="mb-4">

                        <label for="movimientoReferencia" class="mb-2 block text-sm font-semibold text-white">
                            Referencia
                        </label>

                        <input
                            type="text"
                            name="referencia"
                            id="movimientoReferencia"
                            maxlength="150"
                            value="{{ old('referencia') }}"
                            placeholder="Ejemplo: AJUSTE-001"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                        >

                        @error('referencia')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- ======================================== --}}
                    {{-- OBSERVACIONES --}}
                    {{-- ======================================== --}}

                    <div class="mb-5">

                        <label
                            for="movimientoObservacion"
                            class="mb-2 block text-sm font-semibold text-white"
                        >
                            Observaciones

                            <span
                                id="observacionObligatoria"
                                class="hidden text-red-400"
                            >*</span>
                        </label>

                        <textarea
                            name="observacion"
                            id="movimientoObservacion"
                            rows="3"
                            maxlength="2000"
                            placeholder="Describa el motivo del movimiento..."
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-3 text-sm text-white placeholder-zinc-500 focus:border-blue-500 focus:outline-none"
                        >{{ old('observacion') }}</textarea>

                        <p
                            id="observacionAyuda"
                            class="mt-1 hidden text-xs text-amber-400"
                        >
                            Debe ingresar una justificación para realizar
                            un ajuste de inventario.
                        </p>

                        @error('observacion')
                            <p class="mt-1 text-xs text-red-400">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    @if($errors->any())
                        <div class="mb-4 rounded-lg border border-red-800 bg-red-950/40 p-3 text-sm text-red-300">
                            <p class="font-semibold">Revise los datos del movimiento:</p>
                            <ul class="mt-2 list-inside list-disc">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- BOTONES --}}
                    <div class="flex justify-end gap-3 border-t border-zinc-700 pt-4">

                        <button
                            type="button"
                            id="cancelarModalMovimiento"
                            class="rounded-lg border border-zinc-700 bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800">
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            id="guardarMovimiento"
                            class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            Guardar Movimiento
                        </button>

                    </div>

                </form>

            </div>

        </div>

    @endcan

    {{-- ========================================
        JAVASCRIPT DEL MODAL
    ======================================== --}}

    @canany(['inventario.gestionar', 'inventario.ajustar'])

    <script>
        (function () {

            function inicializarModalInventario() {

                // ========================================
                // ELEMENTOS PRINCIPALES
                // ========================================

                const modal = document.getElementById('modalMovimiento');

                if (!modal || modal.dataset.inicializado === '1') {
                    return;
                }

                modal.dataset.inicializado = '1';

                const form = document.getElementById('formMovimiento');

                const botones = document.querySelectorAll('[data-movimiento]');

                const loteId = document.getElementById('movimientoLoteId');
                const producto = document.getElementById('movimientoProducto');
                const lote = document.getElementById('movimientoLote');
                const stock = document.getElementById('movimientoStock');

                const tipo = document.getElementById('movimientoTipo');
                const cantidad = document.getElementById('movimientoCantidad');

                const referencia = document.getElementById('movimientoReferencia');
                const observacion = document.getElementById('movimientoObservacion');

                const guardar = document.getElementById('guardarMovimiento');

                const advertenciaVencido = document.getElementById(
                    'advertenciaLoteVencido'
                );

                // ========================================
                // ELEMENTOS DE OBSERVACIONES
                // ========================================

                const observacionObligatoria = document.getElementById(
                    'observacionObligatoria'
                );

                const observacionAyuda = document.getElementById(
                    'observacionAyuda'
                );

                // ========================================
                // VALIDAR ELEMENTOS DEL FORMULARIO
                // ========================================

                if (
                    !form ||
                    !loteId ||
                    !producto ||
                    !lote ||
                    !stock ||
                    !tipo ||
                    !cantidad ||
                    !referencia ||
                    !observacion ||
                    !guardar
                ) {
                    return;
                }

                // ========================================
                // OBSERVACIONES OBLIGATORIAS EN AJUSTES
                // ========================================

                function actualizarObservaciones() {

                    const esAjuste = [
                        'Ajuste Entrada',
                        'Ajuste Salida'
                    ].includes(tipo.value);

                    // Obligatorio únicamente para ajustes.
                    observacion.required = esAjuste;

                    // Mostrar u ocultar asterisco rojo.
                    if (observacionObligatoria) {
                        observacionObligatoria.classList.toggle(
                            'hidden',
                            !esAjuste
                        );
                    }

                    // Mostrar u ocultar mensaje de ayuda.
                    if (observacionAyuda) {
                        observacionAyuda.classList.toggle(
                            'hidden',
                            !esAjuste
                        );
                    }

                    // Cambiar el texto de ejemplo.
                    observacion.placeholder = esAjuste
                        ? 'Describa obligatoriamente el motivo del ajuste...'
                        : 'Describa el motivo del movimiento...';
                }

                // Actualizar al cambiar el tipo.
                tipo.addEventListener('change', actualizarObservaciones);

                // ========================================
                // ADVERTENCIA DE LOTE VENCIDO
                // ========================================

                function actualizarAdvertencia(boton) {

                    const estaVencido = boton?.dataset.vencido === '1';

                    // ========================================
                    // MOSTRAR ADVERTENCIA
                    // ========================================

                    if (advertenciaVencido) {
                        advertenciaVencido.classList.toggle(
                            'hidden',
                            !estaVencido
                        );
                    }

                    // ========================================
                    // OPCIONES DEL TIPO DE MOVIMIENTO
                    // ========================================

                    const opcionEntrada =
                        document.getElementById('opcionEntrada');

                    const opcionSalida =
                        document.getElementById('opcionSalida');

                    const opcionAjusteEntrada =
                        document.getElementById('opcionAjusteEntrada');

                    // ========================================
                    // BLOQUEAR MOVIMIENTOS EN LOTES VENCIDOS
                    // ========================================

                    if (opcionEntrada) {
                        opcionEntrada.disabled = estaVencido;
                    }

                    if (opcionSalida) {
                        opcionSalida.disabled = estaVencido;
                    }

                    if (opcionAjusteEntrada) {
                        opcionAjusteEntrada.disabled = estaVencido;
                    }

                    // ========================================
                    // LIMPIAR OPCIÓN NO PERMITIDA
                    // ========================================

                    const movimientosBloqueados = [
                        'Entrada',
                        'Salida',
                        'Ajuste Entrada'
                    ];

                    if (
                        estaVencido &&
                        movimientosBloqueados.includes(tipo.value)
                    ) {
                        tipo.value = '';
                    }

                    // ========================================
                    // ACTUALIZAR OBSERVACIONES
                    // ========================================

                    actualizarObservaciones();
                }

                // ========================================
                // ABRIR MODAL
                // ========================================

                function abrirModal() {

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    document.body.style.overflow = 'hidden';

                    actualizarObservaciones();

                    tipo.focus();
                }

                // ========================================
                // CERRAR MODAL
                // ========================================

                function cerrarModal() {

                    modal.classList.add('hidden');
                    modal.classList.remove('flex');

                    document.body.style.overflow = '';
                }

                // ========================================
                // ABRIR MODAL DESDE CADA LOTE
                // ========================================

                botones.forEach(function (boton) {

                    boton.addEventListener('click', function () {

                        // Limpiar datos del movimiento anterior.
                        form.reset();

                        // Restablecer botón Guardar.
                        guardar.disabled = false;
                        guardar.textContent = 'Guardar Movimiento';

                        // Cargar información del lote.
                        loteId.value = boton.dataset.loteId;

                        producto.textContent =
                            boton.dataset.producto || '-';

                        lote.textContent =
                            boton.dataset.lote || '-';

                        stock.textContent =
                            boton.dataset.stock || '0';

                        // Validar vencimiento.
                        actualizarAdvertencia(boton);

                        // Actualizar obligatoriedad.
                        actualizarObservaciones();

                        // Mostrar modal.
                        abrirModal();
                    });
                });

                // ========================================
                // BOTONES PARA CERRAR MODAL
                // ========================================

                const botonCerrar = document.getElementById(
                    'cerrarModalMovimiento'
                );

                const botonCancelar = document.getElementById(
                    'cancelarModalMovimiento'
                );

                botonCerrar?.addEventListener(
                    'click',
                    cerrarModal
                );

                botonCancelar?.addEventListener(
                    'click',
                    cerrarModal
                );

                // ========================================
                // CERRAR AL PRESIONAR FUERA DEL MODAL
                // ========================================

                modal.addEventListener('click', function (evento) {

                    if (evento.target === modal) {
                        cerrarModal();
                    }
                });

                // ========================================
                // CERRAR CON TECLA ESCAPE
                // ========================================

                document.addEventListener('keydown', function (evento) {

                    if (
                        evento.key === 'Escape' &&
                        !modal.classList.contains('hidden')
                    ) {
                        cerrarModal();
                    }
                });

                // ========================================
                // EVITAR ENVÍO DOBLE DEL FORMULARIO
                // ========================================

                form.addEventListener('submit', function () {

                    if (!form.checkValidity()) {
                        return;
                    }

                    guardar.disabled = true;
                    guardar.textContent = 'Guardando...';
                });

                // ========================================
                // RECUPERAR DATOS DESPUÉS DE ERRORES
                // ========================================

                const loteAnterior = @json(old('lote_id'));

                if (loteAnterior) {

                    const botonAnterior = Array.from(botones).find(
                        boton =>
                            boton.dataset.loteId === String(loteAnterior)
                    );

                    if (botonAnterior) {

                        loteId.value = botonAnterior.dataset.loteId;

                        producto.textContent =
                            botonAnterior.dataset.producto || '-';

                        lote.textContent =
                            botonAnterior.dataset.lote || '-';

                        stock.textContent =
                            botonAnterior.dataset.stock || '0';

                        // Recuperar datos del formulario.
                        tipo.value = @json(old('tipo', ''));

                        cantidad.value = @json(old('cantidad', ''));

                        referencia.value = @json(old('referencia', ''));

                        observacion.value = @json(old('observacion', ''));

                        // Actualizar vencimiento y observaciones.
                        actualizarAdvertencia(botonAnterior);

                        actualizarObservaciones();

                        // Abrir modal con datos recuperados.
                        abrirModal();
                    }
                }

                // Estado inicial del campo Observaciones.
                actualizarObservaciones();
            }

            // ========================================
            // INICIALIZACIÓN DEL MODAL
            // ========================================

            if (document.readyState === 'loading') {

                document.addEventListener(
                    'DOMContentLoaded',
                    inicializarModalInventario
                );

            } else {

                inicializarModalInventario();
            }

            // ========================================
            // COMPATIBILIDAD CON LIVEWIRE
            // ========================================

            document.addEventListener(
                'livewire:navigated',
                inicializarModalInventario
            );

        })();
    </script>

    @endcanany

</x-layouts::app>
