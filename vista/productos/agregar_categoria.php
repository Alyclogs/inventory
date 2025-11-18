<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Categoría</title>
    <link rel="stylesheet" href="../../assets/css/productos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <header>
        <a href="categorias.php" class="back-btn"><i data-lucide="arrow-left"></i> Volver</a>
        <h1>Agregar Nueva Categoría</h1>
    </header>

    <div class="modal-content" style="margin:40px auto;display:block;">
        <form id="categoriaForm" method="POST">
            <input type="text" name="nombre" placeholder="Nombre de la nueva categoría" required>

            <div class="btn-group">
                <button type="submit">Guardar</button>
                <a href="categorias.php"><button type="button">Cancelar</button></a>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();
        let base_url = "http://localhost/SYSTEM_INVENTORY/";

        const form = document.getElementById("categoriaForm");
        form.addEventListener("submit", (e) => {
            e.preventDefault();

            if (form.checkValidity()) {
                const formData = new FormData(form);

                fetch(base_url + "controlador/ProductoController.php?action=crearCategoria", {
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