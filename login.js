const identifierInput = document.getElementById("identifier");
const detectedText = document.getElementById("detectedText");
const loginForm = document.getElementById("loginForm");

// Detecta si parece admin o estudiante (solo visual)
function detectUserType(id) {
  if (id.includes("@")) return "admin";
  return "user";
}

identifierInput.addEventListener("input", () => {
  const value = identifierInput.value.trim();
  if (!value) {
    detectedText.textContent = "";
    return;
  }

  const type = detectUserType(value);
  detectedText.textContent =
    type === "admin" ? "Detectado: Administrador" : "Detectado: Estudiante";
});

// -------------------------------------------------------------
//  LOGIN REAL
// -------------------------------------------------------------
loginForm.addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData();
  formData.append("identifier", identifierInput.value.trim());
  formData.append("password", document.getElementById("password").value);

  const response = await fetch("./api/login.php", {
    method: "POST",
    body: formData,
  });

  const result = await response.json();

  if (result.status === "error") {
    alert(result.message);
    return;
  }

  // Redirect según el tipo de usuario
  if (result.tipo_usuario === "administrador") {
    window.location.href = "views/dashboard-admin.php";
  } else {
    window.location.href = "views/dashboard-user.php";
  }
});

const passwordInput = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("click", () => {
  const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
  passwordInput.setAttribute("type", type);
  // Cambiar icono si quieres
  togglePassword.textContent = type === "password" ? "👁️" : "🙈";
});

