document.addEventListener('DOMContentLoaded', () => {
    const formRevision = document.getElementById("form-acceso-revision");
    const contenedorResultados = document.getElementById("contenedor-resultados");
    const resumenNota = document.getElementById("resumen-nota");
    const contenedorRespuestas = document.getElementById("contenedor-respuestas-revisadas");

    formRevision.addEventListener("submit", async (e) => {
        e.preventDefault();
        const codigo = document.getElementById("codigoExamenRevision").value.trim();
        if (!codigo) return alert("Ingrese el código de examen.");

        resumenNota.innerHTML = "";
        contenedorRespuestas.innerHTML = "";
        contenedorResultados.style.display = "none";

        try {
            const fd = new FormData();
            fd.append("accion", "verResultados");
            fd.append("codigoExamen", codigo);

            const res = await fetch("controladores/resultados_estudiante.php", {
                method: "POST",
                body: fd
            });
            const data = await res.json();

            if (data.status === "ok") {
                // Mostrar nota
                resumenNota.innerHTML = `<h4>Tu calificación: <span style="color:#1e88e5;">${data.nota}</span></h4>`;

                // Mostrar respuestas
                if (data.respuestas.length) {
                    let html = '<ul style="list-style:none;padding:0;">';
                    data.respuestas.forEach((r,i) => {
                        const color = r.cComentario && r.cComentario.toLowerCase().includes("correcta") ? 'green' : 'red';
                        html += `
                            <li style="border-bottom:1px solid #eee; padding:8px 0;">
                                <strong>${i+1}. ${r.cPregunta}</strong><br>
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