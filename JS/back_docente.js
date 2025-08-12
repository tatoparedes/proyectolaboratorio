            // ---- NUEVA LÓGICA PARA EDITAR Y ELIMINAR FAMILIAS ----
            document.getElementById('table-familias').addEventListener('click', function(e) {
              if (e.target.classList.contains('btn-edit-familia')) {
                  e.preventDefault();
                  const id = e.target.dataset.id;
                  const nombre = e.target.dataset.nombre;
                  
                  document.getElementById('nombre_familia').value = nombre;
                  document.getElementById('id_familia_edit').value = id;
                  
                  const form = document.getElementById('form-familias');
                  form.querySelector('.btn-submit').textContent = 'Actualizar Familia';
                  form.querySelector('.btn-submit').name = 'editar_familia';
              }
              if (e.target.classList.contains('btn-delete-familia')) {
                  e.preventDefault();
                  if (confirm('¿Estás seguro de que quieres eliminar esta familia?')) {
                      const id = e.target.dataset.id;
                      const form = document.createElement('form');
                      form.method = 'POST';
                      form.action = 'vista_docente.php';
                      form.style.display = 'none';

                      const inputId = document.createElement('input');
                      inputId.type = 'hidden';
                      inputId.name = 'id_familia';
                      inputId.value = id;
                      form.appendChild(inputId);

                      const inputAction = document.createElement('input');
                      inputAction.type = 'hidden';
                      inputAction.name = 'eliminar_familia';
                      inputAction.value = 'true';
                      form.appendChild(inputAction);

                      document.body.appendChild(form);
                      form.submit();
                  }
              }
          });

          // ---- NUEVA LÓGICA PARA EDITAR Y ELIMINAR GÉNEROS ----
          document.getElementById('table-generos').addEventListener('click', function(e) {
              if (e.target.classList.contains('btn-edit-genero')) {
                  e.preventDefault();
                  const id = e.target.dataset.id;
                  const nombre = e.target.dataset.nombre;
                  const familiaId = e.target.dataset.familiaid;

                  document.getElementById('familia_select_genero').value = familiaId;
                  document.getElementById('nombre_genero').value = nombre;
                  document.getElementById('id_genero_edit').value = id;

                  const form = document.getElementById('form-generos');
                  form.querySelector('.btn-submit').textContent = 'Actualizar Género';
                  form.querySelector('.btn-submit').name = 'editar_genero';
              }
              if (e.target.classList.contains('btn-delete-genero')) {
                  e.preventDefault();
                  if (confirm('¿Estás seguro de que quieres eliminar este género?')) {
                      const id = e.target.dataset.id;
                      const form = document.createElement('form');
                      form.method = 'POST';
                      form.action = 'vista_docente.php';
                      form.style.display = 'none';

                      const inputId = document.createElement('input');
                      inputId.type = 'hidden';
                      inputId.name = 'id_genero';
                      inputId.value = id;
                      form.appendChild(inputId);

                      const inputAction = document.createElement('input');
                      inputAction.type = 'hidden';
                      inputAction.name = 'eliminar_genero';
                      inputAction.value = 'true';
                      form.appendChild(inputAction);

                      document.body.appendChild(form);
                      form.submit();
                  }
              }
          });

          // ---- NUEVA LÓGICA PARA EDITAR Y ELIMINAR ESPECIES ----
          document.getElementById('table-especies').addEventListener('click', function(e) {
              if (e.target.classList.contains('btn-edit-especie')) {
                  e.preventDefault();
                  const id = e.target.dataset.id;
                  const nombre = e.target.dataset.nombre;
                  const generoId = e.target.dataset.generoid;

                  document.getElementById('genero_select_especie').value = generoId;
                  document.getElementById('nombre_especie').value = nombre;
                  document.getElementById('id_especie_edit').value = id;

                  const form = document.getElementById('form-especies');
                  form.querySelector('.btn-submit').textContent = 'Actualizar Especie';
                  form.querySelector('.btn-submit').name = 'editar_especie';
              }
              if (e.target.classList.contains('btn-delete-especie')) {
                  e.preventDefault();
                  if (confirm('¿Estás seguro de que quieres eliminar esta especie?')) {
                      const id = e.target.dataset.id;
                      const form = document.createElement('form');
                      form.method = 'POST';
                      form.action = 'vista_docente.php';
                      form.style.display = 'none';

                      const inputId = document.createElement('input');
                      inputId.type = 'hidden';
                      inputId.name = 'id_especie';
                      inputId.value = id;
                      form.appendChild(inputId);

                      const inputAction = document.createElement('input');
                      inputAction.type = 'hidden';
                      inputAction.name = 'eliminar_especie';
                      inputAction.value = 'true';
                      form.appendChild(inputAction);

                      document.body.appendChild(form);
                      form.submit();
                  }
              }
          });