<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelo/ProductoModel.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login/index.php");
    exit();
}
$productoModel = new ProductoModel();

$categorias = $productoModel->getCategorias();

$rol = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Categorías | Sistema de Inventario</title>
    <link rel="stylesheet" href="../../assets/css/productos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <header>
        <a href="./productos.php" class="back-btn">
            <i data-lucide="arrow-left"></i> Volver a Productos
        </a>
        <h1>Gestión de Categorías</h1>

        <?php if ($rol == 'admin'): ?>
            <a href="agregar_categoria.php" class="btn-categoria">+ Agregar Categoría</a>
        <?php endif; ?>
    </header>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <?php if ($rol == 'admin'): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($categorias)) {
                foreach ($categorias as $fila) {
                    echo "<tr>";
                    echo "<td>{$fila['id']}</td>";
                    echo "<td>{$fila['nombre']}</td>";

                    if ($rol == 'admin') {
                        echo "<td>
                    <a data-id={$fila['id']} class='delete-btn' onclick='return confirm(\"¿Eliminar esta categoría?\")'>Eliminar</a>
                  </td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay categorías registradas.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        lucide.createIcons();
        let base_url = "http://34.42.80.200/inventory/";

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.dataset.id;

                    fetch(base_url + "controlador/ProductoController.php?action=eliminarCategoria&id=" + id)
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