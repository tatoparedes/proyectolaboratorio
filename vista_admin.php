<?php
session_start();
require_once 'conexion.php';

// Validar sesión
if (!isset($_SESSION["usuario"]["nUsuario"], $_SESSION["usuario"]["nRol"])) {
    header("Location: login.php");
    exit();
}

$usuarioId = intval($_SESSION["usuario"]["nUsuario"]);
$usuarioRol = intval($_SESSION["usuario"]["nRol"]);

if ($usuarioId <= 0) {
    die("Acceso no autorizado: ID de usuario inválido.");
}

// Solo permitir rol 3 (Admin)
if ($usuarioRol !== 3) {
    die("Acceso no autorizado: solo usuarios con rol Admin pueden acceder.");
}

$usuarioNombre = $_SESSION["usuario"]["cNombres"] ?? "Admin";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <img src="imagenes/logo.jpg" alt="Logo Laboratorio">
            </div>

            <nav class="nav-menu" id="nav-menu">
                <span class="bienvenida">Bienvenido, <?php echo htmlspecialchars($usuarioNombre); ?></span>
                <a href="logout.php" class="btn-login">Cerrar Sesión</a>
            </nav>

            <div class="hamburger" id="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </header>

    <section id="user-management" class="user-management-section">
        <div class="container">
            <h2>Administración de Usuarios</h2>
            <div class="table-responsive">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>DNI</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Nombres</th>
                            <th>Correo Electrónico</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
<script src="JS/back_admin.js"></script>
</html>