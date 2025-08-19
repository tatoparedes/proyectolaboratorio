<?php
session_start();
$usuarioNombre = isset($_SESSION["usuario"]["cNombres"]) ? $_SESSION["usuario"]["cNombres"] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Laboratorio Clínico</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/contactanos.css">
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
                    <li class="nav-item"><a href="blog.php" class="nav-link">Blog</a></li>
                    <li class="nav-item"><a href="contactanos.php" class="nav-link active">Contáctanos</a></li>
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

<main>
    <div class="container">
        <h1 class="page-title">Contáctanos</h1>
        <p class="subtitle">¿Tienes dudas? Escríbenos o visítanos. Estamos para ayudarte.</p>

        <div class="contact-grid">
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Correo</h3>
                <p>informes@iestp-trujillo.edu.pe</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h3>Teléfono</h3>
                <p>Telf. (044) 350009</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Dirección</h3>
                <p>Psje. Olaya N° 180, Trujillo, Perú</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-clock"></i>
                <h3>Horario</h3>
                <p>Lunes a Viernes de 6:45 am a 08:35 pm</p>
            </div>
        </div>

        <div class="card redes-sociales">
            <p>Síguenos en redes sociales</p>
            <div class="iconos-redes">
                <a href="https://es-la.facebook.com/people/IESTP-Trujillo/100057443259181/" target="_blank" aria-label="Facebook" class="facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/iestp_trujillo/" target="_blank" aria-label="Instagram" class="instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.tiktok.com/@iestp.trujillo?_t=ZM-8x90GSjxfjs&_r=1" target="_blank" aria-label="TikTok" class="tiktok">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>

        <div class="mapa">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.943226161809!2d-79.02394502587397!3d-8.107263681106112!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91ad3d87a50f62b9%3A0xe9c7097097b45f8c!2sInstituto%20Superior%20Tecnol%C3%B3gico%20P%C3%BAblico%20Trujillo!5e0!3m2!1ses!2spe!4v1755562401637!5m2!1ses!2spe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</main>
<div class="whatsapp-float">
    <a href="https://wa.me/51942879129" target="_blank" title="Chatea por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>
<script src="JS/barradenavegacion.js"></script>
</body>
</html>