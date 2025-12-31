<?php
class AuthController {

    public function loginForm() {
        require BASE_PATH . '/app/views/auth/login.php';
    }

    public function login() {
        session_start();

        $usuario = (new Usuario())->login(
            $_POST['usuario'], 
            $_POST['password']
        );

        if ($usuario) {
            $_SESSION['usuario'] = $usuario['usuario']; // Almacenar el nombre de usuario en la sesión
            $_SESSION['rol'] = $usuario['rol']; // Almacenar el rol del usuario
            $_SESSION['id_usuario'] = $usuario['id_usuario']; // Almacenar el ID del usuario
            
            // Redirigir según el rol
            if ($usuario['rol'] === 'ADMIN') {
                header('Location: index.php?controller=dashboard'); // Admin va al dashboard
            } else {
                header('Location: index.php?controller=cita'); // Otros roles van a citas
            }
        } else {
            header('Location: index.php?error=login'); // Redirigir de vuelta al formulario de login con un error
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php');
    }
}
