document.addEventListener('DOMContentLoaded', () => {
    const sidebarBtns = document.querySelectorAll('.sidebar-btn');
    const panels = document.querySelectorAll('.content-panel');
    const formAccesoExamen = document.getElementById('form-acceso-examen');
    const panelExamenes = document.getElementById('panel-examenes');
    const panelExamenActivo = document.getElementById('panel-examen-activo');
    const contenedorPreguntas = document.getElementById("contenedor-preguntas");
    const btnEnviarExamen = document.getElementById("btn-enviar-examen");
    let examenActivo = null;

    // Panel de resultados del estudiante
    const formRevision = document.getElementById("form-acceso-revision");
    const contenedorResultados = document.getElementById("contenedor-resultados");
    const resumenNota = document.getElementById("resumen-nota");
    const contenedorRespuestas = document.getElementById("contenedor-respuestas-revisadas");

    // === Menú lateral ===
    sidebarBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            panels.forEach(panel => panel.classList.remove('active'));
            sidebarBtns.forEach(b => b.classList.remove('active'));

            const targetPanel = document.querySelector(btn.getAttribute('href'));
            if (targetPanel) targetPanel.classList.add('active');
            btn.classList.add('active');
        });
    });

    // === Acceso a examen ===
    formAccesoExamen.addEventListener("submit", async (e) => {
        e.preventDefault();
        const codigo = document.getElementById("codigoExamen").value.trim();

        if (!codigo) {
            alert("Ingrese un código de examen");
            return;
        }

        const fd = new FormData();
        fd.append("accion", "verificarCodigo");
        fd.append("codigoExamen", codigo);

        try {
            const res = await fetch("controladores/examen_estudiante.php", {
                method: "POST",
                body: fd
            });
            const data = await res.json();

            if (data.status === "ok") {
                examenActivo = data.data.nExamen;

                document.getElementById("examen-titulo").innerText = "Examen: " + data.data.cExamen;

                panelExamenes.classList.remove("active");
                panelExamenActivo.classList.add("active");

                await obtenerPreguntas(examenActivo);
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error(err);
            alert("Error al verificar el examen.");
        }
    });

    // === Obtener preguntas desde la DB ===
    async function obtenerPreguntas(nExamen) {
        const fd = new FormData();
        fd.append("accion", "obtenerPreguntas");
        fd.append("nExamen", nExamen);

        try {
            const res = await fetch("controladores/examen_estudiante.php", {
                method: "POST",
                body: fd
            });
            const data = await res.json();

            if (data.status === "ok") {
                contenedorPreguntas.innerHTML = "";

                data.data.forEach((p, i) => {
                    const div = document.createElement("div");
                    div.className = "examen-pregunta";
                    div.innerHTML = `
                        <label>${i + 1}. ${p.cPregunta}</label>
                        ${p.cFoto ? `<img src="uploads/${p.cFoto}" alt="Imagen de prueba" class="examen-img">` : ""}
                        <textarea data-idpregunta="${p.nPregunta}" rows="4" placeholder="Escribe tu respuesta..."></textarea>
                    `;
                    contenedorPreguntas.appendChild(div);
                });
            } else {
                alert("No se pudieron cargar las preguntas.");
            }
        } catch (err) {
            console.error(err);
            alert("Error al obtener las preguntas.");
        }
    }

    // === Enviar respuestas del examen ===
    btnEnviarExamen.addEventListener("click", async () => {
        const respuestas = [];

        contenedorPreguntas.querySelectorAll("textarea").forEach(t => {
            const nPregunta = t.getAttribute("data-idpregunta");
            const cRespuesta = t.value.trim();
            respuestas.push({ nPregunta: parseInt(nPregunta), cRespuesta });
        });

        const sinRespuesta = respuestas.some(r => !r.cRespuesta);
        if (sinRespuesta) {
            alert("Debe responder todas las preguntas antes de enviar.");
            return;
        }

        const fd = new FormData();
        fd.append("accion", "guardarRespuestas");
        fd.append("nExamen", examenActivo);
        fd.append("respuestas", JSON.stringify(respuestas));

        try {
            const res = await fetch("controladores/examen_estudiante.php", {
                method: "POST",
                body: fd
            });
            const data = await res.json();

            if (data.status === "ok") {
                alert("Examen enviado correctamente.");
                panelExamenActivo.classList.remove("active");
                panelExamenes.classList.add("active");
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error(err);
            alert("Error al enviar respuestas.");
        }
    });

    // === Acceder a resultados del estudiante ===
    // === Acceder a resultados del estudiante ===
formRevision.addEventListener("submit", async (e) => {
    e.preventDefault();
    const codigo = document.getElementById("codigoExamenRevision").value.trim();
    if (!codigo) return alert("Ingrese el código de examen.");

    resumenNota.innerHTML = "";
    contenedorRespuestas.innerHTML = "";
    contenedorResultados.style.display = "none";

    try {
        const fd = new FormData();
        fd.append("accion", "verResultadosEstudiante");
        fd.append("codigoExamen", codigo);

        const res = await fetch("controladores/examen_estudiante.php", {
            method: "POST",
            body: fd
        });
        const data = await res.json();

        if (data.status === "ok") {
            // Mostrar la nota
            resumenNota.innerHTML = `<h4>Tu calificación: <span style="color:#1e88e5;">${data.nota ?? "Pendiente"}</span></h4>`;

            // Mostrar cada respuesta
            if (data.respuestas && data.respuestas.length > 0) {
                let html = '<ul style="list-style:none;padding:0;">';
                data.respuestas.forEach((r, i) => {
                    const color = r.cComentario && r.cComentario.toLowerCase().includes("correcta") ? 'green' : 'red';
                    html += `
                        <li style="border-bottom:1px solid #eee; padding:8px 0;">
                            <strong>${i + 1}. ${r.cPregunta}</strong><br>
                            Tu respuesta: <span>${r.cRespuesta}</span><br>
                            Comentario: <span style="color:${color};">${r.cComentario ?? "Sin revisión"}</span>
                            ${r.cFoto ? `<br><img src="uploads/${r.cFoto}" class="examen-img" alt="Imagen de prueba">` : ''}
                        </li>
                    `;
                });
                html += "</ul>";
                contenedorRespuestas.innerHTML = html;
            } else {
                contenedorRespuestas.innerHTML = "<p>No hay respuestas registradas para este examen.</p>";
            }

            contenedorResultados.style.display = "block";
        } else {
            alert(data.message);
        }
    } catch (err) {
        console.error(err);
        alert("Error al obtener resultados del examen.");
    }
});
});