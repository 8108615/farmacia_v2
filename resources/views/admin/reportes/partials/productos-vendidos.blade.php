<thead class="bg-zinc-900 text-zinc-300">
    <tr>
        @foreach (['Posición', 'Producto', 'Categoría', 'Unidades vendidas'] as $columna)
            <th scope="col" class="px-4 py-3">{{ $columna }}</th>
        @endforeach
    </tr>
</thead>
<tbody class="divide-y divide-zinc-800">
    @forelse ($productos as $producto)
        <tr>
            <td class="px-4 py-3">{{ $posicionInicial + $loop->index }}</td>
            <td class="px-4 py-3"><strong>{{ $producto->nombre }}</strong><br>{{ $producto->codigo }}</td>
            <td class="px-4 py-3">{{ $producto->categoria ?? '—' }}</td>
            <td class="px-4 py-3">{{ number_format($producto->unidades, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="px-4 py-6 text-center">No hay ventas confirmadas que coincidan con los filtros.</td></tr>
    @endforelse
</tbody>
