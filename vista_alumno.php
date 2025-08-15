<?php
// PHP Authentication: Ensures only authenticated users with a specific role can access the page.
require_once 'conexion.php';
if (!isset($_SESSION["usuario"]["nUsuario"]) || !isset($_SESSION["usuario"]["nRol"])) {
    die("Acceso no autorizado: usuario no identificado.");
}

$usuarioId = intval($_SESSION["usuario"]["nUsuario"]);
$usuarioRol = intval($_SESSION["usuario"]["nRol"]);
if ($usuarioId <= 0) {
    die("Acceso no autorizado: ID de usuario inválido.");
}

if ($usuarioRol !== 2) {
    die("Acceso no autorizado: sólo usuarios con rol docente pueden acceder.");
}

$usuarioNombre = $_SESSION["usuario"]["cNombres"] ?? "Docente";
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
                            <img src="https://api.iconify.design/material-symbols/dashboard-outline.svg?color=currentColor" class="sidebar-icon" alt="Dashboard">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-familias" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/family-history.svg?color=currentColor" class="sidebar-icon" alt="Familias">
                            <span>Gestión de Familias</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-generos" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/category-outline.svg?color=currentColor" class="sidebar-icon" alt="Géneros">
                            <span>Gestión de Géneros</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-especies" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/pets.svg?color=currentColor" class="sidebar-icon" alt="Especies">
                            <span>Gestión de Especies</span>
                        </a>
                    </li>
                    <li>
                        <a href="#panel-pruebas" class="sidebar-btn">
                            <img src="https://api.iconify.design/material-symbols/microbiology-outline.svg?color=currentColor" class="sidebar-icon" alt="Pruebas">
                            <span>Gestión de Pruebas</span>
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

                <div id="panel-familias" class="content-panel">
                    <h3>Agregar Familia</h3>
                    <div class="form-section">
                        <h4>Formulario</h4>
                        <form id="form-familias">
                            <div class="form-group">
                                <input type="hidden" name="accion" value="agregar">
                                <label for="nombre_familia">Nombre de la Familia:</label>
                                <input type="text" id="nombre_familia" name="cFamilia" required>
                            </div>
                            <button type="submit" class="btn-submit">Guardar Familia</button>
                        </form>
                    </div>
                    <div class="table-section">
                        <h4>Lista de Familias</h4>
                        <div class="table-container">
                            <table id="table-familias">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="panel-generos" class="content-panel">
                    <h3>Agregar Género</h3>
                    <div class="form-section">
                        <h4>Formulario</h4>
                        <form id="form-generos">
                            <div class="form-group">
                                <label for="familia_select_genero">Seleccionar Familia:</label>
                                <select id="familia_select_genero" name="nFamilia" required>
                                    <option value="">-- Elige una familia --</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT nFamilia, cFamilia FROM familia");
                                    $stmt->execute();
                                    $familias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($familias as $fam) {
                                        echo '<option value="'.$fam['nFamilia'].'">'.$fam['cFamilia'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="accion" value="agregar">
                                <label for="nombre_genero">Nombre del Género:</label>
                                <input type="text" id="nombre_genero" name="cGenero" required>
                            </div>
                            <button type="submit" class="btn-submit">Guardar Género</button>
                        </form>
                    </div>
                    <div class="table-section">
                        <h4>Lista de Géneros</h4>
                        <div class="table-container">
                            <table id="table-generos">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Familia</th>
                                        <th>Género</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="panel-especies" class="content-panel">
                    <h3>Agregar Especie</h3>
                    <div class="form-section">
                        <h4>Formulario</h4>
                        <form id="form-especies">
                            <div class="form-group">
                                <label for="familia_select_especie">Seleccionar Familia:</label>
                                <select id="familia_select_especie" name="nFamilia" required>
                                    <option value="">-- Elige una familia --</option>
                                    <?php
                                    $sql = 'SELECT * FROM familia ORDER BY cFamilia ASC';
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $familias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($familias as $fam) {
                                        echo '<option value="'.$fam['nFamilia'].'">'.$fam['cFamilia'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="genero_select_especie">Seleccionar Género:</label>
                                <select id="genero_select_especie" name="nGenero" required disabled>
                                    <option value="">-- Elige un género --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="accion" value="agregar">
                                <label for="nombre_especie">Nombre de la Especie:</label>
                                <input type="text" id="nombre_especie" name="cEspecie" required>
                            </div>
                            <button type="submit" class="btn-submit">Guardar Especie</button>
                        </form>
                    </div>
                    <div class="table-section">
                        <h4>Lista de Especies</h4>
                        <div class="table-container">
                            <table id="table-especies">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Familia</th>
                                        <th>Género</th>
                                        <th>Especie</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="panel-pruebas" class="content-panel">
                    <h3>Gestión de Pruebas</h3>
                    
                    <div class="form-section">
                        <h4>Agregar Prueba</h4>
                        <form id="formularioProducto" enctype="multipart/form-data">
                            <input type="hidden" name="idusuario" value="6">
                            <input type="hidden" name="accion" id="accion" value="agregar">
                            
                            <div>
                                <label for="familiaSelect">Seleccionar Familia:</label>
                                <select id="familiaSelect" name="nFamilia" required>
                                    <option value="" disabled selected>-- Elige una familia --</option>
                                </select>
                            </div>
                            <div>
                                <label for="generoSelect">Seleccionar Género:</label>
                                <select id="generoSelect" name="nGenero" required disabled>
                                    <option value="" disabled selected>-- Elige un género --</option>
                                </select>
                            </div>

                            <div>
                                <label for="especieSelect">Seleccionar Especie:</label>
                                <select id="especieSelect" name="nEspecie" required disabled>
                                    <option value="" disabled selected>-- Elige una especie --</option>
                                </select>
                            </div>
                            <div>
                                <label for="nombrePruebaInput">Nombre de la Bacteria:</label>
                                <input type="text" id="nombrePruebaInput" name="cBacteria" required>
                            </div>
                            
                            <div style="grid-column: span 2;">
                                <label for="descripcionInput">Descripción:</label>
                                <textarea id="descripcionInput" name="cDescripcion" required maxlength="1000"></textarea>
                            </div>
                            <div style="grid-column: span 2;">
                                <label for="resultadoInput">Resultados:</label>
                                <textarea id="resultadoInput" name="cResultado" required maxlength="1000"></textarea>
                            </div>
                            <div style="grid-column: span 2;">
                                <label for="imagenInput">Imagen:</label>
                                <input type="file" id="imagenInput" name="cFoto" accept="image/*" required>
                                <img id="previewImagen" src="" style="display:none; width: 150px; margin-top: 10px;">
                            </div>
                            <div class="form-buttons">
                                <button type="submit" id="saveButton" class="btn btn-primary">Guardar Prueba</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-section">
                        <h4>Lista de Pruebas</h4>
                        <div class="lista-productos" id="contenedorProductos"></div>
                    </div>
                </div>

                <div id="panel-examenes" class="content-panel">
                    <h3>Acceder a Examen</h3>
                    <div class="form-section" style="max-width: 400px;">
                        <h4>Ingresa el código del examen</h4>
                        <form id="form-acceso-examen">
                            <div class="form-group">
                                <label for="codigoExamen">Código de Acceso:</label>
                                <input type="text" id="codigoExamen" name="codigoExamen" required placeholder="Ej: F4M1L1A23">
                            </div>
                            <button type="submit" class="btn-submit btn-primary">Acceder</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="JS/docente.js"></script>
    <script src="JS/barradenavegacion.js"></script>
    <script src="JS/back_docente.js"></script>
</body>
</html>