<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "../conexion.php";

$usuarioId = isset($_SESSION["usuario"]["nUsuario"]) ? intval($_SESSION["usuario"]["nUsuario"]) : 0;
$usuarioRol = isset($_SESSION["usuario"]["nRol"]) ? intval($_SESSION["usuario"]["nRol"]) : 0;

function limpiar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

$accion = $_POST['accion'] ?? '';

// Validar usuario y rol docente (rol = 2)
if (!$usuarioId || $usuarioId <= 0 || $usuarioRol !== 2) {
    echo json_encode(["status" => "error", "message" => "Usuario no autorizado o rol no permitido"]);
    exit;
}

try {
    switch ($accion) {
        case 'listar':
            // Listar todas las especies con familia y género
            $stmt = $conn->prepare("SELECT e.nEspecie, f.cFamilia, g.cGenero, e.cEspecie, g.nFamilia FROM especie e JOIN genero g ON e.nGenero = g.nGenero JOIN familia f ON g.nFamilia = f.nFamilia ORDER BY e.nEspecie ASC");
            $stmt->execute();
            $especies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "ok", "data" => $especies]);
            break;

        case 'listarGenerosPorFamilia':
            // Listar géneros solo de la familia indicada
            $nFamilia = intval($_POST['nFamilia'] ?? 0);
            if ($nFamilia <= 0) {
                echo json_encode(["status" => "error", "message" => "ID de familia inválido"]);
                exit;
            }
            $stmt = $conn->prepare("SELECT nGenero, cGenero FROM genero WHERE nFamilia = ?");
            $stmt->execute([$nFamilia]);
            $generos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "ok", "data" => $generos]);
            break;

        case 'listarPorGenero':
            // Listar especies filtradas por género
            $nGenero = intval($_POST['nGenero'] ?? 0);
            if ($nGenero <= 0) {
                echo json_encode(["status" => "error", "message" => "ID de género inválido"]);
                exit;
            }
            $stmt = $conn->prepare("SELECT nEspecie, cEspecie FROM especie WHERE nGenero = ?");
            $stmt->execute([$nGenero]);
            $especies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "ok", "data" => $especies]);
            break;

        case 'agregar':
            $nombre = limpiar($_POST['cEspecie'] ?? '');
            $generoId = intval($_POST['nGenero'] ?? 0);

            if ($nombre === '' || $generoId <= 0) {
                echo json_encode(["status" => "error", "message" => "Datos incompletos para agregar"]);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO especie (cEspecie, nGenero, nUsuario) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $generoId, $usuarioId]);

            echo json_encode(["status" => "ok", "message" => "Especie agregada correctamente"]);
            break;

        case 'editar':
            $id = intval($_POST['nEspecie'] ?? 0);
            $nombre = limpiar($_POST['cEspecie'] ?? '');
            $generoId = intval($_POST['nGenero'] ?? 0);

            if ($id <= 0 || $nombre === '' || $generoId <= 0) {
                echo json_encode(["status" => "error", "message" => "Datos inválidos para editar"]);
                exit;
            }

            $stmt = $conn->prepare("UPDATE especie SET cEspecie = ?, nGenero = ?, nUsuario = ? WHERE nEspecie = ? AND nUsuario = ?");
            $stmt->execute([$nombre, $generoId, $usuarioId, $id, $usuarioId]);

            if ($stmt->rowCount() === 0) {
                echo json_encode(["status" => "error", "message" => "Especie no encontrada o sin permisos para editar"]);
                exit;
            }

            echo json_encode(["status" => "ok", "message" => "Especie actualizada correctamente"]);
            break;

        case 'eliminar':
            $id = intval($_POST['nEspecie'] ?? 0);

            if ($id <= 0) {
                echo json_encode(["status" => "error", "message" => "ID inválido para eliminar"]);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM especie WHERE nEspecie = ? AND nUsuario = ?");
            $stmt->execute([$id, $usuarioId]);            

            if ($stmt->rowCount() === 0) {
                echo json_encode(["status" => "error", "message" => "Especie no encontrada o sin permisos para eliminar"]);
                exit;
            }

            echo json_encode(["status" => "ok", "message" => "Especie eliminada correctamente"]);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Acción no válida"]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en la operación: " . $e->getMessage()]);
}
?>