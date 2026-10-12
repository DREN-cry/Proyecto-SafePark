document.addEventListener("DOMContentLoaded", () => {
    fetch("../api/obtener_config.php")
        .then(res => res.json())
        .then(response => {

            if (response.status !== "success") {
                console.error("Error:", response.message);
                return;
            }

            const d = response.data;

            // Campos personales
            document.getElementById("nombre").value = d.nombre || "";
            document.getElementById("apellido").value =
                (d.apellido_paterno + " " + d.apellido_materno).trim();
            document.getElementById("matricula").value = d.matricula || "";
            document.getElementById("telefono").value = d.telefono || "";
            document.getElementById("email").value = d.email || "";
            document.getElementById("programa").value = d.programa || "";

            // Campos de motocicleta
            document.getElementById("moto_matricula").value = d.moto_matricula || "";
            document.getElementById("marca").value = d.marca || "";
            document.getElementById("modelo").value = d.modelo || "";
            document.getElementById("color").value = d.color || "";
            document.getElementById("cilindraje").value = d.cilindraje || "";
            document.getElementById("accesorios").value = d.accesorios || "";
        })
        .catch(err => console.error(err));
});

function guardarCambios() {

    // Preparar apellidos si usas dos campos en BD
    let [apellido_paterno, apellido_materno] =
        document.getElementById("apellido").value.split(" ");

    const data = {
        nombre: document.getElementById("nombre").value,
        apellido_paterno: apellido_paterno || "",
        apellido_materno: apellido_materno || "",
        telefono: document.getElementById("telefono").value,
        email: document.getElementById("email").value,
        programa: document.getElementById("programa").value,

        moto_matricula: document.getElementById("moto_matricula").value,
        marca: document.getElementById("marca").value,
        modelo: document.getElementById("modelo").value,
        color: document.getElementById("color").value,
        cilindraje: document.getElementById("cilindraje").value.replace(/\D/g, ""),
        accesorios: document.getElementById("accesorios").value
    };

    fetch("../api/actualizar_config.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(r => {
        alert(r.message);

        // 🔹 Recargar para reflejar cambios en Dashboard
        if (r.status === "success") {
            window.location.reload();
        }
    })
    .catch(err => console.error(err));
}


