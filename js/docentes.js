    function abrirModal(id = null) {
        document.getElementById('modalForm').style.display = 'flex';
        if(id) {
            // Cargar datos del docente para edición (puedes usar fetch o pasar datos por dataset)
            document.getElementById('tituloModal').textContent = 'Editar Docente';
            document.getElementById('accion').value = 'editar';
            let row = document.querySelector('tr td:first-child[textContent="'+id+'"]').parentNode;
            document.getElementById('id_docente').value = id;
            document.getElementById('nombre').value = row.children[1].textContent;
            document.getElementById('apellidos').value = row.children[2].textContent;
            document.getElementById('correo').value = row.children[3].textContent;
            document.getElementById('telefono').value = row.children[4].textContent;
        } else {
            document.getElementById('tituloModal').textContent = 'Agregar Docente';
            document.getElementById('accion').value = 'agregar';
            document.getElementById('formDocente').reset();
        }
    }
    function cerrarModal() {
        document.getElementById('modalForm').style.display = 'none';
    }
    function cerrarAlerta() {
        document.getElementById('alertaMsg').style.display = 'none';
    }