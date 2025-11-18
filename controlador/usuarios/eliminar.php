<?php
// Redirect legacy eliminar user to new controller
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  header("Location: ../controlador/usuarios/eliminar.php?id=$id");
} else {
  header("Location: usuarios.php");
}
exit();
