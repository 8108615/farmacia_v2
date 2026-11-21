<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos Más Vendidos</title>
    <style>
        body { font: 10px "DejaVu Sans", sans-serif; color: #18181b; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #d4d4d8; padding: 6px; text-align: left; }
        th { background: #e4e4e7; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .text-red-400 { color: #b91c1c; }
        .text-amber-400 { color: #92400e; }
        .text-green-400 { color: #166534; }
    </style>
</head>
<body>
    <h1>Productos Más Vendidos</h1>
    <p>Sistema Farmacia · Generado el {{ now()->format('d/m/Y H:i') }}</p>
    <p>Ranking por unidades de ventas confirmadas.</p>
    <p>
        <strong>Categoría:</strong> {{ $categorias[$filtros['categoria_id'] ?? ''] ?? 'Todas' }} ·
        <strong>Desde:</strong> {{ $filtros['fecha_desde'] ?? 'Sin límite' }} ·
        <strong>Hasta:</strong> {{ $filtros['fecha_hasta'] ?? 'Sin límite' }}
    </p>

    <table>
        <thead>
            <tr>
                @foreach ($indicadores as $etiqueta => $valor)
                    <th scope="col">{{ $etiqueta }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($indicadores as $valor)
                    <td>{{ number_format($valor, 0, ',', '.') }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <table>
        @include('admin.reportes.partials.productos-vendidos')
    </table>
</body>
</html>
