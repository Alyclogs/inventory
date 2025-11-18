<?php
// Redirect to new controlador that handles deletion
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  header("Location: ../controlador/productos/eliminar.php?id=$id");
} else {
  header("Location: productos.php");
}
exit();
