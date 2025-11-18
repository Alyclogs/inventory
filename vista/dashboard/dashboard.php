<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema de Inventario</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <header class="topbar">
        <div class="topbar-left">
            <img src="../../assets/img/logo.png" alt="Logo" class="logo">
            <h1>ANDRES ESPINOZA MOTOS</h1>
        </div>
        <div class="topbar-right">
            <p>👋 Bienvenido, <b><?php echo $_SESSION['nombre']; ?></b></p>
            <span class="rol">Rol: <?php echo ucfirst($_SESSION['rol']); ?></span>
        </div>
    </header>

    <aside class="sidebar">
        <nav>
            <ul class="menu">
                <li><a href="../usuarios/usuarios.php"><i data-lucide="users"></i> Usuarios</a></li>
                <li><a href="../productos/productos.php"><i data-lucide="package"></i> Productos</a></li>
                <li><a href="../stock/stock.php"><i data-lucide="archive"></i> Control de Stock</a></li>
                <li><a href="../reportes/reportes.php"><i data-lucide="bar-chart-2"></i> Reportes</a></li>
            </ul>
        </nav>

        <div class="logout-section">
            <a href="../login/cerrar_sesion.php" class="logout">
                <i data-lucide="log-out"></i> Cerrar Sesión
            </a>
        </div>
    </aside>

    <main class="main-content">
        <section class="welcome-card">
            <h2>Panel de Control</h2>
            <p>Selecciona un módulo del menú lateral para comenzar a trabajar.</p>
        </section>

        <section class="cards">
            <div class="card" onclick="window.location.href='../usuarios/usuarios.php'">
                <i data-lucide="users"></i>
                <h3>Usuarios</h3>
                <p>Gestiona las cuentas de acceso y roles del sistema.</p>
            </div>
            <div class="card" onclick="window.location.href='../productos/productos.php'">
                <i data-lucide="package"></i>
                <h3>Productos</h3>
                <p>Administra el inventario de motos y repuestos.</p>
            </div>
            <div class="card" onclick="window.location.href='../stock/stock.php'">
                <i data-lucide="archive"></i>
                <h3>Stock</h3>
                <p>Controla entradas y salidas del almacén.</p>
            </div>
            <div class="card" onclick="window.location.href='../reportes/reportes.php'">
                <i data-lucide="bar-chart-2"></i>
                <h3>Reportes</h3>
                <p>Genera informes de ventas y movimientos.</p>
            </div>
        </section>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>