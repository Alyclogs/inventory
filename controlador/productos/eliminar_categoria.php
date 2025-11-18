<?php
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  header("Location: ../controlador/productos/eliminar_categoria.php?id=$id");
} else {
  header("Location: categorias.php");
}
exit();
