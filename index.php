<?php
session_start();
$usuarioNombre = isset($_SESSION["usuario"]["cNombres"]) ? $_SESSION["usuario"]["cNombres"] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio Clínico</title>
    <link rel="stylesheet" href="css/index.css">
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
                    <li class="nav-item"><a href="muestras.php" class="nav-link">Muestras</a></li>
                    <li class="nav-item"><a href="blog.php" class="nav-link active">Blog</a></li>
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

    <main class="hero">
        <div class="container hero-content">
            <h1>Laboratorio Clínico</h1>
            <p>Comprometidos con tu salud y bienestar</p>
        </div>
    </main>

    <script src="JS/barradenavegacion.js"></script>
</body>
</html>