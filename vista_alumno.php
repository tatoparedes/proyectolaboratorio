<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include 'conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Catálogo de Laboratorio Clínico</title>
  <link rel="stylesheet" href="css/subcategoria2.css">
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

  <main>
    <aside class="filtros">
      <h2>Agregar Nueva Bacteria</h2>
      <form id="formAgregarEspecie">
        <label for="categoria">Tipos de Familia:</label>
        <select id="categoria">
          <option value="Nueva">+ Nueva Familia</option>
        </select>
        <div id="nuevaCategoriaDiv" style="display: none;">
          <label for="nuevaCategoria">Nueva Familia:</label>
          <input type="text" id="nuevaCategoria" placeholder="Nueva Familia">
        </div>
        <label for="tipoMuestra">Nuevo Genero:</label>
        <input type="text" id="tipoMuestra" placeholder="Genero">
        <label for="especie">Nueva Especie:</label>
        <input type="text" id="especie" placeholder="Ej. Virus, Bacterias, Glóbulos">
        <button type="submit" class="btn-especie">Agregar Bateria</button>
        <p id="mensajeExito">¡Tipo de especie agregado correctamente!</p>
      </form>
    </aside>

    <section class="productos">
      <button id="mostrarFormularioBtn">Agregar bateria</button>

      <form id="formularioProducto" style="display:none;">
        <div>
          <label>Genero:</label>
          <select id="nuevageneroInput" required>
            <option value="" disabled selected>Selecciona un Genero</option>
          </select>
        </div>
        <div>
          <label>Tipo de Especie:</label>
          <select id="nuevaEspecie" required>
            <option value="" disabled selected>Selecciona un tipo</option>
          </select>
        </div>
        <div style="grid-column: span 2;">
          <label>Descripción:</label>
          <textarea id="nuevaDescripcion" required maxlength="1000"></textarea>
        </div>
        <div style="grid-column: span 2;">
          <label>Resultados:</label>
          <textarea id="nuevoResultado" required maxlength="1000"></textarea>
        </div>
        <div style="grid-column: span 2;">
          <label>Imagen:</label>
          <input type="file" id="nuevaImagen" accept="image/*">
        </div>
        <button type="submit">Guardar Especie</button>
      </form>

      <div class="lista-productos" id="contenedorProductos"></div>
    </section>
  </main>
<script src="JS/subcategoria2.js"></script>
</body>
</html>