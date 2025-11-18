<?php
session_start();
include("../../config/conexion.php");

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

$sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND clave=MD5('$clave')";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $_SESSION['usuario'] = $data['usuario'];
    $_SESSION['nombre'] = $data['nombre_completo'];
    $_SESSION['rol'] = $data['rol'];

    echo json_encode(["status" => "ok", "nombre" => $data['nombre_completo']]);
} else {
    echo json_encode(["status" => "error"]);
}

$conn->close();
