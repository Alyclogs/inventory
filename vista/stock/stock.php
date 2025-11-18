<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelo/ProductoModel.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login/index.php");
    exit();
}

$productoModel = new ProductoModel();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Control de Stock | Sistema de Inventario</title>
    <link rel="stylesheet" href="../../assets/css/stock.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <header>
        <a href="../dashboard/dashboard.php" class="back-btn">
            <i data-lucide="arrow-left"></i> Volver al Dashboard
        </a>
        <h1>Control de Stock</h1>
    </header>

    <section class="filter-bar">
        <div class="select-container">
            <label for="categoriaSelect">Categoría:</label>
            <select id="categoriaSelect" onchange="cargarProductos(this.value)">
                <option value="">-- Selecciona una categoría --</option>
                <?php
                $categorias = $productoModel->getCategorias();
                foreach ($categorias as $row) {
                    echo "<option value='{$row['nombre']}'>{$row['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="search-container">
            <i data-lucide="search"></i>
            <input type="text" id="buscarInput" placeholder="Buscar producto..." onkeyup="buscarSugerencias(this.value)">
            <ul id="sugerencias" class="sugerencias"></ul>
        </div>
    </section>

    <section id="productosContainer" class="productos-grid">
        <p style="text-align:center;color:#555;"></p>
    </section>

    <div id="stockModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitulo">Actualizar Stock</h2>
            <form id="formStock">
                <input type="hidden" name="producto_id" id="producto_id">

                <label>Tipo de movimiento:</label>
                <select name="tipo" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="Entrada">Entrada</option>
                    <option value="Salida">Salida</option>
                </select>

                <label>Cantidad:</label>
                <input type="number" name="cantidad" min="1" required>

                <label>Observación:</label>
                <input type="text" name="observacion" placeholder="Ej: compra o venta de producto">

                <div class="btn-group">
                    <button type="submit">Guardar</button>
                    <button type="button" class="cancel" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
        let base_url = "http://localhost/SYSTEM_INVENTORY/";

        function cargarProductos(categoria) {
            const contenedor = document.getElementById("productosContainer");
            contenedor.innerHTML = "<p style='text-align:center;'>Cargando...</p>";

            fetch(base_url + "controlador/ProductoController.php?action=listar&categoria=" + encodeURIComponent(categoria))
                .then(res => res.json())
                .then(prods => {
                    if (prods.length === 0) {
                        contenedor.innerHTML = "<p style='grid-column:1/-1;text-align:center;color:#777;'>No hay productos en esta categoría.</p>";
                        return;
                    }
                    let html = '';
                    prods.forEach(prod => {
                        html += `
                        <div class='producto-card'>
                            <img src='${base_url + prod.foto}' alt='${prod.nombre}'>
                            <h3>${prod.nombre}</h3>
                            <p><b>Stock:</b> ${prod.stock}</p>
                            <button onclick="abrirModal(${prod.id}, '${prod.nombre}')">Actualizar</button>
                        </div>`;
                    });
                    contenedor.innerHTML = html;
                })
                .catch(() => contenedor.innerHTML = "<p style='color:red;'>Error al cargar productos.</p>");
        }

        function abrirModal(id, nombre) {
            document.getElementById('producto_id').value = id;
            document.getElementById('modalTitulo').innerText = "Actualizar stock: " + nombre;
            document.getElementById('stockModal').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('stockModal').style.display = 'none';
            document.getElementById('formStock').reset();
        }

        document.getElementById('formStock').addEventListener('submit', async e => {
            e.preventDefault();
            const formData = new FormData(e.target);

            const res = await fetch(base_url + "controlador/StockController.php?action=actualizar", {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            alert(data.message);
            if (data.status === "ok") {
                cerrarModal();
                cargarProductos(document.getElementById("categoriaSelect").value);
            }
        });

        function buscarSugerencias(termino) {
            const lista = document.getElementById("sugerencias");
            if (termino.length < 2) {
                lista.innerHTML = "";
                lista.style.display = "none";
                return;
            }

            fetch(base_url + "controlador/ProductoController.php?action=buscar&filtro=" + encodeURIComponent(termino))
                .then(res => res.json())
                .then(data => {
                    lista.innerHTML = "";
                    if (data.length > 0) {
                        lista.style.display = "block";
                        data.forEach(p => {
                            const item = document.createElement("li");
                            item.textContent = p.nombre;
                            item.onclick = () => mostrarProducto(p.id);
                            lista.appendChild(item);
                        });
                    } else {
                        lista.style.display = "none";
                    }
                });
        }

        function mostrarProducto(id) {
            const contenedor = document.getElementById("productosContainer");
            fetch(base_url + "controlador/ProductoController.php?action=ver&id=" + id)
                .then(res => res.json())
                .then(prod => {
                    contenedor.innerHTML = `
                    <div class='producto-card'>
                        <img src='${base_url + prod.foto}' alt='${prod.nombre}'>
                        <h3>${prod.nombre}</h3>
                        <p><b>Stock:</b> ${prod.stock}</p>
                        <button onclick="abrirModal(${prod.id}, '${prod.nombre}')">Actualizar</button>
                    </div>`;
                    document.getElementById("sugerencias").style.display = "none";
                });
        }
    </script>

</body>

</html>