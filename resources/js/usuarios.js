window.confirmarEliminarUsuario = function(id, nombre) {

    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar al usuario "${nombre}"?`,
        icon: 'warning',

        showCancelButton: true,

        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',

        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#52525b',

        reverseButtons: true,

        background: '#18181b',
        color: '#ffffff',

        customClass: {
            popup: 'rounded-xl'
        }

    }).then((result) => {

        if (result.isConfirmed) {

            const formulario = document.getElementById(
                `form-eliminar-${id}`
            );

            if (formulario) {
                formulario.submit();
            }
        }

    });

};

window.confirmarRestaurarUsuario = function(id, nombre) {

    Swal.fire({
        title: '¿Restaurar usuario?',
        text: `¿Deseas restaurar al usuario "${nombre}"?`,
        icon: 'question',

        showCancelButton: true,

        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar',

        confirmButtonColor: '#059669',
        cancelButtonColor: '#52525b',

        background: '#18181b',
        color: '#ffffff',

        reverseButtons: true

    }).then((resultado) => {

        if (resultado.isConfirmed) {

            document
                .getElementById(`form-restaurar-${id}`)
                .submit();

        }

    });

};
