<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelo/UsuarioModel.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login/index.php");
    exit();
}
$usuarioModel = new UsuarioModel();

$rol = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | Sistema de Inventario</title>
    <link rel="stylesheet" href="../../assets/css/usuarios.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <header>
        <a href="../dashboard/dashboard.php" class="back-btn">
            <i data-lucide="arrow-left"></i> Volver al Dashboard
        </a>
        <h1>Gestión de Usuarios</h1>

        <?php if ($rol == 'admin'): ?>
            <a href="agregar.php" class="btn-agregar">+ Agregar Usuario</a>
        <?php endif; ?>
    </header>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Nombre Completo</th>
                <?php if ($rol == 'admin'): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $usuarios = $usuarioModel->getAll();

            if (!empty($usuarios)) {
                foreach ($usuarios as $fila) {
                    echo "<tr>";
                    echo "<td>{$fila['id']}</td>";
                    echo "<td>{$fila['usuario']}</td>";
                    echo "<td>{$fila['rol']}</td>";
                    echo "<td>{$fila['nombre_completo']}</td>";

                    if ($rol == 'admin') {
                        echo "<td>
                    <a href='editar.php?id={$fila['id']}' class='edit-btn'>Editar</a>
                    <a data-id='{$fila['id']}' class='delete-btn' onclick='return confirm(\"¿Seguro que deseas eliminar este usuario?\")'>Eliminar</a>
                  </td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        lucide.createIcons();
        let base_url = "http://34.42.80.200/SYSTEM_INVENTORY/";

        document.addEventListener("DOMContentLoaded", function(e) {
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.preventDefault();

                    const formData = new FormData();
                    formData.append("id", btn.dataset.id);

                    fetch(base_url + "controlador/UsuarioController.php?action=eliminarUsuario", {
                            method: "POST",
                            body: formData
                        })
                        .then(res => res.json())
                        .then(res => {
                            alert(res.message);
                        });
                });
            });
        });
    </script>
</body>

</html>