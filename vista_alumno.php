<?php
require_once 'conexion.php';

if (!isset($_SESSION["usuario"]["nUsuario"]) || !isset($_SESSION["usuario"]["nRol"])) {
    die("Acceso no autorizado: usuario no identificado.");
}

$usuarioId = intval($_SESSION["usuario"]["nUsuario"]);
$usuarioRol = intval($_SESSION["usuario"]["nRol"]);

if ($usuarioId <= 0) {
    die("Acceso no autorizado: ID de usuario inválido.");
}

if ($usuarioRol !== 1) {
    die("Acceso no autorizado: sólo usuarios con rol estudiante pueden acceder.");
}

$usuarioNombre = $_SESSION["usuario"]["cNombres"] ?? "Estudiante";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión de Laboratorio</title>
    <link rel="stylesheet" href="css/vista_examen.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <img src="imagenes/logo.jpg" alt="Logo Laboratorio">
            </div>

            <nav class="nav-menu" id="nav-menu">
                <ul class="nav-list">
                    <li class="nav-item"><a href="index.php" class="nav-link">Inicio</a></li>
                    <li class="nav-item"><a href="muestras.php" class="nav-link active">Muestras</a></li>
                    <li class="nav-item"><a href="blog.php" class="nav-link">Blog</a></li>
                    <li class="nav-item"><a href="contactanos.php" class="nav-link">Contáctanos</a></li>
                </ul>
                <?php if ($usuarioNombre): ?>
                    <span class="bienvenida">Bienvenido, <?php echo htmlspecialchars($usuarioNombre); ?></span>
                    <a href="logout.php" class="btn-login">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>

            <div class="hamburger" id="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="management-wrapper">
            <aside class="management-sidebar">
                <ul class="sidebar-nav">
                    <li>
                        <a href="#panel-dashboard" class="sidebar-btn active">
                            <img src="https://api.iconify.design/material-symbols/dashboard-outline.svg?color=currentColor" class="sidebar-icon" alt="Dashboard">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-examenes" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/quiz-outline.svg?color=currentColor" class="sidebar-icon" alt="Exámenes">
                            <span>Exámenes</span>
                        </a>
                    </li>
                </ul>
            </aside>

            <section class="management-content">
                <div id="panel-dashboard" class="content-panel active">
                    <img src="imagenes/banner.png" alt="Imagen de Bienvenida al Panel de Gestión" style="width: 100%; border-radius: 12px; display: block;">
                </div>
                
                <div id="panel-examenes" class="content-panel">
                    <h3>Acceder a Examen</h3>
                    <div class="centered-form-container">
                        <form id="form-acceso-examen">
                            <div class="form-group">
                                <label for="codigoExamen">Ingresa el código del examen:</label>
                                <input type="text" id="codigoExamen" name="codigoExamen" required placeholder="Ej: F4M1L1A23">
                            </div>
                            <button type="submit" class="btn-primary">Acceder</button>
                        </form>
                    </div>
                </div>

                <div id="panel-examen-activo" class="content-panel">
                    <div class="card">
                        <div class="card-header card-title-center">
                            <h3 id="examen-titulo">Examen</h3>
                        </div>

                        <div id="contenedor-preguntas"></div>

                        <div class="examen-actions">
                            <button type="submit" id="btn-enviar-examen" class="btn-primary">Enviar Examen</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="JS/barradenavegacion.js"></script>
    <script src="JS/back_estudiante.js"></script>
</body>
</html>