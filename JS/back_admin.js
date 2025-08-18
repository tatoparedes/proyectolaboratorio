document.addEventListener("DOMContentLoaded", () => {
    const tbody = document.querySelector(".user-table tbody");

    async function listarUsuarios() {
        try {
            const formData = new FormData();
            formData.append("accion", "listar");

            const res = await fetch("controladores/admin.php", { method: "POST", body: formData });
            const data = await res.json();

            tbody.innerHTML = "";

            if (data.status === "ok") {
                data.data.forEach((u, index) => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${index+1}</td>
                        <td contenteditable="true" class="editable" data-campo="cDNI" data-id="${u.nUsuario}">${u.cDNI}</td>
                        <td contenteditable="true" class="editable" data-campo="cApePaterno" data-id="${u.nUsuario}">${u.cApePaterno}</td>
                        <td contenteditable="true" class="editable" data-campo="cApeMaterno" data-id="${u.nUsuario}">${u.cApeMaterno}</td>
                        <td contenteditable="true" class="editable" data-campo="cNombres" data-id="${u.nUsuario}">${u.cNombres}</td>
                        <td contenteditable="true" class="editable" data-campo="cCorreo" data-id="${u.nUsuario}">${u.cCorreo}</td>
                        <td>
                            <select class="rol-select" data-id="${u.nUsuario}">
                                <option value="1" ${u.nRol==1?"selected":""}>Alumno</option>
                                <option value="2" ${u.nRol==2?"selected":""}>Docente</option>
                                <option value="3" ${u.nRol==3?"selected":""}>Admin</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn-eliminar" data-id="${u.nUsuario}">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Editar contenido editable
                tbody.querySelectorAll(".editable").forEach(td => {
                    td.addEventListener("blur", async () => {
                        const nUsuario = td.dataset.id;
                        const campo = td.dataset.campo;
                        const valor = td.textContent.trim();

                        const formData = new FormData();
                        formData.append("accion", "actualizar");
                        formData.append("nUsuario", nUsuario);
                        formData.append("campo", campo);
                        formData.append("valor", valor);

                        const res = await fetch("controladores/admin.php", { method: "POST", body: formData });
                        const data = await res.json();
                        if (data.status !== "ok") alert(data.message);
                    });
                });

                // Cambiar rol
                tbody.querySelectorAll(".rol-select").forEach(select => {
                    select.addEventListener("change", async () => {
                        const nUsuario = select.dataset.id;
                        const valor = select.value;

                        const formData = new FormData();
                        formData.append("accion", "actualizar");
                        formData.append("nUsuario", nUsuario);
                        formData.append("campo", "nRol");
                        formData.append("valor", valor);

                        const res = await fetch("controladores/admin.php", { method: "POST", body: formData });
                        const data = await res.json();
                        if (data.status !== "ok") alert(data.message);
                    });
                });

                // Eliminar usuario
                tbody.querySelectorAll(".btn-eliminar").forEach(btn => {
                    btn.addEventListener("click", async () => {
                        if (!confirm("¿Seguro que quieres eliminar este usuario?")) return;
                        const nUsuario = btn.dataset.id;

                        const formData = new FormData();
                        formData.append("accion", "eliminar");
                        formData.append("nUsuario", nUsuario);

                        const res = await fetch("controladores/admin.php", { method: "POST", body: formData });
                        const data = await res.json();
                        if (data.status === "ok") {
                            alert(data.message);
                            listarUsuarios();
                        } else {
                            alert(data.message);
                        }
                    });
                });

            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error(err);
            alert("Error al cargar usuarios");
        }
    }

    listarUsuarios();
});