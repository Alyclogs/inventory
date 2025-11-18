<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | Sistema de Inventario</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>

<body class="login-body">
    <div class="login-box">
        <img src="../../assets/img/logo.png" alt="Logo" class="logo">
        <h2>ANDRES ESPINOZA MOTOS</h2>
        <p class="sub">Sistema de Inventario</p>

        <form id="loginForm">
            <input type="text" id="usuario" placeholder="Usuario" required>
            <input type="password" id="clave" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>

        <footer>
            <p>© 2025 Andres Espinoza Motos E.I.R.L.</p>
        </footer>
    </div>

    <script src="../../assets/js/login.js?v=<?php echo time(); ?>"></script>
</body>

</html>