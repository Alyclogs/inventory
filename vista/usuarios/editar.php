<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelo/UsuarioModel.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: usuarios.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}
$usuarioModel = new UsuarioModel();

$id = intval($_GET['id']);
$usuario = $usuarioModel->getById($id);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuario | Sistema de Inventario</title>
    <link rel="stylesheet" href="../../assets/css/usuarios.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #003d99;
            margin-top: 10px
        }
    </style>
</head>

<body>
    <header>
        <a href="usuarios.php" class="back-btn"><i data-lucide="arrow-left"></i> Volver</a>
        <h1>Editar Usuario</h1>
    </header>

    <div class="modal-content" style="margin: 40px auto; display:block;">
        <form id="usuarioForm" method="POST" enctype="multipart/form-data">
            <input type="text" name="usuario" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
            <input type="password" name="clave" placeholder="Nueva contraseña (dejar en blanco para mantener)">

            <select name="rol" required>
                <option value="">Seleccionar Rol</option>
                <option value="admin" <?php if ($usuario['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
                <option value="empleado" <?php if ($usuario['rol'] == 'empleado') echo 'selected'; ?>>Empleado</option>
            </select>

            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>

            <label style="margin-top:10px; font-weight:600;">Foto de usuario:</label>
            <input type="file" name="foto" accept="image/*" id="fotoInput">
            <img id="previewImg" src="../../<?php echo $usuario['foto']; ?>" alt="Vista previa" class="preview">

            <div class="btn-group">
                <button type="submit">Guardar</button>
                <a href="usuarios.php"><button type="button">Cancelar</button></a>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();
        const input = document.getElementById('fotoInput');
        const preview = document.getElementById('previewImg');
        input.addEventListener('change', () => {
            const file = input.files[0];
            preview.src = file ? URL.createObjectURL(file) : preview.src;
        });

        let base_url = "http://34.42.80.200/SYSTEM_INVENTORY/";

        const form = document.getElementById("usuarioForm");
        form.addEventListener("submit", (e) => {
            e.preventDefault();

            if (form.checkValidity()) {
                const formData = new FormData(form);

                fetch(base_url + "controlador/UsuarioController.php?acion=actualizar&id=<?php echo $id; ?>", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        alert(res.message);
                    });
            } else {
                form.reportValidity();
            }
        });
    </script>
</body>

</html>