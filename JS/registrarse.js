    document.addEventListener('DOMContentLoaded', () => {
        const dniInput = document.querySelector('input[name="dni"]');
        const nombresInput = document.querySelector('input[name="nombres"]');
        const apellidoPaternoInput = document.querySelector('input[name="apellido_paterno"]');
        const apellidoMaternoInput = document.querySelector('input[name="apellido_materno"]');

        dniInput.addEventListener('input', async (e) => {
            const dni = e.target.value;

            // Limpia los campos y remueve el atributo readonly si el DNI es incompleto
            if (dni.length < 8) {
                nombresInput.value = '';
                apellidoPaternoInput.value = '';
                apellidoMaternoInput.value = '';
                nombresInput.removeAttribute('readonly');
                apellidoPaternoInput.removeAttribute('readonly');
                apellidoMaternoInput.removeAttribute('readonly');
                return;
            }
            
            // Si el DNI tiene 8 dígitos, realiza la consulta
            if (dni.length === 8) {
                try {
                    const response = await fetch('registro.php?dni=' + dni);
                    const data = await response.json();

                    if (!data.error) {
                        nombresInput.value = data.nombres;
                        apellidoPaternoInput.value = data.apellidoPaterno;
                        apellidoMaternoInput.value = data.apellidoMaterno;

                        // **Añadir el atributo readonly para bloquear los campos**
                        nombresInput.setAttribute('readonly', 'readonly');
                        apellidoPaternoInput.setAttribute('readonly', 'readonly');
                        apellidoMaternoInput.setAttribute('readonly', 'readonly');
                    } else {
                        // Si hay un error, limpia los campos pero permite editarlos
                        nombresInput.value = '';
                        apellidoPaternoInput.value = '';
                        apellidoMaternoInput.value = '';
                        nombresInput.removeAttribute('readonly');
                        apellidoPaternoInput.removeAttribute('readonly');
                        apellidoMaternoInput.removeAttribute('readonly');
                        alert(data.error);
                    }
                } catch (error) {
                    console.error('Error al consultar DNI:', error);
                    nombresInput.value = '';
                    apellidoPaternoInput.value = '';
                    apellidoMaternoInput.value = '';
                    nombresInput.removeAttribute('readonly');
                    apellidoPaternoInput.removeAttribute('readonly');
                    apellidoMaternoInput.removeAttribute('readonly');
                    alert('Error al conectar con el servicio de DNI.');
                }
            }
        });
    });
