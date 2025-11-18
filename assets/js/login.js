// Migrated login.js
let base_url = "http://localhost/SYSTEM_INVENTORY/";

document.getElementById("loginForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const usuario = document.getElementById("usuario").value.trim();
    const clave = document.getElementById("clave").value.trim();

    const formData = new FormData();
    formData.append("usuario", usuario);
    formData.append("clave", clave);

    try {
        const response = await fetch(base_url + "controlador/login/validar_login.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (data.status === "ok") {
            alert(`Bienvenido, ${data.nombre}!`);
            window.location.href = base_url + "vista/dashboard/dashboard.php";
        } else {
            alert("⚠️ Usuario o contraseña incorrectos.");
        }
    } catch (error) {
        console.error("Error en la conexión:", error);
        alert("❌ Error de conexión con el servidor.");
    }
});
