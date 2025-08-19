<?php
session_start();
$usuarioNombre = isset($_SESSION["usuario"]["cNombres"]) ? $_SESSION["usuario"]["cNombres"] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | Laboratorio Clínico</title>
    <link rel="stylesheet" href="css/blog.css">
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

    <main class="blog-content">
        <div class="container">
            <div class="blog-header">
                <h1>Nuestro Blog de Salud</h1>
                <p>Descubre los últimos avances, consejos y guías sobre análisis clínicos para cuidar tu salud.</p>
            </div>
    
            <div class="blog-grid">
                <article class="blog-post">
                    <img src="imagenes/1.png" alt="Imagen de Análisis de Sangre" class="post-img">
                    <div class="post-content">
                        <h3>Guia sobre los metodos de identificación bioquimica</h3>
                        <p>Conoce los principales métodos bioquímicos utilizados para identificar bacterias de acuerdo a los resultados obtenidos en las pruebas mas comunes.</p>
                        <a href="powerpoint/IdentificacionBioquimica.pptx" class="read-more" download>Descargar Guía &rarr;</a>
                    </div>
                </article>
    
                <article class="blog-post">
                    <img src="imagenes/2.jpg" alt="Imagen de Muestra de Orina" class="post-img">
                    <div class="post-content">
                        <h3>Guía sobre las pruebas bioquimicas</h3>
                        <p>La identificación del género de enterobacterias se basa en un conjunto de pruebas bioquímicas, como las IMViC (Indol, Rojo de metilo, Voges-Proskauer y Citrato), entre otras, que permiten una caracterización precisa a nivel microbiológico.</p>
                        <a href="powerpoint/PruebasBioquimicas.pptx" class="read-more" download>Descargar Guía &rarr;</a>
                    </div>
                </article>
    
                <article class="blog-post">
                    <img src="imagenes/3.png" alt="Imagen de ADN" class="post-img">
                    <div class="post-content">
                        <h3>Guía sobre el métdo de lisina hierro agar</h3>
                        <p>Este medio de diagnostico diferencial detecta bacterias que fermentan la lactosa a través de la actividad de la lisina descarboxilasa</p>
                        <a href="powerpoint/LIA.pptx" class="read-more" download>Descargar Guía &rarr;</a>
                    </div>
                </article>
            </div>
        </div>
    </main>

    <script src="JS/barradenavegacion.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>