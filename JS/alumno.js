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

            // --- Lógica para la gestión local con JavaScript de Familias, Géneros y Especies (sin modificar) ---
            let familias = [
                { id: 1, nombre: 'Felidae' },
                { id: 2, nombre: 'Canidae' }
            ];
            let generos = [
                { id: 1, nombre: 'Panthera', id_familia: 1 },
                { id: 2, nombre: 'Felis', id_familia: 1 },
                { id: 3, nombre: 'Canis', id_familia: 2 }
            ];
            let especies = [
                { id: 1, nombre: 'Panthera leo', id_genero: 1 },
                { id: 2, nombre: 'Felis catus', id_genero: 2 },
                { id: 3, nombre: 'Canis lupus', id_genero: 3 }
            ];
            let nextFamiliaId = familias.length + 1;
            let nextGeneroId = generos.length + 1;
            let nextEspecieId = especies.length + 1;

            const formFamilias = document.getElementById('form-familias');
            const tableFamiliasBody = document.querySelector('#table-familias tbody');
            const familiaSelectGeneros = document.getElementById('familia_select_genero');
            const formGeneros = document.getElementById('form-generos');
            const tableGenerosBody = document.querySelector('#table-generos tbody');
            const familiaSelectEspecies = document.getElementById('familia_select_especie');
            const generoSelectEspecies = document.getElementById('genero_select_especie');
            const formEspecies = document.getElementById('form-especies');
            const tableEspeciesBody = document.querySelector('#table-especies tbody');

            function renderFamilias() {
                tableFamiliasBody.innerHTML = '';
                familias.forEach(familia => {
                    const row = `<tr>
                                    <td>${familia.id}</td>
                                    <td>${familia.nombre}</td>
                                    <td class="table-actions">
                                        <a href="#" class="btn-action" onclick="alert('Funcionalidad de editar aún no implementada');">Editar</a>
                                        <a href="#" class="btn-action btn-delete" onclick="alert('Funcionalidad de eliminar aún no implementada');">Eliminar</a>
                                    </td>
                                </tr>`;
                    tableFamiliasBody.insertAdjacentHTML('beforeend', row);
                });
                const selectsFamilia = [familiaSelectGeneros, familiaSelectEspecies];
                selectsFamilia.forEach(select => {
                    const selectedValue = select.value;
                    select.innerHTML = '<option value="">-- Elige una familia --</option>';
                    familias.forEach(familia => {
                        const option = `<option value="${familia.id}" ${familia.id == selectedValue ? 'selected' : ''}>${familia.nombre}</option>`;
                        select.insertAdjacentHTML('beforeend', option);
                    });
                });
            }

            function renderAllGeneros() {
                tableGenerosBody.innerHTML = '';
                generos.forEach(genero => {
                    const row = `<tr>
                                    <td>${genero.nombre}</td>
                                    <td class="table-actions">
                                        <a href="#" class="btn-action" onclick="alert('Funcionalidad de editar aún no implementada para el género ${genero.nombre}');">Editar</a>
                                        <a href="#" class="btn-action btn-delete" onclick="alert('Funcionalidad de eliminar aún no implementada para el género ${genero.nombre}');">Eliminar</a>
                                    </td>
                                  </tr>`;
                    tableGenerosBody.insertAdjacentHTML('beforeend', row);
                });
            }

            function renderAllEspecies() {
                tableEspeciesBody.innerHTML = '';
                especies.forEach(especie => {
                    const row = `<tr>
                                    <td>${especie.nombre}</td>
                                    <td class="table-actions">
                                        <a href="#" class="btn-action" onclick="alert('Funcionalidad de editar aún no implementada para la especie ${especie.nombre}');">Editar</a>
                                        <a href="#" class="btn-action btn-delete" onclick="alert('Funcionalidad de eliminar aún no implementada para la especie ${especie.nombre}');">Eliminar</a>
                                    </td>
                                  </tr>`;
                    tableEspeciesBody.insertAdjacentHTML('beforeend', row);
                });
            }

            renderFamilias();
            renderAllGeneros();
            renderAllEspecies();

            formFamilias.addEventListener('submit', function(event) {
                event.preventDefault();
                const nombre = document.getElementById('nombre_familia').value;
                familias.push({ id: nextFamiliaId++, nombre: nombre });
                renderFamilias();
                renderAllGeneros();
                renderAllEspecies();
                formFamilias.reset();
            });

            formGeneros.addEventListener('submit', function(event) {
                event.preventDefault();
                const idFamilia = parseInt(document.getElementById('familia_select_genero').value);
                const nombre = document.getElementById('nombre_genero').value;
                if (!idFamilia) {
                    alert('Por favor, selecciona una familia.');
                    return;
                }
                generos.push({ id: nextGeneroId++, nombre: nombre, id_familia: idFamilia });
                renderFamilias();
                renderAllGeneros();
                renderAllEspecies();
                formGeneros.reset();
            });

            familiaSelectEspecies.addEventListener('change', function() {
                const idFamilia = parseInt(this.value);
                generoSelectEspecies.innerHTML = '<option value="">-- Elige un género --</option>';
                generoSelectEspecies.disabled = true;
                if (idFamilia) {
                    const generosFiltrados = generos.filter(g => g.id_familia === idFamilia);
                    generosFiltrados.forEach(genero => {
                        const option = `<option value="${genero.id}">${genero.nombre}</option>`;
                        generoSelectEspecies.insertAdjacentHTML('beforeend', option);
                    });
                    generoSelectEspecies.disabled = false;
                }
            });

            formEspecies.addEventListener('submit', function(event) {
                event.preventDefault();
                const idGenero = parseInt(document.getElementById('genero_select_especie').value);
                const nombre = document.getElementById('nombre_especie').value;
                if (!idGenero) {
                    alert('Por favor, selecciona un género.');
                    return;
                }
                especies.push({ id: nextEspecieId++, nombre: nombre, id_genero: idGenero });
                renderFamilias();
                renderAllEspecies();
                formEspecies.reset();
            });

            // --- Lógica para la nueva sección de Gestión de Pruebas ---
            const mostrarFormularioBtn = document.getElementById('mostrarFormularioBtn');
            const formularioProducto = document.getElementById('formularioProducto');
            const formProducto = document.getElementById('formularioProducto');
            const contenedorProductos = document.getElementById('contenedorProductos');

            const familiaSelect = document.getElementById('familiaSelect');
            const generoSelect = document.getElementById('generoSelect');
            const especieSelect = document.getElementById('especieSelect');
            const nombrePruebaInput = document.getElementById('nombrePruebaInput');
            const descripcionInput = document.getElementById('descripcionInput');
            const resultadoInput = document.getElementById('resultadoInput');
            const imagenInput = document.getElementById('imagenInput');

            const taxonomia = [
                { familia: "Enterobacteriaceae", genero: "Escherichia", especie: "coli" },
                { familia: "Enterobacteriaceae", genero: "Salmonella", especie: "typhi" },
                { familia: "Staphylococcaceae", genero: "Staphylococcus", especie: "aureus" },
                { familia: "Staphylococcaceae", genero: "Staphylococcus", especie: "epidermidis" },
                { familia: "Streptococcaceae", genero: "Streptococcus", especie: "pyogenes" },
                { familia: "Streptococcaceae", genero: "Streptococcus", especie: "pneumoniae" },
            ];

            let pruebas = JSON.parse(localStorage.getItem('pruebas')) || [];

            const saveData = () => {
                localStorage.setItem('pruebas', JSON.stringify(pruebas));
            };

            const populateFamiliaSelect = () => {
                const familias = [...new Set(taxonomia.map(item => item.familia))];
                familiaSelect.innerHTML = '<option value="" disabled selected>Selecciona una familia</option>';
                familias.forEach(familia => {
                    const option = document.createElement('option');
                    option.value = familia;
                    option.textContent = familia;
                    familiaSelect.appendChild(option);
                });
                generoSelect.innerHTML = '<option value="" disabled selected>Selecciona un género</option>';
                generoSelect.disabled = true;
                especieSelect.innerHTML = '<option value="" disabled selected>Selecciona una especie</option>';
                especieSelect.disabled = true;
            };

            const populateGeneroSelect = (selectedFamilia) => {
                const generos = [...new Set(taxonomia.filter(item => item.familia === selectedFamilia).map(item => item.genero))].sort();
                generoSelect.innerHTML = '<option value="" disabled selected>Selecciona un género</option>';
                generos.forEach(genero => {
                    const option = document.createElement('option');
                    option.value = genero;
                    option.textContent = genero;
                    generoSelect.appendChild(option);
                });
                generoSelect.disabled = false;
                especieSelect.innerHTML = '<option value="" disabled selected>Selecciona una especie</option>';
                especieSelect.disabled = true;
            };

            const populateEspecieSelect = (selectedGenero) => {
                const especies = [...new Set(taxonomia.filter(item => item.genero === selectedGenero).map(item => item.especie))].sort();
                especieSelect.innerHTML = '<option value="" disabled selected>Selecciona una especie</option>';
                especies.forEach(especie => {
                    const option = document.createElement('option');
                    option.value = especie;
                    option.textContent = especie;
                    especieSelect.appendChild(option);
                });
                especieSelect.disabled = false;
            };

            familiaSelect.addEventListener('change', (e) => {
                populateGeneroSelect(e.target.value);
            });

            generoSelect.addEventListener('change', (e) => {
                populateEspecieSelect(e.target.value);
            });

            const renderPruebas = () => {
                contenedorProductos.innerHTML = '';
                if (pruebas.length === 0) {
                    contenedorProductos.innerHTML = '<p style="text-align:center; width:100%;">No hay pruebas registradas. ¡Agrega una!</p>';
                    return;
                }

                pruebas.forEach((prueba) => {
                    const productoDiv = document.createElement('div');
                    productoDiv.classList.add('producto');

                    const img = document.createElement('img');
                    img.src = prueba.imagen || 'https://via.placeholder.com/140x140?text=No+Image';
                    img.alt = prueba.nombrePrueba;

                    const h3 = document.createElement('h3');
                    h3.classList.add('producto-title');
                    h3.textContent = prueba.nombrePrueba;

                    const pSubtitle = document.createElement('p');
                    pSubtitle.classList.add('producto-subtitle');
                    pSubtitle.textContent = `(${prueba.especie})`;

                    const pFamilia = document.createElement('p');
                    pFamilia.classList.add('producto-info');
                    pFamilia.textContent = `Familia: ${prueba.familia}`;

                    const pGenero = document.createElement('p');
                    pGenero.classList.add('producto-info');
                    pGenero.textContent = `Género: ${prueba.genero}`;

                    const pDescripcion = document.createElement('p');
                    pDescripcion.classList.add('producto-info');
                    pDescripcion.textContent = `Descripción: ${prueba.descripcion}`;
                    pDescripcion.style.fontSize = '14px';

                    const pResultados = document.createElement('p');
                    pResultados.classList.add('producto-info');
                    pResultados.textContent = `Resultados: ${prueba.resultado}`;
                    pResultados.style.fontSize = '14px';

                    productoDiv.appendChild(img);
                    productoDiv.appendChild(h3);
                    productoDiv.appendChild(pSubtitle);
                    productoDiv.appendChild(pFamilia);
                    productoDiv.appendChild(pGenero);
                    productoDiv.appendChild(pDescripcion);
                    productoDiv.appendChild(pResultados);

                    contenedorProductos.appendChild(productoDiv);
                });
            };

            const resetForm = () => {
                formProducto.reset();
                populateFamiliaSelect();
            };

            mostrarFormularioBtn.addEventListener('click', () => {
                formularioProducto.style.display = formularioProducto.style.display === 'grid' ? 'none' : 'grid';
                if (formularioProducto.style.display === 'grid') {
                    resetForm();
                }
            });

            formProducto.addEventListener('submit', (e) => {
                e.preventDefault();
                const reader = new FileReader();
                const file = imagenInput.files[0];

                const savePrueba = (imagenUrl) => {
                    const nuevaPrueba = {
                        familia: familiaSelect.value,
                        genero: generoSelect.value,
                        especie: especieSelect.value,
                        nombrePrueba: nombrePruebaInput.value,
                        descripcion: descripcionInput.value,
                        resultado: resultadoInput.value,
                        imagen: imagenUrl || ''
                    };
                    pruebas.push(nuevaPrueba);
                    saveData();
                    renderPruebas();
                    resetForm();
                    formularioProducto.style.display = 'none';
                };

                if (file) {
                    reader.onload = (event) => savePrueba(event.target.result);
                    reader.readAsDataURL(file);
                } else {
                    savePrueba('');
                }
            });

            populateFamiliaSelect();
            renderPruebas();
        });
