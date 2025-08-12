<?php
error_reporting(E_ALL); // Muestra todos los tipos de errores
ini_set('display_errors', 1); // Activa la visualización en pantalla
ini_set('display_startup_errors', 1); // Muestra errores al iniciar

require_once 'conexion.php';  // Aquí está tu $conn PDO
$usuarioId = isset($_SESSION["usuario"]["nUsuario"]) ? intval($_SESSION["usuario"]["nUsuario"]) : 6;
$usuarioNombre = isset($_SESSION["usuario"]["cNombres"]) ? $_SESSION["usuario"]["cNombres"] : null;
if ($usuarioId <= 0) {
    die("Usuario no autorizado.");
}
function limpiar($dato) {
    $dato = trim($dato);
    $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
    return $dato;
}

   echo "<script>console.log('PHP dice: " . "a" . "');</script>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ideditar = intval($_POST['id_familia_edit']);
    
    // Agregar Familia
    if (isset($_POST['nombre_familia']) && $ideditar <=0) {
        $nombre = limpiar($_POST['nombre_familia']);
        if ($nombre !== '') {
            $stmt = $conn->prepare("INSERT INTO familia (cFamilia, nUsuario) VALUES (?, ?)");
            $stmt->execute([$nombre, $usuarioId]);
            header("Location: vista_docente.php#panel-familias");
            exit;
        }
    }

    // Editar Familia
    if ($ideditar >0) {
        $id = intval($_POST['id_familia_edit']);
        $nombre = limpiar($_POST['nombre_familia']);
        if ($id > 0 && $nombre !== '') {
            //$stmt = $conn->prepare("UPDATE familia SET cFamilia=? WHERE nFamilia=? AND nUsuario=?");
            $stmt = $conn->prepare("UPDATE familia SET cFamilia=? WHERE nFamilia=?");
            //$stmt->execute([$nombre, $id, $usuarioId]);
            $stmt->execute([$nombre, $id]);
            header("Location: vista_docente.php#panel-familias");
            exit;
        }
    }

    // Eliminar Familia
    if (isset($_POST['eliminar_familia'])) {
        $id = intval($_POST['id_familia']);
        if ($id > 0) {
            // Verificar que no tenga géneros asociados
            $check = $conn->prepare("SELECT COUNT(*) FROM Genero WHERE nFamilia=?");
            $check->execute([$id]);
            $count = $check->fetchColumn();
            if ($count == 0) {
                $stmt = $conn->prepare("DELETE FROM familia WHERE nFamilia=? AND nUsuario=?");
                $stmt->execute([$id, $usuarioId]);
            }
            header("Location: vista_docente.php#panel-familias");
            exit;
        }
    }
    // Agregar Género
    if (isset($_POST['nombre_genero'], $_POST['id_familia']) && !isset($_POST['editar_genero'])) {
        $nombre = limpiar($_POST['nombre_genero']);
        $idFamilia = intval($_POST['id_familia']);
        if ($nombre !== '' && $idFamilia > 0) {
            $stmt = $conn->prepare("INSERT INTO genero (cGenero, nFamilia, nUsuario) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $idFamilia, $usuarioId]);
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Editar Género
    if (isset($_POST['editar_genero'], $_POST['id_genero_edit'])) {
        $id = intval($_POST['id_genero_edit']);
        $nombre = limpiar($_POST['editar_genero']);
        if ($id > 0 && $nombre !== '') {
            $stmt = $conn->prepare("UPDATE genero SET cGenero=? WHERE nGenero=? AND nUsuario=?");
            $stmt->execute([$nombre, $id, $usuarioId]);
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Eliminar Género
    if (isset($_POST['eliminar_genero'])) {
        $id = intval($_POST['eliminar_genero']);
        if ($id > 0) {
            // Verificar que no tenga especies asociadas
            $check = $conn->prepare("SELECT COUNT(*) FROM Especie WHERE nGenero=?");
            $check->execute([$id]);
            $count = $check->fetchColumn();
            if ($count == 0) {
                $stmt = $conn->prepare("DELETE FROM genero WHERE nGenero=? AND nUsuario=?");
                $stmt->execute([$id, $usuarioId]);
            }
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Agregar Especie
    if (isset($_POST['nombre_especie'], $_POST['id_genero']) && !isset($_POST['editar_especie'])) {
        $nombre = limpiar($_POST['nombre_especie']);
        $idGenero = intval($_POST['id_genero']);
        if ($nombre !== '' && $idGenero > 0) {
            $stmt = $conn->prepare("INSERT INTO especie (cEspecie, nGenero, nUsuario) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $idGenero, $usuarioId]);
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Editar Especie
    if (isset($_POST['editar_especie'], $_POST['id_especie_edit'])) {
        $id = intval($_POST['id_especie_edit']);
        $nombre = limpiar($_POST['editar_especie']);
        if ($id > 0 && $nombre !== '') {
            $stmt = $conn->prepare("UPDATE especie SET cEspecie=? WHERE nEspecie=? AND nUsuario=?");
            $stmt->execute([$nombre, $id, $usuarioId]);
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Eliminar Especie
    if (isset($_POST['eliminar_especie'])) {
        $id = intval($_POST['eliminar_especie']);
        if ($id > 0) {
            // Verificar que no tenga pruebas asociadas
            $check = $conn->prepare("SELECT COUNT(*) FROM prueba WHERE nEspecie=?");
            $check->execute([$id]);
            $count = $check->fetchColumn();
            if ($count == 0) {
                $stmt = $conn->prepare("DELETE FROM especie WHERE nEspecie=? AND nUsuario=?");
                $stmt->execute([$id, $usuarioId]);
            }
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Agregar Prueba
    if (isset($_POST['nombrePruebaInput'], $_POST['especieSelect']) && !isset($_POST['editar_prueba'])) {
        $nombrePrueba = limpiar($_POST['nombrePruebaInput']);
        $idEspecie = intval($_POST['especieSelect']);
        $descripcion = isset($_POST['descripcionInput']) ? limpiar($_POST['descripcionInput']) : '';
        $resultado = isset($_POST['resultadoInput']) ? limpiar($_POST['resultadoInput']) : '';
        $imagenNombre = null;
        if (isset($_FILES['imagenInput']) && $_FILES['imagenInput']['error'] === UPLOAD_ERR_OK) {
            $rutaUploads = __DIR__ . '/uploads/';
            if (!is_dir($rutaUploads)) mkdir($rutaUploads, 0777, true);
            $tmpName = $_FILES['imagenInput']['tmp_name'];
            $ext = pathinfo($_FILES['imagenInput']['name'], PATHINFO_EXTENSION);
            $imagenNombre = uniqid('prueba_') . '.' . $ext;
            move_uploaded_file($tmpName, $rutaUploads . $imagenNombre);
        }
        if ($nombrePrueba !== '' && $idEspecie > 0) {
            $stmt = $conn->prepare("INSERT INTO prueba (nEspecie, cFoto, cDescripcion, cResultado, cBacteria, nUsuario) VALUES (?, ?, ?, ?, '', ?)");
            $stmt->execute([$idEspecie, $imagenNombre, $descripcion, $resultado, $usuarioId]);
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Editar Prueba
    if (isset($_POST['editar_prueba'], $_POST['id_prueba_edit'])) {
        $id = intval($_POST['id_prueba_edit']);
        $nombrePrueba = limpiar($_POST['editar_prueba']);
        $descripcion = isset($_POST['descripcion_edit']) ? limpiar($_POST['descripcion_edit']) : '';
        $resultado = isset($_POST['resultado_edit']) ? limpiar($_POST['resultado_edit']) : '';
        $imagenNombre = null;
        if (isset($_FILES['imagen_edit']) && $_FILES['imagen_edit']['error'] === UPLOAD_ERR_OK) {
            $rutaUploads = __DIR__ . '/uploads/';
            if (!is_dir($rutaUploads)) mkdir($rutaUploads, 0777, true);
            $tmpName = $_FILES['imagen_edit']['tmp_name'];
            $ext = pathinfo($_FILES['imagen_edit']['name'], PATHINFO_EXTENSION);
            $imagenNombre = uniqid('prueba_') . '.' . $ext;
            move_uploaded_file($tmpName, $rutaUploads . $imagenNombre);
        }
        if ($id > 0 && $nombrePrueba !== '') {
            if ($imagenNombre) {
                $stmt = $conn->prepare("UPDATE prueba SET cDescripcion=?, cResultado=?, cFoto=?, cBacteria='', nUsuario=? WHERE nPrueba=?");
                $stmt->execute([$descripcion, $resultado, $imagenNombre, $usuarioId, $id]);
            } else {
                $stmt = $conn->prepare("UPDATE prueba SET cDescripcion=?, cResultado=?, cBacteria='', nUsuario=? WHERE nPrueba=?");
                $stmt->execute([$descripcion, $resultado, $usuarioId, $id]);
            }
            header("Location: vista_docente.php");
            exit;
        }
    }
    // Eliminar Prueba
    if (isset($_POST['eliminar_prueba'])) {
        $id = intval($_POST['eliminar_prueba']);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM prueba WHERE nPrueba=? AND nUsuario=?");
            $stmt->execute([$id, $usuarioId]);
            header("Location: vista_docente.php");
            exit;
        }
    }
}
// Consultas para mostrar datos en selects y tablas
$familias = [];
$stmt = $conn->prepare("SELECT nFamilia, cFamilia FROM familia WHERE nUsuario = ? ORDER BY cFamilia ASC");
$stmt->execute([$usuarioId]);
$familias = $stmt->fetchAll();
$generos = [];
$stmt = $conn->prepare("SELECT nGenero, cGenero, nFamilia FROM genero WHERE nUsuario = ? ORDER BY cGenero ASC");
$stmt->execute([$usuarioId]);
$generos = $stmt->fetchAll();
$especies = [];
$stmt = $conn->prepare("SELECT nEspecie, cEspecie, nGenero FROM especie WHERE nUsuario = ? ORDER BY cEspecie ASC");
$stmt->execute([$usuarioId]);
$especies = $stmt->fetchAll();
$pruebas = [];
$sql = "SELECT p.nPrueba, p.cFoto, p.cDescripcion, p.cResultado, p.cBacteria, 
        e.cEspecie, g.cGenero, f.cFamilia
        FROM Prueba p
        JOIN Especie e ON p.nEspecie = e.nEspecie
        JOIN Genero g ON e.nGenero = g.nGenero
        JOIN Familia f ON g.nFamilia = f.nFamilia
        WHERE p.nUsuario = ?
        ORDER BY p.dtFechaRegistro DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$usuarioId]);
$pruebas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión de Laboratorio</title>
    <link rel="stylesheet" href="css/docente.css">
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
                        <form id="form-familias" method="POST" action="vista_docente.php">
                            <div class="form-group">
                                <input type="hidden" id="id_familia_edit" name="id_familia_edit" value="0" >
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
                                    <?php
                                         
                                    $sql = 'SELECT * FROM familia';
                                    $stmt = $conn->prepare($sql);                                    
                                    $stmt->execute();
                                    $familiass = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                         foreach ($familiass as $prod){
                                           echo '<tr>';
                                           echo '<td>';
                                           echo $prod['nFamilia'];
                                           echo '</td>';                                
                                           echo '<td>';
                                           echo $prod['cFamilia'];
                                           echo '</td>'  ;                                                                           
                                          
                                           echo '<td class="table-actions">';
                                           echo '<a href="#" class="btn-action btn-edit-familia" data-id = '.  $prod['nFamilia'] .'  data-nombre = '. $prod['cFamilia'] .'>Editar</a>';
                                           echo '<a href="#" class="btn-action btn-delete" data-id = '.  $prod['nFamilia'] .'>Eliminar</a>';
                                           echo '</td>';
                                           echo '</tr>';
                                        }
                                       
                                    ?>                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="panel-generos" class="content-panel">
                    <h3>Gestión de Géneros</h3>
                    <div class="form-section">
                        <h4>Crear Nuevo Género</h4>
                        <form id="form-generos" method="POST" action="vista_docente.php">
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
                        <form id="form-especies" method="POST" action="vista_docente.php">
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
                        <form id="formularioProducto" >
                            <input type="hidden" id="productoIndex" value="-1">
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
    <script src="JS/docente.js"></script>
    <script src="JS/barradenavegacion.js"></script>
    <script src="JS/back_docente.js"></script>
</body>
</html>