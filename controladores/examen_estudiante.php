<?php
session_start();
header('Content-Type: application/json');
require_once "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no válido.']);
    exit;
}

$accion = $_POST['accion'] ?? '';

try {

    // ================= VALIDAR CÓDIGO DEL EXAMEN =================
    if ($accion === 'verificarCodigo') {
        $codigo = trim($_POST['codigoExamen'] ?? '');
        if ($codigo === '') {
            echo json_encode(['status' => 'error', 'message' => 'Debe ingresar un código.']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT e.nExamen, e.cExamen, e.cCodigoExamen, COUNT(p.nPregunta) AS totalPreguntas
            FROM examen e
            LEFT JOIN pregunta p ON e.nExamen = p.nExamen
            WHERE e.cCodigoExamen = :codigo
            GROUP BY e.nExamen
        ");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        $examen = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$examen) {
            echo json_encode(['status' => 'error', 'message' => 'Código inválido o examen no encontrado.']);
            exit;
        }

        $usuarioId = $_SESSION["usuario"]["nUsuario"] ?? 0;
        if (!$usuarioId) {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado.']);
            exit;
        }

        // Revisar si ya rindió el examen
        $stmtCheck = $conn->prepare("
            SELECT COUNT(*) FROM calificacion 
            WHERE nExamen = :nExamen AND nUsuario = :nUsuario
        ");
        $stmtCheck->bindParam(':nExamen', $examen['nExamen'], PDO::PARAM_INT);
        $stmtCheck->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);
        $stmtCheck->execute();
        if ($stmtCheck->fetchColumn() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Ya completaste este examen.']);
            exit;
        }

        echo json_encode(['status' => 'ok', 'data' => $examen]);
        exit;
    }

    // ================= OBTENER PREGUNTAS =================
    if ($accion === 'obtenerPreguntas') {
        $nExamen = intval($_POST['nExamen'] ?? 0);
        if (!$nExamen) {
            echo json_encode(['status' => 'error', 'message' => 'ID de examen inválido.']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT p.nPregunta, p.cPregunta, pr.cFoto
            FROM pregunta p
            LEFT JOIN prueba pr ON p.nPrueba = pr.nPrueba
            WHERE p.nExamen = :nExamen
            ORDER BY p.nPregunta ASC
        ");
        $stmt->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
        $stmt->execute();
        $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        echo json_encode(['status' => 'ok', 'data' => $preguntas]);
        exit;
    }

    // ================= GUARDAR RESPUESTAS =================
    if ($accion === 'guardarRespuestas') {
        $nExamen = intval($_POST['nExamen'] ?? 0);
        $respuestas = json_decode($_POST['respuestas'] ?? '[]', true);
        $usuarioId = $_SESSION["usuario"]["nUsuario"] ?? 0;

        if (!$nExamen || !$usuarioId || empty($respuestas)) {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
            exit;
        }

        // Insertar calificación pendiente
        $stmtCal = $conn->prepare("
            INSERT INTO calificacion (cCalificacion, nExamen, nUsuario)
            VALUES (NULL, :nExamen, :nUsuario)
        ");
        $stmtCal->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
        $stmtCal->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);
        $stmtCal->execute();
        $nCalificacion = $conn->lastInsertId();

        $stmtResp = $conn->prepare("
            INSERT INTO respuesta (nPregunta, cRespuesta, nCalificacion)
            VALUES (:nPregunta, :cRespuesta, :nCalificacion)
        ");
        foreach ($respuestas as $r) {
            $stmtResp->bindParam(':nPregunta', $r['nPregunta'], PDO::PARAM_INT);
            $stmtResp->bindParam(':cRespuesta', $r['cRespuesta']);
            $stmtResp->bindParam(':nCalificacion', $nCalificacion, PDO::PARAM_INT);
            $stmtResp->execute();
        }

        echo json_encode(['status' => 'ok', 'message' => 'Respuestas guardadas correctamente.']);
        exit;
    }

    // ================= VER RESULTADOS DEL ESTUDIANTE =================
    if ($accion === 'verResultadosEstudiante') {
        $codigo = trim($_POST['codigoExamen'] ?? '');
        if ($codigo === '') {
            echo json_encode(['status' => 'error', 'message' => 'Debe ingresar un código de examen.']);
            exit;
        }

        $usuarioId = $_SESSION["usuario"]["nUsuario"] ?? 0;
        if (!$usuarioId) {
            echo json_encode(['status'=>'error','message'=>'Usuario no autenticado.']);
            exit;
        }

        // Obtener calificación
        $sqlNota = "SELECT c.cCalificacion, c.nCalificacion, e.nExamen
                    FROM calificacion c
                    INNER JOIN examen e ON c.nExamen = e.nExamen
                    WHERE e.cCodigoExamen = :codigo AND c.nUsuario = :nUsuario
                    ORDER BY c.nCalificacion DESC LIMIT 1";
        $stmtNota = $conn->prepare($sqlNota);
        $stmtNota->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmtNota->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);
        $stmtNota->execute();
        $calificacion = $stmtNota->fetch(PDO::FETCH_ASSOC);

        if (!$calificacion) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron resultados para este examen.']);
            exit;
        }

        // Obtener respuestas del examen
        $sqlResp = "SELECT r.nPregunta, r.cRespuesta, r.cComentario, p.cPregunta, pr.cFoto
                    FROM respuesta r
                    INNER JOIN pregunta p ON r.nPregunta = p.nPregunta
                    LEFT JOIN prueba pr ON p.nPrueba = pr.nPrueba
                    WHERE r.nCalificacion = :nCalificacion
                    ORDER BY r.nPregunta ASC";
        $stmtResp = $conn->prepare($sqlResp);
        $stmtResp->bindParam(':nCalificacion', $calificacion['nCalificacion'], PDO::PARAM_INT);
        $stmtResp->execute();
        $respuestas = $stmtResp->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'ok',
            'nota' => $calificacion['cCalificacion'] ?? 'Pendiente',
            'respuestas' => $respuestas
        ]);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida.']);
    exit;

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor: ' . $e->getMessage()]);
    exit;
}
?>