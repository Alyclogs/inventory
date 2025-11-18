<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: categorias.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);

    if (!empty($nombre)) {
        $sql = "INSERT INTO categorias (nombre) VALUES ('$nombre')";
        if ($conn->query($sql)) {
            echo "<script>alert('Categoría agregada correctamente'); window.location='categorias.php';</script>";
        } else {
            echo "<script>alert('Error: la categoría ya existe o no se pudo agregar');</script>";
        }
    } else {
        echo "<script>alert('Ingresa un nombre de categoría');</script>";
    }
}
