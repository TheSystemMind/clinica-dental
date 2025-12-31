<?php
/**
 * Modelo Cita
 * Tabla: cita
 * Campos: id_cita, fecha, hora, estado, id_paciente, id_odontologo
 */
class Cita {

    private $conexion;

    public function __construct() {
        $this->conexion = (new Database())->conectar();
    }

    /**
     * Listar todas las citas con información del paciente y odontólogo
     */
    public function listar() {
        $sql = "SELECT 
                    c.id_cita,
                    c.fecha,
                    c.hora,
                    c.estado,
                    c.id_paciente,
                    c.id_odontologo,
                    CONCAT(p.nombres, ' ', p.apellidos) AS paciente_nombre,
                    o.nombres AS odontologo_nombre
                FROM cita c
                INNER JOIN paciente p ON c.id_paciente = p.id_paciente
                INNER JOIN odontologo o ON c.id_odontologo = o.id_odontologo
                ORDER BY c.fecha DESC, c.hora DESC";
        
        $result = $this->conexion->query($sql);
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Insertar una nueva cita
     */
    public function insertar($fecha, $hora, $estado, $id_paciente, $id_odontologo) {
        $sql = "INSERT INTO cita (fecha, hora, estado, id_paciente, id_odontologo)
                VALUES (:fecha, :hora, :estado, :id_paciente, :id_odontologo)";
        
        $stmt = $this->conexion->prepare($sql);
        
        return $stmt->execute([
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':estado' => $estado,
            ':id_paciente' => $id_paciente,
            ':id_odontologo' => $id_odontologo
        ]);
    }

    /**
     * Obtener una cita por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM cita WHERE id_cita = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar una cita existente
     */
    public function actualizar($id, $fecha, $hora, $estado, $id_paciente, $id_odontologo) {
        $sql = "UPDATE cita SET
                    fecha = :fecha,
                    hora = :hora,
                    estado = :estado,
                    id_paciente = :id_paciente,
                    id_odontologo = :id_odontologo
                WHERE id_cita = :id";
        
        $stmt = $this->conexion->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':estado' => $estado,
            ':id_paciente' => $id_paciente,
            ':id_odontologo' => $id_odontologo
        ]);
    }

    /**
     * Eliminar una cita
     */
    public function eliminar($id) {
        // Primero eliminar registros relacionados (tratamientos y pagos)
        $this->conexion->prepare("DELETE FROM tratamiento WHERE id_cita = :id")->execute([':id' => $id]);
        $this->conexion->prepare("DELETE FROM pago WHERE id_cita = :id")->execute([':id' => $id]);
        
        // Luego eliminar la cita
        $sql = "DELETE FROM cita WHERE id_cita = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Cambiar el estado de una cita
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE cita SET estado = :estado WHERE id_cita = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':estado' => $estado
        ]);
    }

    /**
     * Obtener citas por fecha
     */
    public function obtenerPorFecha($fecha) {
        $sql = "SELECT 
                    c.*,
                    CONCAT(p.nombres, ' ', p.apellidos) AS paciente_nombre,
                    o.nombres AS odontologo_nombre
                FROM cita c
                INNER JOIN paciente p ON c.id_paciente = p.id_paciente
                INNER JOIN odontologo o ON c.id_odontologo = o.id_odontologo
                WHERE c.fecha = :fecha
                ORDER BY c.hora";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar citas por estado
     */
    public function contarPorEstado() {
        $sql = "SELECT estado, COUNT(*) as total FROM cita GROUP BY estado";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener estadísticas generales para el dashboard
     */
    public function obtenerEstadisticas() {
        $stats = [];
        
        // Total de citas
        $sql = "SELECT COUNT(*) as total FROM cita";
        $stats['total'] = $this->conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Citas de hoy
        $sql = "SELECT COUNT(*) as total FROM cita WHERE fecha = CURDATE()";
        $stats['hoy'] = $this->conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Citas pendientes (PROGRAMADA + CONFIRMADA)
        $sql = "SELECT COUNT(*) as total FROM cita WHERE estado IN ('PROGRAMADA', 'CONFIRMADA')";
        $stats['pendientes'] = $this->conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Citas completadas
        $sql = "SELECT COUNT(*) as total FROM cita WHERE estado = 'COMPLETADA'";
        $stats['completadas'] = $this->conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Citas canceladas
        $sql = "SELECT COUNT(*) as total FROM cita WHERE estado = 'CANCELADA'";
        $stats['canceladas'] = $this->conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Citas esta semana
        $sql = "SELECT COUNT(*) as total FROM cita WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
        $stats['semana'] = $this->conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Citas este mes
        $sql = "SELECT COUNT(*) as total FROM cita WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        $stats['mes'] = $this->conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $stats;
    }

    /**
     * Citas por odontólogo
     */
    public function citasPorOdontologo() {
        $sql = "SELECT o.nombres AS odontologo, COUNT(c.id_cita) as total 
                FROM odontologo o 
                LEFT JOIN cita c ON o.id_odontologo = c.id_odontologo 
                GROUP BY o.id_odontologo, o.nombres
                ORDER BY total DESC";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Citas por mes (últimos 6 meses)
     */
    public function citasPorMes() {
        $sql = "SELECT 
                    DATE_FORMAT(fecha, '%Y-%m') as mes,
                    DATE_FORMAT(fecha, '%M %Y') as mes_nombre,
                    COUNT(*) as total 
                FROM cita 
                WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(fecha, '%Y-%m'), DATE_FORMAT(fecha, '%M %Y')
                ORDER BY mes ASC";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Citas por día de la semana
     */
    public function citasPorDiaSemana() {
        $sql = "SELECT 
                    DAYOFWEEK(fecha) as dia_num,
                    DAYNAME(fecha) as dia_nombre,
                    COUNT(*) as total 
                FROM cita 
                GROUP BY DAYOFWEEK(fecha), DAYNAME(fecha)
                ORDER BY dia_num";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Próximas citas (siguientes 7 días)
     */
    public function proximasCitas($limite = 5) {
        $sql = "SELECT 
                    c.id_cita, c.fecha, c.hora, c.estado,
                    CONCAT(p.nombres, ' ', p.apellidos) AS paciente,
                    o.nombres AS odontologo
                FROM cita c
                INNER JOIN paciente p ON c.id_paciente = p.id_paciente
                INNER JOIN odontologo o ON c.id_odontologo = o.id_odontologo
                WHERE c.fecha >= CURDATE() AND c.estado IN ('PROGRAMADA', 'CONFIRMADA')
                ORDER BY c.fecha ASC, c.hora ASC
                LIMIT :limite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}