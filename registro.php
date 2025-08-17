<?php
// Lógica para manejar el formulario POST (registro de usuario)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once "conexion.php";

    $dni = $_POST["dni"];
    $nombres = $_POST["nombres"];
    $apellido_paterno = $_POST["apellido_paterno"];
    $apellido_materno = $_POST["apellido_materno"];
    $correo = $_POST["correo"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $rol = 1;

    if (preg_match("/^[0-9]{8}$/", $dni)) {
        try {
            $stmt = $conn->prepare("CALL sp_registrar_usuario(?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$dni, $nombres, $apellido_paterno, $apellido_materno, $correo, $password, $rol]);

            echo "<script>alert('Usuario registrado correctamente'); window.location.href='login.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error al registrar: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('El DNI debe tener exactamente 8 dígitos.');</script>";
    }
    // Finaliza la ejecución del script si se procesó un POST
    exit; 
}

// Lógica para manejar la solicitud de la API de RENIEC
if (isset($_GET['dni'])) {
    header('Content-Type: application/json');

    $token = 'apis-token-17367.emx6cDVIZHq6KVbm7wb4Kl5uSKqvupIl';
    $dni = $_GET['dni'];

    // Validar el DNI antes de la consulta
    if (!preg_match('/^\d{8}$/', $dni)) {
        die(json_encode(['error' => 'El DNI no es válido.']));
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 1,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Referer: https://apis.net.pe/consulta-dni-api',
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if (curl_errno($curl)) {
        die(json_encode(['error' => 'Error en la conexión: ' . curl_error($curl)]));
    }
    if ($httpCode !== 200) {
        die(json_encode(['error' => 'Error al consultar la API, código: ' . $httpCode]));
    }

    echo $response;
    exit; // Finaliza la ejecución después de enviar la respuesta JSON
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<script src="JS/registrarse.js"></script>
</body>
</html>