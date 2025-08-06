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
                    <img src="https://images.unsplash.com/photo-1628122394639-688a221f190e?q=80&w=2670&auto=format&fit=crop" alt="Imagen de Análisis de Sangre" class="post-img">
                    <div class="post-content">
                        <h3>La Importancia de los Análisis de Sangre Anuales</h3>
                        <p>Descubre por qué un simple análisis de sangre puede ser la clave para la detección temprana de enfermedades y el mantenimiento de tu bienestar general...</p>
                        <a href="#" class="read-more">Leer más &rarr;</a>
                    </div>
                </article>
    
                <article class="blog-post">
                    <img src="https://images.unsplash.com/photo-1587352355403-1a84f33b1e32?q=80&w=2787&auto=format&fit=crop" alt="Imagen de Muestra de Orina" class="post-img">
                    <div class="post-content">
                        <h3>Guía Práctica: Cómo Prepararte para un Análisis de Orina</h3>
                        <p>Una correcta preparación es fundamental para obtener resultados precisos. Te explicamos paso a paso lo que necesitas saber antes de tu prueba de orina...</p>
                        <a href="#" class="read-more">Leer más &rarr;</a>
                    </div>
                </article>
    
                <article class="blog-post">
                    <img src="https://images.unsplash.com/photo-1624635848529-6577531776ce?q=80&w=2787&auto=format&fit=crop" alt="Imagen de ADN" class="post-img">
                    <div class="post-content">
                        <h3>El Futuro de la Medicina: Análisis Genéticos en Laboratorio</h3>
                        <p>Explora cómo la secuenciación de ADN y los estudios genéticos están revolucionando el diagnóstico, el tratamiento y la prevención de enfermedades...</p>
                        <a href="#" class="read-more">Leer más &rarr;</a>
                    </div>
                </article>
                
                <article class="blog-post">
                    <img src="https://images.unsplash.com/photo-1628122248554-1b7713401061?q=80&w=2670&auto=format&fit=crop" alt="Imagen de Microscopio" class="post-img">
                    <div class="post-content">
                        <h3>Descifrando los Resultados: Glóbulos Rojos, Blancos y Plaquetas</h3>
                        <p>Te ayudamos a entender qué significan esos valores en tu hemograma y cómo influyen en la salud de tu cuerpo...</p>
                        <a href="#" class="read-more">Leer más &rarr;</a>
                    </div>
                </article>

                <article class="blog-post">
                    <img src="https://images.unsplash.com/photo-1607613615462-f703e30f145f?q=80&w=2670&auto=format&fit=crop" alt="Imagen de glucómetro" class="post-img">
                    <div class="post-content">
                        <h3>Diabetes y el Control de Glucosa: Lo que Necesitas Saber</h3>
                        <p>Una guía completa sobre el control de la glucosa, la importancia de los análisis de rutina y los pasos para vivir una vida saludable con diabetes...</p>
                        <a href="#" class="read-more">Leer más &rarr;</a>
                    </div>
                </article>

                <article class="blog-post">
                    <img src="https://images.unsplash.com/photo-1598379471714-c104e1383842?q=80&w=2832&auto=format&fit=crop" alt="Imagen de hormonas" class="post-img">
                    <div class="post-content">
                        <h3>La Influencia de las Hormonas en tu Bienestar Diario</h3>
                        <p>Conoce la función de las hormonas en tu cuerpo y cómo los análisis hormonales pueden detectar desequilibrios que afectan tu energía y estado de ánimo...</p>
                        <a href="#" class="read-more">Leer más &rarr;</a>
                    </div>
                </article>
            </div>
        </div>
    </main>

    <script src="JS/barradenavegacion.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>