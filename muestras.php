<?php
session_start();
$usuarioNombre = isset($_SESSION["usuario"]["cNombres"]) ? $_SESSION["usuario"]["cNombres"] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Moderno de Laboratorio</title>
    <link rel="stylesheet" href="css/muestras.css">

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

    <br><br>

    <main class="container">
        <div class="mega-menu-container">
            <div class="menu-categories">
                <div class="menu-item">
                    <button class="category-trigger">
                        <span>Equipos de Diagnóstico</span>
                        <img src="https://api.iconify.design/ic/round-add.svg?color=%2300aaff" class="icon" alt="toggle icon">
                    </button>
                    <div class="submenu-content">
                        <ul>
                            <li><a href="verificar_rol.php">Ecógrafos</a></li>
                            <li><a href="#">Electrocardiógrafos</a></li>
                            <li><a href="#">Analizadores Clínicos</a></li>
                            <li><a href="#">Rayos X Digitales</a></li>
                        </ul>
                        <ul>
                            <li><a href="#">Monitores Multiparámetro</a></li>
                            <li><a href="#">Centrífugas</a></li>
                            <li><a href="#">Autoclaves</a></li>
                            <li><a href="#">Analizadores de Orina</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-item">
                    <button class="category-trigger">
                        <span>Instrumental Médico</span>
                        <img src="https://api.iconify.design/ic/round-add.svg?color=%2300aaff" class="icon" alt="toggle icon">
                    </button>
                    <div class="submenu-content">
                        <ul>
                            <li><a href="#">Microscopios Ópticos</a></li>
                            <li><a href="#">Microscopios Digitales</a></li>
                            <li><a href="#">Cámaras para Microscopía</a></li>
                            <li><a href="#">Tubos de Centrifugación</a></li>
                        </ul>
                        <ul>
                            <li><a href="#">Termocicladores (PCR)</a></li>
                            <li><a href="#">Cabinas de Bioseguridad</a></li>
                            <li><a href="#">Refrigeradores</a></li>
                            <li><a href="#">Ultracongeladores</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="menu-item">
                    <button class="category-trigger">
                        <span>Reactivos y Consumibles</span>
                        <img src="https://api.iconify.design/ic/round-add.svg?color=%2300aaff" class="icon" alt="toggle icon">
                    </button>
                    <div class="submenu-content">
                        <ul>
                            <li><a href="#">Reactivos Hematológicos</a></li>
                            <li><a href="#">Reactivos Químicos</a></li>
                            <li><a href="#">Controles y Calibradores</a></li>
                            <li><a href="#">Tiras Reactivas</a></li>
                        </ul>
                        <ul>
                            <li><a href="#">Medios de Cultivo</a></li>
                            <li><a href="#">Pipetas</a></li>
                            <li><a href="#">Puntas y Tubos</a></li>
                            <li><a href="#">Contenedores Bioseguridad</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // JavaScript Integrado
        document.addEventListener('DOMContentLoaded', () => {
            // Manejo del menú hamburguesa para dispositivos móviles
            const hamburger = document.getElementById('hamburger');
            const navMenu = document.getElementById('nav-menu');

            hamburger.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                hamburger.classList.toggle('active'); // Opcional: para animar el ícono de hamburguesa
            });

            // Manejo del acordeón del mega menú
            const categoryTriggers = document.querySelectorAll('.category-trigger');

            categoryTriggers.forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const menuItem = trigger.closest('.menu-item');
                    
                    // Cierra cualquier otro menú abierto
                    document.querySelectorAll('.menu-item.active').forEach(item => {
                        if (item !== menuItem) {
                            item.classList.remove('active');
                        }
                    });

                    // Alterna la clase 'active' en el elemento padre '.menu-item'
                    menuItem.classList.toggle('active');
                });
            });
        });
    </script>
</body>
</html>