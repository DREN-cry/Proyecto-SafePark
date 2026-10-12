const btnUser = document.getElementById("btnUser");
const btnAdmin = document.getElementById("btnAdmin");

const studentFields = document.getElementById("studentFields");
const adminFields = document.getElementById("adminFields");
const motorcycleSection = document.getElementById("motorcycleSection");

let currentType = "user";

btnUser.onclick = () => setType("user");
btnAdmin.onclick = () => setType("admin");

function setType(type) {
  currentType = type;

  btnUser.classList.toggle("active", type === "user");
  btnAdmin.classList.toggle("active", type === "admin");

  studentFields.classList.toggle("hidden", type !== "user");
  motorcycleSection.classList.toggle("hidden", type !== "user");

  adminFields.classList.toggle("hidden", type !== "admin");
  // Quitar/poner required dependiendo del tipo
  document.getElementById("studentId").required = type === "user";
  document.getElementById("educationalProgram").required = type === "user";

  document.getElementById("employeeId").required = type === "admin";
  document.getElementById("role").required = type === "admin";
}

// ------------------------------------------
// ENVÍO DEL FORMULARIO
// ------------------------------------------

document.getElementById("registerForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData();

  // Tipo usuario
  const tipoUsuario = currentType === "admin" ? "administrador" : "estudiante";
  formData.append("tipo_usuario", tipoUsuario);

  // Datos personales
  formData.append("firstName", document.getElementById("firstName").value);
  formData.append("lastNameP", document.getElementById("lastNameP").value);
  formData.append("lastNameM", document.getElementById("lastNameM").value);

  formData.append("email", document.getElementById("email").value);
  formData.append("phone", document.getElementById("phone").value);
  formData.append("password", document.getElementById("password").value);

  // Admin
  if (tipoUsuario === "administrador") {
    formData.append("employeeId", document.getElementById("employeeId").value);
    formData.append("role", document.getElementById("role").value);
  }

  // Estudiante
  if (tipoUsuario === "estudiante") {
    formData.append("studentId", document.getElementById("studentId").value);
    formData.append("educationalProgram", document.getElementById("educationalProgram").value);

    formData.append("motorcyclePlate", document.getElementById("motorcyclePlate").value);
    formData.append("motorcycleBrand", document.getElementById("motorcycleBrand").value);
    formData.append("motorcycleModel", document.getElementById("motorcycleModel").value);
    formData.append("motorcycleColor", document.getElementById("motorcycleColor").value);
    formData.append("motorcycleDisplacement", document.getElementById("motorcycleDisplacement").value);
    formData.append("motorcycleAccessories", document.getElementById("motorcycleAccessories").value);
  }

  // Enviar a PHP
  const response = await fetch("../api/register.php", {
    method: "POST",
    body: formData
  });

  const result = await response.json();

  alert(result.message);

  if (result.status === "success") {
    window.location.href = "../index.html";
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

