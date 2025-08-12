<?php
require_once "../conexion.php";

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        // Opcionalmente puedes filtrar por familia
        $familiaId = $_POST['nFamilia'] ?? null;
        if ($familiaId) {
            $stmt = $pdo->prepare("SELECT nGenero, cGenero, nFamilia FROM Genero WHERE nFamilia = ?");
            $stmt->execute([$familiaId]);
        } else {
            $stmt = $pdo->query("SELECT nGenero, cGenero, nFamilia FROM Genero");
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'agregar':
        $nombre = $_POST['cGenero'] ?? null;
        $familia = $_POST['nFamilia'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($nombre && $familia && $usuario) {
            $stmt = $pdo->prepare("INSERT INTO Genero (cGenero, nFamilia, nUsuario) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $familia, $usuario]);
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        }
        break;

    case 'editar':
        $id = $_POST['nGenero'] ?? null;
        $nombre = $_POST['cGenero'] ?? null;
        $familia = $_POST['nFamilia'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($id && $nombre && $familia && $usuario) {
            $stmt = $pdo->prepare("UPDATE Genero SET cGenero = ?, nFamilia = ?, nUsuario = ? WHERE nGenero = ?");
            $stmt->execute([$nombre, $familia, $usuario, $id]);
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        }
        break;

    case 'eliminar':
        $id = $_POST['nGenero'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM Genero WHERE nGenero = ?");
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
