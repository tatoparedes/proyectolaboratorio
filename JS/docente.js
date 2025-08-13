document.addEventListener("DOMContentLoaded", function () {
    // ---- Lógica para mostrar/ocultar el formulario ----
    const btnMostrar = document.getElementById("mostrarFormularioBtn");
    const formulario = document.getElementById("formularioProducto");

    if (btnMostrar && formulario) {
        btnMostrar.addEventListener("click", function (e) {
            e.preventDefault();
            formulario.style.display =
                (formulario.style.display === "none" || formulario.style.display === "")
                    ? "block"
                    : "none";
        });
    }

    // ---- Lógica para la navegación móvil (hamburguesa) ----
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("nav-menu");

    if (hamburger && navMenu) {
        hamburger.addEventListener("click", () => {
            navMenu.classList.toggle("active");
        });
    }

    // ---- Lógica para la navegación de la barra lateral ----
    const sidebarButtons = document.querySelectorAll(".sidebar-btn");
    const contentPanels = document.querySelectorAll(".content-panel");

    sidebarButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();

            if (navMenu && navMenu.classList.contains("active")) {
                navMenu.classList.remove("active");
            }

            sidebarButtons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            contentPanels.forEach(panel => panel.classList.remove("active"));

            const targetId = button.getAttribute("href");
            const targetPanel = document.querySelector(targetId);
            if (targetPanel) {
                targetPanel.classList.add("active");
            }
        });
    });
});