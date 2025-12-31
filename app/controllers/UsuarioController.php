<?php
/**
 * UsuarioController - Controlador de Usuarios
 * Maneja las operaciones CRUD de usuarios del sistema
 */
class UsuarioController {

    /**
     * Verificar que el usuario sea ADMIN
     */
    private function verificarAdmin() {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php');
            exit;
        }
        // Solo ADMIN puede acceder a este módulo
        if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
            header('Location: index.php?controller=cita&error=access_denied');
            exit;
        }
    }

    public function index() {
        $this->verificarAdmin();

        $data = (new Usuario())->listar();
        require BASE_PATH . '/app/views/usuarios/index.php';
    }

    public function guardar() {
        $this->verificarAdmin();

        require_once BASE_PATH . '/app/helpers/Validator.php';

        $usuario = Validator::sanitize($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol = Validator::sanitize($_POST['rol'] ?? 'USER');

        if (empty($usuario) || empty($password)) {
            header('Location: index.php?controller=usuario&error=invalid_data');
            exit;
        }

        // Verificar usuario duplicado
        if ((new Usuario())->existeUsuario($usuario)) {
            header('Location: index.php?controller=usuario&error=user_exists');
            exit;
        }

        (new Usuario())->insertar($usuario, $password, $rol);
        header('Location: index.php?controller=usuario&msg=created');
    }

    public function eliminar() {
        $this->verificarAdmin();

        $id = filter_var($_GET['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        
        // No permitir eliminar al usuario actual
        if ($id > 0 && $id != ($_SESSION['id_usuario'] ?? 0)) {
            (new Usuario())->eliminar($id);
            header('Location: index.php?controller=usuario&msg=deleted');
        } else {
            header('Location: index.php?controller=usuario&error=cannot_delete_self');
        }
    }

    public function actualizar() {
        $this->verificarAdmin();

        require_once BASE_PATH . '/app/helpers/Validator.php';

        $id = filter_var($_POST['id_usuario'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $usuario = Validator::sanitize($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol = Validator::sanitize($_POST['rol'] ?? 'USER');

        if (empty($usuario)) {
            header('Location: index.php?controller=usuario&error=invalid_data');
            exit;
        }

        (new Usuario())->actualizar($id, $usuario, $password, $rol);
        header('Location: index.php?controller=usuario&msg=updated');
    }

    public function exportarExcel() {
        $this->verificarAdmin();

        $usuarios = (new Usuario())->listar();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Usuarios_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        
        fputcsv($output, ['ID', 'Usuario', 'Rol', 'Estado']);
        
        foreach ($usuarios as $u) {
            fputcsv($output, [
                $u['id_usuario'],
                $u['usuario'],
                $u['rol'],
                $u['activo'] ? 'Activo' : 'Inactivo'
            ]);
        }
        
        fclose($output);
        exit;
    }
}
