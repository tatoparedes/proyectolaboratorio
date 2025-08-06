<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

$dni = $_SESSION['usuario']['cDNI'];

try {

    $stmt = $conn->prepare("CALL sp_verificar_usuario(?)");
    $stmt->execute([$dni]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nombre = $usuario['cNombres'];
        $rol = $usuario['nRol'];

        echo "<h2>Hola, $nombre</h2>";
        echo "<a href='logout.php'>Cerrar sesión</a><br><br>";

        if ($rol == 1) {
            include 'vista_alumno.php';
        } elseif ($rol == 2) {
            include 'vista_docente.php';
        } else {
            echo "Rol no reconocido.";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location.href='login.php';</script>";
    }

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "<script>alert('Error al cargar datos del usuario: " . $e->getMessage() . "');</script>";
}
?>