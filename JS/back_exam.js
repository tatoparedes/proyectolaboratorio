// ==================== EXÁMENES ==================== //
let preguntasTmp = []; // Array temporal de preguntas
let pruebasDisponibles = []; // Para almacenar info de pruebas con foto

// Renderizar preguntas en la lista temporal
function renderPreguntasTmp() {
    const lista = document.getElementById("listaPreguntas");
    lista.innerHTML = "";
    preguntasTmp.forEach((p, i) => {
        const div = document.createElement("div");
        div.className = "pregunta-item";
        div.innerHTML = `
            <strong>${i + 1}.</strong> ${p.descripcion}
            ${p.nombrePrueba ? `<br><small>Prueba: ${p.nombrePrueba}</small>` : ""}
            ${p.foto ? `<br><img src="${p.foto}" class="preview-img" style="max-width:100px; display:block; margin-top:5px;">` : ""}
            <button class="btn btn-danger btn-sm" onclick="eliminarPreguntaTmp(${i})">X</button>
        `;
        lista.appendChild(div);
    });
    document.getElementById("count").innerText = preguntasTmp.length;
}

// Eliminar pregunta del array temporal
function eliminarPreguntaTmp(index) {
    preguntasTmp.splice(index, 1);
    renderPreguntasTmp();
}

// --- Agregar pregunta temporal ---
document.getElementById("agregarBtn").addEventListener("click", () => {
    const descripcion = document.getElementById("descripcion").value.trim();
    const selectPrueba = document.getElementById("pruebaSelect");
    const nPrueba = selectPrueba.value || null;
    const nombrePrueba = selectPrueba.options[selectPrueba.selectedIndex]?.text || "";

    if (descripcion === "") {
        alert("Debe ingresar la descripción de la pregunta");
        return;
    }

    let foto = "";
    if (nPrueba && pruebasDisponibles.length > 0) {
        const prueba = pruebasDisponibles.find(p => p.nPrueba == nPrueba);
        if (prueba && prueba.cFoto) {
            foto = "uploads/" + prueba.cFoto;
        }
    }

    preguntasTmp.push({
        descripcion: descripcion,
        nPrueba: nPrueba,
        nombrePrueba: nombrePrueba !== "-- Seleccione prueba --" ? nombrePrueba : null,
        foto: foto
    });

    document.getElementById("descripcion").value = "";
    renderPreguntasTmp();
});

// --- Limpiar preguntas temporales ---
document.getElementById("clearAll").addEventListener("click", () => {
    if (!confirm("¿Seguro de limpiar todas las preguntas temporales?")) return;
    preguntasTmp = [];
    renderPreguntasTmp();
});

// --- Guardar examen completo ---
document.getElementById("guardarExamen").addEventListener("click", async () => {
    const cExamen = prompt("Ingrese un nombre para el examen:");
    if (!cExamen) {
        alert("Debe ingresar un nombre");
        return;
    }
    if (preguntasTmp.length === 0) {
        alert("Debe agregar al menos una pregunta");
        return;
    }

    const formData = new FormData();
    formData.append("accion", "agregar");
    formData.append("cExamen", cExamen);
    formData.append("preguntas", JSON.stringify(preguntasTmp));

    try {
        const res = await fetch("controladores/examen.php", { method: "POST", body: formData });
        const data = await res.json();
        if (data.status === "ok") {
            alert("Examen guardado correctamente. Código generado: " + data.codigo);
            preguntasTmp = [];
            renderPreguntasTmp();
            listarExamenes(); // Refrescar tabla
        } else {
            alert(data.message || "Error al guardar examen");
        }
    } catch (err) {
        console.error(err);
        alert("Error de conexión al guardar examen");
    }
});

// ==================== DEPENDENCIAS ==================== //
// Cargar pruebas al seleccionar especie
document.getElementById("especie").addEventListener("change", async function () {
    const especieId = this.value;
    const pruebaSelect = document.getElementById("pruebaSelect");
    const previewImg = document.getElementById("preview");

    pruebaSelect.innerHTML = '<option value="">-- Seleccione prueba --</option>';
    pruebaSelect.disabled = true;
    previewImg.style.display = "none";
    previewImg.src = "";
    pruebasDisponibles = [];

    if (!especieId) return;

    const formData = new FormData();
    formData.append("accion", "listarPruebas");
    formData.append("nEspecie", especieId);

    try {
        const res = await fetch("controladores/examen.php", { method: "POST", body: formData });
        const data = await res.json();
        if (data.status === "ok" && data.data.length > 0) {
            pruebasDisponibles = data.data;
            data.data.forEach(prueba => {
                const option = document.createElement("option");
                option.value = prueba.nPrueba;
                option.textContent = prueba.cBacteria;
                pruebaSelect.appendChild(option);
            });
            pruebaSelect.disabled = false;
        }
    } catch (err) {
        console.error("Error cargando pruebas:", err);
    }
});

