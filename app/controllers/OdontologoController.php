<?php
/**
 * OdontologoController - Controlador de Odontologos
 * Maneja las operaciones CRUD de odontologos
 */
class OdontologoController {

    /**
     * Verificar acceso al módulo de odontólogos
     * Acceso: ADMIN, RECEPCION
     */
    private function verificarAcceso() {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }
        $rolesPermitidos = ['ADMIN', 'RECEPCION'];
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
        if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
            header('Location: index.php?controller=odontologo&error=access_denied');
            exit;
        }
    }

    public function index() {
        $this->verificarAcceso();

        $data = (new Odontologo())->listar();
        $soloLectura = ($_SESSION['rol'] ?? '') !== 'ADMIN';
        require BASE_PATH . '/app/views/odontologos/index.php';
    }

    public function guardar() {
        $this->verificarAdmin();

        require_once BASE_PATH . '/app/helpers/Validator.php';

        $nombres = Validator::sanitize($_POST['nombres'] ?? '');
        $especialidad = Validator::sanitize($_POST['especialidad'] ?? '');
        $telefono = Validator::sanitize($_POST['telefono'] ?? '');
        $email = Validator::sanitize($_POST['email'] ?? '');

        if (empty($nombres) || empty($especialidad)) {
            header('Location: index.php?controller=odontologo&error=invalid_data');
            exit;
        }

        (new Odontologo())->insertar($nombres, $especialidad, $telefono, $email);
        header('Location: index.php?controller=odontologo&msg=created');
    }

    public function eliminar() {
        $this->verificarAdmin();

        $id = filter_var($_GET['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        if ($id > 0) {
            (new Odontologo())->eliminar($id);
        }
        header('Location: index.php?controller=odontologo&msg=deleted');
    }

    public function actualizar() {
        $this->verificarAdmin();

        require_once BASE_PATH . '/app/helpers/Validator.php';

        $id = filter_var($_POST['id_odontologo'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $nombres = Validator::sanitize($_POST['nombres'] ?? '');
        $especialidad = Validator::sanitize($_POST['especialidad'] ?? '');
        $telefono = Validator::sanitize($_POST['telefono'] ?? '');
        $email = Validator::sanitize($_POST['email'] ?? '');

        if (empty($nombres) || empty($especialidad)) {
            header('Location: index.php?controller=odontologo&error=invalid_data');
            exit;
        }

        (new Odontologo())->actualizar($id, $nombres, $especialidad, $telefono, $email);
        header('Location: index.php?controller=odontologo&msg=updated');
    }

    public function exportarExcel() {
        $this->verificarAcceso();

        $odontologos = (new Odontologo())->listar();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Odontologos_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        
        fputcsv($output, ['ID', 'Nombres', 'Especialidad', 'Telefono', 'Email']);
        
        foreach ($odontologos as $o) {
            fputcsv($output, [
                $o['id_odontologo'],
                $o['nombres'],
                $o['especialidad'],
                $o['telefono'],
                $o['email']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
