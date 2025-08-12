        document.addEventListener('DOMContentLoaded', function () {
            // ---- Lógica para la navegación móvil (hamburguesa) ----
            const hamburger = document.getElementById('hamburger');
            const navMenu = document.getElementById('nav-menu');

            if (hamburger && navMenu) {
                hamburger.addEventListener('click', () => {
                    navMenu.classList.toggle('active');
                });
            }

            // ---- Lógica para la navegación de la barra lateral ----
            const sidebarButtons = document.querySelectorAll('.sidebar-btn');
            const contentPanels = document.querySelectorAll('.content-panel');

            sidebarButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();

                    if (navMenu && navMenu.classList.contains('active')) {
                        navMenu.classList.remove('active');
                    }

                    sidebarButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    contentPanels.forEach(panel => panel.classList.remove('active'));

                    const targetId = button.getAttribute('href');
                    const targetPanel = document.querySelector(targetId);
                    if (targetPanel) {
                        targetPanel.classList.add('active');
                    }
                });
            });
        });

        function limpiarDatos() {
            // Vaciar los cuerpos de tabla
            document.querySelector('#table-familias tbody').innerHTML = '';
            document.querySelector('#table-generos tbody').innerHTML = '';
            document.querySelector('#table-especies tbody').innerHTML = '';
        
            // Resetear selects relacionados
            const familiaSelectGenero = document.getElementById('familia_select_genero');
            if (familiaSelectGenero) familiaSelectGenero.value = '';
        
            const familiaSelectEspecie = document.getElementById('familia_select_especie');
            if (familiaSelectEspecie) familiaSelectEspecie.value = '';
        
            const generoSelectEspecie = document.getElementById('genero_select_especie');
            if (generoSelectEspecie) {
                generoSelectEspecie.value = '';
                generoSelectEspecie.disabled = true; // lo deshabilitamos porque no hay familia seleccionada
            }
        
            // Resetear inputs de formularios
            const formFamilias = document.getElementById('form-familias');
            if (formFamilias) formFamilias.reset();
        
            const formGeneros = document.getElementById('form-generos');
            if (formGeneros) formGeneros.reset();
        
            const formEspecies = document.getElementById('form-especies');
            if (formEspecies) formEspecies.reset();
        
            // Si tienes variables JS que contienen datos, resetéalas también (opcional)
            familias = [];
            generos = [];
            especies = [];
        }
        