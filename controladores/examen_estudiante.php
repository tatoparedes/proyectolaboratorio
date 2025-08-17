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

        $sql = "SELECT e.nExamen, e.cExamen, e.cCodigoExamen, e.fechaRegistro,
                       COUNT(p.nPregunta) AS totalPreguntas
                FROM examen e
                LEFT JOIN pregunta p ON e.nExamen = p.nExamen
                WHERE e.cCodigoExamen = :codigo
                GROUP BY e.nExamen";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        $examen = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($examen) {
            // Verificar si el estudiante ya rindió el examen
            $usuarioId = $_SESSION["usuario"]["nUsuario"] ?? 0;
            $sqlCheck = "SELECT COUNT(*) FROM calificacion WHERE nExamen = :nExamen AND nUsuario = :nUsuario";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bindParam(':nExamen', $examen['nExamen'], PDO::PARAM_INT);
            $stmtCheck->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);
            $stmtCheck->execute();
            $yaRindo = $stmtCheck->fetchColumn();

            if ($yaRindo) {
                echo json_encode(['status' => 'error', 'message' => 'Ya completaste este examen. No puedes volver a ingresarlo.']);
                exit;
            }

            // Examen válido y aún no rendido
            echo json_encode(['status' => 'ok', 'data' => $examen]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Código inválido o examen no encontrado.']);
        }
        exit;
    }

    // ================= OBTENER PREGUNTAS DEL EXAMEN =================
    if ($accion === 'obtenerPreguntas') {
        $nExamen = intval($_POST['nExamen'] ?? 0);
        if (!$nExamen) {
            echo json_encode(['status' => 'error', 'message' => 'ID de examen inválido.']);
            exit;
        }

        $sql = "SELECT p.nPregunta, p.cPregunta, pr.nPrueba, pr.cDescripcion AS cDescripcionPrueba,
                       pr.cBacteria, pr.cFoto
                FROM pregunta p
                LEFT JOIN prueba pr ON p.nPrueba = pr.nPrueba
                WHERE p.nExamen = :nExamen";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
        $stmt->execute();
        $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($preguntas === false) $preguntas = []; // asegurar array vacío si no hay resultados
        echo json_encode(['status' => 'ok', 'data' => $preguntas]);
        exit;
    }

    // ================= GUARDAR RESPUESTAS DEL ESTUDIANTE =================
    if ($accion === 'guardarRespuestas') {
        $nExamen = intval($_POST['nExamen'] ?? 0);
        $respuestas = json_decode($_POST['respuestas'] ?? '[]', true);
        $usuarioId = $_SESSION["usuario"]["nUsuario"] ?? 0;

        if (!$nExamen || !$usuarioId || empty($respuestas)) {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
            exit;
        }

        // Insertar calificación en blanco
        $sqlCal = "INSERT INTO calificacion (cCalificacion, nExamen, nUsuario) 
                   VALUES (NULL, :nExamen, :nUsuario)";
        $stmtCal = $conn->prepare($sqlCal);
        $stmtCal->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
        $stmtCal->bindParam(':nUsuario', $usuarioId, PDO::PARAM_INT);
        $stmtCal->execute();
        $nCalificacion = $conn->lastInsertId();

        // Insertar respuestas
        $sqlResp = "INSERT INTO respuesta (nPregunta, cRespuesta, nCalificacion) 
                    VALUES (:nPregunta, :cRespuesta, :nCalificacion)";
        $stmtResp = $conn->prepare($sqlResp);

        foreach ($respuestas as $r) {
            $stmtResp->bindParam(':nPregunta', $r['nPregunta'], PDO::PARAM_INT);
            $stmtResp->bindParam(':cRespuesta', $r['cRespuesta']);
            $stmtResp->bindParam(':nCalificacion', $nCalificacion, PDO::PARAM_INT);
            $stmtResp->execute();
        }

        echo json_encode(['status' => 'ok', 'message' => 'Respuestas guardadas correctamente.']);
        exit;
    }

    // ================= ACCIÓN NO RECONOCIDA =================
    echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida en estudiante.']);
    exit;

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor: ' . $e->getMessage()]);
    exit;
}
?>