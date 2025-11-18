document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');

    // Verifica que ambos elementos existen antes de agregar el evento
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }
});


    // Mostrar el modal al cargar la página
    window.onload = function() {
        document.getElementById("modal-banner-1").style.display = "flex";
    };

    // Cerrar modal
    function closeModal() {
        document.getElementById("modal-banner-1").style.display = "none";
    }
