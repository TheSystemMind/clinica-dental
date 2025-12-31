<?php
/**
 * Modelo Odontologo
 * Tabla: odontologo
 */
class Odontologo {

    private $conexion;

    public function __construct() {
        $this->conexion = (new Database())->conectar();
    }

    public function listar() {
        $result = $this->conexion->query("SELECT * FROM odontologo ORDER BY id_odontologo DESC");
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function listarSimple() {
        $sql = "SELECT id_odontologo, nombres AS nombre FROM odontologo ORDER BY nombres";
        $result = $this->conexion->query($sql);
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function insertar($nombres, $especialidad, $telefono, $email) {
        $sql = "INSERT INTO odontologo (nombres, especialidad, telefono, email)
                VALUES (:nombres, :especialidad, :telefono, :email)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nombres' => $nombres,
            ':especialidad' => $especialidad,
            ':telefono' => $telefono,
            ':email' => $email
        ]);
    }

    public function actualizar($id, $nombres, $especialidad, $telefono, $email) {
        $sql = "UPDATE odontologo SET 
                    nombres = :nombres,
                    especialidad = :especialidad,
                    telefono = :telefono,
                    email = :email
                WHERE id_odontologo = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nombres' => $nombres,
            ':especialidad' => $especialidad,
            ':telefono' => $telefono,
            ':email' => $email
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM odontologo WHERE id_odontologo = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM odontologo WHERE id_odontologo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function contar() {
        $result = $this->conexion->query("SELECT COUNT(*) FROM odontologo");
        return $result->fetchColumn();
    }
}
