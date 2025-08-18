<?php
session_start();
header('Content-Type: application/json');
require_once "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Acceso no válido.']);
    exit;
}

$accion = $_POST['accion'] ?? '';

try {

    $usuarioId = $_SESSION["usuario"]["nUsuario"] ?? 0;
    if (!$usuarioId) {
        echo json_encode(['status'=>'error','message'=>'Usuario no autenticado.']);
        exit;
    }

    if ($accion === 'verResultados') {

        $codigo = trim($_POST['codigoExamen'] ?? '');
        if ($codigo === '') {
            echo json_encode(['status'=>'error','message'=>'Debe ingresar un código de examen.']);
            exit;
        }

        // Obtener examen por código
        $stmtExamen = $conn->prepare("SELECT nExamen, cExamen FROM examen WHERE cCodigoExamen = :codigo LIMIT 1");
        $stmtExamen->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmtExamen->execute();
        $examen = $stmtExamen->fetch(PDO::FETCH_ASSOC);

        if (!$examen) {
            echo json_encode(['status'=>'error','message'=>'Examen no encontrado.']);
            exit;
        }

        $nExamen = $examen['nExamen'];

        // Obtener la calificación del alumno
        $stmtCal = $conn->prepare("
            SELECT nCalificacion, cCalificacion 
            FROM calificacion 
            WHERE nUsuario = :usuarioId AND nExamen = :nExamen
            LIMIT 1
        ");
        $stmtCal->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtCal->bindParam(':nExamen', $nExamen, PDO::PARAM_INT);
        $stmtCal->execute();
        $calificacion = $stmtCal->fetch(PDO::FETCH_ASSOC);

        if (!$calificacion) {
            echo json_encode(['status'=>'error','message'=>'No hay calificación registrada para este examen.']);
            exit;
        }

        // Obtener respuestas con comentarios
        $stmtResp = $conn->prepare("
            SELECT r.nPregunta, r.cRespuesta, r.cComentario, p.cPregunta, pr.cFoto
            FROM respuesta r
            INNER JOIN pregunta p ON r.nPregunta = p.nPregunta
            LEFT JOIN prueba pr ON p.nPrueba = pr.nPrueba
            WHERE r.nCalificacion = :nCalificacion
            ORDER BY r.nPregunta ASC
        ");
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

    echo json_encode(['status'=>'error','message'=>'Acción no reconocida.']);

} catch (Exception $e) {
    echo json_encode(['status'=>'error','message'=>'Error del servidor: '.$e->getMessage()]);
    exit;
}
?>