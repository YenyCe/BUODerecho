function abrirModal(id=null){
    document.getElementById('modalForm').style.display = 'block';
    if(id){
        document.getElementById('tituloModal').innerText = 'Editar Docente';
        document.getElementById('accion').value = 'editar';

        // Obtener los datos de la fila
        const row = document.querySelector(`table tr td:first-child[textContent="${id}"]`) || null;
        const tds = Array.from(document.querySelectorAll('table tr')).find(tr => tr.children[0].textContent==id)?.children;
        if(tds){
            document.getElementById('id_docente').value = id;
            document.getElementById('nombre').value = tds[1].textContent;
            document.getElementById('apellidos').value = tds[2].textContent;
            document.getElementById('correo').value = tds[3].textContent;
            document.getElementById('telefono').value = tds[4].textContent;
        }
    } else {
        document.getElementById('tituloModal').innerText = 'Agregar Docente';
        document.getElementById('accion').value = 'agregar';
        document.getElementById('id_docente').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('apellidos').value = '';
        document.getElementById('correo').value = '';
        document.getElementById('telefono').value = '';
    }
}

function cerrarModal(){
    document.getElementById('modalForm').style.display = 'none';
}

// Cerrar modal al hacer click fuera
window.onclick = function(event){
    if(event.target == document.getElementById('modalForm')){
        cerrarModal();
    }
}

// Cerrar alerta manualmente
function cerrarAlerta(){
    const alerta = document.getElementById('alertaMsg');
    if(alerta){
        alerta.classList.add('ocultar');
        setTimeout(() => { alerta.style.display = 'none'; }, 600);
    }
}

// Ocultar alerta automáticamente después de 4 segundos
window.addEventListener('DOMContentLoaded', () => {
    const alerta = document.getElementById('alertaMsg');
    if(alerta){
        setTimeout(() => {
            alerta.classList.add('ocultar');
            setTimeout(() => { alerta.style.display = 'none'; }, 600);
        }, 4000);
    }
});
