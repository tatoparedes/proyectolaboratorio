<?php
require_once "../conexion.php";

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        $generoId = $_POST['nGenero'] ?? null;
        if ($generoId) {
            $stmt = $pdo->prepare("SELECT nEspecie, cEspecie, nGenero FROM Especie WHERE nGenero = ?");
            $stmt->execute([$generoId]);
        } else {
            $stmt = $pdo->query("SELECT nEspecie, cEspecie, nGenero FROM Especie");
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'agregar':
        $nombre = $_POST['cEspecie'] ?? null;
        $genero = $_POST['nGenero'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($nombre && $genero && $usuario) {
            $stmt = $pdo->prepare("INSERT INTO Especie (cEspecie, nGenero, nUsuario) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $genero, $usuario]);
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        }
        break;

    case 'editar':
        $id = $_POST['nEspecie'] ?? null;
        $nombre = $_POST['cEspecie'] ?? null;
        $genero = $_POST['nGenero'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($id && $nombre && $genero && $usuario) {
            $stmt = $pdo->prepare("UPDATE Especie SET cEspecie = ?, nGenero = ?, nUsuario = ? WHERE nEspecie = ?");
            $stmt->execute([$nombre, $genero, $usuario, $id]);
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        }
        break;

    case 'eliminar':
        $id = $_POST['nEspecie'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM Especie WHERE nEspecie = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Falta ID"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida"]);
        break;
}
?>