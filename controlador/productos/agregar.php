<?php
include("../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];

    $foto = "uploads/default.png";
    if (!empty($_FILES['foto']['name'])) {
        $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
        $rutaDestino = "uploads/" . $nombreArchivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
            $foto = $rutaDestino;
        }
    }

    $sql = "INSERT INTO productos (nombre, categoria, precio, stock, foto)
          VALUES ('$nombre', '$categoria', '$precio', 0, '$foto')";

    if ($conn->query($sql)) {
        echo "<script>alert('Producto agregado correctamente'); window.location='productos.php';</script>";
    } else {
        echo "<script>alert('Error al agregar producto');</script>";
    }
}
