<?php

define('BASE_PATH', dirname(__DIR__)); // Define la ruta base del proyecto

require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/models/Usuario.php';
require_once BASE_PATH . '/app/models/Paciente.php';
require_once BASE_PATH . '/app/models/Cita.php';
require_once BASE_PATH . '/app/models/Odontologo.php';

// Resolver controller
$controller = $_GET['controller'] ?? 'auth';

// Controladores que usan 'index' como acción por defecto
$controllersWithIndex = ['paciente', 'cita', 'odontologo', 'usuario', 'dashboard'];

// Resolver action
$action = $_GET['action'] ?? (
    in_array($controller, $controllersWithIndex) ? 'index' : 'loginForm'
);

$controllerClass = ucfirst($controller) . 'Controller';
$controllerFile  = BASE_PATH . '/app/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    die("Controlador no encontrado");
}

require_once $controllerFile;

$instance = new $controllerClass();

if (!method_exists($instance, $action)) {
    die("Acción no encontrada");
}

error_log("Controller recibido: " . ($controller ?? 'null'));
error_log("Action resuelta: " . ($action ?? 'null'));
error_log("Clase controlador: " . $controllerClass);
error_log("Archivo controlador: " . $controllerFile);

$instance->$action();
