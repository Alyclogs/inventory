<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelo/ProductoModel.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login/index.php");
    exit();
}

$base_url = "http://34.42.80.200/SYSTEM_INVENTORY/";
$rol = $_SESSION['rol'];
$productoModel = new ProductoModel();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | Sistema de Inventario</title>
    <link rel="stylesheet" href="../../assets/css/productos.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #003d99;
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            font-size: 1.5rem;
            margin: 0;
        }

        .btn-agregar,
        .btn-categoria {
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-agregar {
            background: #00b894;
        }

        .btn-agregar:hover {
            background: #00a07f;
            transform: translateY(-2px);
        }

        .btn-categoria {
            background: #ffb300;
        }

        .btn-categoria:hover {
            background: #ff9f00;
            transform: translateY(-2px);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #004aad;
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: #0060ff;
        }

        table {
            width: 92%;
            margin: 40px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
        }

        th,
        td {
            padding: 14px;
            font-size: 15px;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #003d99;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: #f7f9ff;
            transition: 0.2s;
        }

        .edit-btn,
        .delete-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            margin: 0 4px;
            display: inline-block;
        }

        .edit-btn {
            background: #007bff;
        }

        .edit-btn:hover {
            background: #005edc;
        }

        .delete-btn {
            background: #e74c3c;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        td img {
            width: 55px;
            height: 55px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 800px) {
            table {
                width: 100%;
                font-size: 13px;
            }

            header {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <header>
        <a href="../dashboard/dashboard.php" class="back-btn">
            <i data-lucide="arrow-left"></i> Volver al Dashboard
        </a>
        <h1>Gestión de Productos</h1>

        <?php if ($rol == 'admin'): ?>
            <div style="display: flex; gap: 10px;">
                <a href="agregar_categoria.php" class="btn-categoria">+ Agregar Categoría</a>
                <a href="agregar.php" class="btn-agregar">+ Agregar Producto</a>
            </div>
        <?php endif; ?>
    </header>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio (S/)</th>
                <th>Stock</th>
                <?php if ($rol == 'admin'): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $productos = $productoModel->getAll();

            if (!empty($productos)) {
                foreach ($productos as $fila) {
                    $foto = $base_url . $fila['foto'];

                    echo "<tr>";
                    echo "<td>{$fila['id']}</td>";
                    echo "<td><img src='{$foto}' alt='foto'></td>";
                    echo "<td>{$fila['nombre']}</td>";
                    echo "<td>{$fila['categoria']}</td>";
                    echo "<td><b>S/ {$fila['precio']}</b></td>";
                    echo "<td>{$fila['stock']}</td>";

                    if ($rol == 'admin') {
                        echo "<td>
                    <a href='editar.php?id={$fila['id']}' class='edit-btn'>Editar</a>
                    <a data-id='{$fila['id']}' class='delete-btn'>Eliminar</a>
                  </td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='color:#888;font-style:italic;'>No hay productos registrados.</td></tr>";
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

                    if (confirm("¿Eliminar producto?")) {
                        const formData = new FormData();
                        formData.append("id", btn.dataset.id);

                        fetch(base_url + "controlador/ProductoController.php?action=eliminar", {
                                method: "POST",
                                body: formData
                            })
                            .then(res => res.json())
                            .then(res => {
                                alert(res.message);
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>