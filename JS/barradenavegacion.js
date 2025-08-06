        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('nav-menu');

        hamburger.addEventListener('click', () => {
            // Toggles the 'active' class on both the hamburger and the menu
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
