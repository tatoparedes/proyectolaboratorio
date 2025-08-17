document.addEventListener('DOMContentLoaded', () => {
    const sidebarBtns = document.querySelectorAll('.sidebar-btn');
    const panels = document.querySelectorAll('.content-panel');
    const formAccesoExamen = document.getElementById('form-acceso-examen');
    const panelExamenes = document.getElementById('panel-examenes');
    const panelExamenActivo = document.getElementById('panel-examen-activo');
    const contenedorPreguntas = document.getElementById("contenedor-preguntas");
    const btnEnviarExamen = document.getElementById("btn-enviar-examen");
    let examenActivo = null;

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

                // Mostrar título dinámico del examen
                document.getElementById("examen-titulo").innerText = "Examen: " + data.data.cExamen;

                // Cambiar panel
                panelExamenes.classList.remove("active");
                panelExamenActivo.classList.add("active");

                // Cargar preguntas dinámicamente
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
            // Limpiar preguntas previas
            contenedorPreguntas.innerHTML = "";

            // Renderizar preguntas dinámicamente
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

        // Recorrer todas las preguntas y capturar la respuesta
        contenedorPreguntas.querySelectorAll("textarea").forEach(t => {
            const nPregunta = t.getAttribute("data-idpregunta");
            const cRespuesta = t.value.trim();
            respuestas.push({ nPregunta: parseInt(nPregunta), cRespuesta });
        });

        // Validar que todas tengan respuesta
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
});