<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "../conexion.php";

$usuarioId = isset($_SESSION["usuario"]["nUsuario"]) ? intval($_SESSION["usuario"]["nUsuario"]) : 0;
$usuarioRol = isset($_SESSION["usuario"]["nRol"]) ? intval($_SESSION["usuario"]["nRol"]) : 0; // capturamos el rol

function limpiar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

$accion = $_POST['accion'] ?? '';

if (!$usuarioId || $usuarioId <= 0 || $usuarioRol !== 2) {
    echo json_encode(["status" => "error", "message" => "Usuario no autorizado o rol no permitido"]);
    exit;
}

try {
    switch ($accion) {
        case 'listar':
            // Listar géneros sólo del usuario
            $stmt = $conn->prepare("SELECT g.nGenero, g.cGenero, f.cFamilia, g.nFamilia 
                                    FROM genero g 
                                    JOIN familia f ON g.nFamilia = f.nFamilia 
                                    WHERE g.nUsuario = ? 
                                    ORDER BY g.cGenero ASC");
            $stmt->execute([$usuarioId]);
            $generos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "ok", "data" => $generos]);
            break;

        case 'listarPorFamilia':
            $nFamilia = intval($_POST['nFamilia'] ?? 0);

            if ($nFamilia <= 0) {
                echo json_encode(["status" => "error", "message" => "ID de familia inválido"]);
                exit;
            }

            // Listar géneros filtrados por familia, opcionalmente podrías filtrar por usuario si quieres
            $stmt = $conn->prepare("SELECT nGenero, cGenero FROM genero WHERE nFamilia = ?");
            $stmt->execute([$nFamilia]);
            $generos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["status" => "ok", "data" => $generos]);
            break;

        case 'agregar':
            $nombre = limpiar($_POST['cGenero'] ?? '');
            $familiaId = intval($_POST['nFamilia'] ?? 0);

            if ($nombre === '' || $familiaId <= 0) {
                echo json_encode(["status" => "error", "message" => "Datos incompletos para agregar"]);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO genero (cGenero, nFamilia, nUsuario) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $familiaId, $usuarioId]);
            echo json_encode(["status" => "ok", "message" => "Género agregado correctamente"]);
            break;

        case 'editar':
            $id = intval($_POST['nGenero'] ?? 0);
            $nombre = limpiar($_POST['cGenero'] ?? '');
            $familiaId = intval($_POST['nFamilia'] ?? 0);

            if ($id <= 0 || $nombre === '' || $familiaId <= 0) {
                echo json_encode(["status" => "error", "message" => "Datos inválidos para editar"]);
                exit;
            }

            $stmt = $conn->prepare("UPDATE genero SET cGenero = ?, nFamilia = ? WHERE nGenero = ? AND nUsuario = ?");
            $stmt->execute([$nombre, $familiaId, $id, $usuarioId]);
            echo json_encode(["status" => "ok", "message" => "Género actualizado correctamente"]);
            break;

        case 'eliminar':
            $id = intval($_POST['nGenero'] ?? 0);

            if ($id <= 0) {
                echo json_encode(["status" => "error", "message" => "ID inválido para eliminar"]);
                exit;
            }

            // Verificar si existen especies asociadas a ese género para evitar borrado
            $check = $conn->prepare("SELECT COUNT(*) FROM especie WHERE nGenero = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                echo json_encode(["status" => "error", "message" => "No se puede eliminar, existen especies asociadas"]);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM genero WHERE nGenero = ? AND nUsuario = ?");
            $stmt->execute([$id, $usuarioId]);
            echo json_encode(["status" => "ok", "message" => "Género eliminado correctamente"]);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Acción no válida"]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en la operación: " . $e->getMessage()]);
}
?>