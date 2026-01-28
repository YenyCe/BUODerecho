
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

// ================= ALERTAS AUTO-DESAPARECER =================
const alertaInterval = setInterval(() => {
    const alerta = document.querySelector(".alerta");

    if (alerta) {
        clearInterval(alertaInterval);

        // esperar visible
        setTimeout(() => {
            alerta.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            alerta.style.opacity = "0";
            alerta.style.transform = "translateY(-10px)";
        }, 3000);

        // eliminar
        setTimeout(() => {
            alerta.remove();
        }, 3600);
    }
}, 100);
