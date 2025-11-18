<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelo/ProductoModel.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login/index.php");
    exit();
}
$id = intval($_GET['id']);
$productoModel = new ProductoModel();

$producto = $productoModel->getById($id);
$categorias = $productoModel->getCategorias();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../../assets/css/productos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .preview {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #003d99;
            margin-top: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <header>
        <a href="productos.php" class="back-btn"><i data-lucide="arrow-left"></i> Volver</a>
        <h1>Editar Producto</h1>
    </header>

    <div class="modal-content" style="margin:40px auto;display:block;">
        <form id="productoForm" method="POST" enctype="multipart/form-data">
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

            <label>Categoría:</label>
            <select name="categoria" required>
                <option value="">-- Selecciona una categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['nombre']; ?>" <?php if ($cat['nombre'] == $producto['categoria']) echo 'selected'; ?>>
                        <?php echo ucfirst($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>

            <label>Stock actual:</label>
            <input type="number" value="<?php echo $producto['stock']; ?>" disabled>

            <label>Foto actual:</label>
            <img src="../../<?php echo $producto['foto']; ?>" width="120" height="120" style="border-radius:10px;object-fit:contain;margin-top:10px;">

            <label>Nueva foto (opcional):</label>
            <input type="file" name="foto" accept="image/*" id="fotoInput">
            <img id="previewImg" src="../../<?php echo $producto['foto']; ?>" width="120" height="120" style="border-radius:10px;object-fit:contain;margin-top:10px;">

            <div class="btn-group">
                <button type="submit">Guardar Cambios</button>
                <a href="productos.php"><button type="button">Cancelar</button></a>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();
        const input = document.getElementById('fotoInput');
        const preview = document.getElementById('previewImg');
        input.addEventListener('change', () => {
            const file = input.files[0];
            preview.src = file ? URL.createObjectURL(file) : '../../assets/uploads/default.png';
        });

        let base_url = "http://34.42.80.200/SYSTEM_INVENTORY/";

        const form = document.getElementById("productoForm");
        form.addEventListener("submit", (e) => {
            e.preventDefault();

            if (form.checkValidity()) {
                const formData = new FormData(form);

                fetch(base_url + "controlador/ProductoController.php?action=actualizar&id=<?php echo $id; ?>", {
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