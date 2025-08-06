<?php
session_start();
$usuarioNombre = isset($_SESSION["usuario"]["cNombres"]) ? $_SESSION["usuario"]["cNombres"] : null;
?>
<?php

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Extraer los datos del usuario
$usuario = $_SESSION['usuario'];
$nombre = $usuario['cNombres'];
$rol = $usuario['nRol'];

// Mostrar encabezado de bienvenida
echo "<h2>Hola, $nombre</h2>";
echo "<a href='logout.php'>Cerrar sesión</a><br><br>";

// Mostrar la vista según el rol
if ($rol == 1) {
    include 'vista_alumno.php';
} elseif ($rol == 2) {
    include 'vista_docente.php';
} else {
    echo "Rol no reconocido.";
}
?>
