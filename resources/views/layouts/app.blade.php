<x-layouts::app.sidebar :title="$title ?? null">

    <flux:main>
        {{-- CONTENIDO DEL MÓDULO --}}
        {{ $slot }}
    </flux:main>

    {{-- ALERTAS GLOBALES SWEETALERT2 --}}
    <x-alertas-globales />

</x-layouts::app.sidebar>
