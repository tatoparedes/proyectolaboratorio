<?php
$usuarioNombre = isset($_SESSION["usuario"]["cNombres"]) ? $_SESSION["usuario"]["cNombres"] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión de Laboratorio</title>
    <link rel="stylesheet" href="css/alumno.css">
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
                            <img src="https://api.iconify.design/material-symbols/dashboard-outline.svg?color=currentColor" class="sidebar-icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-familias" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/family-history.svg?color=currentColor" class="sidebar-icon">
                            <span>Gestión de Familias</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-generos" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/category-outline.svg?color=currentColor" class="sidebar-icon">
                            <span>Gestión de Géneros</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-especies" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/pets.svg?color=currentColor" class="sidebar-icon">
                            <span>Gestión de Especies</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-pruebas" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/microbiology-outline.svg?color=currentColor" class="sidebar-icon">
                            <span>Gestión de Pruebas</span>
                        </a>
                    </li>
                </ul>
            </aside>

            <section class="management-content">
                <div id="panel-dashboard" class="content-panel active">
                    <img src="imagenes/banner.png" alt="Imagen de Bienvenida al Panel de Gestión" style="width: 100%; border-radius: 12px; display: block;">
                </div>

                <div id="panel-familias" class="content-panel">
                    <h3>Gestión de Familias</h3>
                    <div class="form-section">
                        <h4>Agregar Nueva Familia</h4>
                        <form id="form-familias">
                            <div class="form-group">
                                <label for="nombre_familia">Nombre de la Familia:</label>
                                <input type="text" id="nombre_familia" name="nombre_familia" required>
                            </div>
                            <button type="submit" class="btn-submit">Agregar Familia</button>
                        </form>
                    </div>
                    <div class="table-section">
                        <div class="table-container">
                            <table id="table-familias">
                                <thead>
                                    <tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="panel-generos" class="content-panel">
                    <h3>Gestión de Géneros</h3>
                    <div class="form-section">
                        <h4>Crear Nuevo Género</h4>
                        <form id="form-generos">
                            <div class="form-group">
                                <label for="familia_select_genero">Seleccionar Familia:</label>
                                <select id="familia_select_genero" name="id_familia" required>
                                    <option value="">-- Elige una familia --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nombre_genero">Nombre del Género:</label>
                                <input type="text" id="nombre_genero" name="nombre_genero" required>
                            </div>
                            <button type="submit" class="btn-submit">Crear Género</button>
                        </form>
                    </div>
                    <div class="table-section">
                        <div class="table-container">
                            <table id="table-generos">
                                <thead>
                                    <tr><th>Género</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="panel-especies" class="content-panel">
                    <h3>Gestión de Especies</h3>
                    <div class="form-section">
                        <h4>Crear Nueva Especie</h4>
                        <form id="form-especies">
                            <div class="form-group">
                                <label for="familia_select_especie">Seleccionar Familia:</label>
                                <select id="familia_select_especie" name="id_familia" required>
                                    <option value="">-- Elige una familia --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="genero_select_especie">Seleccionar Género:</label>
                                <select id="genero_select_especie" name="id_genero" required disabled>
                                    <option value="">-- Elige un género --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nombre_especie">Nombre de la Especie:</label>
                                <input type="text" id="nombre_especie" name="nombre_especie" required>
                            </div>
                            <button type="submit" class="btn-submit">Crear Especie</button>
                        </form>
                    </div>
                    <div class="table-section">
                        <div class="table-container">
                            <table id="table-especies">
                                <thead>
                                    <tr><th>Especie</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="panel-pruebas" class="content-panel">
                    <section class="productos">
                        <div class="action-buttons">
                            <button id="mostrarFormularioBtn">Agregar Prueba</button>
                        </div>
                        <form id="formularioProducto">
                            <div>
                                <label for="familiaSelect">Familia:</label>
                                <select id="familiaSelect" required>
                                    <option value="" disabled selected>Selecciona una familia</option>
                                </select>
                            </div>
                            <div>
                                <label for="generoSelect">Género:</label>
                                <select id="generoSelect" required disabled>
                                    <option value="" disabled selected>Selecciona un género</option>
                                </select>
                            </div>
                            <div>
                                <label for="especieSelect">Especie:</label>
                                <select id="especieSelect" required disabled>
                                    <option value="" disabled selected>Selecciona una especie</option>
                                </select>
                            </div>
                            <div>
                                <label for="nombrePruebaInput">Nombre de la Prueba:</label>
                                <input type="text" id="nombrePruebaInput" required>
                            </div>
                            <div style="grid-column: span 2;">
                                <label for="descripcionInput">Descripción:</label>
                                <textarea id="descripcionInput" required maxlength="1000"></textarea>
                            </div>
                            <div style="grid-column: span 2;">
                                <label for="resultadoInput">Resultados:</label>
                                <textarea id="resultadoInput" required maxlength="1000"></textarea>
                            </div>
                            <div style="grid-column: span 2;">
                                <label for="imagenInput">Imagen:</label>
                                <input type="file" id="imagenInput" accept="image/*">
                            </div>
                            <button type="submit" id="saveButton">Guardar Prueba</button>
                        </form>
                        <div class="lista-productos" id="contenedorProductos"></div>
                    </section>
                </div>
            </section>
        </div>
    </main>
    <script src="JS/alumno.js"></script>
    <script src="JS/barradenavegacion.js"></script>
</body>
</html>