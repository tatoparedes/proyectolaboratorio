<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once "conexion.php";

    $dni = $_POST["dni"];
    $password = $_POST["password"];

    try {
        // Llamar al procedimiento almacenado
        $stmt = $conn->prepare("CALL sp_login_usuario(?)");
        $stmt->execute([$dni]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar contraseña
            if (password_verify($password, $usuario["cContrasena"])) {
                $_SESSION["usuario"] = [
                    "nUsuario" => $usuario["nUsuario"],
                    "cNombres" => $usuario["cNombres"],
                    "nRol"     => $usuario["nRol"],
                    "cDNI"     => $usuario["cDocumento"],
                    "cCorreo"  => $usuario["cCorreo"],
                    "cUsuario" => $usuario["cUsuario"] ?? ''
                ];

                echo "<script>alert('Inicio de sesión exitoso'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Contraseña incorrecta'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('DNI no encontrado'); window.location.href='login.php';</script>";
        }

        $stmt->closeCursor(); // Necesario para liberar el procedimiento
    } catch (PDOException $e) {
        echo "<script>alert('Error en el login: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Laboratorio Clínico - Acceso</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/registrarse.css">
</head>
<body>
<main class="main-container">
    <div class="welcome-section">
        <div class="logo">
            <i class="fas fa-book-open"></i>
            <span>Laboratorio Clinico</span>
        </div>
        <h1>¡Bienvenido!</h1>
        <p>Tu camino hacia el conocimiento comienza aquí. Explora un mundo de aprendizaje con nosotros.</p>
        <p class="small-text">Únete a miles de estudiantes y educadores en nuestra comunidad y comienza tu primera lección hoy.</p>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-google"></i></a>
            <a href="#"><i class="fab fa-telegram-plane"></i></a>
        </div>
    </div>

    <div class="form-section">
        <h2>Acceso</h2>
        <form method="POST" action="login.php">
            <div class="input-group">
                <input type="text" name="dni" placeholder="DNI" pattern="\d{8}" required>
                <i class="fas fa-id-card"></i>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Contraseña" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="options">
                <div class="remember-me">
                    <input type="checkbox" id="remember">
                    <label for="remember">Recuérdame</label>
                </div>
                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="form-button">Iniciar Sesión</button>

            <div class="link-text">
                ¿No tienes una cuenta? <a href="registro.php">Regístrate</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>