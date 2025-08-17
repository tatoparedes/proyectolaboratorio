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

    // ================= LISTAR EXÁMENES =================
    if ($accion === 'listar') {
        try {
            $sql = "SELECT e.nExamen, e.cExamen, e.cCodigoExamen, e.fechaRegistro,
                           COUNT(p.nPregunta) AS totalPreguntas
                    FROM examen e
                    LEFT JOIN pregunta p ON e.nExamen = p.nExamen
                    WHERE e.nUsuario = :usuarioId
                    GROUP BY e.nExamen
                    ORDER BY e.nExamen DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            $examenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'ok', 'data' => $examenes]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al listar exámenes: ' . $e->getMessage()]);
        }
        exit;
    }

    // ================= LISTAR PRUEBAS POR ESPECIE =================
    if ($accion === 'listarPruebas') {
        $nEspecie = intval($_POST['nEspecie'] ?? 0);
        if (!$nEspecie) {
            echo json_encode(['status' => 'error', 'message' => 'Especie inválida.']);
            exit;
        }

        try {
            $sql = "SELECT nPrueba, cBacteria, cFoto
                    FROM prueba 
                    WHERE nEspecie = :nEspecie AND nUsuario = :usuarioId
                    ORDER BY nPrueba DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nEspecie', $nEspecie, PDO::PARAM_INT);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            $pruebas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'ok', 'data' => $pruebas]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al listar pruebas: ' . $e->getMessage()]);
        }
        exit;
    }

    // ================= AGREGAR EXAMEN =================
    if ($accion === 'agregar') {
        $cExamen   = trim($_POST['cExamen'] ?? '');
        $preguntas = isset($_POST['preguntas']) ? json_decode($_POST['preguntas'], true) : [];

        if ($cExamen === '' || empty($preguntas)) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
            exit;
        }

        try {
            // Generar código de 6 dígitos único
            do {
                $codigo = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM examen WHERE cCodigoExamen = ?");
                $stmtCheck->execute([$codigo]);
            } while ($stmtCheck->fetchColumn() > 0);

            // Insertar examen
            $sqlExamen = "INSERT INTO examen (cExamen, cCodigoExamen, nUsuario) 
                          VALUES (:cExamen, :cCodigoExamen, :nUsuario)";
            $stmtExamen = $conn->prepare($sqlExamen);
            $stmtExamen->bindParam(':cExamen', $cExamen);
            $stmtExamen->bindParam(':cCodigoExamen', $codigo);
            $stmtExamen->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);
            $stmtExamen->execute();
            $nExamen = $conn->lastInsertId();

            // Insertar preguntas asociadas
            $sqlPregunta = "INSERT INTO pregunta (cPregunta, nPrueba, nExamen) 
                            VALUES (:cPregunta, :nPrueba, :nExamen)";
            $stmtPregunta = $conn->prepare($sqlPregunta);

            foreach ($preguntas as $p) {
                $cPregunta = trim($p['descripcion'] ?? '');
                $nPrueba   = !empty($p['nPrueba']) ? intval($p['nPrueba']) : null;

                if ($cPregunta !== '') {
                    $stmtPregunta->bindParam(':cPregunta', $cPregunta);
                    if ($nPrueba !== null) {
                        $stmtPregunta->bindParam(':nPrueba', $nPrueba, PDO::PARAM_INT);
                    } else {
                        $stmtPregunta->bindValue(':nPrueba', null, PDO::PARAM_NULL);
                    }
                    $stmtPregunta->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
                    $stmtPregunta->execute();
                }
            }

            echo json_encode(['status' => 'ok', 'message' => 'Examen creado correctamente.', 'codigo' => $codigo]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar el examen: ' . $e->getMessage()]);
        }
        exit;
    }

    // ================= VER PREGUNTAS DE UN EXAMEN =================
    if ($accion === 'verPreguntas') {
        $nExamen = intval($_POST['nExamen'] ?? 0);
        if (!$nExamen) {
            echo json_encode(['status' => 'error', 'message' => 'ID de examen inválido.']);
            exit;
        }

        try {
            $sql = "SELECT p.nPregunta, p.cPregunta, pr.cDescripcion AS cDescripcionPrueba
                    FROM pregunta p
                    LEFT JOIN prueba pr ON p.nPrueba = pr.nPrueba
                    WHERE p.nExamen = :nExamen";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
            $stmt->execute();
            $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'ok', 'data' => $preguntas]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener preguntas: ' . $e->getMessage()]);
        }
        exit;
    }

    // ================= EDITAR EXAMEN =================
    if ($accion === 'editar') {
        $nExamen = intval($_POST['nExamen'] ?? 0);
        $cExamen = trim($_POST['cExamen'] ?? '');

        if (!$nExamen || $cExamen === '') {
            echo json_encode(['status' => 'error', 'message' => 'Datos inválidos para editar.']);
            exit;
        }

        try {
            $sqlUpdate = "UPDATE examen SET cExamen = :cExamen WHERE nExamen = :nExamen AND nUsuario = :usuarioId";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bindParam(':cExamen', $cExamen);
            $stmt->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'ok', 'message' => 'Examen actualizado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el examen.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la operación: ' . $e->getMessage()]);
        }
        exit;
    }

    // ================= ELIMINAR EXAMEN =================
    if ($accion === 'eliminar') {
        $nExamen = intval($_POST['nExamen'] ?? 0);
        if (!$nExamen) {
            echo json_encode(['status' => 'error', 'message' => 'ID de examen inválido.']);
            exit;
        }

        try {
            // Primero borrar preguntas asociadas
            $stmtPreg = $conn->prepare("DELETE FROM pregunta WHERE nExamen = :nExamen");
            $stmtPreg->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
            $stmtPreg->execute();

            // Luego borrar examen
            $stmtExamen = $conn->prepare("DELETE FROM examen WHERE nExamen = :nExamen AND nUsuario = :usuarioId");
            $stmtExamen->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
            $stmtExamen->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);

            if ($stmtExamen->execute()) {
                echo json_encode(['status' => 'ok', 'message' => 'Examen eliminado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el examen.']);
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