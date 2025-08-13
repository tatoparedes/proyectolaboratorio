// --- FAMILIAS ---
const formFamilias = document.getElementById('form-familias');
const tbodyFamilias = document.querySelector('#table-familias tbody');
const inputIdFamilia = document.getElementById('id_familia_edit');
const inputAccionFamilia = document.getElementById('accion_familia');
const inputNombreFamilia = document.getElementById('nombre_familia');
const btnCancelarFamilia = document.getElementById('btn-cancelar');

function cargarFamilias() {
  fetch('controladores/familia.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({accion: 'listar'})
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'ok'){
      tbodyFamilias.innerHTML = '';
      data.data.forEach(familia => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${familia.nFamilia}</td>
          <td>${familia.cFamilia}</td>
          <td>
            <a href="#" class="btn-edit-familia" data-id="${familia.nFamilia}" data-nombre="${familia.cFamilia}">Editar</a>
            <a href="#" class="btn-delete-familia" data-id="${familia.nFamilia}">Eliminar</a>
          </td>
        `;
        tbodyFamilias.appendChild(tr);
      });
      agregarEventosAccionFamilia();
    } else {
      alert('Error al cargar familias: ' + data.message);
    }
  })
  .catch(err => alert('Error en la solicitud: ' + err));
}

function limpiarFormularioFamilia() {
  inputIdFamilia.value = '0';
  inputAccionFamilia.value = 'agregar';
  inputNombreFamilia.value = '';
  btnCancelarFamilia.style.display = 'none';
}

function agregarEventosAccionFamilia() {
  document.querySelectorAll('.btn-edit-familia').forEach(btn => {
    btn.onclick = e => {
      e.preventDefault();
      inputIdFamilia.value = btn.dataset.id;
      inputNombreFamilia.value = btn.dataset.nombre;
      inputAccionFamilia.value = 'editar';
      btnCancelarFamilia.style.display = 'inline-block';
    };
  });

  document.querySelectorAll('.btn-delete-familia').forEach(btn => {
    btn.onclick = e => {
      e.preventDefault();
      if(confirm('¿Seguro que quieres eliminar esta familia?')){
        fetch('controladores/familia.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: new URLSearchParams({accion: 'eliminar', nFamilia: btn.dataset.id})
        })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if(data.status === 'ok'){
            cargarFamilias();
            limpiarFormularioFamilia();
          }
        })
        .catch(err => alert('Error en la solicitud: ' + err));
      }
    };
  });
}

formFamilias.addEventListener('submit', e => {
  e.preventDefault();
  const accion = inputAccionFamilia.value;
  const nombre = inputNombreFamilia.value.trim();
  const id = inputIdFamilia.value;

  if(nombre === ''){
    alert('El nombre no puede estar vacío');
    return;
  }

  const datos = new URLSearchParams();
  datos.append('accion', accion);
  datos.append('cFamilia', nombre);
  if(accion === 'editar'){
    datos.append('nFamilia', id);
  }

  fetch('controladores/familia.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: datos
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if(data.status === 'ok'){
      limpiarFormularioFamilia();
      cargarFamilias();
    }
  })
  .catch(err => alert('Error en la solicitud: ' + err));
});

btnCancelarFamilia.onclick = () => {
  limpiarFormularioFamilia();
};

// --- GENEROS ---
const formGeneros = document.getElementById('form-generos');
const tbodyGeneros = document.querySelector('#table-generos tbody');
const inputIdGenero = document.getElementById('id_genero_edit');
const inputAccionGenero = document.getElementById('accion_genero');
const inputNombreGenero = document.getElementById('nombre_genero');
const selectFamiliaGenero = document.getElementById('familia_select_genero');
const btnCancelarGenero = document.getElementById('btn-cancelar-genero');

function cargarGeneros(nFamilia = null) {
  // Si se pasa nFamilia, lista por familia, si no, lista todo
  const params = nFamilia 
    ? new URLSearchParams({accion: 'listarPorFamilia', nFamilia}) 
    : new URLSearchParams({accion: 'listar'});

  fetch('controladores/genero.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: params
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'ok'){
      tbodyGeneros.innerHTML = '';
      data.data.forEach(genero => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${genero.nGenero}</td>
          <td>${genero.cFamilia || ''}</td>
          <td>${genero.cGenero}</td>
          <td>
            <a href="#" class="btn-edit-genero" 
              data-id="${genero.nGenero}" 
              data-nombre="${genero.cGenero}" 
              data-familia="${genero.nFamilia}">Editar</a>
            <a href="#" class="btn-delete-genero" data-id="${genero.nGenero}">Eliminar</a>
          </td>
        `;
        tbodyGeneros.appendChild(tr);
      });
      agregarEventosAccionGenero();
    } else {
      alert('Error al cargar géneros: ' + data.message);
    }
  })
  .catch(err => alert('Error en la solicitud: ' + err));
}