// Mostrar imagen al seleccionar prueba
document.getElementById("pruebaSelect").addEventListener("change", function () {
    const selectedId = parseInt(this.value);
    const previewImg = document.getElementById("preview");

    if (!pruebasDisponibles || selectedId === 0) {
        previewImg.style.display = "none";
        previewImg.src = "";
        return;
    }

    const prueba = pruebasDisponibles.find(p => p.nPrueba === selectedId);
    if (prueba && prueba.cFoto) {
        previewImg.src = "uploads/" + prueba.cFoto;
        previewImg.style.display = "block";
    } else {
        previewImg.style.display = "none";
        previewImg.src = "";
    }
});

// ==================== LISTAR EXÁMENES ==================== //
async function listarExamenes() {
    const formData = new FormData();
    formData.append("accion", "listar");
    try {
        const res = await fetch("controladores/examen.php", { method: "POST", body: formData });
        const data = await res.json();
        const tbody = document.getElementById("examenes-guardados-body");
        tbody.innerHTML = "";
        if (data.status === "ok") {
            data.data.forEach(examen => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${examen.cExamen}<br><small>Código: ${examen.cCodigoExamen}</small></td>
                    <td>${examen.totalPreguntas}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="verPreguntas(${examen.nExamen})">👁 Ver</button>
                        <button class="btn btn-warning btn-sm" onclick="editarExamen(${examen.nExamen}, '${examen.cExamen}')">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarExamen(${examen.nExamen})">Eliminar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (err) {
        console.error(err);
    }
}

// --- Ver preguntas en ventana emergente ---
async function verPreguntas(nExamen) {
    try {
        const formData = new FormData();
        formData.append("accion", "verPreguntas");
        formData.append("nExamen", nExamen);

        const res = await fetch("controladores/examen.php", { method: "POST", body: formData });
        const data = await res.json();

        if (data.status === "ok" && data.data.length > 0) {
            let mensaje = `Preguntas del examen:\n\n`;
            data.data.forEach((p, i) => {
                mensaje += `${i + 1}. ${p.cPregunta}`;
                if (p.cDescripcionPrueba) mensaje;
                mensaje += "\n";
            });
            alert(mensaje);
        } else {
            alert("No hay preguntas para este examen");
        }
    } catch (err) {
        console.error(err);
        alert("Error al cargar preguntas del examen");
    }
}

// --- Editar examen ---
async function editarExamen(nExamen, nombreActual) {
    const nuevoNombre = prompt("Editar nombre del examen:", nombreActual);
    if (!nuevoNombre || nuevoNombre.trim() === "") return;

    const formData = new FormData();
    formData.append("accion", "editar");
    formData.append("nExamen", nExamen);
    formData.append("cExamen", nuevoNombre.trim());

    try {
        const res = await fetch("controladores/examen.php", { method: "POST", body: formData });
        const data = await res.json();

        if (data.status === "ok") {
            alert("Examen actualizado correctamente");
            listarExamenes();
        } else {
            alert(data.message || "Error al actualizar examen");
        }
    } catch (err) {
        console.error(err);
        alert("Error de conexión al actualizar examen");
    }
}

// --- Eliminar examen ---
async function eliminarExamen(nExamen) {
    if (!confirm("¿Seguro de eliminar este examen?")) return;

    const formData = new FormData();
    formData.append("accion", "eliminar");
    formData.append("nExamen", nExamen);

    try {
        const res = await fetch("controladores/examen.php", { method: "POST", body: formData });
        const data = await res.json();

        if (data.status === "ok") {
            alert("Examen eliminado correctamente");
            listarExamenes();
        } else {
            alert(data.message || "Error al eliminar examen");
        }
    } catch (err) {
        console.error(err);
        alert("Error al eliminar examen");
    }
}

// --- Cargar exámenes al inicio ---
document.addEventListener("DOMContentLoaded", listarExamenes);