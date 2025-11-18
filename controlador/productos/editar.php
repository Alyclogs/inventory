<?php
include("../../config/conexion.php");

$id = $_GET['id'];
$sql = "SELECT * FROM productos WHERE id = $id";
$result = $conn->query($sql);
$producto = $result->fetch_assoc();

$categorias = [];
$resCat = $conn->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
while ($row = $resCat->fetch_assoc()) {
  $categorias[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['nombre'];
  $categoria = $_POST['categoria'];
  $precio = $_POST['precio'];

  $foto = $producto['foto'];

  if (!empty($_FILES['foto']['name'])) {
    $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
    $rutaDestino = "uploads/" . $nombreArchivo;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
      $foto = $rutaDestino;
    }
  }

  $sql = "UPDATE productos 
          SET nombre='$nombre', categoria='$categoria', precio='$precio', foto='$foto'
          WHERE id=$id";

  if ($conn->query($sql)) {
    header("Location: productos.php");
    exit();
  } else {
    echo "<script>alert('Error al actualizar producto');</script>";
  }
}
