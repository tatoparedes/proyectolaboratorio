<?php
require_once "../conexion.php";

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        $stmt = $pdo->query("SELECT * FROM Prueba");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'agregar':
        $nEspecie = $_POST['nEspecie'] ?? null;
        $foto = $_POST['cFoto'] ?? null;
        $descripcion = $_POST['cDescripcion'] ?? null;
        $resultado = $_POST['cResultado'] ?? null;
        $bacteria = $_POST['cBacteria'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($nEspecie && $usuario) {
            $stmt = $pdo->prepare("INSERT INTO Prueba (nEspecie, cFoto, cDescripcion, cResultado, cBacteria, nUsuario) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nEspecie, $foto, $descripcion, $resultado, $bacteria, $usuario]);
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        }
        break;

    case 'editar':
        $id = $_POST['nPrueba'] ?? null;
        $nEspecie = $_POST['nEspecie'] ?? null;
        $foto = $_POST['cFoto'] ?? null;
        $descripcion = $_POST['cDescripcion'] ?? null;
        $resultado = $_POST['cResultado'] ?? null;
        $bacteria = $_POST['cBacteria'] ?? null;
        $usuario = $_POST['nUsuario'] ?? null;

        if ($id && $nEspecie && $usuario) {
            $stmt = $pdo->prepare("UPDATE Prueba SET nEspecie = ?, cFoto = ?, cDescripcion = ?, cResultado = ?, cBacteria = ?, nUsuario = ? WHERE nPrueba = ?");
            $stmt->execute([$nEspecie, $foto, $descripcion, $resultado, $bacteria, $usuario, $id]);
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        }
        break;

    case 'eliminar':
        $id = $_POST['nPrueba'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM Prueba WHERE nPrueba = ?");
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