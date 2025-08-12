<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../conexion.php";

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        try {
            $stmt = $pdo->query("SELECT nFamilia, cFamilia FROM Familia");
            $familias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($familias);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error al listar familias: " . $e->getMessage()]);
        }
        break;

    case 'agregar':
        $nombre = $_POST['cFamilia'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($nombre && $usuario) {
            try {
                $stmt = $pdo->prepare("INSERT INTO Familia (cFamilia, nUsuario) VALUES (?, ?)");
                $stmt->execute([$nombre, $usuario]);
                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => "ok"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se pudo agregar familia"]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Error al agregar familia: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos para agregar"]);
        }
        break;

    case 'editar':
        $id = $_POST['nFamilia'] ?? null;
        $nombre = $_POST['cFamilia'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($id && $nombre && $usuario) {
            try {
                $stmt = $pdo->prepare("UPDATE Familia SET cFamilia = ?, nUsuario = ? WHERE nFamilia = ?");
                $stmt->execute([$nombre, $usuario, $id]);
                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => "ok"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se pudo actualizar la familia o sin cambios"]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Error al editar familia: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos para editar"]);
        }
        break;

    case 'eliminar':
        $id = $_POST['nFamilia'] ?? null;

        if ($id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM Familia WHERE nFamilia = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => "ok"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se encontró la familia para eliminar"]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Error al eliminar familia: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Falta ID para eliminar"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida"]);
        break;
}