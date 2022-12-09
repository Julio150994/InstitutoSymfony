function eliminarAlumno(id) {
    Swal.fire({
        title: '¿Desea eliminar a este alumno?',
        text: "Este alumno se eliminará definitivamente",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#178ABC',
        cancelButtonColor: '#B91308',
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
    }).then((alumno) => {
        if (alumno.isConfirmed) {
            window.location = '/alumnos/delete/' + id;
        }
        else {
            window.location = '/alumnos';
        }
    });
}