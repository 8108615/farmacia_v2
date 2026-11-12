@php
    $alerta = null;

    foreach (['success', 'error', 'warning', 'info'] as $tipo) {
        if (session()->has($tipo)) {
            $alerta = [
                'icon' => $tipo,
                'title' => match ($tipo) {
                    'success' => '¡Operación exitosa!',
                    'error' => '¡Ocurrió un error!',
                    'warning' => '¡Atención!',
                    default => 'Información',
                },
                'text' => session($tipo),
            ];

            break;
        }
    }

    if (!$alerta && session()->has('mensaje')) {
        $icono = session('icono', 'info');

        $alerta = [
            'icon' => in_array($icono, ['success', 'error', 'warning', 'info', 'question'])
                ? $icono
                : 'info',
            'title' => session('mensaje'),
            'text' => '',
        ];
    }
@endphp

@if ($alerta || $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alerta = @json($alerta);
            const hayErrores = @json($errors->any());

            function mostrarAlerta() {
                if (!window.Swal) {
                    return;
                }

                const configuracion = {
                    background: '#18181b',
                    color: '#ffffff',
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Aceptar',
                    customClass: {
                        popup: 'rounded-xl'
                    }
                };

                if (alerta) {
                    Swal.fire({
                        ...configuracion,
                        icon: alerta.icon,
                        title: alerta.title,
                        text: alerta.text,
                        timer: alerta.icon === 'success' ? 3500 : undefined,
                        timerProgressBar: alerta.icon === 'success'
                    });
                } else if (hayErrores) {
                    Swal.fire({
                        ...configuracion,
                        icon: 'error',
                        title: '¡Revisa los campos!',
                        text: 'Algunos datos son incorrectos o faltan por completar.'
                    });
                }
            }

            mostrarAlerta();
        });
    </script>
@endif
