<?php
session_start();
header('Content-Type: application/json');
require_once "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Obtener usuario y rol desde sesión o POST para pruebas
    $usuarioId = 0;
    $usuarioRol = 0;
    if (isset($_SESSION["usuario"]["nUsuario"]) && isset($_SESSION["usuario"]["nRol"])) {
        $usuarioId = intval($_SESSION["usuario"]["nUsuario"]);
        $usuarioRol = intval($_SESSION["usuario"]["nRol"]);
    } elseif (isset($_POST['idusuario']) && isset($_POST['rol'])) {
        $usuarioId = intval($_POST['idusuario']);
        $usuarioRol = intval($_POST['rol']);
    }

    if (!$usuarioId || $usuarioRol !== 2) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no autorizado o rol incorrecto.']);
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

    // --- NUEVO: Editar prueba ---
    if ($accion === 'editar') {
        $nPrueba = intval($_POST['nPrueba'] ?? 0);
        $nEspecie = intval($_POST['nEspecie'] ?? 0);
        $cDescripcion = trim($_POST['cDescripcion'] ?? '');
        $cResultado = trim($_POST['cResultado'] ?? '');
        $cBacteria = trim($_POST['cBacteria'] ?? '');

        if (!$nPrueba || !$nEspecie || $cDescripcion === '' || $cResultado === '' || $cBacteria === '') {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios para editar.']);
            exit;
        }

        // Obtener foto actual para borrar si reemplaza
        $sqlSelect = "SELECT cFoto FROM Prueba WHERE nPrueba = :nPrueba AND nUsuario = :usuarioId";
        $stmtSelect = $conn->prepare($sqlSelect);
        $stmtSelect->bindParam(':nPrueba', $nPrueba, PDO::PARAM_INT);
        $stmtSelect->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtSelect->execute();
        $pruebaExistente = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if (!$pruebaExistente) {
            echo json_encode(['status' => 'error', 'message' => 'Prueba no encontrada o sin permisos.']);
            exit;
        }

        $cFoto = $pruebaExistente['cFoto'];

        if (isset($_FILES['cFoto']) && $_FILES['cFoto']['error'] === UPLOAD_ERR_OK) {
            $directorio = __DIR__ . "/../uploads/";
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $extension = strtolower(pathinfo($_FILES['cFoto']['name'], PATHINFO_EXTENSION));
            $nuevaFoto = uniqid("prueba_", true) . "." . $extension;
            $rutaDestino = $directorio . $nuevaFoto;

            if (!move_uploaded_file($_FILES['cFoto']['tmp_name'], $rutaDestino)) {
                echo json_encode(['status' => 'error', 'message' => 'Error al subir la nueva imagen.']);
                exit;
            }

            // Borra la imagen antigua
            if ($cFoto && file_exists($directorio . $cFoto)) {
                unlink($directorio . $cFoto);
            }

            $cFoto = $nuevaFoto;
        }

        try {
            $sqlUpdate = "UPDATE Prueba SET nEspecie = :nEspecie, cFoto = :cFoto, cDescripcion = :cDescripcion, cResultado = :cResultado, cBacteria = :cBacteria
                          WHERE nPrueba = :nPrueba AND nUsuario = :usuarioId";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':nEspecie', $nEspecie, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':cFoto', $cFoto);
            $stmtUpdate->bindParam(':cDescripcion', $cDescripcion);
            $stmtUpdate->bindParam(':cResultado', $cResultado);
            $stmtUpdate->bindParam(':cBacteria', $cBacteria);
            $stmtUpdate->bindParam(':nPrueba', $nPrueba, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);

            if ($stmtUpdate->execute()) {
                echo json_encode(['status' => 'ok', 'message' => 'Prueba actualizada correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la prueba.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la operación: ' . $e->getMessage()]);
        }
        exit;
    }

    // --- NUEVO: Eliminar prueba ---
    if ($accion === 'eliminar') {
        $nPrueba = intval($_POST['nPrueba'] ?? 0);
        if (!$nPrueba) {
            echo json_encode(['status' => 'error', 'message' => 'ID de prueba no válido.']);
            exit;
        }

        $sqlSelect = "SELECT cFoto FROM Prueba WHERE nPrueba = :nPrueba AND nUsuario = :usuarioId";
        $stmtSelect = $conn->prepare($sqlSelect);
        $stmtSelect->bindParam(':nPrueba', $nPrueba, PDO::PARAM_INT);
        $stmtSelect->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtSelect->execute();
        $prueba = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if (!$prueba) {
            echo json_encode(['status' => 'error', 'message' => 'Prueba no encontrada o sin permisos.']);
            exit;
        }

        try {
            $sqlDelete = "DELETE FROM Prueba WHERE nPrueba = :nPrueba AND nUsuario = :usuarioId";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bindParam(':nPrueba', $nPrueba, PDO::PARAM_INT);
            $stmtDelete->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);

            if ($stmtDelete->execute()) {
                $directorio = __DIR__ . "/../uploads/";
                if ($prueba['cFoto'] && file_exists($directorio . $prueba['cFoto'])) {
                    unlink($directorio . $prueba['cFoto']);
                }
                echo json_encode(['status' => 'ok', 'message' => 'Prueba eliminada correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la prueba.']);
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