        document.addEventListener('DOMContentLoaded', () => {
            const loginSection = document.getElementById('login-section');
            const signupSection = document.getElementById('signup-section');
            const showSignupLink = document.getElementById('show-signup-link');
            const showLoginLink = document.getElementById('show-login-link');
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');

            // Event listener for showing the sign-up form
            showSignupLink.addEventListener('click', (e) => {
                e.preventDefault();
                loginSection.classList.add('hidden');
                signupSection.classList.remove('hidden');
            });

            // Event listener for showing the login form
            showLoginLink.addEventListener('click', (e) => {
                e.preventDefault();
                signupSection.classList.add('hidden');
                loginSection.classList.remove('hidden');
            });

            // Event listener for the login form submission
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;
                const level = document.getElementById('login-level').value;

                if (email && password && level) {
                    alert('¡Inicio de sesión exitoso!');
                    console.log('Intento de inicio de sesión con:', { email, password, level });
                } else {
                    alert('Por favor, rellena todos los campos.');
                }
            });

            // Event listener for the sign-up form submission
            signupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const email = document.getElementById('signup-email').value;
                const password = document.getElementById('signup-password').value;
                const level = document.getElementById('signup-level').value;

                if (email && password && level) {
                    alert(`¡Registro exitoso! Nivel seleccionado: ${level}`);
                    console.log('Intento de registro con:', { email, password, level });
                } else {
                    alert('Por favor, rellena todos los campos.');
                }
            });
        });
