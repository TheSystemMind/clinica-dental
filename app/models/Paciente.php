<?php
/**
 * Modelo Paciente
 * Tabla: paciente
 */
class Paciente {

    private $conexion;

    public function __construct() {
        $this->conexion = (new Database())->conectar();
    }

    public function listar() {
        $result = $this->conexion->query("SELECT * FROM paciente ORDER BY id_paciente DESC");
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function insertar($nombres,$apellidos,$dni,$telefono,$email) {
        $sql = "INSERT INTO paciente(nombres,apellidos,dni,telefono,email)
                VALUES(:n,:a,:d,:t,:e)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':n'=>$nombres,
            ':a'=>$apellidos,
            ':d'=>$dni,
            ':t'=>$telefono,
            ':e'=>$email
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM paciente WHERE id_paciente = :id"
        );
        return $stmt->execute([':id'=>$id]);
    }

    public function actualizar($id, $nombres, $apellidos, $dni, $telefono, $email) {

        $sql = "UPDATE paciente SET
                    nombres   = :nombres,
                    apellidos = :apellidos,
                    dni       = :dni,
                    telefono  = :telefono,
                    email     = :email
                WHERE id_paciente = :id";
    
        $stmt = $this->conexion->prepare($sql);
    
        return $stmt->execute([
            ':id'        => $id,
            ':nombres'   => $nombres,
            ':apellidos' => $apellidos,
            ':dni'       => $dni,
            ':telefono'  => $telefono,
            ':email'     => $email
        ]);
    }
    
    public function listarSimple() {
        $sql = "SELECT id_paciente, CONCAT(nombres, ' ', apellidos) AS nombre FROM paciente ORDER BY nombres";
        $result = $this->conexion->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verifica si existe un DNI en la base de datos
     * @param string $dni DNI a verificar
     * @param int|null $excludeId ID a excluir de la busqueda (para edicion)
     * @return bool
     */
    public function existeDNI($dni, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) FROM paciente WHERE dni = :dni AND id_paciente != :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':dni' => $dni, ':id' => $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) FROM paciente WHERE dni = :dni";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':dni' => $dni]);
        }
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtiene un paciente por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM paciente WHERE id_paciente = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Contar total de pacientes
     */
    public function contar() {
        $result = $this->conexion->query("SELECT COUNT(*) FROM paciente");
        return $result->fetchColumn();
    }
}
