<?php
/**
 * Controlador de Citas
 * Gestiona las acciones CRUD para las citas medicas con validaciones
 */
class CitaController {

    /**
     * Mostrar listado de citas
     */
    public function index() {
        session_start();
        
        // Verificar autenticacion
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        // Obtener datos para la vista
        $data = (new Cita())->listar();
        $pacientes = (new Paciente())->listarSimple();
        $odontologos = (new Odontologo())->listarSimple();
        
        require BASE_PATH . '/app/views/citas/index.php';
    }

    /**
     * Guardar nueva cita
     */
    public function guardar() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        // Cargar validador
        require_once BASE_PATH . '/app/helpers/Validator.php';
        $validator = new Validator();

        // Sanitizar datos
        $fecha = Validator::sanitize($_POST['fecha'] ?? '');
        $hora = Validator::sanitize($_POST['hora'] ?? '');
        $estado = Validator::sanitize($_POST['estado'] ?? 'PROGRAMADA');
        $id_paciente = Validator::sanitizeInt($_POST['id_paciente'] ?? 0);
        $id_odontologo = Validator::sanitizeInt($_POST['id_odontologo'] ?? 0);

        // Validar
        if (!$validator->validateCita($_POST)) {
            header('Location: index.php?controller=cita&error=invalid_data');
            exit;
        }

        $cita = new Cita();
        $cita->insertar($fecha, $hora, $estado, $id_paciente, $id_odontologo);
        
        header('Location: index.php?controller=cita&msg=created');
    }

    /**
     * Eliminar cita
     */
    public function eliminar() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        $id = filter_var($_GET['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        if ($id > 0) {
            (new Cita())->eliminar($id);
        }
        header('Location: index.php?controller=cita&msg=deleted');
    }

    /**
     * Actualizar cita existente
     */
    public function actualizar() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        // Cargar validador
        require_once BASE_PATH . '/app/helpers/Validator.php';
        $validator = new Validator();

        // Sanitizar datos
        $id_cita = Validator::sanitizeInt($_POST['id_cita'] ?? 0);
        $fecha = Validator::sanitize($_POST['fecha'] ?? '');
        $hora = Validator::sanitize($_POST['hora'] ?? '');
        $estado = Validator::sanitize($_POST['estado'] ?? 'PROGRAMADA');
        $id_paciente = Validator::sanitizeInt($_POST['id_paciente'] ?? 0);
        $id_odontologo = Validator::sanitizeInt($_POST['id_odontologo'] ?? 0);

        // Validar
        if (!$validator->validateCita($_POST)) {
            header('Location: index.php?controller=cita&error=invalid_data');
            exit;
        }

        (new Cita())->actualizar($id_cita, $fecha, $hora, $estado, $id_paciente, $id_odontologo);
        
        header('Location: index.php?controller=cita&msg=updated');
    }

    /**
     * Cambiar estado de cita
     */
    public function cambiarEstado() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            exit;
        }

        $id = filter_var($_GET['id'] ?? $_POST['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $estado = filter_var($_GET['estado'] ?? $_POST['estado'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Validar estado permitido
        $estadosPermitidos = ['PROGRAMADA', 'CONFIRMADA', 'COMPLETADA', 'CANCELADA'];
        if (!in_array($estado, $estadosPermitidos)) {
            header('Location: index.php?controller=cita&error=invalid_status');
            exit;
        }
        
        if ($id > 0) {
            (new Cita())->cambiarEstado($id, $estado);
        }
        
        header('Location: index.php?controller=cita&msg=status_changed');
    }

    /**
     * Dashboard de reportes de citas
     */
    public function reporte() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        $cita = new Cita();
        
        // Obtener datos para el dashboard
        $estadisticas = $cita->obtenerEstadisticas();
        $citasPorEstado = $cita->contarPorEstado();
        $citasPorOdontologo = $cita->citasPorOdontologo();
        $citasPorMes = $cita->citasPorMes();
        $citasPorDia = $cita->citasPorDiaSemana();
        $proximasCitas = $cita->proximasCitas(5);
        
        // Obtener todas las citas para la tabla de reportes
        $todasLasCitas = $cita->listar();
        
        require BASE_PATH . '/app/views/citas/reporte.php';
    }

    /**
     * Exportar reporte a Excel (CSV)
     */
    public function exportarExcel() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        $cita = new Cita();
        $datos = $cita->listar();
        
        // Configurar headers para descarga CSV
        $filename = 'Reporte_Citas_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Crear archivo CSV
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8 en Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, ['ID', 'Fecha', 'Hora', 'Paciente', 'Odontologo', 'Estado'], ';');
        
        // Datos
        foreach ($datos as $row) {
            fputcsv($output, [
                $row['id_cita'],
                $row['fecha'],
                $row['hora'],
                $row['paciente_nombre'] . ' ' . ($row['paciente_apellido'] ?? ''),
                $row['odontologo_nombre'],
                $row['estado']
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * Exportar estadisticas a Excel
     */
    public function exportarEstadisticas() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }

        $cita = new Cita();
        $estadisticas = $cita->obtenerEstadisticas();
        $porEstado = $cita->contarPorEstado();
        $porOdontologo = $cita->citasPorOdontologo();
        
        $filename = 'Estadisticas_Citas_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Resumen general
        fputcsv($output, ['REPORTE DE ESTADISTICAS - CLINICA DENTAL'], ';');
        fputcsv($output, ['Fecha de generacion: ' . date('d/m/Y H:i:s')], ';');
        fputcsv($output, [], ';');
        
        fputcsv($output, ['RESUMEN GENERAL'], ';');
        fputcsv($output, ['Metrica', 'Valor'], ';');
        fputcsv($output, ['Total de Citas', $estadisticas['total']], ';');
        fputcsv($output, ['Citas de Hoy', $estadisticas['hoy']], ';');
        fputcsv($output, ['Citas Pendientes', $estadisticas['pendientes']], ';');
        fputcsv($output, ['Citas Completadas', $estadisticas['completadas']], ';');
        fputcsv($output, ['Citas Canceladas', $estadisticas['canceladas']], ';');
        fputcsv($output, ['Citas esta Semana', $estadisticas['semana']], ';');
        fputcsv($output, ['Citas este Mes', $estadisticas['mes']], ';');
        fputcsv($output, [], ';');
        
        // Por estado
        fputcsv($output, ['CITAS POR ESTADO'], ';');
        fputcsv($output, ['Estado', 'Cantidad'], ';');
        foreach ($porEstado as $item) {
            fputcsv($output, [$item['estado'], $item['total']], ';');
        }
        fputcsv($output, [], ';');
        
        // Por odontologo
        fputcsv($output, ['CITAS POR ODONTOLOGO'], ';');
        fputcsv($output, ['Odontologo', 'Cantidad'], ';');
        foreach ($porOdontologo as $item) {
            fputcsv($output, [$item['odontologo'], $item['total']], ';');
        }
        
        fclose($output);
        exit;
    }
}