function limpiarFormularioGenero() {
  inputIdGenero.value = '0';
  inputAccionGenero.value = 'agregar';
  inputNombreGenero.value = '';
  selectFamiliaGenero.value = '';
  btnCancelarGenero.style.display = 'none';
}

function agregarEventosAccionGenero() {
  document.querySelectorAll('.btn-edit-genero').forEach(btn => {
    btn.onclick = e => {
      e.preventDefault();
      inputIdGenero.value = btn.dataset.id;
      inputNombreGenero.value = btn.dataset.nombre;
      selectFamiliaGenero.value = btn.dataset.familia;
      inputAccionGenero.value = 'editar';
      btnCancelarGenero.style.display = 'inline-block';
    };
  });

  document.querySelectorAll('.btn-delete-genero').forEach(btn => {
    btn.onclick = e => {
      e.preventDefault();
      if(confirm('¿Seguro que quieres eliminar este género?')){
        fetch('controladores/genero.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: new URLSearchParams({accion: 'eliminar', nGenero: btn.dataset.id})
        })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if(data.status === 'ok'){
            cargarGeneros();
            limpiarFormularioGenero();
          }
        })
        .catch(err => alert('Error en la solicitud: ' + err));
      }
    };
  });
}

formGeneros.addEventListener('submit', e => {
  e.preventDefault();

  const datos = new URLSearchParams();
  datos.append('accion', inputAccionGenero.value);
  datos.append('cGenero', inputNombreGenero.value.trim());
  datos.append('nFamilia', selectFamiliaGenero.value);
  if(inputAccionGenero.value === 'editar'){
    datos.append('nGenero', inputIdGenero.value);
  }

  fetch('controladores/genero.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: datos
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if(data.status === 'ok'){
      limpiarFormularioGenero();
      cargarGeneros();
    }
  })
  .catch(err => alert('Error en la solicitud: ' + err));
});

btnCancelarGenero.onclick = () => {
  limpiarFormularioGenero();
};

// --- ESPECIES ---
const formEspecies = document.getElementById('form-especies');
const tbodyEspecies = document.querySelector('#table-especies tbody');
const inputIdEspecie = document.getElementById('id_especie_edit');
const inputAccionEspecie = document.getElementById('accion_especie');
const inputNombreEspecie = document.getElementById('nombre_especie');
const selectFamiliaEspecie = document.getElementById('familia_select_especie');
const selectGeneroEspecie = document.getElementById('genero_select_especie');
const btnCancelarEspecie = document.getElementById('btn-cancelar-especie');

function cargarGenerosPorFamilia(familiaId, selectedGeneroId = null) {
  if (!familiaId) {
    selectGeneroEspecie.innerHTML = '<option value="">-- Elige un género --</option>';
    selectGeneroEspecie.disabled = true;
    return;
  }
  fetch('controladores/genero.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({accion: 'listarPorFamilia', nFamilia: familiaId})
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      selectGeneroEspecie.innerHTML = '<option value="">-- Elige un género --</option>';
      data.data.forEach(gen => {
        const selected = selectedGeneroId == gen.nGenero ? 'selected' : '';
        selectGeneroEspecie.insertAdjacentHTML('beforeend', `<option value="${gen.nGenero}" ${selected}>${gen.cGenero}</option>`);
      });
      selectGeneroEspecie.disabled = false;
    } else {
      alert('Error al cargar géneros: ' + data.message);
    }
  })
  .catch(err => alert('Error en la solicitud géneros: ' + err));
}

