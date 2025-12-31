<?php
/**
 * PacienteController - Controlador de Pacientes
 * Maneja las operaciones CRUD de pacientes con validaciones
 */
class PacienteController {

    /**
     * Verificar acceso al módulo de pacientes
     * Acceso: ADMIN, ODONTOLOGO, RECEPCION
     */
    private function verificarAcceso() {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }
        $rolesPermitidos = ['ADMIN', 'ODONTOLOGO', 'RECEPCION'];
        if (!in_array($_SESSION['rol'] ?? '', $rolesPermitidos)) {
            header('Location: index.php?controller=cita&error=access_denied');
            exit;
        }
    }

    /**
     * Verificar que el usuario sea ADMIN (para operaciones de escritura)
     */
    private function verificarAdmin() {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }
        $rolesPermitidos = ['ADMIN', 'RECEPCION'];
        if (!in_array($_SESSION['rol'] ?? '', $rolesPermitidos)) {
            header('Location: index.php?controller=paciente&error=access_denied');
            exit;
        }
    }

    public function index() {
        $this->verificarAcceso();

        $data = (new Paciente())->listar();
        $soloLectura = !in_array($_SESSION['rol'] ?? '', ['ADMIN', 'RECEPCION']);
        require BASE_PATH . '/app/views/pacientes/index.php';
    }

    public function guardar() {
        $this->verificarAdmin();

        // Cargar el validador
        require_once BASE_PATH . '/app/helpers/Validator.php';
        $validator = new Validator();

        // Sanitizar datos
        $nombres = Validator::sanitize($_POST['nombres'] ?? '');
        $apellidos = Validator::sanitize($_POST['apellidos'] ?? '');
        $dni = Validator::sanitize($_POST['dni'] ?? '');
        $telefono = Validator::sanitize($_POST['telefono'] ?? '');
        $email = Validator::sanitize($_POST['email'] ?? '');

        // Validar
        if (!$validator->validatePaciente($_POST)) {
            header('Location: index.php?controller=paciente&error=invalid_data');
            exit;
        }

        // Verificar DNI duplicado
        $paciente = new Paciente();
        if ($paciente->existeDNI($dni)) {
            header('Location: index.php?controller=paciente&error=dni_exists');
            exit;
        }

        // Insertar
        $paciente->insertar($nombres, $apellidos, $dni, $telefono, $email);
        header('Location: index.php?controller=paciente&msg=created');
    }

    public function eliminar() {
        $this->verificarAdmin();

        $id = filter_var($_GET['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        if ($id > 0) {
            (new Paciente())->eliminar($id);
        }
        header('Location: index.php?controller=paciente&msg=deleted');
    }

    public function actualizar() {
        $this->verificarAdmin();

        // Cargar el validador
        require_once BASE_PATH . '/app/helpers/Validator.php';
        $validator = new Validator();

        // Sanitizar datos
        $id = filter_var($_POST['id_paciente'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $nombres = Validator::sanitize($_POST['nombres'] ?? '');
        $apellidos = Validator::sanitize($_POST['apellidos'] ?? '');
        $dni = Validator::sanitize($_POST['dni'] ?? '');
        $telefono = Validator::sanitize($_POST['telefono'] ?? '');
        $email = Validator::sanitize($_POST['email'] ?? '');

        // Validar
        if (!$validator->validatePaciente($_POST)) {
            header('Location: index.php?controller=paciente&error=invalid_data');
            exit;
        }

        // Verificar DNI duplicado (excepto el mismo paciente)
        $paciente = new Paciente();
        if ($paciente->existeDNI($dni, $id)) {
            header('Location: index.php?controller=paciente&error=dni_exists');
            exit;
        }

        // Actualizar
        $paciente->actualizar($id, $nombres, $apellidos, $dni, $telefono, $email);
        header('Location: index.php?controller=paciente&msg=updated');
    }

    public function exportarExcel() {
        $this->verificarAdmin();

        $pacientes = (new Paciente())->listar();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Pacientes_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        
        fputcsv($output, ['ID', 'Nombres', 'Apellidos', 'DNI', 'Telefono', 'Email']);
        
        foreach ($pacientes as $p) {
            fputcsv($output, [
                $p['id_paciente'],
                $p['nombres'],
                $p['apellidos'],
                $p['dni'],
                $p['telefono'],
                $p['email']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
