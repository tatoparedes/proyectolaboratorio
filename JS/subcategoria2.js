// Function to handle the hamburger menu toggle
function setupHamburgerMenu() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }
}

// Function to handle the product management logic
function setupProductManagement() {
    // DOM Elements
    const formAgregarEspecie = document.getElementById('formAgregarEspecie');
    const categoriaSelect = document.getElementById('categoria');
    const nuevaCategoriaDiv = document.getElementById('nuevaCategoriaDiv');
    const nuevaCategoriaInput = document.getElementById('nuevaCategoria');
    const tipoMuestraInput = document.getElementById('tipoMuestra'); // Changed from 'tipoMuestra' to 'genero' for clarity
    const especieInput = document.getElementById('especie');

    const mensajeExito = document.getElementById('mensajeExito');

    const mostrarFormularioBtn = document.getElementById('mostrarFormularioBtn');
    const formularioProducto = document.getElementById('formularioProducto');
    const formProducto = document.getElementById('formularioProducto');
    const nuevaGeneroSelect = document.getElementById('nuevageneroInput');
    const nuevaEspecieSelect = document.getElementById('nuevaEspecie');
    const nuevaDescripcionInput = document.getElementById('nuevaDescripcion');
    const nuevoResultadoInput = document.getElementById('nuevoResultado');
    const nuevaImagenInput = document.getElementById('nuevaImagen');
    const contenedorProductos = document.getElementById('contenedorProductos');

    // Storage for data
    let especies = JSON.parse(localStorage.getItem('especies')) || [];
    let productos = JSON.parse(localStorage.getItem('productos')) || [];

    // Helper function to save data to localStorage
    const saveData = () => {
        localStorage.setItem('especies', JSON.stringify(especies));
        localStorage.setItem('productos', JSON.stringify(productos));
    };

    // Helper function to update the select options based on stored data
    const updateSelects = () => {
        // Update "Tipos de Familia" (Categorias)
        categoriaSelect.innerHTML = '<option value="" disabled selected>Selecciona una familia</option><option value="Nueva">+ Nueva Familia</option>';
        const familias = [...new Set(especies.map(e => e.familia))];
        familias.forEach(familia => {
            const option = document.createElement('option');
            option.value = familia;
            option.textContent = familia;
            categoriaSelect.appendChild(option);
        });

        // Update "Genero" and "Tipo de Especie"
        nuevaGeneroSelect.innerHTML = '<option value="" disabled selected>Selecciona un Género</option>';
        const generos = [...new Set(especies.map(e => e.genero))];
        generos.forEach(genero => {
            const option = document.createElement('option');
            option.value = genero;
            option.textContent = genero;
            nuevaGeneroSelect.appendChild(option);
        });

        // Clear especie select
        nuevaEspecieSelect.innerHTML = '<option value="" disabled selected>Selecciona un tipo</option>';
    };

    // Event listener for the "Familia" select to show/hide new category input
    categoriaSelect.addEventListener('change', (e) => {
        if (e.target.value === 'Nueva') {
            nuevaCategoriaDiv.style.display = 'block';
        } else {
            nuevaCategoriaDiv.style.display = 'none';
        }
    });

    // Event listener to filter "Especie" based on selected "Genero"
    nuevaGeneroSelect.addEventListener('change', (e) => {
        const selectedGenero = e.target.value;
        const filteredEspecies = especies.filter(e => e.genero === selectedGenero);

        nuevaEspecieSelect.innerHTML = '<option value="" disabled selected>Selecciona un tipo</option>';
        filteredEspecies.forEach(especie => {
            const option = document.createElement('option');
            option.value = especie.nombre;
            option.textContent = especie.nombre;
            nuevaEspecieSelect.appendChild(option);
        });
    });

    // Event listener for the "Add new species" form
    formAgregarEspecie.addEventListener('submit', (e) => {
        e.preventDefault();

        let familia = categoriaSelect.value;
        if (familia === 'Nueva' && nuevaCategoriaInput.value.trim()) {
            familia = nuevaCategoriaInput.value.trim();
        } else if (familia === '') {
            alert('Por favor, selecciona o agrega una familia.');
            return;
        }

        const genero = tipoMuestraInput.value.trim();
        const nombreEspecie = especieInput.value.trim();

        if (familia && genero && nombreEspecie) {
            especies.push({ familia, genero, nombre: nombreEspecie });
            saveData();
            updateSelects(); // Re-render selects to include the new data

            // Show success message
            mensajeExito.style.display = 'block';
            setTimeout(() => {
                mensajeExito.style.display = 'none';
            }, 3000);

            // Reset form fields
            formAgregarEspecie.reset();
            nuevaCategoriaDiv.style.display = 'none';
            // Re-select placeholder after reset
            categoriaSelect.value = '';
        } else {
            alert('Por favor, completa todos los campos para agregar una bacteria.');
        }
    });

    // Event listener for "Add Product" button to toggle form visibility
    mostrarFormularioBtn.addEventListener('click', () => {
        formularioProducto.style.display = formularioProducto.style.display === 'grid' ? 'none' : 'grid';
    });

    // Function to render all products
    const renderProductos = () => {
        contenedorProductos.innerHTML = '';
        if (productos.length === 0) {
            contenedorProductos.innerHTML = '<p style="text-align:center; width:100%;">No hay especies registradas. ¡Agrega una!</p>';
            return;
        }

        productos.forEach((producto) => {
            const productoDiv = document.createElement('div');
            productoDiv.classList.add('producto');

            const img = document.createElement('img');
            img.src = producto.imagen || 'https://via.placeholder.com/140x140?text=No+Image';
            img.alt = producto.especie;

            const h3 = document.createElement('h3');
            h3.textContent = producto.especie;

            const p1 = document.createElement('p');
            p1.textContent = `Género: ${producto.genero}`;

            const p2 = document.createElement('p');
            p2.textContent = `Descripción: ${producto.descripcion}`;
            p2.style.fontSize = '14px';

            const p3 = document.createElement('p');
            p3.textContent = `Resultados: ${producto.resultados}`;
            p3.style.fontSize = '14px';

            productoDiv.appendChild(img);
            productoDiv.appendChild(h3);
            productoDiv.appendChild(p1);
            productoDiv.appendChild(p2);
            productoDiv.appendChild(p3);

            contenedorProductos.appendChild(productoDiv);
        });
    };

    // Event listener for the "Add Product" form
    formProducto.addEventListener('submit', (e) => {
        e.preventDefault();

        const genero = nuevaGeneroSelect.value;
        const especie = nuevaEspecieSelect.value;
        const descripcion = nuevaDescripcionInput.value;
        const resultados = nuevoResultadoInput.value;
        const imagenFile = nuevaImagenInput.files[0];
        let imagenURL = '';

        if (!genero || !especie || !descripcion || !resultados) {
            alert('Por favor, completa todos los campos del formulario de producto.');
            return;
        }

        if (imagenFile) {
            const reader = new FileReader();
            reader.onload = function(event) {
                imagenURL = event.target.result;
                const nuevoProducto = {
                    genero,
                    especie,
                    descripcion,
                    resultados,
                    imagen: imagenURL
                };
                productos.push(nuevoProducto);
                saveData();
                renderProductos();
                formProducto.reset();
                formularioProducto.style.display = 'none';
            };
            reader.readAsDataURL(imagenFile);
        } else {
            const nuevoProducto = {
                genero,
                especie,
                descripcion,
                resultados,
                imagen: 'https://via.placeholder.com/140x140?text=No+Image' // Placeholder if no image is uploaded
            };
            productos.push(nuevoProducto);
            saveData();
            renderProductos();
            formProducto.reset();
            formularioProducto.style.display = 'none';
        }
    });

    // Initial load
    updateSelects();
    renderProductos();
}

// Call the main functions when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    setupHamburgerMenu();
    setupProductManagement();
});