function cargarEspecies() {
  fetch('controladores/especie.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({accion: 'listar'})
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'ok'){
      tbodyEspecies.innerHTML = '';
      data.data.forEach(esp => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${esp.nEspecie}</td>
          <td>${esp.cFamilia}</td>
          <td>${esp.cGenero}</td>
          <td>${esp.cEspecie}</td>
          <td>
            <a href="#" class="btn-edit-especie" data-id="${esp.nEspecie}" data-nombre="${esp.cEspecie}" data-familia="${esp.nFamilia}" data-genero="${esp.nGenero}">Editar</a>
            <a href="#" class="btn-delete-especie" data-id="${esp.nEspecie}">Eliminar</a>
          </td>
        `;
        tbodyEspecies.appendChild(tr);
      });
      agregarEventosAccionEspecie();
    } else {
      alert('Error al cargar especies: ' + data.message);
    }
  })
  .catch(err => alert('Error en la solicitud especies: ' + err));
}

function limpiarFormularioEspecie() {
  inputIdEspecie.value = '0';
  inputAccionEspecie.value = 'agregar';
  inputNombreEspecie.value = '';
  selectFamiliaEspecie.value = '';
  selectGeneroEspecie.innerHTML = '<option value="">-- Elige un género --</option>';
  selectGeneroEspecie.disabled = true;
  btnCancelarEspecie.style.display = 'none';
}

function agregarEventosAccionEspecie() {
  document.querySelectorAll('.btn-edit-especie').forEach(btn => {
    btn.onclick = e => {
      e.preventDefault();
      inputIdEspecie.value = btn.dataset.id;
      inputNombreEspecie.value = btn.dataset.nombre;
      inputAccionEspecie.value = 'editar';
      selectFamiliaEspecie.value = btn.dataset.familia;
      cargarGenerosPorFamilia(btn.dataset.familia, btn.dataset.genero);
      btnCancelarEspecie.style.display = 'inline-block';
    };
  });

  document.querySelectorAll('.btn-delete-especie').forEach(btn => {
    btn.onclick = e => {
      e.preventDefault();
      if(confirm('¿Seguro que quieres eliminar esta especie?')){
        fetch('controladores/especie.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: new URLSearchParams({accion: 'eliminar', nEspecie: btn.dataset.id})
        })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if(data.status === 'ok'){
            cargarEspecies();
            limpiarFormularioEspecie();
          }
        })
        .catch(err => alert('Error en la solicitud: ' + err));
      }
    };
  });
}

selectFamiliaEspecie.addEventListener('change', () => {
  cargarGenerosPorFamilia(selectFamiliaEspecie.value);
  selectGeneroEspecie.innerHTML = '<option value="" disabled selected>-- Elige un género --</option>';
  selectGeneroEspecie.disabled = true;
});

formEspecies.addEventListener('submit', e => {
  e.preventDefault();

  const accion = inputAccionEspecie.value;
  const nombre = inputNombreEspecie.value.trim();
  const id = inputIdEspecie.value;
  const familiaId = selectFamiliaEspecie.value;
  const generoId = selectGeneroEspecie.value;

  if(!familiaId){
    alert('Seleccione una familia');
    return;
  }
  if(!generoId){
    alert('Seleccione un género');
    return;
  }
  if(nombre === ''){
    alert('El nombre de la especie no puede estar vacío');
    return;
  }

  const datos = new URLSearchParams();
  datos.append('accion', accion);
  datos.append('cEspecie', nombre);
  datos.append('nFamilia', familiaId);
  datos.append('nGenero', generoId);
  if(accion === 'editar'){
    datos.append('nEspecie', id);
  }

  fetch('controladores/especie.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: datos
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if(data.status === 'ok'){
      limpiarFormularioEspecie();
      cargarEspecies();
    }
  })
  .catch(err => alert('Error en la solicitud: ' + err));
});

btnCancelarEspecie.onclick = () => {
  limpiarFormularioEspecie();
};

// --- PRUEBAS ---
document.addEventListener('DOMContentLoaded', () => {
  const formulario = document.getElementById('formularioProducto');
  const cancelButton = document.getElementById('cancelButton');
  const mostrarFormularioBtn = document.getElementById('mostrarFormularioBtn');

  const familiaSelect = document.getElementById('familiaSelect');
  const generoSelect = document.getElementById('generoSelect');
  const especieSelect = document.getElementById('especieSelect');

  const contenedorProductos = document.getElementById('contenedorProductos');

  const accionInput = document.getElementById('accion');
  const nPruebaInput = document.getElementById('nPrueba');

  // Cargar familias al cargar página
  function cargarFamilias() {
    fetch('controladores/familia.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({accion: 'listar'})
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        familiaSelect.innerHTML = '<option value="" disabled selected>-- Elige una familia --</option>';
        data.data.forEach(fam => {
          const option = document.createElement('option');
          option.value = fam.nFamilia;
          option.textContent = fam.cFamilia;
          familiaSelect.appendChild(option);
        });
      } else {
        alert('Error al cargar familias: ' + data.message);
      }
    })
    .catch(() => alert('Error en la solicitud para familias'));
  }

  // Al cambiar familia, cargar géneros
  familiaSelect.addEventListener('change', () => {
    const nFamilia = familiaSelect.value;
    generoSelect.innerHTML = '<option value="" disabled selected>-- Elige un género --</option>';
    generoSelect.disabled = true;
    especieSelect.innerHTML = '<option value="" disabled selected>-- Elige una especie --</option>';
    especieSelect.disabled = true;

    if (!nFamilia) {
      generoSelect.innerHTML = '<option value="" disabled selected>-- Elige un género --</option>';
      generoSelect.disabled = true;
      return;
    }

    fetch('controladores/genero.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({accion: 'listarPorFamilia', nFamilia})
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        generoSelect.innerHTML = '<option value="" disabled selected>-- Elige un género --</option>';
        data.data.forEach(gen => {
          const option = document.createElement('option');
          option.value = gen.nGenero;
          option.textContent = gen.cGenero;
          generoSelect.appendChild(option);
        });
        generoSelect.disabled = false;
      } else {
        alert('Error al cargar géneros: ' + data.message);
      }
    })
    .catch(() => alert('Error en la solicitud para géneros'));
  });

  // Al cambiar género, cargar especies
  generoSelect.addEventListener('change', () => {
    const nGenero = generoSelect.value;

    especieSelect.innerHTML = '<option value="" disabled selected>-- Elige una especie --</option>';
    especieSelect.disabled = true;

    if (!nGenero) {
      especieSelect.innerHTML = '<option value="" disabled selected>-- Elige una especie --</option>';
      especieSelect.disabled = true;
      return;
    }

    fetch('controladores/especie.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({accion: 'listarPorGenero', nGenero})
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        especieSelect.innerHTML = '<option value="" disabled selected>-- Elige una especie --</option>';
        data.data.forEach(esp => {
          const option = document.createElement('option');
          option.value = esp.nEspecie;
          option.textContent = esp.cEspecie;
          especieSelect.appendChild(option);
        });
        especieSelect.disabled = false;
      } else {
        alert('Error al cargar especies: ' + data.message);
      }
    })
    .catch(() => alert('Error en la solicitud para especies'));
  });

  // Enviar formulario (crear o editar)
  formulario.addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData(formulario);

    fetch('controladores/prueba.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        alert(data.message || 'Prueba guardada correctamente');
        cargarPruebas();
        formulario.style.display = 'none';
        cancelButton.style.display = 'none';
        mostrarFormularioBtn.style.display = 'inline-block';
        limpiarFormulario();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(() => alert('Error en la solicitud al guardar prueba'));
  });

  // Limpiar formulario
  function limpiarFormulario() {
    formulario.reset();
    generoSelect.innerHTML = '<option value="" disabled selected>-- Elige un género --</option>';
    generoSelect.disabled = true;
    especieSelect.innerHTML = '<option value="" disabled selected>-- Elige una especie --</option>';
    especieSelect.disabled = true;
    accionInput.value = 'agregar';
    nPruebaInput.value = '0';
  }

  // Cargar pruebas para mostrar en cuadro
  function cargarPruebas() {
    fetch('controladores/prueba.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({accion: 'listar'})
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        contenedorProductos.innerHTML = ''; // limpiar

        data.data.forEach(prueba => {
          // Crear cuadro con info y botones Editar/Eliminar
          const div = document.createElement('div');
          div.className = 'producto-item'; // agrega clase para estilos si quieres

          div.innerHTML = `
            ${prueba.cFoto ? `<img src="uploads/${prueba.cFoto}" alt="Imagen" style="width:200px; height:200px; object-fit:contain;">` : ''} <br>
            <strong>Familia:</strong> ${prueba.cFamilia} <br>
            <strong>Género:</strong> ${prueba.cGenero} <br>
            <strong>Especie:</strong> ${prueba.cEspecie} <br>
            <strong>Bacteria:</strong> ${prueba.cBacteria} <br>
            <strong>Descripción:</strong> ${prueba.cDescripcion} <br>
            <strong>Resultado:</strong> ${prueba.cResultado} <br>
            <button class="editar-btn" data-id="${prueba.nPrueba}">Editar</button>
            <button class="eliminar-btn" data-id="${prueba.nPrueba}">Eliminar</button>
            <br><br>
          `;

          contenedorProductos.appendChild(div);
        });

        // Agregar event listeners a botones después de crear elementos
        document.querySelectorAll('.editar-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            editarPrueba(id);
          });
        });

        document.querySelectorAll('.eliminar-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            if (confirm('¿Seguro que quieres eliminar esta prueba?')) {
              eliminarPrueba(id);
            }
          });
        });

      } else {
        contenedorProductos.innerHTML = 'No se encontraron pruebas.';
      }
    })
    .catch(() => contenedorProductos.innerHTML = 'Error al cargar pruebas.');
  }
    // Función para cargar datos de una prueba al formulario para editar
    function editarPrueba(id) {
      fetch('controladores/prueba.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({accion: 'listar'})
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'ok') {
          const prueba = data.data.find(p => p.nPrueba == id);
          if (!prueba) {
            alert('Prueba no encontrada');
            return;
          }
  
          // Llenar formulario con datos de la prueba
          familiaSelect.value = prueba.nFamilia;
          // Disparar cambio para cargar géneros y esperar
          familiaSelect.dispatchEvent(new Event('change'));
  
          // Después de un pequeño delay, seleccionar género y disparar evento para especies
          setTimeout(() => {
            generoSelect.value = prueba.nGenero;
            generoSelect.dispatchEvent(new Event('change'));
          }, 300);
  
          // Después otro delay, seleccionar especie
          setTimeout(() => {
            especieSelect.value = prueba.nEspecie;
          }, 600);
  
          document.getElementById('nombrePruebaInput').value = prueba.cBacteria;
          document.getElementById('descripcionInput').value = prueba.cDescripcion;
          document.getElementById('resultadoInput').value = prueba.cResultado;
          accionInput.value = 'editar';
          nPruebaInput.value = prueba.nPrueba;
  
          // Mostrar formulario y ocultar botón agregar
          formulario.style.display = 'block';
          cancelButton.style.display = 'inline-block';
          mostrarFormularioBtn.style.display = 'none';
        } else {
          alert('Error al obtener prueba para editar: ' + data.message);
        }
      })
      .catch(() => alert('Error en la solicitud para editar prueba'));
    }
  
    // Función para eliminar prueba
    function eliminarPrueba(id) {
      fetch('controladores/prueba.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({accion: 'eliminar', nPrueba: id})
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'ok') {
          alert(data.message || 'Prueba eliminada');
          cargarPruebas();
        } else {
          alert('Error al eliminar: ' + data.message);
        }
      })
      .catch(() => alert('Error en la solicitud para eliminar prueba'));
    }
  
    // Mostrar formulario botón
    mostrarFormularioBtn.addEventListener('click', () => {
      limpiarFormulario();
      formulario.style.display = 'block';
      cancelButton.style.display = 'inline-block';
      mostrarFormularioBtn.style.display = 'none';
    });
  
    // Cancelar formulario
    cancelButton.addEventListener('click', () => {
      limpiarFormulario();
      formulario.style.display = 'none';
      cancelButton.style.display = 'none';
      mostrarFormularioBtn.style.display = 'inline-block';
    });
  
    // Limpiar formulario
    function limpiarFormulario() {
      formulario.reset();
      generoSelect.innerHTML = '<option value="" disabled selected>-- Elige un género --</option>';
      generoSelect.disabled = true;
      especieSelect.innerHTML = '<option value="" disabled selected>-- Elige una especie --</option>';
      especieSelect.disabled = true;
      accionInput.value = 'agregar';
      nPruebaInput.value = '0';
      document.getElementById('previewImagen').style.display = 'none';
    }

  // Inicializar carga al entrar
  cargarFamilias();
  cargarGeneros();
  cargarEspecies();
  cargarPruebas();
});

// --- INICIO ---
document.addEventListener('DOMContentLoaded', () => {
  cargarFamilias();
  cargarGeneros();
  cargarEspecies();
  cargarPruebas();
});

const imagenInput = document.getElementById('imagenInput');
const previewImagen = document.getElementById('previewImagen');

imagenInput.addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = event => {
      previewImagen.src = event.target.result;
      previewImagen.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    previewImagen.src = '';
    previewImagen.style.display = 'none';
  }
});