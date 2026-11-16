
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Historial de Inventario - Farmacia</title>

    <style>
        @page {
            margin: 28px 30px 35px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #263238;
        }

        .encabezado {
            width: 100%;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .empresa {
            font-size: 22px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .subtitulo {
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
        }

        .fecha {
            text-align: right;
            font-size: 9px;
            color: #64748b;
        }

        .titulo-seccion {
            background: #e2e8f0;
            color: #1e293b;
            font-size: 11px;
            font-weight: bold;
            padding: 9px;
            margin-bottom: 10px;
        }

        .tabla-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .tabla-info td {
            padding: 7px;
            border: 1px solid #e2e8f0;
        }

        .etiqueta {
            font-size: 9px;
            color: #64748b;
        }

        .valor {
            font-size: 10px;
            font-weight: bold;
            margin-top: 4px;
        }

        .resumen {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .resumen td {
            width: 25%;
            padding: 12px;
            text-align: center;
            border: 1px solid #cbd5e1;
        }

        .resumen-titulo {
            font-size: 9px;
            color: #64748b;
        }

        .resumen-valor {
            font-size: 17px;
            font-weight: bold;
            margin-top: 6px;
        }

        .verde {
            color: #15803d;
        }

        .rojo {
            color: #dc2626;
        }

        .azul {
            color: #2563eb;
        }

        .naranja {
            color: #c2410c;
        }

        .tabla-movimientos {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            table-layout: fixed;
        }

        .tabla-movimientos th {
            background: #1e293b;
            color: #ffffff;
            padding: 8px 5px;
            text-align: left;
        }

        .tabla-movimientos td {
            padding: 7px 5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .tabla-movimientos tbody tr:nth-child(even) {
            background: #f1f5f9;
        }

        .tabla-movimientos thead {
            display: table-header-group;
        }

        .tabla-movimientos tr {
            page-break-inside: avoid;
        }

        .sin-registros {
            text-align: center;
            padding: 20px;
            color: #64748b;
        }

        .filtros {
            margin-bottom: 15px;
            font-size: 9px;
            color: #475569;
        }

        .pie {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
            font-size: 8px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- ========================================
         ENCABEZADO DEL REPORTE
    ======================================== --}}

    <table class="encabezado">
        <tr>
            <td>
                <div class="empresa">FARMACIA</div>

                <div class="subtitulo">
                    Reporte de Historial de Inventario
                </div>
            </td>

            <td class="fecha">
                <strong>Fecha de emisión:</strong>
                {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    {{-- ========================================
         INFORMACIÓN DEL LOTE
    ======================================== --}}

    <div class="titulo-seccion">
        INFORMACIÓN DEL LOTE
    </div>

    <table class="tabla-info">
        <tr>
            <td width="35%">
                <div class="etiqueta">PRODUCTO</div>

                <div class="valor">
                    {{ $lote->producto?->nombre ?? 'No disponible' }}
                </div>
            </td>

            <td width="25%">
                <div class="etiqueta">NÚMERO DE LOTE</div>

                <div class="valor">
                    {{ $lote->numero_lote }}
                </div>
            </td>

            <td width="20%">
                <div class="etiqueta">STOCK ACTUAL</div>

                <div class="valor verde">
                    {{ number_format($lote->stock_actual) }}
                </div>
            </td>

            <td width="20%">
                <div class="etiqueta">VENCIMIENTO</div>

                <div class="valor">
                    {{ $lote->fecha_vencimiento?->format('d/m/Y') ?? 'Sin fecha' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================
         RESUMEN DE MOVIMIENTOS
    ======================================== --}}

    <div class="titulo-seccion">
        RESUMEN DE MOVIMIENTOS EXPORTADOS
    </div>

    <table class="resumen">
        <tr>
            <td>
                <div class="resumen-titulo">
                    TOTAL ENTRADAS
                </div>

                <div class="resumen-valor verde">
                    +{{ number_format($resumen['entradas']) }}
                </div>
            </td>

            <td>
                <div class="resumen-titulo">
                    TOTAL SALIDAS
                </div>

                <div class="resumen-valor rojo">
                    {{ number_format($resumen['salidas']) }}
                </div>
            </td>

            <td>
                <div class="resumen-titulo">
                    AJUSTES DE ENTRADA
                </div>

                <div class="resumen-valor azul">
                    +{{ number_format($resumen['ajustes_entrada']) }}
                </div>
            </td>

            <td>
                <div class="resumen-titulo">
                    AJUSTES DE SALIDA
                </div>

                <div class="resumen-valor naranja">
                    -{{ number_format($resumen['ajustes_salida']) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================
         FILTROS APLICADOS
    ======================================== --}}

    <div class="filtros">
        <strong>Filtros aplicados:</strong>

        Tipo:
        {{ $filtros['tipo'] ?? 'Todos los movimientos' }}

        |

        Desde:
        {{ !empty($filtros['fecha_desde'])
            ? \Illuminate\Support\Carbon::parse($filtros['fecha_desde'])->format('d/m/Y')
            : 'Sin límite' }}

        |

        Hasta:
        {{ !empty($filtros['fecha_hasta'])
            ? \Illuminate\Support\Carbon::parse($filtros['fecha_hasta'])->format('d/m/Y')
            : 'Sin límite' }}
    </div>

    {{-- ========================================
         TABLA DE MOVIMIENTOS
    ======================================== --}}

    <div class="titulo-seccion">
        DETALLE DE MOVIMIENTOS
        ({{ $movimientos->count() }} registros)
    </div>

    <table class="tabla-movimientos">
        <thead>
            <tr>
                <th width="4%">Nro.</th>
                <th width="11%">Fecha</th>
                <th width="12%">Tipo</th>
                <th width="7%">Cantidad</th>
                <th width="8%">Anterior</th>
                <th width="8%">Posterior</th>
                <th width="15%">Responsable</th>
                <th width="14%">Referencia</th>
                <th width="21%">Observación</th>
            </tr>
        </thead>

        <tbody>

            @forelse($movimientos as $movimiento)

                @php
                    $entrada = in_array(
                        $movimiento->tipo,
                        ['Entrada', 'Ajuste Entrada'],
                        true
                    );
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ $movimiento->created_at?->format('d/m/Y H:i') }}
                    </td>

                    <td>
                        {{ $movimiento->tipo }}
                    </td>

                    <td class="{{ $entrada ? 'verde' : 'rojo' }}">
                        {{ $entrada ? '+' : '-' }}{{ $movimiento->cantidad }}
                    </td>

                    <td>
                        {{ $movimiento->stock_anterior }}
                    </td>

                    <td>
                        {{ $movimiento->stock_posterior }}
                    </td>

                    <td>
                        {{ $movimiento->usuario?->name ?? 'No disponible' }}
                    </td>

                    <td>
                        {{ $movimiento->referencia ?? 'Sin referencia' }}
                    </td>

                    <td>
                        {{ $movimiento->observacion ?? 'Sin observaciones' }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="9" class="sin-registros">
                        No existen movimientos para los filtros seleccionados.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

    {{-- ========================================
         PIE DEL REPORTE
    ======================================== --}}

    <div class="pie">
        Sistema Farmacia | Reporte generado automáticamente
        <p>
            Documento informativo de movimientos de inventario.
        </p>
    </div>

</body>
</html>
