<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
  header("Location: usuarios.php");
  exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM usuarios WHERE id = $id";
$result = $conn->query($query);
$fila = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $usuario = $_POST['usuario'];
  $rol = $_POST['rol'];
  $nombre = $_POST['nombre'];

  $sql = "UPDATE usuarios SET usuario='$usuario', rol='$rol', nombre_completo='$nombre' WHERE id=$id";
  if ($conn->query($sql)) {
    header("Location: usuarios.php");
    <?php
    // Redirect legacy edit user to new MVC view
    if (isset($_GET['id'])) {
      $id = intval($_GET['id']);
      header("Location: ../vista/usuarios/editar.php?id=$id");
    } else {
      header("Location: ../vista/usuarios/usuarios.php");
    }
    exit();
    ?>
  <meta charset="UTF-8">
