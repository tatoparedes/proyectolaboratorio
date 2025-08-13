<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "../conexion.php"; // Aquí debe existir $conn (PDO)

$usuarioId = isset($_SESSION["usuario"]["nUsuario"]) ? intval($_SESSION["usuario"]["nUsuario"]) : 6; // fallback para pruebas

// Función para limpiar datos
function limpiar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

$accion = $_POST['accion'] ?? '';

if (!$usuarioId || $usuarioId <= 0) {
    echo json_encode(["status" => "error", "message" => "Usuario no autorizado"]);
    exit;
}

try {
    switch ($accion) {
        case 'listar':
            $stmt = $conn->prepare("SELECT nFamilia, cFamilia FROM familia WHERE nUsuario = ?");
            $stmt->execute([$usuarioId]);
            $familias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "ok", "data" => $familias]);
            break;

        case 'agregar':
            $nombre = limpiar($_POST['cFamilia'] ?? '');
            if ($nombre === '') {
                echo json_encode(["status" => "error", "message" => "Nombre vacío"]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO familia (cFamilia, nUsuario) VALUES (?, ?)");
            $stmt->execute([$nombre, $usuarioId]);
            echo json_encode(["status" => "ok", "message" => "Familia agregada correctamente"]);
            break;

        case 'editar':
            $id = intval($_POST['nFamilia'] ?? 0);
            $nombre = limpiar($_POST['cFamilia'] ?? '');
            if ($id <= 0 || $nombre === '') {
                echo json_encode(["status" => "error", "message" => "Datos inválidos para editar"]);
                exit;
            }
            $stmt = $conn->prepare("UPDATE familia SET cFamilia = ? WHERE nFamilia = ? AND nUsuario = ?");
            $stmt->execute([$nombre, $id, $usuarioId]);
            echo json_encode(["status" => "ok", "message" => "Familia actualizada correctamente"]);
            break;

        case 'eliminar':
            $id = intval($_POST['nFamilia'] ?? 0);
            if ($id <= 0) {
                echo json_encode(["status" => "error", "message" => "ID inválido para eliminar"]);
                exit;
            }
            // Verificar si existen géneros asociados
            $check = $conn->prepare("SELECT COUNT(*) FROM genero WHERE nFamilia = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                echo json_encode(["status" => "error", "message" => "No se puede eliminar, existen géneros asociados"]);
                exit;
            }
            $stmt = $conn->prepare("DELETE FROM familia WHERE nFamilia = ? AND nUsuario = ?");
            $stmt->execute([$id, $usuarioId]);
            echo json_encode(["status" => "ok", "message" => "Familia eliminada correctamente"]);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Acción no válida"]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en la operación: " . $e->getMessage()]);
}
?>