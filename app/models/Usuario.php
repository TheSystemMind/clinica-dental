<?php
/**
 * Modelo Usuario
 * Tabla: usuario
 */
class Usuario {

    private $conexion;

    public function __construct() {
        $this->conexion = (new Database())->conectar();
    }

    public function login($usuario, $password) {
        $sql = "SELECT * FROM usuario 
                WHERE usuario = :usuario 
                AND password = :password 
                AND activo = 1 
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':usuario' => $usuario,
            ':password' => $password
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listar() {
        $result = $this->conexion->query("SELECT id_usuario, usuario, rol, activo FROM usuario ORDER BY id_usuario DESC");
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function insertar($usuario, $password, $rol = 'USER') {
        $sql = "INSERT INTO usuario (usuario, password, rol, activo)
                VALUES (:usuario, :password, :rol, 1)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':usuario' => $usuario,
            ':password' => $password,
            ':rol' => $rol
        ]);
    }

    public function actualizar($id, $usuario, $password = null, $rol = 'USER') {
        if (!empty($password)) {
            $sql = "UPDATE usuario SET 
                        usuario = :usuario,
                        password = :password,
                        rol = :rol
                    WHERE id_usuario = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':usuario' => $usuario,
                ':password' => $password,
                ':rol' => $rol
            ]);
        } else {
            $sql = "UPDATE usuario SET 
                        usuario = :usuario,
                        rol = :rol
                    WHERE id_usuario = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':usuario' => $usuario,
                ':rol' => $rol
            ]);
        }
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM usuario WHERE id_usuario = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function existeUsuario($usuario, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) FROM usuario WHERE usuario = :usuario AND id_usuario != :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':usuario' => $usuario, ':id' => $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) FROM usuario WHERE usuario = :usuario";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':usuario' => $usuario]);
        }
        return $stmt->fetchColumn() > 0;
    }

    public function cambiarEstado($id, $activo) {
        $sql = "UPDATE usuario SET activo = :activo WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':id' => $id, ':activo' => $activo]);
    }

    public function contar() {
        $result = $this->conexion->query("SELECT COUNT(*) FROM usuario WHERE activo = 1");
        return $result->fetchColumn();
    }
}
