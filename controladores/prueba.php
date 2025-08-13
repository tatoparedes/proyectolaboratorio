<?php
session_start();
header('Content-Type: application/json');
require_once "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Obtener usuario desde sesión o desde POST para pruebas
    $usuarioId = 0;
    if (isset($_SESSION["usuario"]["nUsuario"])) {
        $usuarioId = intval($_SESSION["usuario"]["nUsuario"]);
    } elseif (isset($_POST['idusuario'])) {
        $usuarioId = intval($_POST['idusuario']);
    }

    if (!$usuarioId) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no autorizado.']);
        exit;
    }

    if ($accion === 'listar') {
        try {
            $sql = "SELECT p.nPrueba, p.cFoto, p.cDescripcion, p.cResultado, p.cBacteria, 
                           e.nEspecie, e.cEspecie, g.nGenero, g.cGenero, f.nFamilia, f.cFamilia
                    FROM Prueba p
                    JOIN Especie e ON p.nEspecie = e.nEspecie
                    JOIN Genero g ON e.nGenero = g.nGenero
                    JOIN Familia f ON g.nFamilia = f.nFamilia
                    WHERE p.nUsuario = :usuarioId
                    ORDER BY p.nPrueba DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            $pruebas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'ok', 'data' => $pruebas]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al listar pruebas: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($accion === 'agregar') {
        // Recibir y validar datos
        $nEspecie     = intval($_POST['nEspecie'] ?? 0);
        $cDescripcion = trim($_POST['cDescripcion'] ?? '');
        $cResultado   = trim($_POST['cResultado'] ?? '');
        $cBacteria    = trim($_POST['cBacteria'] ?? '');

        if (!$nEspecie || $cDescripcion === '' || $cResultado === '' || $cBacteria === '') {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
            exit;
        }

        // Manejo de imagen (opcional)
        $cFoto = null;
        if (isset($_FILES['cFoto']) && $_FILES['cFoto']['error'] === UPLOAD_ERR_OK) {
            $directorio = __DIR__ . "/../uploads/";
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $extension = strtolower(pathinfo($_FILES['cFoto']['name'], PATHINFO_EXTENSION));
            $cFoto = uniqid("prueba_", true) . "." . $extension;
            $rutaDestino = $directorio . $cFoto;

            if (!move_uploaded_file($_FILES['cFoto']['tmp_name'], $rutaDestino)) {
                echo json_encode(['status' => 'error', 'message' => 'Error al subir la imagen.']);
                exit;
            }
        }

        try {
            $sql = "INSERT INTO Prueba (nEspecie, cFoto, cDescripcion, cResultado, cBacteria, nUsuario) 
                    VALUES (:nEspecie, :cFoto, :cDescripcion, :cResultado, :cBacteria, :nUsuario)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nEspecie', $nEspecie, PDO::PARAM_INT);
            $stmt->bindParam(':cFoto', $cFoto);
            $stmt->bindParam(':cDescripcion', $cDescripcion);
            $stmt->bindParam(':cResultado', $cResultado);
            $stmt->bindParam(':cBacteria', $cBacteria);
            $stmt->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'ok', 'message' => 'Prueba registrada correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al registrar la prueba.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la operación: ' . $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida.']);
    exit;

} else {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no válido.']);
    exit;
}
?>