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
                        AND e.bEstado = 1
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
            // Generar código único
            do {
                $codigo = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM examen WHERE cCodigoExamen = ?");
                $stmtCheck->execute([$codigo]);
            } while ($stmtCheck->fetchColumn() > 0);

            // Insertar examen
            $sqlExamen = "INSERT INTO examen (cExamen, cCodigoExamen, nUsuario, bEstado) 
                          VALUES (:cExamen, :cCodigoExamen, :nUsuario, 1)";
            $stmtExamen = $conn->prepare($sqlExamen);
            $stmtExamen->bindParam(':cExamen', $cExamen);
            $stmtExamen->bindParam(':cCodigoExamen', $codigo);
            $stmtExamen->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);
            $stmtExamen->execute();
            $nExamen = $conn->lastInsertId();

            // Insertar preguntas
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
            $stmtExamen = $conn->prepare("UPDATE examen SET bEstado = 0 WHERE nExamen = :nExamen AND nUsuario = :usuarioId");
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

// BUSCAR RESULTADOS POR CÓDIGO
if ($accion === 'buscarResultados') {
    $codigo = trim($_POST['codigoExamen'] ?? '');
    if ($codigo === '') {
        echo json_encode(['status' => 'error', 'message' => 'Código inválido.']); exit;
    }

    try {
        // Buscar examen
        $sqlExamen = "SELECT nExamen, cExamen FROM examen WHERE cCodigoExamen = :codigo LIMIT 1";
        $stmt = $conn->prepare($sqlExamen);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        $examen = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$examen) {
            echo json_encode(['status' => 'error', 'message' => 'Examen no encontrado.']); exit;
        }

        // Traer estudiantes con calificación en este examen
        $sqlRes = "SELECT 
                    c.nCalificacion, 
                    c.cCalificacion, 
                    u.cNombres, 
                    u.cApePaterno, 
                    u.cApeMaterno
                FROM calificacion c
                INNER JOIN usuario u ON c.nUsuario = u.nUsuario
                WHERE c.nExamen = :nExamen
                ORDER BY u.cApePaterno ASC, u.cApeMaterno ASC, u.cNombres ASC";
        $stmtRes = $conn->prepare($sqlRes);
        $stmtRes->bindParam(':nExamen', $examen['nExamen'], PDO::PARAM_INT);
        $stmtRes->execute();
        $resultados = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'ok', 'examen' => $examen, 'resultados' => $resultados]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// VER RESPUESTAS
if ($accion === 'verRespuestas') {
    $nCalificacion = intval($_POST['nCalificacion'] ?? 0);
    if (!$nCalificacion) { 
        echo json_encode(['status' => 'error', 'message' => 'ID inválido.']); 
        exit; 
    }

    try {
        // Traer respuestas con ID y comentario
        $sql = "SELECT r.nRespuesta, p.cPregunta, r.cRespuesta, r.cComentario
                FROM respuesta r
                INNER JOIN pregunta p ON r.nPregunta = p.nPregunta
                WHERE r.nCalificacion = :nCalificacion
                ORDER BY r.nPregunta ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nCalificacion', $nCalificacion, PDO::PARAM_INT);
        $stmt->execute();
        $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'ok', 'respuestas' => $respuestas]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al obtener respuestas: ' . $e->getMessage()]);
    }
    exit;
}


// GUARDAR CALIFICACIÓN
if ($accion === 'guardarCalificacion') {
    $nCalificacion = intval($_POST['nCalificacion'] ?? 0);
    $nota = trim($_POST['calificacion'] ?? '');
    if (!$nCalificacion || $nota === '') { echo json_encode(['status' => 'error', 'message' => 'Datos inválidos.']); exit; }

    try {
        $sql = "UPDATE calificacion SET cCalificacion = :nota WHERE nCalificacion = :nCalificacion";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nota', $nota, PDO::PARAM_STR);
        $stmt->bindParam(':nCalificacion', $nCalificacion, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'ok', 'message' => 'Calificación guardada.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en operación: ' . $e->getMessage()]);
    }
    exit;
}
// GUARDAR COMENTARIOS
if ($accion === 'guardarComentarios') {
    $comentarios = json_decode($_POST['comentarios'] ?? '[]', true);

    if (empty($comentarios)) {
        echo json_encode(['status' => 'error', 'message' => 'No se recibieron comentarios.']);
        exit;
    }

    try {
        $sql = "UPDATE respuesta SET cComentario = :comentario WHERE nRespuesta = :nRespuesta";
        $stmt = $conn->prepare($sql);

        foreach ($comentarios as $c) {
            $stmt->bindParam(':comentario', $c['comentario'], PDO::PARAM_STR);
            $stmt->bindParam(':nRespuesta', $c['nRespuesta'], PDO::PARAM_INT);
            $stmt->execute();
        }

        echo json_encode(['status' => 'ok', 'message' => 'Comentarios guardados correctamente.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar comentarios: ' . $e->getMessage()]);
    }
    exit;
}

    // ================= OTRAS ACCIONES (listar, agregar, etc.) =================
    // (mantén tu código original aquí si lo necesitas, yo solo ajusté buscarResultados y verRespuestas)

    echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida.']);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no válido.']);
    exit;
}
?>