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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
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
        <h1>Agregar Producto</h1>
    </header>

    <div class="modal-content" style="margin:40px auto;display:block;">
        <form id="productoForm" method="POST" enctype="multipart/form-data">
            <input type="text" name="nombre" placeholder="Nombre del producto" required>

            <label>Categoría:</label>
            <select name="categoria" required>
                <option value="">-- Selecciona una categoría -- </option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['nombre']; ?>">
                        <?php echo ucfirst($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" step="0.01" name="precio" placeholder="Precio S/" required>

            <label style="margin-top:10px;">Foto del producto:</label>
            <input type="file" name="foto" accept="image/*" id="fotoInput">
            <img id="previewImg" src="../../uploads/default.png" alt="Vista previa" class="preview">

            <div class="btn-group">
                <button type="submit">Guardar</button>
                <a href="productos.php"><button type="button">Cancelar</button></a>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();
        let base_url = "http://localhost/SYSTEM_INVENTORY/";

        const input = document.getElementById('fotoInput');
        const preview = document.getElementById('previewImg');
        input.addEventListener('change', () => {
            const file = input.files[0];
            preview.src = file ? URL.createObjectURL(file) : '../../assets/uploads/default.png';
        });

        const form = document.getElementById("productoForm");
        form.addEventListener("submit", (e) => {
            e.preventDefault();

            if (form.checkValidity()) {
                const formData = new FormData(form);

                fetch(base_url + "controlador/ProductoController.php?action=crear", {
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