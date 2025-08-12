<?php
// crud.php
require_once "conexion.php"; // Conexión PDO

class Crud {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ===============================
    // Familia
    // ===============================
    public function listarFamilia() {
        $stmt = $this->pdo->prepare("CALL sp_listar_familia()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarFamilia($cFamilia, $nUsuario) {
        $stmt = $this->pdo->prepare("CALL sp_insertar_familia(:cFamilia, :nUsuario)");
        $stmt->bindParam(":cFamilia", $cFamilia);
        $stmt->bindParam(":nUsuario", $nUsuario);
        return $stmt->execute();
    }

    public function actualizarFamilia($nFamilia, $cFamilia) {
        $stmt = $this->pdo->prepare("CALL sp_actualizar_familia(:nFamilia, :cFamilia)");
        $stmt->bindParam(":nFamilia", $nFamilia);
        $stmt->bindParam(":cFamilia", $cFamilia);
        return $stmt->execute();
    }

    public function eliminarFamilia($nFamilia) {
        $stmt = $this->pdo->prepare("CALL sp_eliminar_familia(:nFamilia)");
        $stmt->bindParam(":nFamilia", $nFamilia);
        return $stmt->execute();
    }

    // ===============================
    // Género
    // ===============================
    public function listarGenero() {
        $stmt = $this->pdo->prepare("CALL sp_listar_genero()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarGenero($cGenero, $nFamilia, $nUsuario) {
        $stmt = $this->pdo->prepare("CALL sp_insertar_genero(:cGenero, :nFamilia, :nUsuario)");
        $stmt->bindParam(":cGenero", $cGenero);
        $stmt->bindParam(":nFamilia", $nFamilia);
        $stmt->bindParam(":nUsuario", $nUsuario);
        return $stmt->execute();
    }

    public function actualizarGenero($nGenero, $cGenero, $nFamilia) {
        $stmt = $this->pdo->prepare("CALL sp_actualizar_genero(:nGenero, :cGenero, :nFamilia)");
        $stmt->bindParam(":nGenero", $nGenero);
        $stmt->bindParam(":cGenero", $cGenero);
        $stmt->bindParam(":nFamilia", $nFamilia);
        return $stmt->execute();
    }

    public function eliminarGenero($nGenero) {
        $stmt = $this->pdo->prepare("CALL sp_eliminar_genero(:nGenero)");
        $stmt->bindParam(":nGenero", $nGenero);
        return $stmt->execute();
    }

    // ===============================
    // Especie
    // ===============================
    public function listarEspecie() {
        $stmt = $this->pdo->prepare("CALL sp_listar_especie()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarEspecie($cEspecie, $nGenero, $nUsuario) {
        $stmt = $this->pdo->prepare("CALL sp_insertar_especie(:cEspecie, :nGenero, :nUsuario)");
        $stmt->bindParam(":cEspecie", $cEspecie);
        $stmt->bindParam(":nGenero", $nGenero);
        $stmt->bindParam(":nUsuario", $nUsuario);
        return $stmt->execute();
    }

    public function actualizarEspecie($nEspecie, $cEspecie, $nGenero) {
        $stmt = $this->pdo->prepare("CALL sp_actualizar_especie(:nEspecie, :cEspecie, :nGenero)");
        $stmt->bindParam(":nEspecie", $nEspecie);
        $stmt->bindParam(":cEspecie", $cEspecie);
        $stmt->bindParam(":nGenero", $nGenero);
        return $stmt->execute();
    }

    public function eliminarEspecie($nEspecie) {
        $stmt = $this->pdo->prepare("CALL sp_eliminar_especie(:nEspecie)");
        $stmt->bindParam(":nEspecie", $nEspecie);
        return $stmt->execute();
    }

    // ===============================
    // Prueba
    // ===============================
    public function listarPrueba() {
        $stmt = $this->pdo->prepare("CALL sp_listar_prueba()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarPrueba($nEspecie, $cFoto, $cDescripcion, $cResultado, $cBacteria, $nUsuario) {
        $stmt = $this->pdo->prepare("CALL sp_insertar_prueba(:nEspecie, :cFoto, :cDescripcion, :cResultado, :cBacteria, :nUsuario)");
        $stmt->bindParam(":nEspecie", $nEspecie);
        $stmt->bindParam(":cFoto", $cFoto);
        $stmt->bindParam(":cDescripcion", $cDescripcion);
        $stmt->bindParam(":cResultado", $cResultado);
        $stmt->bindParam(":cBacteria", $cBacteria);
        $stmt->bindParam(":nUsuario", $nUsuario);
        return $stmt->execute();
    }

    public function actualizarPrueba($nPrueba, $nEspecie, $cFoto, $cDescripcion, $cResultado, $cBacteria) {
        $stmt = $this->pdo->prepare("CALL sp_actualizar_prueba(:nPrueba, :nEspecie, :cFoto, :cDescripcion, :cResultado, :cBacteria)");
        $stmt->bindParam(":nPrueba", $nPrueba);
        $stmt->bindParam(":nEspecie", $nEspecie);
        $stmt->bindParam(":cFoto", $cFoto);
        $stmt->bindParam(":cDescripcion", $cDescripcion);
        $stmt->bindParam(":cResultado", $cResultado);
        $stmt->bindParam(":cBacteria", $cBacteria);
        return $stmt->execute();
    }

    public function eliminarPrueba($nPrueba) {
        $stmt = $this->pdo->prepare("CALL sp_eliminar_prueba(:nPrueba)");
        $stmt->bindParam(":nPrueba", $nPrueba);
        return $stmt->execute();
    }
}
?>