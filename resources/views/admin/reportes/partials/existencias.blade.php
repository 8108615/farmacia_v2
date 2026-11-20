<thead class="bg-zinc-900 text-zinc-300">
    <tr>
        @foreach (['Producto', 'Categoría', 'Lote', 'Vencimiento', 'Estado del lote', 'Unidades'] as $columna)
            <th scope="col" class="px-4 py-3">{{ $columna }}</th>
        @endforeach
    </tr>
</thead>
<tbody class="divide-y divide-zinc-800">
    @forelse ($lotes as $lote)
        <tr>
            <td class="px-4 py-3">
                <strong>{{ $lote->producto->nombre }}</strong><br>
                <span class="text-xs text-zinc-400">{{ $lote->producto->codigo }}</span>
            </td>
            <td class="px-4 py-3">{{ $lote->producto->categoria?->nombre ?? '—' }}</td>
            <td class="px-4 py-3">{{ $lote->numero_lote }}</td>
            <td class="px-4 py-3">
                {{ $lote->fecha_vencimiento->format('d/m/Y') }}
                @if ($lote->fecha_vencimiento->lt($hoy))
                    <br><span class="text-xs text-red-400">Vencido</span>
                @elseif ($lote->fecha_vencimiento->lte($limite))
                    <br><span class="text-xs text-amber-400">Por vencer</span>
                @endif
            </td>
            <td class="px-4 py-3">{{ $lote->estado }}</td>
            <td class="px-4 py-3">{{ number_format($lote->stock_actual, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="6" class="px-4 py-6 text-center">No hay lotes que coincidan con los filtros.</td></tr>
    @endforelse
</tbody>

