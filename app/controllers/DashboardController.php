<?php
/**
 * Controlador de Dashboard
 * Panel principal con estadisticas generales del sistema
 * Solo accesible para ADMIN
 */
class DashboardController {

    /**
     * Mostrar dashboard principal
     */
    public function index() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        // Solo ADMIN puede ver el dashboard
        if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
            header('Location: index.php?controller=cita&error=access_denied');
            exit;
        }

        // Obtener estadisticas de citas
        $cita = new Cita();
        $estadisticasCitas = $cita->obtenerEstadisticas();
        $citasPorEstado = $cita->contarPorEstado();
        $citasPorOdontologo = $cita->citasPorOdontologo();
        $citasPorMes = $cita->citasPorMes();
        $proximasCitas = $cita->proximasCitas(5);
        
        // Obtener conteos generales
        $totalPacientes = (new Paciente())->contar();
        $totalOdontologos = (new Odontologo())->contar();
        $totalUsuarios = (new Usuario())->contar();
        
        require BASE_PATH . '/app/views/dashboard/index.php';
    }
}
