
// FUNCION UNIVERSAL PARA CERRAR MODAL
function cerrarModal(idModal) {
    const modal = document.getElementById(idModal);
    if(modal) modal.style.display = 'none';
}

// CERRAR MODAL AL HACER CLICK FUERA DE ÉL
window.addEventListener('click', function(event) {
    document.querySelectorAll('.modal').forEach(modal => {
        if(event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
