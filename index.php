<?php
// Punto de entrada principal del Sistema de Inventario
// Redirige al login
session_start();

if (isset($_SESSION['usuario'])) {
    // Si ya está autenticado, ir al dashboard
    header("Location: ./vista/dashboard/dashboard.php");
    exit();
} else {
    // Si no está autenticado, ir al login
    header("Location: ./vista/login/index.php");
    exit();
}
