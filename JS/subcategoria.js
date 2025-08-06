document.addEventListener('DOMContentLoaded', () => {
  // --- Referencias a elementos del DOM ---
  // Formulario de "Agregar Bacteria" (en el aside, para gestionar Familias, Géneros, Tipos de Especie)
  const categoriaSelect = document.getElementById("categoria"); // Select para la Familia
  const nuevaCategoriaDiv = document.getElementById("nuevaCategoriaDiv");
  const nuevaCategoriaInput = document.getElementById("nuevaCategoria"); // Input para nueva Familia
  const tipoGeneroInput = document.getElementById("tipoMuestra"); // Input para el Género
  const especieInput = document.getElementById("especie"); // Input para el Tipo de Especie
  const formAgregarEspecie = document.getElementById("formAgregarEspecie");
  const mensajeExito = document.getElementById("mensajeExito"); // Mensaje de éxito global

  // Formulario de "Agregar/Editar Especie" (en la sección principal, para el catálogo)
  const mostrarFormularioBtn = document.getElementById("mostrarFormularioBtn");
  const formularioProducto = document.getElementById("formularioProducto");
  const nuevaGeneroSelect = document.getElementById("nuevageneroInput"); // Select para el Género en el formulario de producto
  const nuevaEspecieSelect = document.getElementById("nuevaEspecie"); // Select para el Tipo de Especie en el formulario de producto
  const nuevaDescripcionInput = document.getElementById("nuevaDescripcion");
  const nuevoResultadoInput = document.getElementById("nuevoResultado");
  const nuevaImagenInput = document.getElementById("nuevaImagen");
  const formProductoSubmitBtn = formularioProducto.querySelector("button[type='submit']");
  const formProductoTitle = formularioProducto.querySelector("h3");
  const contenedorProductos = document.getElementById("contenedorProductos"); // Dónde se muestran los productos

  // Modales de Administración
  const adminCategoriasBtn = document.getElementById("adminCategoriasBtn");
  const adminEspeciesBtn = document.getElementById("adminEspeciesBtn");
  const modalAdminCategorias = document.getElementById("modalAdminCategorias");
  const modalAdminEspecies = document.getElementById("modalAdminEspecies");
  const listaAdminCategorias = document.getElementById("listaAdminCategorias");
  const listaAdminEspecies = document.getElementById("listaAdminEspecies");
  const filtroCategoriaEspecie = document.getElementById('filtroCategoriaEspecie'); // Filtro en el modal de especies
  const closeButtons = document.querySelectorAll(".modal-close-btn");

  // --- Almacenamiento local de datos ---
  // `especiesData` almacena la estructura jerárquica: Familia, Género, Tipo de Especie
  let especiesData = JSON.parse(localStorage.getItem("especiesData")) || [];
  // `productos` almacena los elementos del catálogo (con su descripción, imagen, etc.)
  let productos = JSON.parse(localStorage.getItem("productos")) || [];
  let editandoProductoIndex = null; // Para saber qué producto estamos editando en el catálogo

  // --- Funciones de Utilidad ---

  /**
   * Guarda los arrays `especiesData` y `productos` en `localStorage`.
   */
  function guardarDatos() {
      localStorage.setItem("especiesData", JSON.stringify(especiesData));
      localStorage.setItem("productos", JSON.stringify(productos));
  }

  /**
   * Muestra un mensaje de éxito temporal.
   * @param {string} mensaje - El texto del mensaje a mostrar.
   */
  function mostrarMensajeExito(mensaje = "¡Operación completada!") {
      mensajeExito.textContent = mensaje;
      mensajeExito.style.display = "block";
      setTimeout(() => mensajeExito.style.display = "none", 2000);
  }

  // --- Lógica del Formulario "Agregar Bacteria" (Aside) ---

  // Maneja la visibilidad del input 'Nueva Familia'
  categoriaSelect.addEventListener("change", function () {
      nuevaCategoriaDiv.style.display = this.value === "Nueva" ? "block" : "none";
  });

  // Envío del formulario para agregar Familia, Género y Tipo de Especie
  formAgregarEspecie.addEventListener("submit", function (e) {
      e.preventDefault();

      let categoriaValue = categoriaSelect.value;
      if (categoriaValue === "Nueva") {
          categoriaValue = nuevaCategoriaInput.value.trim();
      }

      const generoValue = tipoGeneroInput.value.trim();
      const tipoEspecieValue = especieInput.value.trim();

      if (!categoriaValue || !generoValue || !tipoEspecieValue) {
          alert("Todos los campos (Familia, Género, Tipo de Especie) son obligatorios.");
          return;
      }

      // Evitar duplicados exactos (Familia, Género, Tipo de Especie)
      const isDuplicate = especiesData.some(item =>
          item.categoria === categoriaValue &&
          item.genero === generoValue &&
          item.tipoEspecie === tipoEspecieValue
      );

      if (isDuplicate) {
          alert("Esta combinación de Familia, Género y Tipo de Especie ya existe.");
          return;
      }

      especiesData.push({
          categoria: categoriaValue,
          genero: generoValue,
          tipoEspecie: tipoEspecieValue
      });
      guardarDatos();

      // Limpiar formulario y mostrar éxito
      categoriaSelect.value = "Nueva"; // Vuelve a seleccionar "Nueva" o un valor por defecto
      nuevaCategoriaInput.value = "";
      tipoGeneroInput.value = "";
      especieInput.value = "";
      nuevaCategoriaDiv.style.display = "block"; // Deja visible el campo "Nueva Familia" para facilitar la adición continua
      mostrarMensajeExito("¡Familia, Género y Tipo de Especie agregados correctamente!");
      actualizarSelects(); // Importante: actualiza los selects en ambos formularios
  });

  // --- Lógica de Catálogo (Sección Principal) ---

  // Alternar visibilidad del formulario de "Agregar/Editar Especie" del catálogo
  mostrarFormularioBtn.addEventListener("click", () => {
      // Si el formulario está oculto, lo mostramos y preparamos para "agregar"
      if (formularioProducto.style.display === "none" || formularioProducto.style.display === "") {
          formularioProducto.style.display = "grid";
          formProductoTitle.textContent = "Agregar Nueva Especie al Catálogo";
          formProductoSubmitBtn.textContent = "Guardar Especie";
          formularioProducto.reset(); // Limpia el formulario
          editandoProductoIndex = null;
          // Al abrir para agregar, populamos el select de Género y limpiamos el de Especie
          actualizarSelects(); // Esto repopulará el select de Género
          nuevaGeneroSelect.value = ""; // Resetea el género seleccionado
          nuevaEspecieSelect.innerHTML = '<option value="" disabled selected>Selecciona un tipo</option>'; // Limpiar el select de tipo
      } else {
          // Si el formulario está visible, lo ocultamos
          formularioProducto.style.display = "none";
      }
  });

  /**
   * Renderiza los elementos del catálogo en el contenedor principal.
   */
  function renderizarProductos() {
      contenedorProductos.innerHTML = "";
      if (productos.length === 0) {
          contenedorProductos.innerHTML = "<p style='width: 100%; text-align: center; color: #666;'></p>";
          return;
      }

      productos.forEach((producto, index) => {
          const div = document.createElement("div");
          div.classList.add("producto");
          div.innerHTML = `
              <div>
                  <img src="${producto.imagen || 'https://via.placeholder.com/230x140?text=Sin+Imagen'}" alt="Imagen de ${producto.tipoEspecie}">
                  <p><strong>Familia:</strong> ${producto.categoria}</p>
                  <p><strong>Género:</strong> ${producto.genero}</p>
                  <p><strong>Tipo:</strong> ${producto.tipoEspecie}</p>
                  <p><strong>Descripción:</strong> ${producto.descripcion}</p>
                  <p><strong>Resultados:</strong> ${producto.resultado}</p>
              </div>
              <div>
                  <button class="editar-btn" onclick="editarProducto(${index})">Editar</button>
                  <button class="eliminar-btn" onclick="eliminarProducto(${index})">Eliminar</button>
              </div>
          `;
          contenedorProductos.appendChild(div);
      });
  }

  // Permite eliminar un producto del catálogo
  window.eliminarProducto = function(index) {
      if (confirm("¿Seguro que deseas eliminar los datos de esta especie del catálogo?")) {
          productos.splice(index, 1);
          guardarDatos();
          renderizarProductos();
          mostrarMensajeExito("Especie eliminada del catálogo.");
      }
  }

  // Prepara el formulario para editar un producto del catálogo
  window.editarProducto = function(index) {
      const producto = productos[index];
      formProductoTitle.textContent = "Editar Especie en Catálogo";
      formProductoSubmitBtn.textContent = "Guardar Cambios";

      // 1. Populamos el select de Géneros y seleccionamos el Género del producto
      actualizarSelects(); // Esto asegura que `nuevaGeneroSelect` esté poblado
      nuevaGeneroSelect.value = producto.genero;

      // 2. Disparamos el evento `change` en el select de Género para poblar el select de Tipos de Especie
      const event = new Event('change');
      nuevaGeneroSelect.dispatchEvent(event);

      // 3. Con un pequeño retraso, seleccionamos el Tipo de Especie del producto
      //    (Esto da tiempo a que el select de especie se llene después del `change` del género)
      setTimeout(() => {
          nuevaEspecieSelect.value = producto.tipoEspecie;
      }, 50);

      nuevaDescripcionInput.value = producto.descripcion;
      nuevoResultadoInput.value = producto.resultado;
      // La imagen no se precarga por seguridad, el usuario deberá subirla de nuevo si quiere cambiarla

      editandoProductoIndex = index;
      formularioProducto.style.display = "grid";
      window.scrollTo(0, formularioProducto.offsetTop);
  }

  // Envío del formulario de "Agregar/Editar Especie" del catálogo
  formularioProducto.addEventListener("submit", function(e) {
      e.preventDefault();

      const generoSeleccionado = nuevaGeneroSelect.value;
      const tipoEspecieSeleccionado = nuevaEspecieSelect.value;
      const descripcion = nuevaDescripcionInput.value;
      const resultado = nuevoResultadoInput.value;
      const archivo = nuevaImagenInput.files[0];

      // Obtener la Familia (categoría) asociada al Género y Tipo de Especie seleccionados
      const familiaObj = especiesData.find(item =>
          item.genero === generoSeleccionado && item.tipoEspecie === tipoEspecieSeleccionado
      );
      const categoria = familiaObj ? familiaObj.categoria : ''; // Obtener la Familia (categoría)

      if (!categoria || !generoSeleccionado || !tipoEspecieSeleccionado || !descripcion || !resultado) {
          alert("Todos los campos del formulario de especie son obligatorios.");
          return;
      }

      const manejarImagenYGuardar = (imagenDataURL) => {
          const nuevoProducto = {
              categoria: categoria, // La Familia obtenida
              genero: generoSeleccionado,
              tipoEspecie: tipoEspecieSeleccionado,
              descripcion,
              resultado,
              imagen: imagenDataURL
          };

          if (editandoProductoIndex !== null) {
              productos[editandoProductoIndex] = nuevoProducto;
              mostrarMensajeExito("Especie del catálogo actualizada.");
          } else {
              productos.push(nuevoProducto);
              mostrarMensajeExito("Nueva especie agregada al catálogo.");
          }
          guardarDatos();
          renderizarProductos();
          formularioProducto.reset();
          formularioProducto.style.display = "none";
          editandoProductoIndex = null;
      };

      if (archivo) {
          const lector = new FileReader();
          lector.onload = (event) => manejarImagenYGuardar(event.target.result);
          lector.readAsDataURL(archivo);
      } else {
          // Si no hay nueva imagen, usa la anterior si estamos editando, o un placeholder
          const imagenAnterior = editandoProductoIndex !== null && productos[editandoProductoIndex] ?
                               productos[editandoProductoIndex].imagen :
                               "https://via.placeholder.com/230x140?text=Sin+Imagen";
          manejarImagenYGuardar(imagenAnterior);
      }
  });

  // --- Lógica de Actualización de Selects ---

  /**
   * Actualiza los selects de Familias (Categorías), Géneros y Tipos de Especie en ambos formularios.
   */
  function actualizarSelects() {
      // Actualizar el select de "Tipos de Familia" en el formulario del aside
      const categoriasUnicas = [...new Set(especiesData.map(item => item.categoria))];
      const valorSeleccionadoCategoria = categoriaSelect.value; // Guardar el valor actual

      categoriaSelect.innerHTML = '';
      categoriasUnicas.forEach(cat => {
          categoriaSelect.innerHTML += `<option value="${cat}">${cat}</option>`;
      });
      categoriaSelect.innerHTML += '<option value="Nueva">+ Nueva Familia</option>';
      // Intentar restablecer el valor seleccionado, si no, selecciona "Nueva"
      if ([...categoriaSelect.options].some(option => option.value === valorSeleccionadoCategoria)) {
          categoriaSelect.value = valorSeleccionadoCategoria;
      } else {
          categoriaSelect.value = "Nueva";
      }
      // Asegurarse de que el div de nueva categoría se muestre/oculte correctamente
      nuevaCategoriaDiv.style.display = categoriaSelect.value === "Nueva" ? "block" : "none";


      // Actualizar el select de "Género" en el formulario de producto principal
      const generosUnicos = [...new Set(especiesData.map(item => item.genero))];
      const valorSeleccionadoGenero = nuevaGeneroSelect.value; // Guardar el valor actual

      nuevaGeneroSelect.innerHTML = '<option value="" disabled selected>Selecciona un Género</option>';
      generosUnicos.forEach(gen => {
          nuevaGeneroSelect.innerHTML += `<option value="${gen}">${gen}</option>`;
      });
      // Intentar restablecer el valor seleccionado
      if ([...nuevaGeneroSelect.options].some(option => option.value === valorSeleccionadoGenero)) {
          nuevaGeneroSelect.value = valorSeleccionadoGenero;
      } else {
          nuevaGeneroSelect.value = ""; // O dejar el placeholder
      }

      // Si hay un género seleccionado en el formulario de producto, poblar su select de Tipos de Especie
      if (nuevaGeneroSelect.value) {
          populateEspecieSelect(nuevaGeneroSelect.value);
      } else {
          nuevaEspecieSelect.innerHTML = '<option value="" disabled selected>Selecciona un tipo</option>';
      }

      // Actualizar el filtro de categorías en el modal de administrar especies
      populateCategoryFilter();
  }

  /**
   * Popula el select de Tipos de Especie en el formulario del catálogo
   * basado en el Género seleccionado.
   * @param {string} generoSeleccionado - El Género seleccionado.
   */
  function populateEspecieSelect(generoSeleccionado) {
      nuevaEspecieSelect.innerHTML = '<option value="" disabled selected>Selecciona un tipo</option>';
      const especiesFiltradasPorGenero = especiesData.filter(item => item.genero === generoSeleccionado);
      const tiposEspecieUnicos = [...new Set(especiesFiltradasPorGenero.map(item => item.tipoEspecie))];

      tiposEspecieUnicos.forEach(tipo => {
          nuevaEspecieSelect.innerHTML += `<option value="${tipo}">${tipo}</option>`;
      });
  }

  // Event listener para el cambio en el select de género en el formulario de producto
  nuevaGeneroSelect.addEventListener('change', function() {
      populateEspecieSelect(this.value);
  });

  // --- Lógica de Modales de Administración ---

  // Funciones genéricas para abrir y cerrar modales
  function openModal(modal) {
      modal.style.display = "flex";
      // Pequeño retraso para que la transición CSS sea visible
      setTimeout(() => modal.classList.add("visible"), 10);
  }

  function closeModal(modal) {
      modal.classList.remove("visible");
      // Esperar a que la transición termine antes de ocultar completamente
      setTimeout(() => modal.style.display = "none", 300);
  }

  // Renderiza la lista de Familias (Categorías) en su modal de administración
  function renderizarAdminCategorias() {
      listaAdminCategorias.innerHTML = "";
      const categoriasUnicas = [...new Set(especiesData.map(e => e.categoria))];

      if (categoriasUnicas.length === 0) {
          listaAdminCategorias.innerHTML = "<p>No hay familias para administrar.</p>";
          return;
      }

      categoriasUnicas.forEach(cat => {
          const itemDiv = document.createElement("div");
          itemDiv.classList.add("admin-list-item");
          itemDiv.innerHTML = `
              <span>${cat}</span>
              <div class="admin-item-actions">
                  <button class="admin-item-edit-btn" data-categoria="${cat}">Editar</button>
                  <button class="admin-item-delete-btn" data-categoria="${cat}">Eliminar</button>
              </div>
          `;
          listaAdminCategorias.appendChild(itemDiv);
      });
  }

  // Abre el modal de administración de Familias
  adminCategoriasBtn.addEventListener("click", (e) => {
      e.preventDefault();
      renderizarAdminCategorias();
      openModal(modalAdminCategorias);
  });

  // Popula el filtro de Familias en el modal de administración de Tipos de Especie
  function populateCategoryFilter() {
      filtroCategoriaEspecie.innerHTML = '<option value="">-- Seleccione una Familia --</option>';
      const categoriasUnicas = [...new Set(especiesData.map(e => e.categoria))];
      categoriasUnicas.forEach(cat => {
          const option = document.createElement('option');
          option.value = cat;
          option.textContent = cat;
          filtroCategoriaEspecie.appendChild(option);
      });
  }

  // Renderiza la lista de Géneros y Tipos de Especie en su modal, filtrado por Familia
  function renderizarAdminEspecies(categoriaSeleccionada) {
      listaAdminEspecies.innerHTML = "";

      if (!categoriaSeleccionada) {
          listaAdminEspecies.innerHTML = "<p>Seleccione una Familia para ver los Géneros y Tipos de Especie.</p>";
          return;
      }

      let hayEspeciesEnCategoria = false;
      // Filtra `especiesData` por la categoría seleccionada y renderiza
      especiesData.forEach((item, index) => {
          if (item.categoria === categoriaSeleccionada) {
              hayEspeciesEnCategoria = true;
              const itemDiv = document.createElement("div");
              itemDiv.classList.add("admin-list-item");
              itemDiv.innerHTML = `
                  <span>${item.genero} - ${item.tipoEspecie}</span>
                  <div class="admin-item-actions">
                      <button class="admin-item-edit-btn" data-index="${index}">Editar</button>
                      <button class="admin-item-delete-btn" data-index="${index}">Eliminar</button>
                  </div>
              `;
              listaAdminEspecies.appendChild(itemDiv);
          }
      });

      if (!hayEspeciesEnCategoria) {
          listaAdminEspecies.innerHTML = `<p>No hay Géneros/Tipos de Especie en la Familia "${categoriaSeleccionada}".</p>`;
      }
  }

  // Abre el modal de administración de Tipos de Especie
  adminEspeciesBtn.addEventListener("click", (e) => {
      e.preventDefault();
      populateCategoryFilter(); // Primero poblar el filtro
      // Renderiza las especies del filtro actualmente seleccionado (si lo hay) o un mensaje inicial
      renderizarAdminEspecies(filtroCategoriaEspecie.value);
      openModal(modalAdminEspecies);
  });

  // Event listener para el cambio en el filtro de Familias en el modal de especies
  filtroCategoriaEspecie.addEventListener('change', function() {
      renderizarAdminEspecies(this.value);
  });

  // Cierre de modales: botones de "X"
  closeButtons.forEach(btn => {
      btn.addEventListener("click", () => {
          const modalId = btn.getAttribute("data-modal-id");
          closeModal(document.getElementById(modalId));
      });
  });

  // Cierre de modales: click fuera del contenido del modal
  window.addEventListener("click", (e) => {
      if (e.target.classList.contains("modal-overlay")) {
          closeModal(e.target);
      }
  });

  // --- Delegación de eventos para la administración de Familias ---
  listaAdminCategorias.addEventListener("click", function(e) {
      const target = e.target;

      if (target.classList.contains("admin-item-edit-btn")) {
          const categoriaOriginal = target.getAttribute("data-categoria");
          const nuevoNombre = prompt("Introduce el nuevo nombre para la Familia:", categoriaOriginal);

          if (nuevoNombre && nuevoNombre.trim() !== "" && nuevoNombre !== categoriaOriginal) {
              // Actualizar en `especiesData`
              especiesData.forEach(item => {
                  if (item.categoria === categoriaOriginal) {
                      item.categoria = nuevoNombre;
                  }
              });
              // Actualizar en `productos` (si tienen esta Familia asignada)
              productos.forEach(prod => {
                  if (prod.categoria === categoriaOriginal) {
                      prod.categoria = nuevoNombre;
                  }
              });

              guardarDatos();
              renderizarAdminCategorias(); // Volver a dibujar la lista de admin
              renderizarProductos(); // Actualizar el catálogo
              actualizarSelects(); // Refrescar los selects de los formularios
              mostrarMensajeExito(`Familia "${categoriaOriginal}" actualizada a "${nuevoNombre}".`);
          }
      }

      if (target.classList.contains("admin-item-delete-btn")) {
          const categoriaAEliminar = target.getAttribute("data-categoria");
          if (confirm(`¿Seguro que deseas eliminar la Familia "${categoriaAEliminar}" y TODOS los Géneros, Tipos de Especie y datos de catálogo asociados a ella?`)) {
              // Filtrar `especiesData` para eliminar la Familia y sus asociados
              especiesData = especiesData.filter(item => item.categoria !== categoriaAEliminar);
              // Filtrar `productos` para eliminar los asociados a la Familia
              productos = productos.filter(prod => prod.categoria !== categoriaAEliminar);

              guardarDatos();
              renderizarAdminCategorias();
              renderizarProductos();
              actualizarSelects();
              mostrarMensajeExito(`Familia "${categoriaAEliminar}" eliminada.`);
          }
      }
  });

  // --- Delegación de eventos para la administración de Géneros y Tipos de Especie ---
  listaAdminEspecies.addEventListener("click", function(e) {
      const target = e.target;
      const index = target.getAttribute("data-index"); // Este índice es del `especiesData` original

      if (index === null) return; // Si no hay índice, no es un botón de editar/eliminar válido

      const categoriaActualFiltro = filtroCategoriaEspecie.value; // Para mantener el filtro después de la operación

      if (target.classList.contains("admin-item-edit-btn")) {
          const especieOriginalObj = especiesData[index];
          const nombreOriginalEspecie = especieOriginalObj.tipoEspecie;
          const nombreOriginalGenero = especieOriginalObj.genero;

          const nuevoNombreEspecie = prompt("Introduce el nuevo nombre para el Tipo de Especie:", nombreOriginalEspecie);
          const nuevoNombreGenero = prompt("Introduce el nuevo nombre para el Género:", nombreOriginalGenero);

          // Solo proceder si hay cambios válidos
          if ((nuevoNombreEspecie && nuevoNombreEspecie.trim() !== "" && nuevoNombreEspecie !== nombreOriginalEspecie) ||
              (nuevoNombreGenero && nuevoNombreGenero.trim() !== "" && nuevoNombreGenero !== nombreOriginalGenero)) {

              // Actualizar en `productos` (si tienen este Género/Tipo de Especie asignado)
              productos.forEach(prod => {
                  if (prod.categoria === especieOriginalObj.categoria &&
                      prod.genero === nombreOriginalGenero &&
                      prod.tipoEspecie === nombreOriginalEspecie) {
                      prod.genero = nuevoNombreGenero || nombreOriginalGenero; // Usa el nuevo si existe, sino el original
                      prod.tipoEspecie = nuevoNombreEspecie || nombreOriginalEspecie;
                  }
              });

              // Actualizar en `especiesData`
              especiesData[index].genero = nuevoNombreGenero || nombreOriginalGenero;
              especiesData[index].tipoEspecie = nuevoNombreEspecie || nombreOriginalEspecie;

              guardarDatos();
              renderizarAdminEspecies(categoriaActualFiltro); // Volver a dibujar la lista de admin
              renderizarProductos(); // Actualizar el catálogo
              actualizarSelects(); // Refrescar los selects de los formularios
              mostrarMensajeExito(`Género/Tipo de Especie "${nombreOriginalGenero} - ${nombreOriginalEspecie}" actualizado.`);
          }
      }

      if (target.classList.contains("admin-item-delete-btn")) {
          const especieAEliminarObj = especiesData[index];
          if (confirm(`¿Seguro que deseas eliminar el Género "${especieAEliminarObj.genero}" y el Tipo de Especie "${especieAEliminarObj.tipoEspecie}" de la Familia "${especieAEliminarObj.categoria}" y TODOS los datos de catálogo asociados?`)) {

              // Filtrar `productos` para eliminar los asociados
              productos = productos.filter(prod => !(
                  prod.categoria === especieAEliminarObj.categoria &&
                  prod.genero === especieAEliminarObj.genero &&
                  prod.tipoEspecie === especieAEliminarObj.tipoEspecie
              ));

              // Eliminar del array `especiesData` por el índice
              especiesData.splice(index, 1);

              guardarDatos();
              renderizarAdminEspecies(categoriaActualFiltro);
              renderizarProductos();
              actualizarSelects();
              mostrarMensajeExito(`Género/Tipo de Especie "${especieAEliminarObj.genero} - ${especieAEliminarObj.tipoEspecie}" eliminado.`);
          }
      }
  });

  // --- Inicialización al cargar la página ---
  actualizarSelects(); // Asegura que los selects estén poblados al inicio
  renderizarProductos(); // Muestra los productos del catálogo al cargar
});