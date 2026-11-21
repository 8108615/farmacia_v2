<thead class="bg-zinc-900 text-zinc-300">
    <tr>
        @foreach (['Fecha', 'Producto', 'Lote', 'Tipo', 'Unidades', 'Stock anterior', 'Stock posterior', 'Usuario', 'Referencia y observación'] as $columna)
            <th scope="col" class="px-4 py-3">{{ $columna }}</th>
        @endforeach
    </tr>
</thead>
<tbody class="divide-y divide-zinc-800">
    @forelse ($movimientos as $movimiento)
        <tr>
            <td class="px-4 py-3">{{ $movimiento->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
            <td class="px-4 py-3">
                <strong>{{ $movimiento->lote?->producto?->nombre ?? '—' }}</strong>
                @if ($movimiento->lote?->producto?->codigo)
                    <br>{{ $movimiento->lote->producto->codigo }}
                @endif
            </td>
            <td class="px-4 py-3">{{ $movimiento->lote?->numero_lote ?? '—' }}</td>
            <td class="px-4 py-3">{{ $movimiento->tipo }}</td>
            <td class="px-4 py-3">{{ number_format($movimiento->cantidad, 0, ',', '.') }}</td>
            <td class="px-4 py-3">{{ number_format($movimiento->stock_anterior, 0, ',', '.') }}</td>
            <td class="px-4 py-3">{{ number_format($movimiento->stock_posterior, 0, ',', '.') }}</td>
            <td class="px-4 py-3">{{ $movimiento->usuario?->name ?? '—' }}</td>
            <td class="px-4 py-3">
                {{ $movimiento->referencia ?? '—' }}
                @if (filled($movimiento->observacion))
                    <br>{{ $movimiento->observacion }}
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="9" class="px-4 py-6 text-center">No hay movimientos que coincidan con los filtros.</td></tr>
    @endforelse
</tbody>
