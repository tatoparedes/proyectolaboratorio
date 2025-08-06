<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once "conexion.php";

    $dni = $_POST["dni"];
    $nombres = $_POST["nombres"];
    $apellido_paterno = $_POST["apellido_paterno"];
    $apellido_materno = $_POST["apellido_materno"];
    $correo = $_POST["correo"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $rol = $_POST["rol"]; // este es el nRol que viene del formulario

    if (preg_match("/^[0-9]{8}$/", $dni)) {
        $sql = "INSERT INTO usuario (cDNI, cNombres, cApePaterno, cApeMaterno, cCorreo, cContrasena, nRol)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $dni, $nombres, $apellido_paterno, $apellido_materno, $correo, $password, $rol);

        if ($stmt->execute()) {
            echo "<script>alert('Usuario registrado correctamente'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error al registrar. El DNI o correo ya existe.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('El DNI debe tener exactamente 8 dígitos.');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Laboratorio Clínico - Registro</title>
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
        <h2>Regístrate</h2>
        <form method="POST" action="registro.php">
            <div class="input-group">
            <select name="rol" required>
                <option value="" disabled selected>Selecciona tu Rol</option>
                <option value="1">Alumno</option> <!-- 1 = nRol de Alumno -->
                <option value="2">Docente</option>
            </select>
                <i class="fas fa-caret-down"></i>
            </div>
            <div class="input-group">
                <input type="text" name="dni" placeholder="DNI (8 dígitos)" pattern="\d{8}" required>
                <i class="fas fa-id-card"></i>
            </div>
            <div class="input-group">
                <input type="text" name="nombres" placeholder="Nombres" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <input type="text" name="apellido_paterno" placeholder="Apellido Paterno" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <input type="text" name="apellido_materno" placeholder="Apellido Materno" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-group">
                <input type="email" name="correo" placeholder="Correo electrónico" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Contraseña" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="form-button">Crear Cuenta</button>

            <div class="link-text">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>