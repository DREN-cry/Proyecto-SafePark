// dashboard-user.js
document.addEventListener("DOMContentLoaded", () => {
  // PROFILE DROPDOWN
  const toggle = document.getElementById("profileToggle");
  const menu = document.getElementById("profileMenu");

  if (toggle && menu) {
    toggle.addEventListener("click", (e) => {
      e.stopPropagation();
      const open = menu.style.display === "block";
      menu.style.display = open ? "none" : "block";
      toggle.setAttribute("aria-expanded", !open);
    });

    document.addEventListener("click", (e) => {
      if (!toggle.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = "none";
        toggle.setAttribute("aria-expanded", "false");
      }
    });
  }
});
/* =========================
   DASHBOARD EN TIEMPO REAL
========================= */

function actualizarDashboard() {
  fetch("../api/dashboard_data.php")
    .then(response => response.json())
    .then(data => {

      // MÉTRICAS
      const motos = document.getElementById("motosDentro");
      const ingresos = document.getElementById("ingresosHoy");
      const usuarios = document.getElementById("totalUsuarios");

      if (motos) motos.textContent = data.motosDentro;
      if (ingresos) ingresos.textContent = data.ingresosHoy;
      if (usuarios) usuarios.textContent = data.usuarios;

      const capacidadTexto = document.getElementById("capacidadTexto");
      const progressBar = document.getElementById("progressBar");
      const disponibles = document.getElementById("disponibles");
      const porcentaje = document.getElementById("porcentaje");

      if (capacidadTexto)
        capacidadTexto.textContent = data.motosDentro + "/" + data.capacidad;

      if (progressBar)
        progressBar.style.width = data.porcentaje + "%";

      if (disponibles)
        disponibles.textContent = data.disponibles + " espacios disponibles";

      if (porcentaje)
        porcentaje.textContent = data.porcentaje + "% ocupado";

      // ACTIVIDAD RECIENTE
      const lista = document.getElementById("actividadList");
      if (lista) {
        lista.innerHTML = "";

        data.actividad.forEach(item => {
          const iconClass = item.tipo === "entrada" ? "icon-in" : "icon-out";
          const nombre = item.nombre + " " + (item.apellido_paterno ?? "");
          const hora = new Date(item.fecha)
            .toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });

          lista.innerHTML += `
            <li class="activity-item">
              <div class="icon ${iconClass}">➜</div>
              <div class="activity-info">
                <span class="plate">${item.matricula ?? "Sin placa"}</span>
                <span class="tag ${item.tipo}">${item.tipo}</span>
                <span class="name">${nombre}</span>
              </div>
              <div class="activity-time">
                <span>${hora}</span>
              </div>
            </li>
          `;
        });
      }

    })
    .catch(error => console.error("Error:", error));
}

// Ejecutar cada 3 segundos
setInterval(actualizarDashboard, 3000);

// Ejecutar al cargar
actualizarDashboard();