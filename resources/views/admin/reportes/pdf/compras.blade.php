<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras - Farmacia</title>

    <style>
        @page {
            margin: 25px 30px 35px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #263238;
        }

        .encabezado {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .titulo {
            font-size: 21px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .subtitulo {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }

        .texto-gris {
            color: #64748b;
        }

        .seccion {
            margin-top: 18px;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .filtros {
            background: #f1f5f9;
            padding: 12px;
            border: 1px solid #cbd5e1;
            line-height: 1.8;
        }

        .resumen {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }

        .resumen td {
            width: 33.33%;
            padding: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .resumen .numero {
            font-size: 17px;
            font-weight: bold;
            color: #1d4ed8;
            margin-top: 5px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tabla th {
            background: #1e3a8a;
            color: white;
            padding: 9px 6px;
            text-align: left;
            font-size: 9px;
        }

        .tabla td {
            padding: 8px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }

        .tabla tr:nth-child(even) {
            background: #f8fafc;
        }

        .derecha {
            text-align: right !important;
        }

        .centro {
            text-align: center !important;
        }

        .pie {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <div class="encabezado">
        <div class="titulo">FARMACIA</div>
        <div class="subtitulo">REPORTE GENERAL DE COMPRAS</div>

        <p class="texto-gris">
            Generado: {{ now()->format('d/m/Y H:i:s') }}
            <br>
            Usuario: {{ auth()->user()->name ?? 'Sistema' }}
        </p>
    </div>

    <!-- FILTROS -->
    <div class="seccion">Filtros aplicados</div>

    <div class="filtros">
        <strong>Fecha inicial:</strong>
        {{ $fechaInicio ? \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') : 'Todas' }}

        &nbsp;&nbsp;&nbsp;

        <strong>Fecha final:</strong>
        {{ $fechaFin ? \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') : 'Todas' }}

        &nbsp;&nbsp;&nbsp;

        <strong>Estado:</strong>
        {{ $estado ?: 'Todos los estados' }}

        <br>

        <strong>Proveedor:</strong>
        {{ $proveedorSeleccionado?->razon_social ?? 'Todos los proveedores' }}
    </div>

    <!-- INDICADORES -->
    <div class="seccion">Resumen de compras</div>

    <table class="resumen">
        <tr>
            <td>
                Compras registradas
                <div class="numero">{{ $totalCompras }}</div>
            </td>

            <td>
                Compras confirmadas
                <div class="numero">{{ $comprasConfirmadas }}</div>
            </td>

            <td>
                Compras en borrador
                <div class="numero">{{ $comprasBorrador }}</div>
            </td>
        </tr>

        <tr>
            <td>
                Compras anuladas
                <div class="numero">{{ $comprasAnuladas }}</div>
            </td>

            <td>
                Total confirmado
                <div class="numero">
                    Bs {{ number_format($totalConfirmado, 2, ',', '.') }}
                </div>
            </td>

            <td>
                Descuentos confirmados
                <div class="numero">
                    Bs {{ number_format($descuentosConfirmados, 2, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- DETALLE DE COMPRAS -->
    <div class="seccion">Detalle de compras</div>

    <table class="tabla">
        <thead>
            <tr>
                <th>CÓDIGO</th>
                <th>FECHA</th>
                <th>PROVEEDOR</th>
                <th>USUARIO</th>
                <th class="derecha">SUBTOTAL (Bs)</th>
                <th class="derecha">DESCUENTO (Bs)</th>
                <th class="derecha">TOTAL (Bs)</th>
                <th class="centro">ESTADO</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($compras as $compra)
                <tr>
                    <td>{{ $compra->codigo }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ $compra->proveedor?->razon_social ?? 'Sin proveedor' }}
                    </td>

                    <td>
                        {{ $compra->usuario?->name ?? 'Sin usuario' }}
                    </td>

                    <td class="derecha">
                        {{ number_format($compra->subtotal, 2, ',', '.') }}
                    </td>

                    <td class="derecha">
                        {{ number_format($compra->descuento, 2, ',', '.') }}
                    </td>

                    <td class="derecha">
                        <strong>
                            {{ number_format($compra->total, 2, ',', '.') }}
                        </strong>
                    </td>

                    <td class="centro">
                        {{ $compra->estado }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="centro">
                        No se encontraron compras con los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- PIE DE PÁGINA -->
    <div class="pie">
        Sistema Farmacia | Reporte de Compras |
        Documento generado automáticamente.
    </div>

</body>
</html>
